<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

//if ($_GET['key'] === 'srunit') {
//    $db->query("SELECT * FROM `user_comp_leaderboard` WHERE `daily_activity_complete` > 0 ORDER BY `daily_activity_complete` DESC LIMIT 3");
//    $db->execute();
//    $rows = $db->fetch_row();
//
//    $i = 1;
//    foreach ($rows as $row) {
//        if ($i == 1) {
//            $db->query("UPDATE `grpgusers` SET `points` = `points` + 150000 WHERE `id` = " . $row['user_id']);
//            $db->execute();
//
//            Give_Item(270, $row['user_id'], 2);
//            Give_Item(266, $row['user_id'], 1);
//
//            Send_Event($row['user_id'], 'Congratulations on finishing 1st in the Daily Activity Contest, you have been awarded 150k points, 1 x Hourglass Gem & 2 x Stone.');
//        }
//
//        if ($i == 2) {
//            $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $row['user_id']);
//            $db->execute();
//
//            Give_Item(270, $row['user_id'], 1);
//
//            Send_Event($row['user_id'], 'Congratulations on finishing 2nd in the Daily Activity Contest, you have been awarded 25k points & 1 x Stone.');
//        }
//
//        if ($i == 3) {
//            Give_Item(270, $row['user_id'], 1);
//
//            Send_Event($row['user_id'], 'Congratulations on finishing 3rd in the Daily Activity Contest, you have been awarded 1 x Stone.');
//        }
//
//        $i++;
//    }
//
//    $db->query("UPDATE `user_comp_leaderboard` SET `daily_activity_complete` = 0");
//    $db->execute();
//
//}