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



$db->query("INSERT INTO daily_eco (`date`) VALUES '$date'");
$db->execute();