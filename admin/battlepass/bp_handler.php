<?php
require_once __DIR__ . '/../../ajax_header.php';
require_once __DIR__ . '/../../includes/repositories/battlepass_repository.php';

header('Content-Type: application/json; charset=utf-8');

$user_class = new User($_SESSION['id']);

if ($user_class->admin < 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

/** Helpers */
function jerr(int $code, string $msg, array $extra = [])
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg] + $extra);
    exit;
}

function jok(array $data = [], int $code = 200)
{
    http_response_code($code);
    echo json_encode(['ok' => true] + $data);
    exit;
}

function need_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jerr(405, 'method not allowed');
    }
    if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        jerr(400, 'invalid csrf');
    }
}

function bint($v): int
{
    return (isset($v) && ($v === '1' || $v === 1 || $v === true || $v === 'on')) ? 1 : 0;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$repo = new BattlepassRepository();

/** ROUTER */
try {
    switch ($action) {
        /** =======================
         *  CATEGORIES (READ)
         *  ======================= */
        case 'list_categories': { // GET
            $rows = $repo->getAllCategories();
            jok(['data' => array_values($rows)]);
        }

        case 'get_category': { // GET: id=...
            $id = (int) ($_GET['id'] ?? 0);
            if ($id <= 0)
                jerr(400, 'missing id');

            $cat = method_exists($repo, 'getCategoryById') ? $repo->getCategoryById($id) : null;
            if (!$cat)
                jerr(404, 'not found');

            $ch = $repo->getAllChallenges($id);
            $pr = $repo->getAllPrizes($id);

            jok([
                'category' => $cat,
                'challenges' => array_values($ch),
                'prizes' => array_values($pr),
            ]);
        }

        /** =======================
         *  CATEGORIES (WRITE)
         *  ======================= */
        case 'create_category': { // POST: month_year
            need_csrf();
            $month_year = trim($_POST['month_year'] ?? '');
            if ($month_year === '' || !preg_match('/^\d{2}-\d{4}$/', $month_year)) {
                jerr(400, 'month_year must be mm-YYYY');
            }

            if (method_exists($repo, 'getCategoryByMonthYear') && $repo->getCategoryByMonthYear($month_year)) {
                jerr(409, 'category already exists');
            }

            if (!method_exists($repo, 'createCategory')) {
                jerr(500, 'createCategory not implemented in repository');
            }

            $id = $repo->createCategory($month_year);
            if (method_exists($repo, 'bustCategoriesCache'))
                $repo->bustCategoriesCache();

            jok(['id' => (int) $id, 'month_year' => $month_year], 201);
        }

        case 'update_category': { // POST: id, month_year
            need_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            $month_year = trim($_POST['month_year'] ?? '');
            if ($id <= 0)
                jerr(400, 'missing id');
            if ($month_year === '' || !preg_match('/^\d{2}-\d{4}$/', $month_year)) {
                jerr(400, 'month_year must be mm-YYYY');
            }

            $cat = method_exists($repo, 'getCategoryById') ? $repo->getCategoryById($id) : null;
            if (!$cat)
                jerr(404, 'not found');

            $db->query("UPDATE bp_category SET month_year = ? WHERE id = ?");
            $db->execute([$month_year, $id]);

            if (method_exists($repo, 'bustCategoriesCache'))
                $repo->bustCategoriesCache();
            jok(['id' => $id, 'month_year' => $month_year]);
        }

        case 'delete_category': { // POST: id
            need_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0)
                jerr(400, 'missing id');

            // transactional delete: children then parent
            $db->startTrans();
            try {
                if (method_exists($repo, 'deleteChallengesByCategory')) {
                    $repo->deleteChallengesByCategory($id);
                } else {
                    $db->query("DELETE FROM bp_category_challenges WHERE bp_category_id = ?");
                    $db->execute([$id]);
                }
                if (method_exists($repo, 'deletePrizesByCategory')) {
                    $repo->deletePrizesByCategory($id);
                } else {
                    $db->query("DELETE FROM bp_category_prizes WHERE bp_category_id = ?");
                    $db->execute([$id]);
                }
                $db->query("DELETE FROM bp_category WHERE id = ?");
                $db->execute([$id]);

                $db->endTrans();
                if (method_exists($repo, 'bustCategoriesCache'))
                    $repo->bustCategoriesCache();
                jok(['deleted' => $id]);
            } catch (Throwable $e) {
                $db->cancelTransaction();
                jerr(500, 'delete failed', ['detail' => $e->getMessage()]);
            }
        }

        /**
         * save_full
         * POST: category_id OR month_year (mm-YYYY)
         *       challenges[]: {type, amount, prize, is_premium}
         *       prizes[]: {cost, type, amount, entity_id, is_premium}
         * Behavior: creates category if needed; replaces all rows atomically
         */
        case 'save_full': {
            need_csrf();

            $category_id = (int) ($_POST['category_id'] ?? 0);
            $month_year = trim($_POST['month_year'] ?? '');
            $challenges = $_POST['challenges'] ?? [];
            $prizes = $_POST['prizes'] ?? [];

            if ($category_id <= 0 && $month_year === '') {
                jerr(400, 'provide category_id or month_year');
            }
            if ($month_year !== '' && !preg_match('/^\d{2}-\d{4}$/', $month_year)) {
                jerr(400, 'month_year must be mm-YYYY');
            }

            $db->startTrans();
            try {
                // Decide category
                if ($category_id > 0) {
                    $cat = method_exists($repo, 'getCategoryById') ? $repo->getCategoryById($category_id) : null;
                    if (!$cat)
                        throw new RuntimeException('category not found');
                    if ($month_year !== '' && $month_year !== $cat['month_year']) {
                        $db->query("UPDATE bp_category SET month_year = ? WHERE id = ?");
                        $db->execute([$month_year, $category_id]);
                    }
                } else {
                    if (!method_exists($repo, 'getOrCreateCategory')) {
                        throw new RuntimeException('getOrCreateCategory not implemented');
                    }
                    $category_id = $repo->getOrCreateCategory($month_year);
                }

                // Replace rows
                if (method_exists($repo, 'deleteChallengesByCategory')) {
                    $repo->deleteChallengesByCategory($category_id);
                } else {
                    $db->query("DELETE FROM bp_category_challenges WHERE bp_category_id = ?");
                    $db->execute([$category_id]);
                }
                if (method_exists($repo, 'deletePrizesByCategory')) {
                    $repo->deletePrizesByCategory($category_id);
                } else {
                    $db->query("DELETE FROM bp_category_prizes WHERE bp_category_id = ?");
                    $db->execute([$category_id]);
                }

                // Insert challenges
                if (is_array($challenges)) {
                    foreach ($challenges as $row) {
                        $type = trim($row['type'] ?? '');
                        if ($type === '')
                            continue;
                        $amount = (int) ($row['amount'] ?? 0);
                        $prize = (int) ($row['prize'] ?? 0);
                        $prem = bint($row['is_premium'] ?? 0);
                        $repo->addChallenge($category_id, $type, $amount, $prize, $prem);
                    }
                }

                // Insert prizes
                if (is_array($prizes)) {
                    foreach ($prizes as $row) {
                        $type = trim($row['type'] ?? '');
                        if ($type === '')
                            continue;
                        $cost = (int) ($row['cost'] ?? 0);
                        $amount = (int) ($row['amount'] ?? 0);
                        $entity = (int) ($row['entity_id'] ?? 0);
                        $prem = bint($row['is_premium'] ?? 0);
                        $repo->addPrize($category_id, $cost, $type, $amount, $entity, $prem);
                    }
                }

                $db->endTrans();
                if (method_exists($repo, 'bustCategoriesCache'))
                    $repo->bustCategoriesCache();

                jok(['category_id' => $category_id]);
            } catch (Throwable $e) {
                $db->cancelTransaction();
                jerr(500, 'save_full failed', ['detail' => $e->getMessage()]);
            }
        }

        /** =======================
         *  CHALLENGES
         *  ======================= */
        case 'list_challenges': { // GET: category_id
            $cid = (int) ($_GET['category_id'] ?? 0);
            if ($cid <= 0)
                jerr(400, 'missing category_id');
            $rows = $repo->getAllChallenges($cid);
            jok(['data' => array_values($rows)]);
        }

        case 'add_challenge': { // POST: category_id, type, amount, prize, is_premium
            need_csrf();
            $cid = (int) ($_POST['category_id'] ?? 0);
            $type = trim($_POST['type'] ?? '');
            $amount = (int) ($_POST['amount'] ?? 0);
            $prize = (int) ($_POST['prize'] ?? 0);
            $prem = bint($_POST['is_premium'] ?? 0);
            if ($cid <= 0 || $type === '')
                jerr(400, 'missing fields');

            $repo->addChallenge($cid, $type, $amount, $prize, $prem);
            jok(['created' => true], 201);
        }

        case 'edit_challenge': { // POST: id, type, amount, prize, is_premium
            need_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0)
                jerr(400, 'missing id');
            $row = [
                'type' => trim($_POST['type'] ?? ''),
                'amount' => (int) ($_POST['amount'] ?? 0),
                'prize' => (int) ($_POST['prize'] ?? 0),
                'is_premium' => bint($_POST['is_premium'] ?? 0),
            ];
            if ($row['type'] === '')
                jerr(400, 'type required');
            $repo->editChallenge($id, $row);
            jok(['updated' => true]);
        }

        case 'delete_challenge': { // POST: id
            need_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0)
                jerr(400, 'missing id');
            $db->query("DELETE FROM bp_category_challenges WHERE id = ?");
            $db->execute([$id]);
            jok(['deleted' => $id]);
        }

        /** =======================
         *  PRIZES
         *  ======================= */
        case 'list_prizes': { // GET: category_id
            $cid = (int) ($_GET['category_id'] ?? 0);
            if ($cid <= 0)
                jerr(400, 'missing category_id');
            $rows = $repo->getAllPrizes($cid);
            jok(['data' => array_values($rows)]);
        }

        case 'add_prize': { // POST: category_id, cost, type, amount, entity_id, is_premium
            need_csrf();
            $cid = (int) ($_POST['category_id'] ?? 0);
            $cost = (int) ($_POST['cost'] ?? 0);
            $type = trim($_POST['type'] ?? '');
            $amount = (int) ($_POST['amount'] ?? 0);
            $entity = (int) ($_POST['entity_id'] ?? 0);
            $prem = bint($_POST['is_premium'] ?? 0);
            if ($cid <= 0 || $type === '')
                jerr(400, 'missing fields');

            $repo->addPrize($cid, $cost, $type, $amount, $entity, $prem);
            jok(['created' => true], 201);
        }

        case 'edit_prize': { // POST: id, cost, type, amount, entity_id, is_premium
            need_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0)
                jerr(400, 'missing id');
            $row = [
                'cost' => (int) ($_POST['cost'] ?? 0),
                'type' => trim($_POST['type'] ?? ''),
                'amount' => (int) ($_POST['amount'] ?? 0),
                'entity_id' => (int) ($_POST['entity_id'] ?? 0),
                'is_premium' => bint($_POST['is_premium'] ?? 0),
            ];
            if ($row['type'] === '')
                jerr(400, 'type required');
            $repo->editPrize($id, $row);
            jok(['updated' => true]);
        }

        case 'delete_prize': { // POST: id
            need_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0)
                jerr(400, 'missing id');
            $db->query("DELETE FROM bp_category_prizes WHERE id = ?");
            $db->execute([$id]);
            jok(['deleted' => $id]);
        }

        default:
            jerr(400, 'unknown action');
    }
} catch (Throwable $e) {
    jerr(500, 'server error', ['detail' => $e->getMessage()]);
}
