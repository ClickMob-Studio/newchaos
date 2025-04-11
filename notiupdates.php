<?php
include "ajax_header.php";
//require 'vendor/autoload.php';
$user_class = new User($_SESSION['id']);
mysql_select_db('chaoscit_game', mysql_connect('localhost', 'chaoscit_user', '3lrKBlrfMGl2ic14'));
$checkmail = mysql_query("SELECT 'to' FROM `pms` WHERE `to`='{$_SESSION['id']}' and `viewed`='1'");
$nummsgs = mysql_num_rows($checkmail);
$mail = ($nummsgs > 0) ? "<span class='notify'>" . number_format($nummsgs) . "</span>" : 0;

$checkeve = mysql_query("SELECT 'to' FROM `events` WHERE `to`='{$_SESSION['id']}' and `viewed` = '1'");
$numevents = mysql_num_rows($checkeve);
$events = ($numevents > 0) ? "<span class='notify'>" . number_format($numevents) . "</span>" : 0;

$ignore = array($user_class->id);
$ignore = implode(',', $ignore);
// $checkjail = mysql_query("SELECT COUNT(id) as jailed FROM grpgusers WHERE jail > 0 AND id NOT IN ($ignore)");
// $jailed = mysql_fetch_assoc($checkjail);


// $debug = array(
//     'id' => $user_class->id,
//     'l' => $_POST['l'],
//     'w' => $_POST['w'],
// );

// $logger = new Katzgrau\KLogger\Logger('/var/www/logs/fp', Psr\Log\LogLevel::INFO, array (
//     'prefix' => $user_class->id . "-",
// ));
// $logger->info("", $debug);

$db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
$db->execute();
$jailed = $db->fetch_single();

$checkhospital = mysql_query("SELECT COUNT(id) as hospi FROM grpgusers WHERE hospital > 0");
$hospital = mysql_fetch_assoc($checkhospital);

$width = ($user_class->epoints / 120) * 100;

$tot = $nummsgs + $numevents;
print $mail . "|" . $events . "|" . $tot . "|" . $jailed . "|" . $hospital['hospi'] . "|" . $width . "%";  // $jailed['jailed']
mysql_close();
?>