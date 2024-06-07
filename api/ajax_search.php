<?php
include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$response = [
    'status' => 'error',
    'data' => null
];

try {
    $db = database::getInstance();
    $userId = $_POST['user_id']; // Get user_id from POST request
    $user_class = new User($userId);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $search = [];
        
        if (isset($_POST['id'])) {
            $search['id'] = abs((int) $_POST['id']);
        }
        if (isset($_POST['name'])) {
            $search['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        }
        if (isset($_POST['money'])) {
            $search['money'] = abs((int) $_POST['money']);
        }
        if (isset($_POST['level'])) {
            $search['level'] = abs((int) $_POST['level']);
        }
        if (isset($_POST['level2'])) {
            $search['level2'] = abs((int) $_POST['level2']);
        }
        if (isset($_POST['lastactive'])) {
            $search['lastactive'] = abs((int) $_POST['lastactive']);
        }
        if (isset($_POST['lastactive2'])) {
            $search['lastactive2'] = abs((int) $_POST['lastactive2']);
        }
        if (isset($_POST['attack'])) {
            $search['attack'] = abs((int) $_POST['attack']);
        }
        if (isset($_POST['location'])) {
            $search['location'] = abs((int) $_POST['location']);
        }
        if (isset($_POST['gang'])) {
            $search['gang'] = abs((int) $_POST['gang']);
        }
        if (isset($_POST['online'])) {
            $search['online'] = abs((int) $_POST['online']);
        }

        $sql = "id != 0";
        $bindParams = [];

        if (!empty($search['id']) && $search['id'] != 0) {
            $sql .= " AND id = :id";
            $bindParams[':id'] = $search['id'];
        }
        if (!empty($search['name'])) {
            $sql .= " AND username LIKE :name";
            $bindParams[':name'] = "%" . $search['name'] . "%";
        }
        if ($search['level'] > 0 || $search['level2'] > 0) {
            $sql .= " AND (`level` >= :level AND `level` <= :level2)";
            $bindParams[':level'] = $search['level'];
            $bindParams[':level2'] = $search['level2'];
        }
        if (!empty($search['lastactive']) && !empty($search['lastactive2'])) {
            $la = $search['lastactive'] * 86400;
            $la2 = $search['lastactive2'] * 86400;
            $sql .= " AND (`lastactive` >= :lastactive AND `lastactive` <= :lastactive2)";
            $bindParams[':lastactive'] = $la;
            $bindParams[':lastactive2'] = $la2;
        }
        if (!empty($search['location']) && $search['location'] != 0) {
            $sql .= " AND city = :location AND eqarmor <> 43";
            $bindParams[':location'] = $search['location'];
        }
        if (!empty($search['gang']) && $search['gang'] != 0 && $search['gang'] != 999999) {
            $sql .= " AND gang = :gang";
            $bindParams[':gang'] = $search['gang'];
        } elseif (!empty($search['gang']) && $search['gang'] == 999999) {
            $sql .= " AND gang = 0";
        }
        if (!empty($search['money'])) {
            $sql .= " AND money > :money";
            $bindParams[':money'] = $search['money'];
        }
        if ($search['attack'] == 1) {
            $protime = time();
            $sql .= " AND hospital = 0 AND jail = 0 AND aprotection < :protime AND (gang <> :userGang OR gang = 0) AND admin < 1 AND hp > (50*level)/4 AND id <> :userId";
            $bindParams[':protime'] = $protime;
            $bindParams[':userGang'] = $user_class->gang;
            $bindParams[':userId'] = $user_class->id;
        } elseif ($search['attack'] == 2) {
            $sql .= " AND hospital > 0";
        }
        $time = time() - 900;
        if ($search['online'] == 1) {
            $sql .= " AND lastactive > :time";
            $bindParams[':time'] = $time;
        } elseif ($search['online'] == 2) {
            $sql .= " AND lastactive < :time";
            $bindParams[':time'] = $time;
        }

        $limit = ($user_class->rmdays > 0) ? 20 : 10;
        $sql .= " ORDER BY rand() DESC LIMIT :limit";
        $bindParams[':limit'] = $limit;

        $db->query("SELECT * FROM `grpgusers` WHERE $sql");
        $db->execute($bindParams);
        $results = $db->fetch_row();

        if ($results) {
            $response['status'] = 'success';
            $response['data'] = $results;
        } else {
            $response['status'] = 'no_results';
        }
    } else {
        $response['status'] = 'invalid_method';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['data'] = $e->getMessage();
}

echo json_encode($response);
?>
