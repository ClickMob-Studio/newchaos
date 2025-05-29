<?php

if ($_GET['key'] != 'cron94') {
  die();
}
include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

perform_query("UPDATE `grpgusers` SET `tamt` = `0`, `todayskills` = '0', `todaysexp` = '0', `boxes_opened` = '1', `crimeauto` = '0', `csmuggling` = '6', `prayer` = '1', `searchdowntown` = '100', `dailytrains` = '0', `dailymugs` = '0', `spins` = '20', `todayskills` = '0', `dailyClockins` = '0', `todaysexp` = '0',  `gameevents` = '0', `voted1`='0', `doors`='3', `slots_left1`='100', `roulette`='1', `luckydip`='1', `csmuggling` = '6', `chase` = '1'");
perform_query("DELETE FROM votes WHERE 1");
perform_query("UPDATE grpgusers SET ffban = 0");

$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC LIMIT 2");
$db->execute();
$dailyRows = $db->fetch_row();

$i = 1;
foreach ($dailyRows as $row) {
  $db->query("SELECT * FROM `grpgusers` WHERE `gang` = " . $row['gang_id']);
  $db->execute();
  $userRows = $db->fetch_row();

  foreach ($userRows as $uRow) {
    if ($i == 1) {
      $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $uRow['id']);
      $db->execute();

      Give_Item(163, $uRow['id'], 1);
      Give_Item(42, $uRow['id'], 1);

      Send_Event($uRow['id'], "Your gang won 1st place in the daily contest. You have been awarded 25,000 points, 1 Police Badge & 1 Mystery Box.");
    } else {
      Give_Item(42, $uRow['id'], 1);

      Send_Event($uRow['id'], "Your gang won 2nd place in the daily contest. You have been awarded 1 Mystery Box.");
    }
  }

  $i++;
}

$db->query("UPDATE `gang_comp_leaderboard` SET `daily_missions_complete` = 0");
$db->execute();

$db->query("UPDATE user_research_type SET duration_in_days = duration_in_days - 1 WHERE duration_in_days > 0");
$db->execute();

$db->query("UPDATE `user_santas_grotto` SET `todays_gifts_found` = 0");
$db->execute();
?>