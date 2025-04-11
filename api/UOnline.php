<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";

header('Content-Type: application/json');

try {
    $db->query("SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
    $rows = $db->fetch_row();

    if ($rows === false) {
        throw new Exception('Error fetching rows from the database.');
    }
    $onlineNow = count($rows);

    $store = array();
    foreach ($rows as $row) {
        $user_online = new User($row['id']);
        $store[] = array(
            'avatar' => $user_online->avatar,
            'formattedname' => $user_online->formattedname,
            'level' => $user_online->level,
            'money' => $user_online->money,
            'id' => $user_online->id,
            'formattedgang' => $user_online->formattedgang,
            'type' => $user_online->type,
            'cityname' => $user_online->cityname,
            'cityid' => $user_online->city,
            'hospital' => $user_online->hospital,
            'jail' => $user_online->jail,
            'lastactive' => howlongago($user_online->lastactive)
        );
    }

    echo json_encode(array('users_online' => $store, 'onlineNow' => $onlineNow));
} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage()
    ));
}
