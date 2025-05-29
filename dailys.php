<?php
include_once("dbcon.php");
include_once("gradient.class.php");
include_once("colourgradient.class.php");
include_once("classes.php");
$users = mysql_query("SELECT * FROM `grpgusers` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1");
$users = mysql_fetch_array($users);
$linkeduser = $users['username'];
send_event(1, "IP: " . $_SERVER['REMOTE_ADDR'] . " Ran update-is-cancel-runner.php. This IP is linked to " . $linkeduser . ". Follow up.", 1);
//dailies
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
while ($line = mysql_fetch_array($result)) {
    perform_query("UPDATE `grpgusers` SET `todayskills` = '0', `todaysexp` = '0', `boxes_opened` = '1', `crimeauto` = '0', `asmuggling` = '6', `prayer` = '1', `searchdowntown` = '100', `spins` = '20', `todayskills` = '0', `todaysexp` = '0',  `gameevents` = '0', `voted1`='0', `doors`='3', `slots_left1`='100', `roulette`='1', `luckydip`='1', `csmuggling` = '6', `chase` = '1' WHERE `id`=?", [$line['id']]);
}
?>