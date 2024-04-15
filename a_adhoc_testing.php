<?php
include 'header.php';

$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC LIMIT 3");
$db->execute();
$dailyRows = $db->fetch_row();

$i = 1;
foreach ($dailyRows as $row) {
    Send_Event(2, $i . ' ' . $row['gang_id'], 2);

//    $db->query("SELECT * FROM `grpgusers` WHERE `gang` = " . $dailyRows['gang_id']);
//    $db->execute();
//    $userRows = $db->fetch_row();
//
//    foreach ($userRows as $uRow) {
//
//    }

    $i++;
}
echo 'done';

include 'footer.php';
?>