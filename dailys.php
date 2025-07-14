<?php
include_once("dbcon.php");
include_once("gradient.class.php");
include_once("colourgradient.class.php");
include_once("classes.php");

$db->query("SELECT * FROM `grpgusers` WHERE `ip` = ? LIMIT 1");
$db->execute([$_SERVER['REMOTE_ADDR']]);
$users = $db->fetch_row(true);

$linkeduser = $users['username'];
send_event(1059, "IP: " . $_SERVER['REMOTE_ADDR'] . " Ran update-is-cancel-runner.php. This IP is linked to " . $linkeduser . ". Follow up.", 1);

// Reset daily stats for the user
perform_query("UPDATE `grpgusers` SET `todayskills` = '0', `todaysexp` = '0', `boxes_opened` = '1', `crimeauto` = '0', `asmuggling` = '6', `prayer` = '1', `searchdowntown` = '100', `spins` = '20', `todayskills` = '0', `todaysexp` = '0',  `gameevents` = '0', `voted1`='0', `doors`='3', `slots_left1`='100', `roulette`='1', `luckydip`='1', `csmuggling` = '6', `chase` = '1'");
?>