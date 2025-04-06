<?php
if ($_GET['key'] != 'cron94') {
    die();
}
include 'database/pdo_class.php';

$fetchQuery = "SELECT id, strength, defense, speed, agility FROM grpgusers";


$db->query($fetchQuery);
$userStats = $db->fetch_row();


$insertQuery = "INSERT INTO daily_user_stats (user_id, strength, defense, speed, agility, record_date) VALUES (:user_id, :strength, :defense, :speed, :agility, CURDATE())";
$db->query($insertQuery);


$db->startTrans();

try {
    foreach ($userStats as $user) {
        $db->bind(':user_id', $user['id']);
        $db->bind(':strength', $user['strength']);
        $db->bind(':defense', $user['defense']);
        $db->bind(':speed', $user['speed']);
        $db->bind(':agility', $user['agility']);
        $db->execute();
    }

    $db->endTrans();
    echo "Daily stats recorded successfully.\n";
} catch (Exception $e) {
    $db->cancelTransaction();
    echo "Failed to record daily stats: " . $e->getMessage() . "\n";
}