<?php

require 'dbcon.php'; // Ensure database connection is critical
include_once "classes.php";
include_once "database/pdo_class.php";

//Send_Event(2, var_dump($_GET), 2);


if (isset($_GET['id'])) {
    $userId = $_GET['id'];
}

if (isset($_GET['userid'])) {
    $userId = $_GET['userid'];
    $scriptCallback = 'bbogd';
}

if (isset($_GET['userid']) && isset($_GET['secret'])) {
    $userId = $_GET['userid'];
    $scriptCallback = 'arenatop100';
}

if (isset($_POST['userid']) && isset($_POST['secret'])) {
    $userId = $_GET['userid'];
    $scriptCallback = 'ArenaTop100';
}


if (isset($_GET['param'])) {
    $userId = $_GET['param'];
    $scriptCallback = 'mmohub';
}

if (isset($_GET['script_callback']) && $_GET['script_callback'] !== '') {
    $scriptCallback = $_GET['script_callback'];
}

if (isset($_GET['postback']) && $_GET['postback'] !== '') {
    $scriptCallback = $_GET['postback'];
}

if (isset($_GET['reference'])) {
    $parts = explode('_', $_GET['reference']);

    $userId = $parts[1];
    $scriptCallback = $parts[0];
}

//Send_Event(2, $userId, 2);
//Send_Event(2, $scriptCallback, 2);

if (!isset($userId) || !isset($scriptCallback)) {
    echo 'Something went wrong!';
    exit;
}

$user = new User((int) $userId);
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

$response = ['success' => true];
echo json_encode($response);
exit;
exit;
