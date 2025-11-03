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

    public function getLanterns(): array
    {
        global $cache;

        $key = 'chaos_lanterns';
        if ($cache->exists($key)) {
            $cached = $cache->get($key);
            if ($cached !== false) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded))
                    return $decoded;
            }
        }

        $this->db->query("SELECT * FROM chaos_lanterns ORDER BY rank ASC");
        $this->db->execute();
        $rows = $this->db->fetch_row() ?: [];

        $cache->setEx($key, 3600, json_encode($rows));
        return $rows;
    }

    public function getChaosPass(): array
    {
        global $cache;

        $key = 'chaos_pass';
        if ($cache->exists($key)) {
            $cached = $cache->get($key);
            if ($cached !== false) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded))
                    return $decoded;
            }
        }

        $this->db->query("SELECT * FROM chaos_pass ORDER BY curse_level ASC, id ASC");
        $this->db->execute();
        $rows = $this->db->fetch_row() ?: [];

        $cache->setEx($key, 3600, json_encode($rows));
        return $rows;
    }

    public function getChaosUserState($userId): array
    {
        global $cache;

        $key = "chaos_user_state:$userId";
        if ($cache->exists($key)) {
            $cached = $cache->get($key);
            if ($cached !== false) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded))
                    return $decoded;
            }
        }

        $this->db->query("SELECT * FROM chaos_event_user WHERE user_id = ?");
        $this->db->execute([$userId]);
        $row = $this->db->fetch_row(true);

        if (!$row || empty($row)) {
            $this->db->query("INSERT INTO chaos_event_user (user_id, lantern_equipped) VALUES (?, 1)");
            $this->db->execute([$userId]);

            $this->db->query("INSERT INTO chaos_pass_user (user_id) VALUES (?)");
            $this->db->execute([$userId]);

            $this->db->query("SELECT * FROM chaos_event_user WHERE user_id = ?");
            $this->db->execute([$userId]);
            $row = $this->db->fetch_row(true);
        }

        $cache->setEx($key, 300, json_encode($row));
        return $row;
    }

    public function awardSouls(int $userId, int $qty = 1, string $reason = 'raid'): array
    {
        global $cache;

        $state = $this->getChaosUserState($userId);
        $ckey = "chaos_user_state:$userId";

        $baseQty = max(1, (int) $qty);

        $allLanterns = $this->getLanterns();

        $equippedId = (int) ($state['lantern_equipped'] ?? 0);
        $lantern = null;
        foreach ($allLanterns as $row) {
            if ((int) $row['id'] === $equippedId) {
                $lantern = $row;
                break;
            }
        }

        $soulsThisHour = $this->getSoulsThisHour($userId);
        $limit = (int) ($lantern['souls_hour'] ?? 0);
        if ($limit <= 0)
            return $state;

        if ($soulsThisHour >= $limit) {
            return $state;
        }

        $remaining = $limit - (int) $soulsThisHour;

        $bonusPct = isset($lantern['soul_bonus']) ? (int) $lantern['soul_bonus'] : 0;
        $mult = 1 + max(0, $bonusPct) / 100;
        $awarded = max(1, $baseQty * $mult);
        $grant = min($awarded, $remaining);

        if ($grant <= 0) {
            return $state;
        }

        $state['souls_current'] = (int) $state['souls_current'] + $awarded;
        $state['souls_collected'] = (int) $state['souls_collected'] + $awarded;
        $state['curse_exp'] = (int) $state['curse_exp'] + $awarded;

        $nextLevel = (int) $state['curse_level'] + 1;

        $this->db->query("SELECT curse_exp_req FROM chaos_pass WHERE curse_level = ? LIMIT 1");
        $this->db->execute([$nextLevel]);
        $row = $this->db->fetch_row(true);
        $need = isset($row['curse_exp_req']) ? (int) $row['curse_exp_req'] : null;

        $didLevel = false;
        if ($need !== null && $state['curse_exp'] >= $need) {
            $state['curse_level'] = $nextLevel;
            $state['curse_exp'] = $state['curse_exp'] - $need;
            $didLevel = true;
        }
        $cache->setEx($ckey, 300, json_encode($state));

        try {
            $this->db->startTrans();

            $this->db->query("
                SELECT souls_current, souls_collected, curse_level, curse_exp
                FROM chaos_event_user
                WHERE user_id = ?
                FOR UPDATE
            ");
            $this->db->execute([$userId]);
            $cur = $this->db->fetch_row(true) ?: ['souls_current' => 0, 'souls_collected' => 0, 'curse_level' => 0, 'curse_exp' => 0];

            $newSoulsCurrent = (int) $cur['souls_current'] + $awarded;
            $newSoulsCollected = (int) $cur['souls_collected'] + $awarded;
            $newCurseLevel = (int) $cur['curse_level'];
            $newCurseExp = (int) $cur['curse_exp'] + $awarded;

            $nextLevelDb = $newCurseLevel + 1;
            $needDb = null;
            if ($need === null) {
                $this->db->query("SELECT curse_exp_req FROM chaos_pass WHERE curse_level = ? LIMIT 1");
                $this->db->execute([$nextLevelDb]);
                $nrow = $this->db->fetch_row(true);
                $needDb = isset($nrow['curse_exp_req']) ? (int) $nrow['curse_exp_req'] : null;
            } else {
                $needDb = $need;
            }

            if ($needDb !== null && $newCurseExp >= $needDb) {
                $newCurseLevel = $nextLevelDb;
                $newCurseExp = $newCurseExp - $needDb;
            }

            $this->db->query("
                UPDATE chaos_event_user
                SET souls_current   = ?,
                    souls_collected = ?,
                    curse_level     = ?,
                    curse_exp       = ?
                WHERE user_id = ?
            ");
            $this->db->execute([
                $newSoulsCurrent,
                $newSoulsCollected,
                $newCurseLevel,
                $newCurseExp,
                $userId
            ]);

            $this->db->query("INSERT INTO chaos_souls_ledger (user_id, delta, reason) VALUES (?, ?, ?)");
            $this->db->execute([$userId, $awarded, $reason]);


            $this->db->endTrans();

            $this->bumpSoulLimitCache($userId, $awarded);

            return $state;
        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            $cache->del($ckey);
            throw $e;
        }
    }

    public function getSoulsThisHour(int $userId): int
    {
        global $cache;

        $key = "user_souls_hour:$userId";
        if ($cache->exists($key)) {
            $raw = $cache->get($key);
            if ($raw !== false && is_numeric($raw))
                return (int) $raw;
        }

        $this->db->query("
        SELECT
          COALESCE(SUM(CASE WHEN delta > 0 THEN delta ELSE 0 END), 0) AS total,
          TIMESTAMPDIFF(
            SECOND,
            NOW(),
            DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL 1 HOUR)
          ) AS ttl_secs
        FROM chaos_souls_ledger
        WHERE user_id = ?
          AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')
          AND created_at <  DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL 1 HOUR)
    ");
        $this->db->execute([$userId]);
        $row = $this->db->fetch_row(true) ?: ['total' => 0, 'ttl_secs' => 60];
        $total = (int) $row['total'];
        $ttl = max(1, (int) $row['ttl_secs']);

        $cache->setEx($key, $ttl, (string) $total);
        return $total;
    }

    public function bumpSoulLimitCache(int $userId, int $delta): void
    {
        if ($delta <= 0)
            return;

        global $cache;
        $key = "user_souls_hour:$userId";

        $tzUtc = new DateTimeZone('UTC');
        $now = new DateTimeImmutable('now', $tzUtc);
        $start = $now->setTime((int) $now->format('H'), 0, 0);
        $end = $start->modify('+1 hour');
        $ttl = max(1, $end->getTimestamp() - $now->getTimestamp());

        $current = 0;
        if ($cache->exists($key)) {
            $raw = $cache->get($key);
            if ($raw !== false && is_numeric($raw))
                $current = (int) $raw;
        }

        $cache->setEx($key, $ttl, (string) ($current + $delta));
    }

    public function getChaosPassUser(int $userId, bool $ensureRow = true): array
    {
        global $cache;

        $ckey = "chaos_pass_user:$userId";

        if ($cache->exists($ckey)) {
            $raw = $cache->get($ckey);
            if ($raw !== false) {
                $val = json_decode($raw, true);
                if (is_array($val))
                    return $val;
            }
        }

        if ($ensureRow) {
            $this->db->query("
                INSERT INTO chaos_pass_user (user_id, is_premium, upgraded_at)
                VALUES (?, 0, NULL)
                ON DUPLICATE KEY UPDATE user_id = user_id
            ");
            $this->db->execute([$userId]);
        }

        $this->db->query("
            SELECT user_id, is_premium, upgraded_at
            FROM chaos_pass_user
            WHERE user_id = ?
            LIMIT 1
        ");
        $this->db->execute([$userId]);
        $row = $this->db->fetch_row(true);
        $row ??= [
            'user_id' => $userId,
            'is_premium' => 0,
            'upgraded_at' => null,
        ];

        $cache->setEx($ckey, 600, json_encode($row)); // 10 min

        return $row;
    }

    public function bustChaosPassUserCache(int $userId): void
    {
        global $cache;
        $cache->del("chaos_pass_user:$userId");
    }

    public function upgradePassToPremium(int $userId): void
    {
        $this->db->query("
            INSERT INTO chaos_pass_user (user_id, is_premium, upgraded_at)
            VALUES (?, 1, NOW())
            ON DUPLICATE KEY UPDATE is_premium = 1, upgraded_at = NOW()
        ");
        $this->db->execute([$userId]);

        $this->bustChaosPassUserCache($userId);
    }

    public function getChaosPassClaims(int $userId): array
    {
        global $cache;

        $ckey = "chaos_pass_claims:$userId";
        if ($cache->exists($ckey)) {
            $raw = $cache->get($ckey);
            if ($raw !== false) {
                $val = json_decode($raw, true);
                if (is_array($val)) {
                    return array_values(array_map('intval', $val));
                }
            }
        }

        $this->db->query("SELECT pass_id FROM chaos_pass_claims WHERE user_id = ?");
        $this->db->execute([$userId]);
        $rows = $this->db->fetch_row() ?: [];

        $ids = array_values(array_map(static fn($r) => (int) $r['pass_id'], $rows));

        $cache->setEx($ckey, 120, json_encode($ids));

        return $ids;
    }

    public function bustChaosPassClaimsCache(int $userId): void
    {
        global $cache;
        $cache->del("chaos_pass_claims:$userId");
    }

    public function hasClaimedPass(int $userId, int $passId): bool
    {
        $claimed = $this->getChaosPassClaims($userId);
        return in_array($passId, $claimed, true);
    }

    public function claimAllAvailableRewards(int $userId): array
    {
        $result = [
            'claimed' => [],
            'skipped' => [],
            'premium_locked' => [],
            'rewards' => [],
        ];

        try {
            $this->db->startTrans();

            $userState = $this->getChaosUserState($userId);
            $passUser = $this->getChaosPassUser($userId);
            $isPremium = (int) ($passUser['is_premium'] ?? 0) === 1;
            $curLevel = (int) ($userState['curse_level'] ?? 0);

            $pass = $this->getChaosPass();
            $claimedIds = $this->getChaosPassClaims($userId);

            $claimedSet = [];
            foreach ($claimedIds as $cid)
                $claimedSet[(int) $cid] = true;

            foreach ($pass as $row) {
                $passId = (int) $row['id'];
                $tier = (int) ($row['curse_level'] ?? 0);
                $isPrem = (int) ($row['is_premium'] ?? 0) === 1;

                if ($curLevel < $tier) {
                    $result['skipped'][] = $passId;
                    continue;
                }

                if (isset($claimedSet[$passId])) {
                    $result['skipped'][] = $passId;
                    continue;
                }

                if ($isPrem && !$isPremium) {
                    $result['premium_locked'][] = $passId;
                    continue;
                }

                $this->db->query("INSERT IGNORE INTO chaos_pass_claims (user_id, pass_id) VALUES (?, ?)");
                $this->db->execute([$userId, $passId]);

                if ($this->db->affected_rows() > 0) {
                    $grant = $this->grantChaosReward($userId, $row);
                    if ($grant !== null) {
                        $result['rewards'][] = $grant;
                    }
                    $result['claimed'][] = $passId;
                    $claimedSet[$passId] = true;
                } else {
                    $result['skipped'][] = $passId;
                }
            }

            $this->db->endTrans();

            // Bust cache so UI reflects new claims
            $this->bustChaosPassClaimsCache($userId);

        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            throw $e;
        }

        return $result;
    }

    public function grantChaosReward($userId, $row)
    {
        $type = strtolower((string) ($row['reward_type'] ?? ''));
        $qty = (int) ($row['reward_qty'] ?? 0);
        $ref = isset($row['reward_ref_id']) ? (int) $row['reward_ref_id'] : 0;

        if ($qty <= 0)
            return '';

        switch ($type) {
            case 'item':
                Give_Item($ref, $userId, $qty);
                $item = Get_Item($ref);
                return $item['itemname'] . " x$qty";
            case 'money':
                $this->db->query("SELECT bank FROM grpgusers WHERE id = ?");
                $this->db->execute([$userId]);
                $balance = $this->db->fetch_single();

                $this->db->query("UPDATE grpgusers SET bank = bank + ? WHERE id = ?");
                $this->db->execute([$qty, $userId]);

                $balance += $qty;

                $this->db->query("INSERT INTO bank_log (userid, amount, `action`, newbalance, `timestamp`) VALUES (?, ?, 'mdep', ?,  unix_timestamp())");
                $this->db->execute([$userId, $qty, $balance]);

                return number_format($qty) . ' money';
            case 'points':
                $this->db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $this->db->execute([$qty, $userId]);

                return number_format($qty) . ' points';
            case 'exp':
                $user = new User($userId);
                $exp = $user->maxexp / 100 * $qty;
                $this->db->query("UPDATE grpgusers SET exp = exp + ? WHERE id = ?");
                $this->db->execute([$exp, $userId]);

                return number_format($exp) . ' EXP';
            default:
                return '';
        }
    }

    public function upgradeLanternWithSouls($userId, $lanternId)
    {
        global $cache;

        $state = $this->getChaosUserState($userId);
        $ckey = "chaos_user_state:$userId";

        $lanterns = $this->getLanterns();
        $lanternForUpgrade = null;
        $currentLantern = null;
        foreach ($lanterns as $lantern) {
            if ($lantern['id'] == $state['lantern_equipped']) {
                $currentLantern = $lantern;
            }
            if ($lantern['id'] == $lanternId) {
                $lanternForUpgrade = $lantern;
            }
        }

        if ($currentLantern === null || $lanternId === null) {
            return 'Something unexpected went wrong, please contact Matt';
        }

        if ($lanternForUpgrade <= $currentLantern) {
            return 'You already own this lantern or a better one.';
        }

        $soulPrice = (int) ($lanternForUpgrade['soul_price'] ?? 0);
        $soulsCurrent = (int) ($state['souls_current'] ?? 0);

        if ($soulsCurrent < $soulPrice) {
            return 'You do not have enough souls to upgrade your lantern.';
        }


        try {
            $this->db->startTrans();

            $this->db->query("
            UPDATE chaos_event_user
            SET souls_current = souls_current - ?,
                lantern_equipped = ?
            WHERE user_id = ?
              AND souls_current >= ?
        ");
            $this->db->execute([$soulPrice, $lanternId, $userId, $soulPrice]);

            if ($this->db->affected_rows() === 0) {
                $this->db->cancelTransaction();
                return 'Upgrade failed — not enough souls or concurrent update.';
            }

            $this->db->endTrans();

            $state['souls_current'] -= $soulPrice;
            $state['lantern_equipped'] = $lanternId;

            $cache->setEx($ckey, 300, json_encode($state));

            return [
                'ok' => true,
                'message' => sprintf(
                    'Lantern upgraded to %s! (-%s Souls)',
                    $lanternForUpgrade['name'] ?? 'Unknown',
                    number_format($soulPrice)
                ),
                'new_state' => $state,
            ];

        } catch (Throwable $e) {
            $this->db->cancelTransaction();
            return 'Upgrade failed: ' . $e->getMessage();
        }
    }
}
