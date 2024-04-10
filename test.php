<?php

require "header.php";

if($user_class->admin < 1){
    exit();
}
$points1 = 150000;
$points2 = 100000;
$points3 = 100000;
$money1 = 50000000;
$money2 = 25000000;
$money3= 12500000;
$raids1 = 100;
$raids2 = 50;
$raids3 = 25;

$query = "SELECT id, username, raidcomp FROM grpgusers ORDER BY raidcomp DESC LIMIT 3";
$result = mysql_query($query);
$count = 1;

while ($row = mysql_fetch_assoc($result)) {
    echo $row['username'];
    echo "<br>";
    if ($count === 1) {
        $con = 'st';
        $raids = $raids1;
        $money = $money1;
        $points = $points1;
    } else if ($count === 2) {
        $con = 'nd';
        $raids = $raids2;
        $money = $money2;
        $points = $points2;
    }else if ($count === 3) {
        $con = 'rd';
        $raids = $raids3;
        $money = $money3;
        $points = $points3;
    }

    Send_Event(1, 'You finished the raid competition in '.$count.''.$con.' position and gained, $'.number_format($money).', '.number_format($points).' points and '.$raids.' Tokens');
   
    $count++;
    echo "<br>";
}



$query = "SELECT id, username, killcomp1 FROM grpgusers ORDER BY killcomp1 DESC LIMIT 50";
$result = mysql_query($query);
$count = 1;

while ($row = mysql_fetch_assoc($result)) {
    echo $row['username'];
    echo "<br>";
    if ($count === 1) {
        $con = 'st';
        $raids = $raids1;
        $money = $money1;
        $points = $points1;
    } else if ($count === 2) {
        $con = 'nd';
        $raids = $raids2;
        $money = $money2;
        $points = $points2;
    }else if ($count === 3) {
        $con = 'rd';
        $raids = $raids3;
        $money = $money3;
        $points = $points3;
    }

    Send_Event(1, 'You finished the raid competition in '.$count.''.$con.' position and gained, $'.number_format($money).', '.number_format($points).' points and '.$raids.' Tokens');
   
    $count++;
    echo "<br>";
}

