<?php

include "classes.php";
include "database/pdo_class.php";

Send_Event(2, 'VOTE PB', 2);


if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
}

if (isset($_GET['userid'])) {
    $userId = $_GET['userid'];
    $scriptCallback = 'bbogd';
}

if (isset($_GET['script_callback']) && $_GET['script_callback'] !== '') {
    $scriptCallback = $_GET['script_callback'];
}

if (isset($_GET['reference'])) {
    $parts = explode('_', $_GET['reference']);

    $userId = $parts[1];
    $scriptCallback = $parts[0];
}

if (!isset($userId) || !isset($scriptCallback)) {
    echo 'Something went wrong!';
    exit;
}

$user = new User($userId);
if (!$user) {
    echo 'Something went wrong!';
    exit;
}

$query = "SELECT * FROM votes WHERE userid = " . $user->id . " AND site = '" . $scriptCallback . "'";
$result = mysql_query($query);

if (mysql_num_rows($result) > 0) {
    echo 'Something went wrong!';
    exit;
}

mysql_query("INSERT INTO votes (userid, site) VALUES (" . $user->id . ", '" . $scriptCallback . "')");
mysql_query("UPDATE grpgusers SET votetokens = votetokens + 100 WHERE id = " . $user->id);

$response = ['success' => false];
echo json_encode($response); exit;
exit;
