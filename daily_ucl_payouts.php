<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

if ($_GET['key'] === 'srunit') {
    $db->query("SELECT * FROM `user_comp_leaderboard` WHERE `daily_activity_complete` > 0 ORDER BY `daily_activity_complete` DESC LIMIT 3");
    $db->execute();
    $rows = $db->fetch_row();

    foreach ($rows as $row) {
        echo $row['user_id'] . '<br />';
    }
}

exit;