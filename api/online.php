<?php
include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
include "../includes/functions.php";

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db->query("SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
$rows = $db->fetch_row();

$store = [];
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

echo json_encode(['users_online' => $store]);
