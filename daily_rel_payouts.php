<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

if ($_GET['key'] === 'srunit') {
    $db->query("SELECT * FROM `rel_comp_leaderboard` WHERE `daily_activity_complete` > 0 ORDER BY `daily_activity_complete` DESC LIMIT 3");
    $db->execute();
    $rows = $db->fetch_row();

    $i = 1;
    foreach ($rows as $row) {
        if ($i == 1) {
            $db->query("UPDATE `grpgusers` SET `points` = `points` + 150000 WHERE `id` = " . $row['user_id']);
            $db->execute();

            $db->query("UPDATE `grpgusers` SET `points` = `points` + 150000 WHERE `id` = " . $row['two_user_id']);
            $db->execute();

            Send_Event($row['user_id'], 'Congratulations on finishing 1st in the Daily Activity Contest, you have been awarded 150k points');
            Send_Event($row['two_user_id'], 'Congratulations on finishing 1st in the Daily Activity Contest, you have been awarded 150k points');
        }

        if ($i == 2) {
            $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $row['user_id']);
            $db->execute();

            $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $row['two_user_id']);
            $db->execute();


            Send_Event($row['user_id'], 'Congratulations on finishing 2nd in the Daily Activity Contest, you have been awarded 25k points');
            Send_Event($row['two_user_id'], 'Congratulations on finishing 2nd in the Daily Activity Contest, you have been awarded 25k points');
        }

        if ($i == 3) {
            $db->query("UPDATE `grpgusers` SET `points` = `points` + 10000 WHERE `id` = " . $row['user_id']);
            $db->execute();

            $db->query("UPDATE `grpgusers` SET `points` = `points` + 10000 WHERE `id` = " . $row['two_user_id']);
            $db->execute();

            Send_Event($row['user_id'], 'Congratulations on finishing 1st in the Daily Activity Contest, you have been awarded 10k points');
            Send_Event($row['two_user_id'], 'Congratulations on finishing 1st in the Daily Activity Contest, you have been awarded 10k points');
        }

        $i++;
    }

    $db->query("UPDATE `rel_comp_leaderboard` SET `daily_activity_complete` = 0");
    $db->execute();

}
