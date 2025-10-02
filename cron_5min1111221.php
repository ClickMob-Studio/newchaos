<?php

if ($_GET['key'] != 'cron94') {
    die();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'dbcon.php';
include_once 'classes.php';
include_once 'database/pdo_class.php';

$db->query("SELECT id FROM grpgusers WHERE (king != 0 OR queen != 0) AND lastactive < (UNIX_TIMESTAMP() - 86400)");
$db->execute();
$inactiveUsers = $db->fetch_row(true);
if (isset($inactiveUsers) && count($inactiveUsers) > 0) {
    $db->query("UPDATE grpgusers SET king = 0, queen = 0 WHERE (king != 0 OR queen != 0) AND lastactive < (UNIX_TIMESTAMP() - 86400)");
    $db->execute();
    foreach ($inactiveUsers as $user) {
        Send_Event($user['id'], 'You have been dethroned due to inactivity for more than 24 hours.');
        Send_Event(1059, $user['id'] . ' has been dethroned due to inactivity for more than 24 hours.');
    }
}

