#! /usr/bin/php
<?php

if($_GET['key'] != 'cron94'){
    die();
}

chdir("/var/www/html");

(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('');

include("dbcon.php");
include("classes.php");
include 'database/pdo_class.php';
$otds = array(
    array(
        'baotd',
        'Back Alley OTD',
        250
    ),
    array(
        'botd',
        'Buster OTD',
        1500
    ),
    array(
        'motd',
        'Mugger OTD',
        2500
    ),
    array(
        'kotd',
        'Killer OTD',
        2500
    ),
);
foreach ($otds as $otd) {
    $sql  = $otd[0];
    $type = $otd[1];
    $pts  = $otd[2];
    $db->query("SELECT userid, $sql FROM ofthes WHERE $sql > 0 ORDER BY $sql DESC LIMIT 1");
    $db->execute();
    if ($db->num_rows()) {
        $row = $db->fetch_row(true);
        $db->startTrans();
        $db->query("UPDATE grpgusers SET points = points + $pts WHERE id = ?");
        $db->execute(array(
            $row['userid']
        ));
        $db->query("INSERT INTO otdwinners (`userid`, `type`, `howmany`, `timestamp`) VALUES (?, ?, ?, ?)");
        $db->execute(array(
            $row['userid'],
            $type,
            $row[$sql],
            time()
        ));
        $db->endTrans();
        Send_Event($row['userid'], "You won $type! [+$pts Points]");
    }
}

$db->query("SELECT id, todaysexp FROM grpgusers WHERE `todaysexp` > 0 ORDER BY `todaysexp` DESC LIMIT 1");
$db->execute();
$row = $db->fetch_row(true);
$db->query("UPDATE grpgusers SET points = points + 2000 WHERE id = ?");
$db->execute(array(
    $row['id']
));
$db->query("INSERT INTO otdwinners (`userid`, `type`, `howmany`, `timestamp`) VALUES (?, ?, ?, ?)");
$db->execute(array(
    $row['id'],
    'Leveller OTD',
    $row['todaysexp'],
    time()
));
Send_Event($row['id'], "You won Leveller Of The Day [+2000 Points]");

$db->query("SELECT id, tamt FROM grpgusers WHERE `tamt` > 0 ORDER BY `tamt` DESC LIMIT 1");
$db->execute();
$row = $db->fetch_row(true);
$db->query("UPDATE grpgusers SET points = points + 2500 WHERE id = ?");
$db->execute(array(
    $row['id']
));
$db->query("INSERT INTO otdwinners (`userid`, `type`, `howmany`, `timestamp`) VALUES (?, ?, ?, ?)");
$db->execute(array(
    $row['id'],
    "Most Mugged Today",
    $row['tamt'],
    time()
));
Send_Event($row['id'], "You won Most Money Mugged Today [+2500 Points]");
mysql_query("UPDATE grpgusers SET ffban = ffban - 1 WHERE ffban > 0");
mysql_query("UPDATE `grpgusers` SET `tamt` = '0', `todayskills` = '0', `todaysexp` = '0', `boxes_opened` = '1', `crimeauto` = '0', `csmuggling` = '6', `prayer` = '1', `searchdowntown` = '100', `dailytrains` = '0', `dailymugs` = '0', `spins` = '20', `gameevents` = '0', `voted1`='0', `dailyClockins` = '0', `doors`='3', `slots_left1`='100', `psmuggling2`='5', `roulette`='1', `luckydip`='1', `luckydip2`='1',`chase` = '1'") or die(mysql_error());

$db->query("UPDATE ofthes SET baotd = 0, botd = 0, motd = 0, kotd = 0");
$db->execute();
mysql_query("UPDATE jobInfo SET addedPercent = 0 WHERE dailyClockins < 5");
mysql_query("UPDATE jobInfo SET addedPercent = addedPercent + 5 WHERE dailyClockins >= 5 AND addedPercent < 50");
mysql_query("UPDATE jobInfo SET dailyClockins = 0");
mysql_query("UPDATE grpgusers SET apban = apban - 1 WHERE apban > 0");
mysql_query("UPDATE grpgusers SET relationshipdays = relationshipdays + 1 WHERE relationship > 0");

mysql_query("DELETE FROM votes WHERE 1");
mysql_query("DELETE FROM dond WHERE 1");
mysql_query("UPDATE grpgusers SET rmdays = GREATEST(rmdays-1,0)");
$users = mysql_query("SELECT * FROM grpgusers WHERE ip = {$_SERVER['REMOTE_ADDR']} LIMIT 1");
$users = mysql_fetch_array($users);
$linkeduser = $users['username'];
send_event(1, "IP: {$_SERVER['REMOTE_ADDR']} Ran update-is-cancel-runner.php. This IP is linked to $linkeduser. Follow up.", 1);
$result3 = mysql_query("SELECT * FROM grpgusers ORDER BY id ASC");
while ($line = mysql_fetch_array($result3)) {
    $person_class = new User($line['id']);
    if ($line['rmdays'] >= 1)
        $multiply = 0.04;
    else
        $multiply = 0.02;
    $addmul = $ptsadd = 0;
    if ($person_class->donations >= 50) {
        $addmul = .02;
        $ptsadd = 75;
    }
    if ($person_class->donations >= 100) {
        $addmul = .03;
        $ptsadd = 120;
    }
    if ($person_class->donations >= 200) {
        $addmul = .05;
        $ptsadd = 150;
    }
    $multiply += $addmul;
    if ($line['bank'] >= 15000000)
        $interest = ceil(15000000 * $multiply);
    else
        $interest = ceil($line['bank'] * $multiply);
    $newmoney = round($line['bank'] + $interest);
    mysql_query("UPDATE grpgusers SET bank = $newmoney, points = points + $ptsadd WHERE id = {$line['id']}");
    Send_Event($line['id'], "You have earned " . prettynum($interest, 1) . " for your bank", $line['id']);
}
$result = mysql_query("DELETE FROM rating");
$result3 = mysql_query("SELECT * FROM bans");
while ($line = mysql_fetch_array($result3)) {
    $newbandays = $line['days'] - 1;
    if ($line['days'] > 1)
        mysql_query("UPDATE bans SET days= $newbandays WHERE banid = {$line['banid']}");
    else if ($line['days'] == 1) {
        mysql_query("UPDATE grpgusers SET `ban/freeze` = 0 WHERE id = {$line['id']}");
        mysql_query("DELETE FROM bans WHERE banid = {$line['banid']}");
    }
}

$tickCost = 250000;
$db->query("SELECT SUM(tickets) FROM cashlottery");
$db->execute();
$numlotto = $db->fetch_single();
$amountlotto = $numlotto * $tickCost;

$checklotto = mysql_query("SELECT * FROM cashlottery");
$numlotto = mysql_num_rows($checklotto);
if ($numlotto > 0) {
    $result = mysql_query("SELECT * FROM cashlottery WHERE userid NOT IN (1, 174) ORDER BY RAND() LIMIT 1");
    $worked = mysql_fetch_array($result);
    // $amountlotto = $numlotto * 250000;
    $cashlottery_class = new User($worked['userid']);
    $newbank = $cashlottery_class->bank + $amountlotto;
    $result = mysql_query("UPDATE grpgusers SET bank = $newbank WHERE id = {$worked['userid']}");
    mysql_query("INSERT INTO mlottowinners VALUES ('', {$worked['userid']}, $amountlotto)");
    Send_Event($cashlottery_class->id, "Congratulations! You have won " . prettynum($amountlotto, 1) . " on the lottery!", $cashlottery_class->id);
    mysql_query("DELETE FROM `cashlottery`");
    mysql_query("UPDATE gameevents SET cashlottery = '<li>There were " . prettynum($numlotto) . " lottery tickets bought yesterday.</li><li>The jackpot was " . prettynum($amountlotto, 1) . ".</li><li>The winner was [-_USER_-].</li>', cashlotteryid = $cashlottery_class->id");
} else
    mysql_query("UPDATE gameevents SET cashlottery = '<li>There were $numlotto lottery tickets bought yesterday.</li>'");

$tickCost = 50;
$db->query("SELECT SUM(tickets) FROM `ptslottery`");
$db->execute();
$numlotto = $db->fetch_single();
$amountlotto = round($numlotto * $tickCost);

$checklotto = mysql_query("SELECT * FROM `ptslottery`");
$numlotto = mysql_num_rows($checklotto);
if ($numlotto > 0) {
    $result = mysql_query("SELECT * FROM ptslottery WHERE userid NOT IN (1, 174) ORDER BY RAND() LIMIT 1");
    $worked = mysql_fetch_array($result);
    $checklotto = mysql_query("SELECT * FROM ptslottery");
    $numlotto = mysql_num_rows($checklotto);
    // $amountlotto = $numlotto * 50;
    // $amountlotto = round($amountlotto);
    $cashlottery_class = new User($worked['userid']);
    $newpoints = $cashlottery_class->points + $amountlotto;
    $result = mysql_query("UPDATE grpgusers SET points = $newpoints WHERE id = {$worked['userid']}");
    mysql_query("INSERT INTO plottowinners VALUES ('', {$worked['userid']}, $amountlotto)");
    Send_Event($cashlottery_class->id, "Congratulations! You have won " . prettynum($amountlotto) . " points on the lottery!", $cashlottery_class->id);
    mysql_query("DELETE FROM ptslottery");
    mysql_query("UPDATE gameevents SET ptslottery = '<li>There were " . prettynum($numlotto) . " lottery tickets bought yesterday.</li><li>The jackpot was " . prettynum($amountlotto) . " points.</li><li>The winner was [-_USER_-].</li>', `ptslotteryid` = $cashlottery_class->id");
} else
    mysql_query("UPDATE gameevents SET ptslottery = '<li>There were " . $numlotto . " lottery tickets bought yesterday.</li>'");
mysql_query("UPDATE grpgusers SET boxes_opened = 1, csmuggling = 6, psmuggling = 6, prayer = 1, searchdowntown = 100,spins = 20, todayskills = 0,voted1 = 0, doors = 3, slots_left1 = 100, roulette = 1, luckydip = 1, csmuggling = 6, chase = 1");
mysql_query("UPDATE grpgusers SET gndays = gndays - 1 WHERE gndays > 0");
mysql_query("UPDATE grpgusers SET blocked = blocked - 1 WHERE blocked > 0");
mysql_query("UPDATE grpgusers SET actionpoints = 25 WHERE actionpoints < 25 ");

$result2 = mysql_query("SELECT * FROM grpgusers ORDER BY id ASC");
while ($line = mysql_fetch_array($result2)) {
    $newrmupgrade = $line['rmdupgrade'] - 1;
    if ($line['rmupgrade'] >= 1)
        $result = mysql_query("UPDATE grpgusers SET rmupgrade = $newrmupgrade WHERE id = {$line['id']}");
}
// $files = array(
//     'actlog'
// );
// foreach ($files as $name) {
//     $file = "/usr/share/nginx/logs/{$name}.txt";
//     $current = file_get_contents($file);
//     $month = date('m', time());
//     $day = date('d', time());
//     $hour = date('G', time()) - 1;
//     $file = "/usr/share/nginx/logs/{$name}/" . $month . "_" . $day . "_" . $hour . "{$name}.txt";
//     file_put_contents($file, $current);
//     file_put_contents("/usr/share/nginx/logs/{$name}.txt", '');
// }
$db->startTrans();
$db->query("UPDATE rentedProperties SET days = days - 1");
$db->execute();
$db->query("SELECT * FROM rentedProperties WHERE days = 0");
$db->execute();
$rows = $db->fetch_row();
foreach($rows as $row){
    $db->query("INSERT INTO ownedProperties VALUES ('', ?, ?)");
    $db->execute(array(
        $row['owner'],
        $row['houseid']
    ));
}
$db->query("DELETE FROM rentedProperties WHERE days <= 0");
$db->execute();
$db->endTrans();


// Gang Of The Day
$ignoreGangs = "11, 31"; // admin gangs to exclude
$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyKills` DESC LIMIT 1");
$db->execute();
$topKills = $db->fetch_row()[0]['id'];

$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyCrimes` DESC LIMIT 1");
$db->execute();
$topCrimes = $db->fetch_row()[0]['id'];

$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyBusts` DESC LIMIT 1");
$db->execute();
$topBusts = $db->fetch_row()[0]['id'];

$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyMugs` DESC LIMIT 1");
$db->execute();
$topMugs = $db->fetch_row()[0]['id'];

$_ids = compact("topKills", "topCrimes", "topBusts", "topMugs");

foreach ($_ids as $_id) {
    $db->query("UPDATE gangs SET respect = respect + 100 WHERE id = ?");
    $db->execute(
        array(
            $_id
        )
    );
}

Gang_Event($topKills, "Respect Gang Of The Day - Kills +100 Respect", 0);
Gang_Event($topCrimes, "Respect Gang Of The Day - Crimes +100 Respect", 0);
Gang_Event($topMugs, "Respect Gang Of The Day - Mugs +100 Respect", 0);
Gang_Event($topBusts, "Respect Gang Of The Day -Bustss +100 Respect", 0);

$db->query("UPDATE gangs SET dailyCrimes = 0, dailyKills = 0, dailyBusts = 0, dailyMugs = 0");
$db->query("UPDATE grpgusers SET nerref = 0");
$db->execute();


$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC LIMIT 2");
$db->execute();
$dailyRows = $db->fetch_row();

$i = 1;
foreach ($dailyRows as $row) {
    $db->query("SELECT * FROM `grpgusers` WHERE `gang` = " . $row['gang_id']);
    $db->execute();
    $userRows = $db->fetch_row();

    foreach ($userRows as $uRow) {
        if ($i == 1) {
            $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $uRow['id']);
            $db->execute();

            Give_Item(163, $uRow['id'], 1);
            Give_Item(42, $uRow['id'], 1);

            Send_Event($uRow['id'], "Your gang won 1st place in the daily contest. You have been awarded 25,000 points, 1 Police Badge & 1 Mystery Box.");
        } else {
            Give_Item(42, $uRow['id'], 1);

            Send_Event($uRow['id'], "Your gang won 2nd place in the daily contest. You have been awarded 1 Mystery Box.");
        }
    }

    $i++;
}

$db->query("UPDATE `gang_comp_leaderboard` SET `daily_missions_complete` = 0");
$db->execute();
Send_Event(1, "daycron211111.php");
?>