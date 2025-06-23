#! /usr/bin/php
<?php

if ($_GET['key'] != 'cron94') {
    die();
}
include_once 'dbcon.php';
include_once 'classes.php';
include_once 'database/pdo_class.php';

$randN = mt_rand(1, 25);

$db->query("SELECT * FROM numbergame WHERE `userid` > 0 ORDER BY RAND() DESC LIMIT 1");
$db->execute();
$g = $db->fetch_row(true);

Send_Event($g['userid'], "Congratulations, You have won 1000 points in the number game!");

perform_query("UPDATE `grpgusers` SET `points` = `points` + 1000 WHERE `id` = ?", [$g['userid']]);
perform_query("UPDATE `numbergame` SET `userid` = 0");

perform_query("UPDATE `grpgusers` SET `hourbases` = `hourbases` + 1 WHERE `hourbases` >= '24'");

perform_query("UPDATE `grpgusers` SET `claimed` = `claimed` - 1 WHERE `claimed` >= '1'");

perform_query("UPDATE `grpgusers` SET `attackprotection` = `attackprotection` - 1 WHERE `attackprotection` >= '1'");
perform_query("UPDATE `grpgusers` SET `mugprotection` = `mugprotection` - 1 WHERE `mugprotection` >= '1'");

//MOTH
$resulth = mysql_query("SELECT * FROM `grpgusers` WHERE `admin` = 0 AND `moth` > 0 ORDER BY `moth` DESC LIMIT 1");
$workedh = mysql_fetch_array($resulth);
if (isset($workedh['id']) && $workedh['id']) {
    $moth = new User($workedh['id']);
    $newpoints = $moth->points + 500;
    perform_query("UPDATE `grpgusers` SET `moth` = 0, `points` = ? WHERE `id` = ?", [$newpoints, $moth->id]);
    Send_Event($moth->id, "You are mugger of the hour with a total of " . prettynum($workedh['moth']) . " Mugs. [ + 500 pts ]");
    perform_query("INSERT INTO `MOTH` (`userid`, `kills`, `time`) VALUES (?, ?, ?)", [$moth->id, $workedh['moth'], time()]);
}

//KOTH killer of the hour
$resultq = mysql_query("SELECT * FROM `grpgusers` WHERE `admin` = 0 AND `koth` > 0 ORDER BY `koth` DESC LIMIT 1");
$workedq = mysql_fetch_array($resultq);
if (isset($workedq['id']) && $workedq['id']) {
    $koth = new User($workedq['id']);
    $newpoints = $koth->points + 500;
    perform_query("UPDATE `grpgusers` SET `koth` = 0, `points` = ? WHERE `id` = ?", [$newpoints, $koth->id]);
    Send_event($koth->id, "You have won the Killer of the hour with " . prettynum($workedq['koth']) . " Kills. [ + 500 pts ]");

    perform_query("INSERT INTO oth (userid, `type`, amnt, timestamp) VALUES(?, 'killer', ?, unix_timestamp())", [$koth->id, $workedq['koth']]);
}

//Leveller Of the Hour
$resultq = mysql_query("SELECT * FROM grpgusers WHERE `admin` = 0 AND `loth` > 0 ORDER BY `loth` DESC LIMIT 1");
$workedq = mysql_fetch_array($resultq);
if (isset($workedq['id']) && $workedq['id']) {
    $loth = new User($workedq['id']);
    $newpoints = $loth->points + 500;
    Send_event($loth->id, "You have won the leveler of the hour with " . prettynum($workedq['loth']) . " EXP gained. [ + 500 pts ]");
    perform_query("UPDATE `grpgusers` SET `loth` = 0, `points` = ? WHERE `id` = ?", [$newpoints, $loth->id]);
    perform_query("INSERT INTO oth (userid, `type`, amnt, timestamp) VALUES(?, 'leveler' , ?, unix_timestamp())", [$loth->id, $workedq['loth']]);
}

//Buster Of the Hour
$resultq = mysql_query("SELECT * FROM grpgusers WHERE `admin` = 0 AND `both` > 0 ORDER BY `both` DESC LIMIT 1");
$workedq = mysql_fetch_array($resultq);
if (isset($workedq['id']) && $workedq['id']) {
    $bust = new User($workedq['id']);
    $newpoints = $bust->points + 500;
    Send_event($bust->id, "You have won the buster of the hour with " . prettynum($workedq['both']) . " Busts gained. [ + 500 pts ]");
    perform_query("UPDATE `grpgusers` SET `both` = 0, `points` = ? WHERE `id` = ?", [$newpoints, $bust->id]);
}

// Reset MOTH, KOTH, LOTH & BOTH & others
perform_query("UPDATE grpgusers SET `moth` = 0, `koth` = 0, `loth` = 0, `both` = 0");

perform_query("UPDATE grpgusers SET `searchdowntown` = 20 WHERE `searchdowntown` = 0");


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
        perform_query("UPDATE `grpgusers` SET `missionkills`= ?, `missionmugs`= ?, `missionbusts`= ?, `missioncrimes`= ?, `missionsactive`= ? WHERE `id`= ?", [$newkills, $newmugs, $newbusts, $newcrimes, $newmission, $line['id']]);
        Send_Event($line['id'], "You have Completed your mission! You Receive a Mini Mission Pack.", $line['id']);
    } elseif ($line['missionkills'] < 35 && $line['missionmugs'] < 10 && $line['missioncrimes'] < 100 && $line['missionbusts'] < 5) {
        $newkills = 0;
        $newmugs = 0;
        $newcrimes = 0;
        $newbusts = 0;
        $newmission = 0;
        perform_query("UPDATE `grpgusers` SET `missionkills`= ?, `missionmugs`= ?, `missionbusts`= ?, `missioncrimes`= ?, `missionsactive`= ? WHERE `id`= ?", [$newkills, $newmugs, $newbusts, $newcrimes, $newmission, $line['id']]);
        Send_Event($line['id'], "You have Failed your Mission this Hour! Try Again.", $line['id']);
    } else {
        $newkills = 0;
        $newmugs = 0;
        $newcrimes = 0;
        $newbusts = 0;
        $newmission = 0;
        perform_query("UPDATE `grpgusers` SET `missionkills`= ?, `missionmugs`= ?, `missionbusts`= ?, `missioncrimes`= ?, `missionsactive`= ? WHERE `id`= ?", [$newkills, $newmugs, $newbusts, $newcrimes, $newmission, $line['id']]);
    }
}



$ladderRewards = [150, 100, 100, 100, 100, 100, 100, 100, 100, 100];

$attackLadderRes = mysql_query("SELECT * FROM `attackladder` ORDER BY `spot` ASC");
while ($row = mysql_fetch_array($attackLadderRes)) {
    if ((time() - $row['last_attack']) > 14400) {
        perform_query("DELETE FROM attackladder WHERE `user` = ?", [$row['user']]);
        Send_Event($row['user'], "[-_USERID_-] You were removed from the Attack Ladder due to inactivity.", $row['user']);
    } else {
        perform_query("UPDATE `grpgusers` SET `points` = `points` + ? WHERE `id` = ? LIMIT 1", [$ladderRewards[$row['spot'] - 1], $row['user']]);
        Send_Event($row['user'], "[-_USERID_-] you are ranked " . ordinal($row['spot']) . " in the attack ladder and you've been rewarded " . $ladderRewards[$row['spot'] - 1] . " points ", $row['user']);
    }
}

perform_query("SET @counter := 0; UPDATE `attackladder` SET `attackladder`.`spot` = (@counter := @counter + 1) ORDER BY `attackladder`.`spot` ASC");


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
        perform_query("UPDATE `grpgusers` SET `points` = `points` + ? WHERE `id` = ?", [$owned_points, $line['id']]);
        Send_event($line['id'], "You earned " . number_format($owned_points, 0) . " points for being the Boss!");
    }
}

$queen_result = mysql_query("SELECT `id`, `city` FROM `grpgusers` WHERE `queen` > 0");
while ($line = mysql_fetch_array($queen_result)) {
    $cityId = $line['city'];

    $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($line['city']) . "' LIMIT 1");
    $city_result = mysql_fetch_assoc($city_query);

    if ($city_result['owned_points'] > 0) {
        if ($cityID == 600) {
            $owned_points = 3250;
        } else {
            $owned_points = $city_result['owned_points'];
        }

        $bossUser = new User($line['id']);

        $userPrestigeSkills = getUserPrestigeSkills($bossUser);
        if ($userPrestigeSkills['throne_points_unlock'] > 0) {
            $owned_points = $owned_points + ($owned_points / 100 * 20);
        }
        $twenty_percent = $owned_points - ($owned_points * 0.20);

        perform_query("UPDATE `grpgusers` SET `points` = `points` + ? WHERE `id` = ?", [$twenty_percent, $line['id']]);
        Send_event($line['id'], "You earned " . $twenty_percent . " points for being the *Under Boss!");
    }
}

perform_query("UPDATE `grpgusers` SET `jail_bot_credits` = `jail_bot_credits` + 50 WHERE `jail_bot_credits` < 1");

//Credit the contest winners!
$now = time();
$targetTime = strtotime("2024-04-10 17:00");

if (abs($now - $targetTime) <= 60) {
    $points1 = 150000;
    $points2 = 100000;
    $points3 = 100000;
    $money1 = 50000000;
    $money2 = 25000000;
    $money3 = 12500000;
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
        } else if ($count === 3) {
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
        } else if ($count === 3) {
            $con = 'rd';
            $raids = $raids3;
            $money = $money3;
            $points = $points3;
        }

        Send_Event($row['id'], 'You finished the raid competition in ' . $count . '' . $con . ' position and gained, $' . number_format($money) . ', ' . number_format($points) . ' points and ' . $raids . ' Tokens');

        perform_query("UPDATE grpgusers SET money = money + ?, points = points + ?, raidtokens = raidtokens + ? WHERE id = ?", [$money, $points, $raids, $row['id']]);

        $count++;
        echo "<br>";
    }
    Send_Event(1, "Contests paid out");
    Send_Event(2, "contests paid out");

    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 999 AND killcomp1 < 2500  ORDER BY killcomp1 DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 25000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }

    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 2499 AND killcomp1 < 5000  ORDER BY killcomp1 DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 6500 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 4999 AND killcomp1 < 15000  ORDER BY killcomp1 DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 13000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 14999 AND killcomp1 < 30000  ORDER BY killcomp1 DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 37500 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }

    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 49 AND raidcomp < 100  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 25000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 99 AND raidcomp < 250  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 50000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 249 AND raidcomp < 500  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 100000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 499 AND raidcomp < 750  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 125000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 749 AND raidcomp < 1000  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 150000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }

    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 999 AND raidcomp < 1250  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 200000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 1250 ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
    while ($r = mysql_fetch_assoc($results)) {
        perform_query("UPDATE grpgusers SET points = points + 250000 WHERE id = ?", [$r['id']]);
        Send_Event($r['id'], 'You got rewarded ' . $points . ' for hitting your milesstone in the contest');
    }
}

$prizes = array();
$prizes[1] = 3000;
$prizes[2] = 1500;
$prizes[3] = 500;

$db->query("SELECT * FROM petladder WHERE attacks > 0 ORDER BY attacks DESC LIMIT 3");
$rows = $db->fetch_row();

$i = 1;
foreach ($rows as $row) {
    $pet = $db->query("SELECT * FROM pets WHERE id = " . $row['pet_id'] . " LIMIT 1");
    $pet = $db->fetch_row(true);

    if (isset($prizes[$i])) {
        $prize = $prizes[$i];

        $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $pet['userid']);
        $db->execute();

        Send_Event($pet['userid'], "You have won " . $prize . " points for being in spot " . $i . " of the pet ladder for attacks.");
    }

    $i++;
}

$db->query("SELECT * FROM petladder WHERE gym > 0 ORDER BY gym DESC LIMIT 3");
$rows = $db->fetch_row();

$i = 1;
foreach ($rows as $row) {
    $pet = $db->query("SELECT * FROM pets WHERE id = " . $row['pet_id'] . " LIMIT 1");
    $pet = $db->fetch_row(true);

    if (isset($prizes[$i])) {
        $prize = $prizes[$i];

        $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $pet['userid']);
        $db->execute();

        Send_Event($pet['userid'], "You have won " . $prize . " points for being in spot " . $i . " of the pet ladder for Gym.");
    }

    $i++;
}

$db->query("SELECT * FROM petladder WHERE exp > 0 ORDER BY exp DESC LIMIT 3");
$rows = $db->fetch_row();

$i = 1;
foreach ($rows as $row) {
    $pet = $db->query("SELECT * FROM pets WHERE id = " . $row['pet_id'] . " LIMIT 1");
    $pet = $db->fetch_row(true);

    if (isset($prizes[$i])) {
        $prize = $prizes[$i];

        $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $pet['userid']);
        $db->execute();

        Send_Event($pet['userid'], "You have won " . $prize . " points for being in spot " . $i . " of the pet ladder for Crime EXP.");
    }

    $i++;
}

$db->query("UPDATE petladder SET exp = 0, gym = 0, attacks = 0");
$db->execute();

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
    $typeValue = mt_rand(1, 100);
}

perform_query("UPDATE `activity_contest` SET `type` = ?, `type_value` = ?", [$typeToUse, $typeValue]);

Send_Event(1, 'Hourly cron ran fine');
Send_Event(2, 'Hourly cron ran fine');
