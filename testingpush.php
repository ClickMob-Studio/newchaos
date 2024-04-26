<?php
require ("header.php");

if($user_class->admin < 1){
    exit();
}

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


$db->query("SELECT sum(points) as tpoints, sum(bpoints) as bpoints FROM grpgusers");
$db->execute();

$a = $db->fetch_row(true);
$points = $a['tpoints'] + $a['bpoints'];
$db->query("INSERT INTO daily_eco (`timestamp`, credits, inactive_users, points) VALUES (".time().", ".$r['tcredits'].", ".$inactiveUser.", ".$points.")");
$db->execute();


