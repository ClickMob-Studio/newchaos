<?php
if($_GET['key'] != 'cron94'){
    die();
}

include "classes.php";
include "database/pdo_class.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

//$db->query("SELECT * FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE missions.crimes >= mission.crimes AND completed = 'no' AND userid = 174 LIMIT 1");
$db->query("SELECT *, ms.id as missionid, m.crimes as reqCrimes, m.kills as reqKills, m.busts as reqBusts, m.mugs as reqMugs, m.backalleys as reqBackalleys, m.raids as reqRaids, ms.crimes as cCrimes, ms.kills as cKills, ms.mugs as cMugs, ms.backalleys as cBackalleys, ms.raids as cRaids, ms.busts as cBusts, m.exp_level AS mExpLevel FROM missions ms LEFT JOIN mission m ON ms.mid = m.id WHERE completed = 'no'");
$db->execute();

$missions = $db->fetch_row();

foreach ($missions as $mission) {

    $user_class = new User($mission['userid']);

    $itemTempUse = getItemTempUse($user_class->id);

    $prestigeUserSKills = getUserPrestigeSkills($user_class);
    $pointsPayoutBoost = 0;
    if ($prestigeUserSKills['mission_point_boost_level'] > 0) {
        $pointsPayoutBoost = 2 * $prestigeUserSKills['mission_point_boost_level'];
    }

    if ($mission['cKills'] >= $mission['reqKills'] && $mission['reqKills'] > 0 && $mission['kills_paid'] == 0) {
        $mPointsPayout = $mission['payKills'];
        if ($pointsPayoutBoost) {
            $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
        }

        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $db->execute(array(
            $mPointsPayout,
            $mission['userid']
        ));
        $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$mission['name']} objective to get {$mission['reqKills']} kills,{$mission['userid']}',unix_timestamp())");
        $db->execute();
        $db->query("UPDATE missions SET kills_paid = 1 WHERE id = ?");
        $db->execute(array(
            $mission['missionid']
        ));
        Send_event($mission['userid'], "You have completed {$mission['name']} objective to get {$mission['kills']} kills. [+ {$mission['payKills']} Points]");
    }
    if ($mission['cMugs'] >= $mission['reqMugs'] && $mission['reqMugs'] > 0 && $mission['mugs_paid'] == 0) {
        $mPointsPayout = $mission['payMugs'];
        if ($pointsPayoutBoost) {
            $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
        }

        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $db->execute(array(
            $mPointsPayout,
            $mission['userid']
        ));
        $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$mission['name']} objective to get {$mission['mugs']} mugs,{$mission['userid']}',unix_timestamp())");
        $db->execute();
        $db->query("UPDATE missions SET mugs_paid = 1 WHERE id = ?");
        $db->execute(array(
            $mission['missionid']
        ));
        Send_event($mission['userid'], "You have completed {$mission['name']} objective to get {$mission['mugs']} mugs. [+ {$mission['payMugs']} Points]");
    }
    if ($mission['cCrimes'] >= $mission['reqCrimes'] && $mission['reqCrimes'] > 0 && $mission['crimes_paid'] == 0) {
        $mPointsPayout = $mission['payCrimes'];
        if ($pointsPayoutBoost) {
            $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
        }

        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $db->execute(array(
            $mPointsPayout,
            $mission['userid']
        ));
        $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$mission['name']} objective to get {$mission['crimes']} crimes,{$mission['userid']}',unix_timestamp())");
        $db->execute();
        $db->query("UPDATE missions SET crimes_paid = 1 WHERE id = ?");
        $db->execute(array(
            $mission['missionid']
        ));
        Send_event($mission['userid'], "You have completed {$mission['name']} objective to get {$mission['crimes']} crimes. [+ {$mission['payCrimes']} Points]");
    }

    if ($mission['cBusts'] >= $mission['reqBusts'] && $mission['reqBusts'] > 0 && $mission['busts_paid'] == 0) {
        $mPointsPayout = $mission['payBusts'];
        if ($pointsPayoutBoost) {
            $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
        }

        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $db->execute(array(
            $mPointsPayout,
            $mission['userid']
        ));
        $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$mission['name']} objective to get {$mission['busts']} busts,{$mission['userid']}',unix_timestamp())");
        $db->execute();
        $db->query("UPDATE missions SET busts_paid = 1 WHERE id = ?");
        $db->execute(array(
            $mission['missionid']
        ));
        Send_event($mission['userid'], "You have completed {$mission['name']} objective to get {$mission['busts']} busts. [+ {$mission['payBusts']} Points]");
    }

    if ($mission['cKills'] >= $mission['reqKills'] && $mission['cCrimes'] >= $mission['reqCrimes'] && $mission['cBusts'] >= $mission['reqBusts'] && $mission['cMugs'] >= $mission['reqMugs'] && $mission['cBackalleys'] >= $mission['reqBackalleys'] && $mission['cRaids'] >= $mission['reqRaids']) {

//        $exp = 5 + (5 * ($mission['mExpLevel'] + 2));
//        $levelhurts = floor($user_class->level / 10);
//        $exp = ($exp - $levelhurts < 3) ? 3 : $exp - $levelhurts;
//
//        if ($prestigeUserSKills['mission_exp_boost_level'] > 0) {
//            $exp = $exp + ($exp / 100 * (2 * $prestigeUserSKills['mission_exp_boost_level']));
//        }

        $expgain = round($user_class->maxexp / 100 * $mission['mExpLevel']);
        if ($prestigeUserSKills['mission_exp_boost_level'] > 0) {
            $expgain = $expgain + ($expgain / 100 * (2 * $prestigeUserSKills['mission_exp_boost_level']));
        }

        if ($itemTempUse['perfume'] > 0) {
            $expgain = $expgain * 2;

            removeItemTempUse($user_class->id, 'love_potions_time', 1);
        }

        $db->query("UPDATE grpgusers SET exp = exp + ?, mission_count = mission_count + 1 WHERE id = ?");
        $db->execute(array(
            $expgain,
            $user_class->id
        ));

        if ($user_class->admin > 0) {
            Send_event($user_class->id, $mission['mExpLevel']);
            $expgain = number_format_short($expgain);
            Send_event($user_class->id, "You have completed the {$mission['name']}! [+ $expgain EXP]");
        } else {
            $expgain = number_format_short($expgain);
            Send_event($user_class->id, "You have completed the {$mission['name']}! [+ $expgain EXP]");
        }

        $db->query("UPDATE missions SET completed = 'successful' WHERE id = ?");
        $db->execute(array(
            $mission['missionid']
        ));

        $db->query("INSERT INTO missionlog VALUES (NULL,'[x] successfully completed their {$mission['name']}.,$user_class->id',unix_timestamp())");
        $db->execute();

        addCountTracking($user_class->id);
        addToGangCompLeaderboard($user_class->gang, 'missions_complete', 1);
    }
}
