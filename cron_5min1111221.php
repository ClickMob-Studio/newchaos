<?php

if ($_GET['key'] != 'cron94') {
    die();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbcon.php';
include 'database/pdo_class.php';

// Dethrone kings and queens who have been inactive for more than 24 hours
$db->query("SELECT id FROM grpgusers WHERE (king != 0 OR queen != 0) AND lastactive < (UNIX_TIMESTAMP() - 86400)");
$db->execute();

$inactiveUsers = $db->fetch_row(true);
if (count($inactiveUsers) > 0) {
    $db->query("UPDATE grpgusers SET king = 0, queen = 0 WHERE (king != 0 OR queen != 0) AND lastactive < (UNIX_TIMESTAMP() - 86400);");
    $db->execute();
    foreach ($inactiveUsers as $user) {
        Send_Event($user['id'], 'You have been dethroned due to inactivity for more than 24 hours.');
    }
}

