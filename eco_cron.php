<?php
include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

$db->query("SELECT sum(credits) as tcredits FROM grpgusers");
$db->execute();

$r = $db->fetch_row(true);

echo $r['tcredits'];
$date = date("Y-m-d");

$time = time() - 432000;
$db->query("SELECT COUNT(id) as total FROM grpgusers WHERE lastactive < $time");
$db->execute();
$inactiveUsers = $db->fetch_row(true);
$inactiveUser = $inactiveUsers['total'];


$db->query("SELECT sum(points) as tpoints, sum(pbank) as bpoints FROM grpgusers");
$db->execute();
$a = $db->fetch_row(true);
$points = $a['tpoints'] + $a['bpoints'];

$db->query("SELECT count(`id`) as total FROM grpgusers");
$db->execute(); 
$totalUsers = $db->fetch_row(true);
$totalUser = $totalUsers['total'];

$db->query("SELECT sum(`money`) AS tmoney, sum(`bank`) AS bmoney FROM grpgusers");
$db->execute();
$b = $db->fetch_row(true);
$money = $b['tmoney'] + $b['bmoney'];

$db->query("SELECT sum(raidtokens) as traidtokens FROM grpgusers");
$db->execute();

$raidtokens = $db->fetch_row(true);
$raidtoken = $raidtokens['traidtokens'];


$db->query("INSERT INTO daily_eco (`timestamp`, credits, inactive_users, points, users, `money`, `raidtokens`) VALUES (".time().", ".$r['tcredits'].", ".$inactiveUser.", ".$points.", ".$totalUser.", ".$money.", ".$raidtokens.")");
$db->execute();


