<?php

require_once __DIR__ . '/../../dbcon.php';
require_once __DIR__ . '/../../classes.php';
require_once __DIR__ . '/../../database/pdo_class.php';
require_once __DIR__ . '/../cache.php';
require_once __DIR__ . '/../functions.php';

class BattlepassRepository
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

    /**
     * Fetches all Battlepass Categories, first from
     * cache if possible, if not from DB.
     * 
     * @return array
     */
    public function getAllCategories(): array
    {
        global $cache;

        $key = "bp_categories";
        if ($cache->exists($key)) {
            $cached = $cache->get($key);
            if ($cached !== false) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        $this->db->query("SELECT * FROM bp_category ORDER BY id DESC");
        $this->db->execute();
        $rows = $this->db->fetch_row() ?: [];

        $cache->setEx($key, 3600, json_encode($rows));
        return $rows;
    }

    public function getCategoryById(int $id): ?array
    {
        $categories = $this->getAllCategories();

        $category = null;
        foreach ($categories as $c) {
            if ((int) $c['id'] == $id) {
                $category = $c;
            }
        }

        return $category;
    }

    public function getCategoryByMonthYear(string $monthYear): ?array
    {
        $categories = $this->getAllCategories();

        $category = null;
        foreach ($categories as $c) {
            if ((int) $c['month_year'] == $monthYear) {
                $category = $c;
            }
        }

        return $category;
    }

    public function createCategory(string $monthYear): int
    {
        $this->db->query("INSERT INTO bp_category (month_year) VALUES (?)");
        $this->db->execute([$monthYear]);

        return (int) $this->db->insert_id();
    }

    public function getOrCreateCategory(string $monthYear): int
    {
        $existing = $this->getCategoryByMonthYear($monthYear);
        if ($existing) {
            return (int) $existing['id'];
        }

        return $this->createCategory($monthYear);
    }

    public function bustCategoriesCache(): void
    {
        global $cache;

        $cache->del("bp_categories");
    }

    public function addChallenge(int $categoryId, string $type, int $amount, int $prize, bool $isPremium)
    {
        $this->db->query("INSERT INTO bp_category_challenges (bp_category_id, `type`, amount, prize, is_premium) VALUES (?, ?, ?, ?, ?)");
        $this->db->execute([$categoryId, $type, $amount, $prize, $isPremium]);
    }

    public function getAllChallenges(int $categoryId): array
    {
        $this->db->query("SELECT * FROM bp_category_challenges WHERE bp_category_id = ? ORDER BY id ASC");
        $this->db->execute([$categoryId]);
        $rows = $this->db->fetch_row() ?: [];

        return $rows;
    }

    public function editChallenge(int $id, array $row)
    {
        $this->db->query("UPDATE bp_category_challenges SET `type` = ?, amount = ?, prize = ?, is_premium = ? WHERE id = ?");
        $this->db->execute([$row['type'], $row['amount'], $row['prize'], $row['is_premium'], $id]);
    }

    public function deleteChallengesByCategory(int $categoryId): void
    {
        $this->db->query("DELETE FROM bp_category_challenges WHERE bp_category_id = ?");
        $this->db->execute([$categoryId]);
    }


    public function addPrize(int $categoryId, int $cost, string $type, int $amount, int $entityId, bool $isPremium)
    {
        $this->db->query("INSERT INTO bp_category_prizes (bp_category_id, cost, `type`, amount, entity_id, is_premium) VALUES (?, ?, ?, ?, ?, ?)");
        $this->db->execute([$categoryId, $cost, $type, $amount, $entityId, $isPremium]);
    }

    public function getAllPrizes(int $categoryId): array
    {
        $this->db->query("SELECT * FROM bp_category_prizes WHERE bp_category_id = ? ORDER BY id ASC");
        $this->db->execute([$categoryId]);
        $rows = $this->db->fetch_row() ?: [];

        return $rows;
    }

    public function editPrize(int $id, array $row)
    {
        $this->db->query("UPDATE bp_category_prizes SET cost = ?, `type` = ?, amount = ?, entity_id = ?, is_premium = ? WHERE id = ?");
        $this->db->execute([$row['cost'], $row['type'], $row['amount'], $row['entity_id'], $row['is_premium'], $id]);
    }

    public function deletePrizesByCategory(int $categoryId): void
    {
        $this->db->query("DELETE FROM bp_category_prizes WHERE bp_category_id = ?");
        $this->db->execute([$categoryId]);
    }
}