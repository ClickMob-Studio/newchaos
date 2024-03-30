#! /usr/bin/php
<?php

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
$result2 = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
while ($line = mysql_fetch_array($result2)) {
    // $newstuff = $line['hourbases'] + 1;
    // if ($line['hourbases'] <= 0) {
    //     $result = mysql_query("UPDATE `grpgusers` SET `hourbases`='" . $newstuff . "' WHERE `id`='" . $line['id'] . "'");
    // }
    // $newstuff = $line['attackprotection'] - 1;
    // if ($line['attackprotection'] >= 1) {
    //     $result = mysql_query("UPDATE `grpgusers` SET `attackprotection`='" . $newstuff . "' WHERE `id`='" . $line['id'] . "'");
    // }
    // $newstuff = $line['mugprotection'] - 1;
    // if ($line['mugprotection'] >= 1) {
    //     $result = mysql_query("UPDATE `grpgusers` SET `mugprotection`='" . $newstuff . "' WHERE `id`='" . $line['id'] . "'");
    // }
    if ($line['missionsactive'] == 1 && $line['missionkills'] >= 35 && $line['missionmugs'] >= 10 && $line['missioncrimes'] >= 100 && $line['missionbusts'] >= 5) {
        $newkills = 0;
        $newmugs = 0;
        $newcrimes = 0;
        $newbusts = 0;
        $newmission = 0;
        Give_Item(114, $line['id']); //give the user their item they bought
        $result = mysql_query("UPDATE `grpgusers` SET `missionkills`='" . $newkills . "', `missionmugs`='" . $newmugs . "', `missionbusts`='" . $newbusts . "', `missioncrimes`='" . $newcrimes . "', `missionsactive`='" . $newmission . "' WHERE `id`='" . $line['id'] . "'");
        Send_Event($line['id'], "You have Completed your mission! You Receive a Mini Mission Pack.", $line['id']);
    } elseif ($line['missionsactive'] == 1 && $line['missionkills'] < 35 && $line['missionmugs'] < 10 && $line['missioncrimes'] < 100 && $line['missionbusts'] < 5) {
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

// REMOVED - ADDED ABOVE TO SAVE QUERIES
// //attackprotection
// $result2 = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
// while ($line = mysql_fetch_array($result2)) {
//     $newstuff = $line['attackprotection'] - 1;
//     if ($line['attackprotection'] >= 1) {
//         $result = mysql_query("UPDATE `grpgusers` SET `attackprotection`='" . $newstuff . "' WHERE `id`='" . $line['id'] . "'");
//     }
// }

// //MugProtection
// $result2 = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
// while ($line = mysql_fetch_array($result2)) {
//     $newstuff = $line['mugprotection'] - 1;
//     if ($line['mugprotection'] >= 1) {
//         $result = mysql_query("UPDATE `grpgusers` SET `mugprotection`='" . $newstuff . "' WHERE `id`='" . $line['id'] . "'");
//     }
// }
//MISSIONS
// $result2 = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
// while ($line = mysql_fetch_array($result2)) {

// }

// $result2 = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
// while ($line = mysql_fetch_array($result2)) {
//     $result = mysql_query("UPDATE `grpgusers` SET `moth` = '0', `godfather` = '0', `hourdip` = '1',`hoursearch` = '100', `bomb` = '1' WHERE `id`='" . $line['id'] . "'");
// }
// $koth = mysql_fetch_array(mysql_query("SELECT id,koth FROM grpgusers ORDER BY koth DESC LIMIT 1"));
// $loth = mysql_fetch_array(mysql_query("SELECT id,loth FROM grpgusers ORDER BY loth DESC LIMIT 1"));
// if (!empty($koth)) {
//  Send_event($koth['id'], "You have won the killer of the hour with " . prettynum($koth['koth']) . " kills. [ + 50 pts ]");
//mysql_query("INSERT INTO oth VALUES('',{$koth['id']},'killer',{$koth['koth']},unix_timestamp())");
//mysql_query("UPDATE grpgusers SET points = points + 50 WHERE id = {$koth['id']}");
//}
//if (!empty($loth)) {
//  Send_event($loth['id'], "You have won the leveler of the hour with " . prettynum($loth['loth']) . " EXP gained. [ + 50 pts ]");
///mysql_query("INSERT INTO oth VALUES('',{$loth['id']},'leveler',{$loth['loth']},unix_timestamp())");
//  mysql_query("UPDATE grpgusers SET points = points + 50 WHERE id = {$loth['id']}");
// }

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

// $userId = intval($row['user']);
// $user = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = {$userId} ORDER BY `id` DESC");
// $rowUser = mysql_fetch_array($user);
// $currentTime = time();
// $lastActive = $rowUser['lastactive'];
// $diff = abs($currentTime - $lastActive)/60/60 ;
// if ($diff >= 4){
//     $currentSpot = intval($row['spot']);
//     while ($rowSpotUpdate = mysql_fetch_array($attackLadderRes)){
//         if ($currentSpot < intval($rowSpotUpdate['spot'])){
//             mysql_query("UPDATE `attackladder` SET `spot` = $currentSpot WHERE `user` = '{$rowSpotUpdate['user']}' LIMIT 1") or mysql_error();
//         }
//         $currentSpot++;
//     }
//     mysql_query("DELETE FROM attackladder WHERE `user` = '{$row['user']}'");
// }
// $reward = $row['spot'] == '1' ? '150' : '100';
// Send_Event($row['user'], "[-_USERID_-] you are ranked ".$row['spot']." in the attack ladder and you’ve been rewarded ".$reward." points ", $row['user']);
//}
