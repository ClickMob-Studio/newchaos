<?php
include 'header.php';


$db->query("SELECT * FROM missions WHERE timestamp > 1713308400 AND completed = 'successful'");
$db->execute();
$rows = $db->fetch_row();

foreach ($rows as $row) {
    $gUser = new User($row['userid']);

    addToGangCompLeaderboard($gUser->gang, 'missions_complete', 1);
}
echo 'done';


//$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC LIMIT 2");
//$db->execute();
//$dailyRows = $db->fetch_row();
//
//$i = 1;
//foreach ($dailyRows as $row) {
//    $db->query("SELECT * FROM `grpgusers` WHERE `gang` = " . $row['gang_id']);
//    $db->execute();
//    $userRows = $db->fetch_row();
//
//    foreach ($userRows as $uRow) {
//        if ($i == 1) {
//            $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $uRow['id']);
//            $db->execute();
//
//            Give_Item(163, $uRow['id'], 1);
//            Give_Item(42, $uRow['id'], 1);
//
//            Send_Event($uRow['id'], "Your gang won 1st place in the daily content, you have been awarded 25,000 points, 1 Police Badge & 1 Mystery Box.");
//        } else {
//            Give_Item(42, $uRow['id'], 1);
//
//            Send_Event($uRow['id'], "Your gang won 2nd place in the daily content, you have been awarded 1 Mystery Box.");
//        }
//    }
//
//    $i++;
//}
//
//$db->query("UPDATE `gang_comp_leaderboard` SET `daily_missions_complete` = 0");
//$db->execute();
//
//
//echo 'done';

include 'footer.php';
?>