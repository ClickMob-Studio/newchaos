#! /usr/bin/php
<?php

if($_GET['key'] != 'cron94'){
    die();
}
include 'dbcon.php';

// $link = mysql_connect('127.0.0.1', 'aa_user', 'GmUq38&SVccVSpt');
// if (!$link) {
//     die('Could not connect: ' . mysql_error());
// }
// mysql_select_db("ml2");

include 'classes.php';
include 'database/pdo_class.php';

//$m->flush();
//$m->delete('')


$randN = mt_rand(1, 25);
$ng = mysql_query("SELECT * FROM numbergame WHERE `userid` > 0 ORDER BY RAND() DESC LIMIT 1") or mysql_error();
$g = mysql_fetch_array($ng);
Send_Event($g['userid'], "Congratulations, You have won 1000 points in the number game!");

mysql_query("UPDATE `grpgusers` SET `points` = `points` + 1000 WHERE `id` = '{$g['userid']}' LIMIT 1") or mysql_error();
mysql_query("UPDATE `numbergame` SET `userid` = 0") or mysql_error();

mysql_query("UPDATE `grpgusers` SET `hourbases` + 1  WHERE `hourbases` >= '24'") or mysql_error();

mysql_query("UPDATE `grpgusers` SET `claimed` - 1  WHERE `claimed` >= '1'") or mysql_error();

mysql_query("UPDATE `grpgusers` SET `attackprotection` - 1  WHERE `attackprotection` >= '1'") or mysql_error();
mysql_query("UPDATE `grpgusers` SET `mugprotection` - 1  WHERE `mugprotection` >= '1'") or mysql_error();

//MOTH
$resulth = mysql_query("SELECT * FROM `grpgusers` WHERE `admin` = 0 AND `moth` > 0 ORDER BY `moth` DESC LIMIT 1");
$workedh = mysql_fetch_array($resulth);
if (isset($workedh['id']) && $workedh['id']) {
    $moth = new User($workedh['id']);
    $newpoints = $moth->points + 500;
    $query11 = mysql_query("UPDATE `grpgusers` SET `moth` = 0, `points` = '" . $newpoints . "' WHERE `id` = '" . $moth->id . "'");
    Send_Event($moth->id, "You are mugger of the hour with a total of " . prettynum($workedh['moth']) . " Mugs. [ + 500 pts ]");
    mysql_query("INSERT INTO `MOTH` (`ID`, `userid`, `kills`, `time`) VALUES ('', " . $moth->id . ", " . $workedh['moth'] . ", " . time() . ")");
}

//KOTH killer of the hour
$resultq = mysql_query("SELECT * FROM `grpgusers` WHERE `admin` = 0 AND `koth` > 0 ORDER BY `koth` DESC LIMIT 1");
$workedq = mysql_fetch_array($resultq);
if (isset($workedq['id']) && $workedq['id']) {
    $koth = new User($workedq['id']);
    $newpoints = $koth->points + 500;
    $query19 = mysql_query("UPDATE `grpgusers` SET `koth` = 0, `points` = '" . $newpoints . "' WHERE `id` = '" . $koth->id . "'");
    Send_event($koth->id, "You have won the Killer of the hour with " . prettynum($workedq['koth']) . " Kills. [ + 500 pts ]");
    // $points=$koth->points+250;
    // $query20 = mysql_query("UPDATE `grpgusers` SET `points` = '" . $points . "' WHERE `id` = '" . $koth->id . "'");

    mysql_query("INSERT INTO oth (userid, `type`, amnt, timestamp) VALUES(" . $koth->id . ", 'killer' , " . $workedq['koth'] . ", unix_timestamp())");
}

//Leveller Of the Hour
$resultq = mysql_query("SELECT * FROM grpgusers WHERE `admin` = 0 AND `loth` > 0 ORDER BY `loth` DESC LIMIT 1");
$workedq = mysql_fetch_array($resultq);
if (isset($workedq['id']) && $workedq['id']) {
    $loth = new User($workedq['id']);
    $newpoints = $loth->points + 500;
    Send_event($loth->id, "You have won the leveler of the hour with " . prettynum($workedq['loth']) . " EXP gained. [ + 500 pts ]");
    $query21 = mysql_query("UPDATE `grpgusers` SET `loth` = 0, `points` = '" . $newpoints . "' WHERE `id` = '" . $loth->id . "'");
    mysql_query("INSERT INTO oth (userid, `type`, amnt, timestamp) VALUES(" . $loth->id . ", 'leveler' , " . $workedq['loth'] . ", unix_timestamp())");
}

//Buster Of the Hour
$resultq = mysql_query("SELECT * FROM grpgusers WHERE `admin` = 0 AND `both` > 0 ORDER BY `both` DESC LIMIT 1");
$workedq = mysql_fetch_array($resultq);
if (isset($workedq['id']) && $workedq['id']) {
    $bust = new User($workedq['id']);
    $newpoints = $bust->points + 500;
    Send_event($bust->id, "You have won the buster of the hour with " . prettynum($workedq['both']) . " Busts gained. [ + 500 pts ]");
    $query21 = mysql_query("UPDATE `grpgusers` SET `both` = 0, `points` = '" . $newpoints  . "' WHERE `id` = '" . $bust->id . "'");
}

// Reset MOTH, KOTH, LOTH & BOTH & others
//mysql_query("UPDATE grpgusers SET `moth` = 0, `koth` = 0, `loth` = 0, `both` = 0, `hourdip` = 1,`hoursearch` = 100, `bomb` = 1") or mysql_error(); // BROKEN
mysql_query("UPDATE grpgusers SET `moth` = 0, `koth` = 0, `loth` = 0, `both` = 0");

mysql_query("UPDATE grpgusers SET `searchdowntown` = 20 WHERE `searchdowntown` = 0");


//Bases
$result2 = mysql_query("SELECT * FROM `grpgusers` WHERE missionsactive == 1 ORDER BY `id` ASC");
while ($line = mysql_fetch_array($result2)) {
    if ($line['missionkills'] >= 35 && $line['missionmugs'] >= 10 && $line['missioncrimes'] >= 100 && $line['missionbusts'] >= 5) {
        $newkills = 0;
        $newmugs = 0;
        $newcrimes = 0;
        $newbusts = 0;
        $newmission = 0;
        Give_Item(114, $line['id']); //give the user their item they bought
        $result = mysql_query("UPDATE `grpgusers` SET `missionkills`='" . $newkills . "', `missionmugs`='" . $newmugs . "', `missionbusts`='" . $newbusts . "', `missioncrimes`='" . $newcrimes . "', `missionsactive`='" . $newmission . "' WHERE `id`='" . $line['id'] . "'");
        Send_Event($line['id'], "You have Completed your mission! You Receive a Mini Mission Pack.", $line['id']);
    } elseif ($line['missionkills'] < 35 && $line['missionmugs'] < 10 && $line['missioncrimes'] < 100 && $line['missionbusts'] < 5) {
        $newkills = 0;
        $newmugs = 0;
        $newcrimes = 0;
        $newbusts = 0;
        $newmission = 0;
        $result = mysql_query("UPDATE `grpgusers` SET `missionkills`='" . $newkills . "', `missionmugs`='" . $newmugs . "', `missionbusts`='" . $newbusts . "', `missioncrimes`='" . $newcrimes . "', `missionsactive`='" . $newmission . "' WHERE `id`='" . $line['id'] . "'");
        Send_Event($line['id'], "You have Failed your Mission this Hour! Try Again.", $line['id']);
    } else {
        $newkills = 0;
        $newmugs = 0;
        $newcrimes = 0;
        $newbusts = 0;
        $newmission = 0;
        $result = mysql_query("UPDATE `grpgusers` SET `missionkills`='" . $newkills . "', `missionmugs`='" . $newmugs . "', `missionbusts`='" . $newbusts . "', `missioncrimes`='" . $newcrimes . "', `missionsactive`='" . $newmission . "' WHERE `id`='" . $line['id'] . "'");
    }
}



$ladderRewards = [150, 100, 100, 100, 100, 100, 100, 100, 100, 100];

$attackLadderRes = mysql_query("SELECT * FROM `attackladder` ORDER BY `spot` ASC");
while ($row = mysql_fetch_array($attackLadderRes)) {
    if ((time() - $row['last_attack']) > 14400) {
        mysql_query("DELETE FROM attackladder WHERE `user` = '{$row['user']}'");
        Send_Event($row['user'], "[-_USERID_-] You were removed from the Attack Ladder due to inactivity.", $row['user']);
    } else {
        mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $ladderRewards[$row['spot'] - 1] . " WHERE `id` = '{$row['user']}' LIMIT 1") or mysql_error();
        Send_Event($row['user'], "[-_USERID_-] you are ranked ".ordinal($row['spot'])." in the attack ladder and you've been rewarded ".$ladderRewards[$row['spot'] - 1]." points ", $row['user']);
    }
}

mysql_query("SET @counter := 0; UPDATE `attackladder` SET `attackladder`.`spot` = (@counter := @counter + 1) ORDER BY `attackladder`.`spot` ASC");


// King & Queen
$king_result = mysql_query("SELECT `id`, `city` FROM `grpgusers` WHERE `king` > 0");
while ($line = mysql_fetch_array($king_result)) {
    $cityId = $line['city'];

    $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($line['city']) . "' LIMIT 1");
    $city_result = mysql_fetch_assoc($city_query);

    $bossUser = new User($line['id']);

    $owned_points = $city_result['owned_points'];
    $userPrestigeSkills = getUserPrestigeSkills($bossUser);
    if ($userPrestigeSkills['throne_points_unlock'] > 0) {
        $owned_points = $owned_points + ($owned_points / 100 * 20);
    }

    if ($city_result['owned_points'] > 0) {
        mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $owned_points . " WHERE `id` = " . $line['id']);
        Send_event($line['id'], "You earned " . number_format($owned_points, 0) . " points for being the Boss!");
    }
}

$queen_result = mysql_query("SELECT `id`, `city` FROM `grpgusers` WHERE `queen` > 0");
while ($line = mysql_fetch_array($queen_result)) {
    $cityId = $line['city'];

    $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($line['city']) . "' LIMIT 1");
    $city_result = mysql_fetch_assoc($city_query);

    if ($city_result['owned_points'] > 0) {
        if($cityID == 600){
            $owned_points = 3250;
        }else{
        $owned_points = $city_result['owned_points'];
        }

        $bossUser = new User($line['id']);

        $userPrestigeSkills = getUserPrestigeSkills($bossUser);
        if ($userPrestigeSkills['throne_points_unlock'] > 0) {
            $owned_points = $owned_points + ($owned_points / 100 * 20);
        }
        $twenty_percent = $owned_points - ($owned_points * 0.20);



        mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $twenty_percent . " WHERE `id` = " . $line['id']);
        Send_event($line['id'], "You earned " . $twenty_percent . " points for being the *Under Boss!");
    }
}

mysql_query("UPDATE `grpgusers` SET `jail_bot_credits` = `jail_bot_credits` + 50 WHERE `jail_bot_credits` < 1");
//Credit the contest winners!
$now = time();
$targetTime = strtotime("2024-04-10 17:00");

if (abs($now - $targetTime) <= 60) {
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
   // $raidtokens_query = "UPDATE grpgusers SET money = money + $money, points = points + $points, raidtokens = raidtokens + $raids WHERE id = " . $row['id'];
    //mysql_query($raidtokens_query);
    //Send_Event($row['id'], 'You finished the raid competition in '.$count.''.$con.' position and gained, $'.number_format($money).', '.number_format($points).' points and '.$raids.' Tokens');
   
    $count++;
    echo "<br>";
}



$query = "SELECT id, username, killcomp1 FROM grpgusers ORDER BY killcomp1 DESC LIMIT 3";
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
    
    Send_Event($row['id'], 'You finished the raid competition in '.$count.''.$con.' position and gained, $'.number_format($money).', '.number_format($points).' points and '.$raids.' Tokens');
    $raidtokens_query = "UPDATE grpgusers SET money = money + $money, points = points + $points, raidtokens = raidtokens + $raids WHERE id = " . $row['id'];
    mysql_query($raidtokens_query);


    $count++;
    echo "<br>";
}
Send_Event(1,"Contests paid out");
Send_Event(2, "contests paid out");

$querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 999 AND killcomp1 < 2500  ORDER BY killcomp1 DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 25000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }

$querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 2499 AND killcomp1 < 5000  ORDER BY killcomp1 DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 6500 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 4999 AND killcomp1 < 15000  ORDER BY killcomp1 DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 13000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 14999 AND killcomp1 < 30000  ORDER BY killcomp1 DESC";
    $results = mysql_query($querys);
        while($r = mysql_fetch_assoc($results)){
            mysql_query("UPDATE grpgusers SET points = points + 37500 WHERE id = " . $r['id']);
            Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
        }

$querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 49 AND raidcomp < 100  ORDER BY raidcomp DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 25000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 99 AND raidcomp < 250  ORDER BY raidcomp DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 50000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 249 AND raidcomp < 500  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
        while($r = mysql_fetch_assoc($results)){
            mysql_query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $r['id']);
            Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
        }
        $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 499 AND raidcomp < 750  ORDER BY raidcomp DESC";
        $results = mysql_query($querys);
            while($r = mysql_fetch_assoc($results)){
                mysql_query("UPDATE grpgusers SET points = points + 125000 WHERE id = " . $r['id']);
                Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
            }
            $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 749 AND raidcomp < 1000  ORDER BY raidcomp DESC";
            $results = mysql_query($querys);
                while($r = mysql_fetch_assoc($results)){
                    mysql_query("UPDATE grpgusers SET points = points + 150000 WHERE id = " . $r['id']);
                    Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
                }

            $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 999 AND raidcomp < 1250  ORDER BY raidcomp DESC";
            $results = mysql_query($querys);
                while($r = mysql_fetch_assoc($results)){
                    mysql_query("UPDATE grpgusers SET points = points + 200000 WHERE id = " . $r['id']);
                    Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
                }
                $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 1250 ORDER BY raidcomp DESC";
                $results = mysql_query($querys);
                    while($r = mysql_fetch_assoc($results)){
                        mysql_query("UPDATE grpgusers SET points = points + 250000 WHERE id = " . $r['id']);
                        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
                    }
}



$activityContestTypes = array(
    'backalley',
    'attacks',
    'mugs',
    'busts',
);
$typeToUse = $activityContestTypes[mt_rand(0, count($activityContestTypes) - 1)];

if ($typeToUse === 'crimes') {
    $typeValue = 1;
} else {
    $typeValue = mt_rand(1,100);
}

mysql_query("UPDATE `activity_contest` SET `type` = '" . $typeToUse . "', `type_value` = " . $typeValue);
Send_Event(1, 'Hourly cron ran fine');