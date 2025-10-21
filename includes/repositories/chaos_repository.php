<?php

require_once __DIR__ . '/../../dbcon.php';
require_once __DIR__ . '/../../classes.php';
require_once __DIR__ . '/../../database/pdo_class.php';
require_once __DIR__ . '/../cache.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../dto/chaos_dto.php';

class ChaosRepository
{
    /** @var database */
    private $db;

    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            global $db;
            if (!$db) {
                throw new RuntimeException('DB handle not available');
            }
            $this->db = $db;
        }
    }

    public function getUserEventState(int $userId): ChaosUser
    {
        $this->db->query("SELECT user_id, souls_current, souls_collected, souls_spent,
                             lantern_equipped, curse_level, curse_exp, updated_at
                      FROM chaos_event_user WHERE user_id = ? LIMIT 1");
        $this->db->execute([$userId]);
        $row = $this->db->fetch_row(true);
        if (!$row) {
            $row = [
                'user_id' => $userId,
                'souls_current' => 0,
                'souls_collected' => 0,
                'souls_spent' => 0,
                'lantern_equipped' => null,
                'curse_level' => 0,
                'curse_exp' => 0,
                'updated_at' => null
            ];
        }
        return ChaosUser::fromRow($row);
    }

    public function getSoulsLastHour(int $userId): int
    {
        $this->db->query("
            SELECT COALESCE(SUM(delta),0) AS s
            FROM chaos_souls_ledger
            WHERE user_id = ? AND created_at >= NOW() - INTERVAL 1 HOUR
        ");
        $this->db->execute([$userId]);
        $row = $this->db->fetch_row(true);
        return (int) ($row['s'] ?? 0);
    }

    public function awardSouls(int $userId, int $delta, string $reason, ?string $refId = null): ChaosUser
    {
        if ($delta === 0) {
            return $this->getUserEventState($userId);
        }

        try {
            $this->db->startTrans();
            $this->db->query("INSERT INTO chaos_souls_ledger (user_id, delta, reason, ref_id) VALUES (?,?,?,?)");
            $this->db->execute([$userId, $delta, $reason, $refId]);

            $this->db->query("
                INSERT INTO chaos_event_user (user_id, souls_current, souls_collected, souls_spent, curse_level, curse_exp)
                VALUES (?, 0, 0, 0, 0, 0)
                ON DUPLICATE KEY UPDATE user_id = user_id
            ");
            $this->db->execute([$userId]);

            $this->db->query("
                UPDATE chaos_event_user
                SET souls_current = souls_current + ?,
                    souls_collected = souls_collected + ?,
                    souls_spent = souls_spent + ?
                WHERE user_id = ?
            ");
            $this->db->execute([
                $delta,
                max(0, $delta),
                max(0, -$delta),
                $userId
            ]);

            $this->db->endTrans();
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }

        return $this->getUserEventState($userId);
    }

    /**
     * Purchase a store item with souls; returns updated balances + the purchased item row.
     */
    public function purchaseStoreItem(int $userId, int $storeItemId): array
    {
        try {
            $this->db->startTrans();

            $this->db->query("SELECT souls_current FROM chaos_event_user WHERE user_id = ? FOR UPDATE");
            $this->db->execute([$userId]);
            $balance = (int) ($this->db->fetch_single() ?? 0);

            $this->db->query("
                SELECT id, item_id, amount, soul_price, is_active
                FROM chaos_store_items
                WHERE id = ? AND is_active = 1
                LIMIT 1
            ");
            $this->db->execute([$storeItemId]);
            $item = $this->db->fetch_row(true);
            if (!$item) {
                throw new RuntimeException('Store item is unavailable.');
            }

            $price = (int) $item['soul_price'];
            if ($balance < $price) {
                throw new RuntimeException('Not enough Souls.');
            }

            // Deduct & log
            $this->db->query("
                UPDATE chaos_event_user
                   SET souls_current = souls_current - ?,
                       souls_spent   = souls_spent + ?
                 WHERE user_id = ?
            ");
            $this->db->execute([$price, $price, $userId]);

            $this->db->query("
                INSERT INTO chaos_store_purchases (user_id, store_item_id, souls_spent)
                VALUES (?, ?, ?)
            ");
            $this->db->execute([$userId, $storeItemId, $price]);

            $this->db->query("
                INSERT INTO chaos_souls_ledger (user_id, delta, reason, ref_id)
                VALUES (?, ?, 'store_purchase', ?)
            ");
            $this->db->execute([$userId, -$price, (string) $storeItemId]);

            $this->db->endTrans();

            return [
                'item' => $item,
                'state' => $this->getUserEventState($userId),
                'status' => 'ok'
            ];
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }
    }

    public function upgradeLanternWithSouls(int $userId, int $targetLanternId): array
    {
        try {
            $this->db->startTrans();

            // Lock the user row and get current lantern + balance
            $this->db->query("
                SELECT ceu.souls_current, ceu.lantern_equipped, cl.rank AS current_rank
                FROM chaos_event_user ceu
                LEFT JOIN chaos_lanterns cl ON cl.id = ceu.lantern_equipped
                WHERE ceu.user_id = ?
                FOR UPDATE
            ");
            $this->db->execute([$userId]);
            $u = $this->db->fetch_row(true) ?: ['souls_current' => 0, 'lantern_equipped' => null, 'current_rank' => null];

            // Load target lantern (price + rank)
            $this->db->query("
                SELECT id, name, soul_price, rank
                FROM chaos_lanterns
                WHERE id = ?
                LIMIT 1
            ");
            $this->db->execute([$targetLanternId]);
            $lantern = $this->db->fetch_row(true);
            if (!$lantern) {
                throw new RuntimeException('Lantern not found.');
            }

            $currentRank = (int) ($u['current_rank'] ?? 0);
            $targetRank = (int) $lantern['rank'];

            // Enforce strictly-upgrade
            if ($currentRank >= $targetRank) {
                throw new RuntimeException('Cannot downgrade or re-buy the same rank.');
            }

            $price = (int) $lantern['soul_price'];
            $balance = (int) $u['souls_current'];
            if ($balance < $price) {
                throw new RuntimeException('Not enough Souls.');
            }

            // Deduct, equip, and log
            $this->db->query("
                UPDATE chaos_event_user
                SET souls_current    = souls_current - ?,
                    souls_spent      = souls_spent + ?,
                    lantern_equipped = ?
                WHERE user_id = ?
            ");
            $this->db->execute([$price, $price, $targetLanternId, $userId]);

            $this->db->query("
                INSERT INTO chaos_souls_ledger (user_id, delta, reason, ref_id)
                VALUES (?, ?, 'lantern_upgrade', ?)
            ");
            $this->db->execute([$userId, -$price, (string) $targetLanternId]);

            $this->db->endTrans();
            return $this->getUserEventState($userId);
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }
    }

    /* =========================
     * CHAOS PASS API
     * ========================= */

    public function getPassState(int $userId): ChaosPassState
    {
        // user + premium flag
        $this->db->query("
        SELECT ceu.curse_level, ceu.curse_exp, COALESCE(cpu.is_premium, 0) AS is_premium
        FROM chaos_event_user ceu
        LEFT JOIN chaos_pass_user cpu ON cpu.user_id = ceu.user_id
        WHERE ceu.user_id = ?
        LIMIT 1
    ");
        $this->db->execute([$userId]);
        $u = $this->db->fetch_row(true) ?: ['curse_level' => 0, 'curse_exp' => 0, 'is_premium' => 0];

        $level = (int) $u['curse_level'];
        $exp = (int) $u['curse_exp'];
        $isPremium = ((int) $u['is_premium'] === 1);

        // next level requirement (null if max)
        $this->db->query("SELECT curse_req_exp FROM curse_levels WHERE level = ? LIMIT 1");
        $this->db->execute([$level + 1]);
        $next = $this->db->fetch_row(true);
        $nextReq = isset($next['curse_req_exp']) ? (int) $next['curse_req_exp'] : null;
        $atMax = ($nextReq === null);

        // progress calculation (clamp exp to requirement)
        $progressPct = 100;
        if (!$atMax) {
            $den = max(1, $nextReq);
            $num = max(0, min($exp, $den)); // clamp
            $progressPct = (int) min(100, round(($num / $den) * 100));
        }

        // claimables as int[]
        $claimable = array_map('intval', $this->getClaimablePassIds($userId, $level, $isPremium));

        return new ChaosPassState(
            curseLevel: $level,
            curseExp: $exp,
            isPremium: $isPremium,
            nextReqExp: $nextReq,
            progressPct: $progressPct,
            atMaxLevel: $atMax,
            claimableIds: $claimable
        );
    }

    public function claimPassReward(int $userId, int $passId): array
    {
        try {
            $this->db->startTrans();

            $this->db->query("SELECT id, is_premium, curse_level, reward_type, reward_ref_id, reward_qty
                          FROM chaos_pass WHERE id = ? LIMIT 1");
            $this->db->execute([$passId]);
            $p = $this->db->fetch_row(true);
            if (!$p)
                throw new RuntimeException('Reward not found.');

            $this->db->query("
                SELECT ceu.curse_level, COALESCE(cpu.is_premium,0) AS is_premium
                FROM chaos_event_user ceu
                LEFT JOIN chaos_pass_user cpu ON cpu.user_id = ceu.user_id
                WHERE ceu.user_id = ? FOR UPDATE
            ");
            $this->db->execute([$userId]);
            $u = $this->db->fetch_row(true) ?: ['curse_level' => 0, 'is_premium' => 0];

            if ((int) $u['curse_level'] < (int) $p['curse_level']) {
                throw new RuntimeException('Not eligible yet.');
            }
            if ((int) $p['is_premium'] === 1 && (int) $u['is_premium'] !== 1) {
                throw new RuntimeException('Premium required.');
            }

            $this->db->query("INSERT INTO chaos_pass_claims (user_id, pass_id) VALUES (?, ?)");
            try {
                $this->db->execute([$userId, $passId]);
            } catch (Throwable $dup) {
                $this->db->cancelTransaction();
                return ['status' => 'already_claimed', 'state' => $this->getPassState($userId)];
            }

            $this->grantPassReward($userId, $p);

            $this->db->endTrans();
            return ['status' => 'ok', 'state' => $this->getPassState($userId)];
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }
    }

    public function claimAllEligiblePassRewards(int $userId): array
    {
        try {
            $this->db->startTrans();

            $this->db->query("
                SELECT ceu.curse_level, COALESCE(cpu.is_premium,0) AS is_premium
                FROM chaos_event_user ceu
                LEFT JOIN chaos_pass_user cpu ON cpu.user_id = ceu.user_id
                WHERE ceu.user_id = ? FOR UPDATE
            ");
            $this->db->execute([$userId]);
            $u = $this->db->fetch_row(true) ?: ['curse_level' => 0, 'is_premium' => 0];

            $ids = $this->getClaimablePassIds($userId, (int) $u['curse_level'], (bool) $u['is_premium'], true);

            $granted = [];
            if ($ids) {
                $in = implode(',', array_fill(0, count($ids), '?'));
                $this->db->query("
                    SELECT id, is_premium, curse_level, reward_type, reward_ref_id, reward_qty
                    FROM chaos_pass
                    WHERE id IN ($in)
                ");
                $this->db->execute($ids);
                $rows = $this->db->fetch_row();

                foreach ($rows as $p) {
                    $this->db->query("INSERT INTO chaos_pass_claims (user_id, pass_id) VALUES (?, ?)");
                    $this->db->execute([$userId, (int) $p['id']]);
                    $this->grantPassReward($userId, $p);
                    $granted[] = (int) $p['id'];
                }
            }

            $this->db->endTrans();
            return ['status' => 'ok', 'claimed_ids' => $granted, 'state' => $this->getPassState($userId)];
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }
    }

    public function upgradePassToPremium(int $userId): void
    {
        $this->db->query("
            INSERT INTO chaos_pass_user (user_id, is_premium, upgraded_at)
            VALUES (?, 1, NOW())
            ON DUPLICATE KEY UPDATE is_premium = VALUES(is_premium), upgraded_at = NOW()
        ");
        $this->db->execute([$userId]);
    }

    /**
     * Add curse EXP, auto-level up as needed.
     * Returns: ['gained_levels' => n, 'new_level' => L, 'new_exp' => E]
     */
    public function addCurseExp(int $userId, int $exp): array
    {
        if ($exp <= 0)
            return $this->getPassState($userId);

        try {
            $this->db->startTrans();

            // lock row
            $this->db->query("SELECT curse_level, curse_exp FROM chaos_event_user WHERE user_id=? FOR UPDATE");
            $this->db->execute([$userId]);
            $u = $this->db->fetch_row(true) ?: ['curse_level' => 0, 'curse_exp' => 0];

            $level = (int) $u['curse_level'];
            $expNow = (int) $u['curse_exp'] + $exp;

            // fetch level thresholds up to a reasonable window
            $this->db->query("
                SELECT level, curse_req_exp
                FROM curse_levels
                WHERE level BETWEEN ? AND ? + 5
                ORDER BY level ASC
            ");
            $this->db->execute([$level + 1, $level]);
            $levels = $this->db->fetch_row();

            $gained = 0;
            foreach ($levels as $row) {
                $need = (int) $row['curse_req_exp'];
                if ($expNow >= $need) {
                    $level++;
                    $gained++;
                    $expNow -= $need;
                } else
                    break;
            }

            $this->db->query("UPDATE chaos_event_user SET curse_level=?, curse_exp=? WHERE user_id=?");
            $this->db->execute([$level, $expNow, $userId]);

            $this->db->endTrans();
            return ['gained_levels' => $gained, 'new_level' => $level, 'new_exp' => $expNow];
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }
    }

    private function getClaimablePassIds(int $userId, int $userLevel, bool $isPremium, bool $onlyUnclaimed = false): array
    {
        $this->db->query("
            SELECT p.id
            FROM chaos_pass p
            WHERE p.curse_level <= ?
            AND (p.is_premium = 0 OR ? = 1)
        ");
        $this->db->execute([$userLevel, (int) $isPremium]);
        $all = $this->db->fetch_row();
        $ids = array_map(static fn($r) => (int) $r['id'], $all ?: []);

        if ($onlyUnclaimed && $ids) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $this->db->query("
                SELECT pass_id FROM chaos_pass_claims
                WHERE user_id = ? AND pass_id IN ($in)
            ");
            $params = array_merge([$userId], $ids);
            $this->db->execute($params);
            $claimed = $this->db->fetch_row() ?: [];
            $claimedSet = array_flip(array_map(static fn($r) => (int) $r['pass_id'], $claimed));
            $ids = array_values(array_filter($ids, static fn($id) => !isset($claimedSet[$id])));
        }

        return $ids;
    }

    private function grantPassReward(int $userId, array $passRow): void
    {
        $type = $passRow['reward_type'];
        $qty = (int) $passRow['reward_qty'];
        $ref = $passRow['reward_ref_id'];

        switch ($type) {
            case 'souls':
                $this->awardSouls($userId, $qty, 'pass_reward', (string) $passRow['id']);
                break;
            case 'money':
                break;
            case 'points':
                break;
            case 'item':
                break;
            case 'exp':
                break;
            default:
                break;
        }
    }

    private function grantLanternIfUpgrade(int $userId, int $lanternId): void
    {
        $this->db->query("
            SELECT cl.rank AS current_rank
            FROM chaos_event_user u
            LEFT JOIN chaos_lanterns cl ON cl.id = u.lantern_equipped
            WHERE u.user_id = ?
        ");
        $this->db->execute([$userId]);
        $curr = $this->db->fetch_row(true);
        $currentRank = (int) ($curr['current_rank'] ?? 0);

        $this->db->query("SELECT rank FROM chaos_lanterns WHERE id = ? LIMIT 1");
        $this->db->execute([$lanternId]);
        $targetRank = (int) ($this->db->fetch_row(true)['rank'] ?? 0);

        if ($targetRank > $currentRank) {
            $this->db->query("UPDATE chaos_event_user SET lantern_equipped = ? WHERE user_id = ?");
            $this->db->execute([$lanternId, $userId]);
        }
    }
}