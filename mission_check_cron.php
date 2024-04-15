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
$db->query("SELECT *, ms.id as missionid, m.crimes as reqCrimes, m.kills as reqKills, m.busts as reqBusts, m.mugs as reqMugs, ms.crimes as cCrimes, ms.kills as cKills, ms.mugs as cMugs, ms.busts as cBusts FROM missions ms LEFT JOIN mission m ON ms.mid = m.id WHERE completed = 'no'");
$db->execute();

$missions = $db->fetch_row();

foreach ($missions as $mission) {

    if ($mission['cCrimes'] >= $mission['reqCrimes'] && $mission['reqCrimes'] > 0 && $mission['crimes_paid'] == 0) {
        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $db->execute(array(
            $mission['payCrimes'],
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

    if ($mission['cKills'] >= $mission['reqKills'] && $mission['cCrimes'] >= $mission['reqCrimes'] && $mission['cBusts'] >= $mission['reqBusts'] && $mission['cMugs'] >= $mission['reqMugs']) {

        $user_class = new User($mission['userid']);

        $exp = 5 + (5 * $mission['mid']);
        $levelhurts = floor($user_class->level / 10);
        $exp = ($exp - $levelhurts < 3) ? 3 : $exp - $levelhurts;
        $expgain = floor($user_class->maxexp * ($exp / 100));

        $db->query("UPDATE grpgusers SET exp = exp + ? WHERE id = ?");
        $db->execute(array(
            $expgain,
            $user_class->id
        ));

        Send_event($user_class->id, "You have completed the {$mission['name']}! [+ $expgain EXP]");

        $db->query("UPDATE missions SET completed = 'successful' WHERE id = ?");
        $db->execute(array(
            $mission['missionid']
        ));

        $db->query("INSERT INTO missionlog VALUES (NULL,'[x] successfully completed their {$mission['name']}.,$user_class->id',unix_timestamp())");
        $db->execute();

        addToGangCompLeaderboard($user_class->gang, 'missions_complete', 1);
    }
}