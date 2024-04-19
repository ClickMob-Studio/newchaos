<?php
include 'header.php';
exit;
//$db->query("SELECT * FROM missions WHERE timestamp > 1713409200 AND completed = 'successful'");
//$db->execute();
//$rows = $db->fetch_row();
//
//foreach ($rows as $row) {
//    $gUser = new User($row['userid']);
//
//    $db->query("UPDATE `gang_comp_leaderboard` SET `daily_missions_complete` = `daily_missions_complete` + 1 WHERE gang_id = " . $gUser->gang);
//    $db->execute();
//}
//echo 'done';


$db->query("SELECT * FROM `grpgusers` WHERE `gang` = 3");
$db->execute();
$userRows = $db->fetch_row();

foreach ($userRows as $uRow) {
    $db->query("UPDATE `grpgusers` SET `points` = `points` + 50000 WHERE `id` = " . $uRow['id']);
    $db->execute();

    Give_Item(163, $uRow['id'], 5);
    Give_Item(42, $uRow['id'], 5);
    Give_Item(250, $uRow['id'], 1);

    Send_Event($uRow['id'], "Your gang won 1st place in the contest, you have been awarded 50,000 points, 5 x Police Badges, 5 x Mystery Boxes & 1 x Advanced Booster.");
}


$db->query("SELECT * FROM `grpgusers` WHERE `gang` = 6");
$db->execute();
$userRows = $db->fetch_row();

foreach ($userRows as $uRow) {
    $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $uRow['id']);
    $db->execute();

    Give_Item(163, $uRow['id'], 1);
    Give_Item(42, $uRow['id'], 1);
    Give_Item(231, $uRow['id'], 1);

    Send_Event($uRow['id'], "Your gang won 2nd place in the contest, you have been awarded 25,000 points, 1 x Police Badges, 1 x Mystery Boxes & 1 x Heroic Booster.");
}

$db->query("SELECT * FROM `grpgusers` WHERE `gang` = 16");
$db->execute();
$userRows = $db->fetch_row();

foreach ($userRows as $uRow) {
    $db->query("UPDATE `grpgusers` SET `points` = `points` + 10000 WHERE `id` = " . $uRow['id']);
    $db->execute();

    Give_Item(163, $uRow['id'], 1);
    Give_Item(230, $uRow['id'], 1);

    Send_Event($uRow['id'], "Your gang won 3rd place in the contest, you have been awarded 10,000 points, 1 x Mystery Boxes & 1 x Exotic Booster.");
}


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


echo 'done';

include 'footer.php';
?>