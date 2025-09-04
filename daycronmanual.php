<?php
#chdir("/var/www/html");

if ($_GET['key'] != 'cron94') {
    die();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

#(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('Manual');

include("dbcon.php");
include("classes.php");
include 'database/pdo_class.php';
include_once 'includes/functions.php';

$otds = array(
    array(
        'baotd',
        'Back Alley OTD',
        250
    ),
    array(
        'botd',
        'Buster OTD',
        10000
    ),
    array(
        'motd',
        'Mugger OTD',
        10000
    ),
    array(
        'kotd',
        'Killer OTD',
        10000
    ),
);
foreach ($otds as $otd) {
    $sql = $otd[0];
    $type = $otd[1];
    $pts = $otd[2];
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
$db->query("UPDATE grpgusers SET ffban = 0");
$db->execute();

$db->query("SELECT id, todaysexp FROM grpgusers WHERE `todaysexp` > 0 ORDER BY `todaysexp` DESC LIMIT 1");
$db->execute();
$row = $db->fetch_row(true);
$db->query("UPDATE grpgusers SET points = points + 10000 WHERE id = ?");
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
Send_Event($row['id'], "You won Leveller Of The Day [+10000 Points]");

$db->query("SELECT id, tamt FROM grpgusers WHERE `tamt` > 0 ORDER BY `tamt` DESC LIMIT 1");
$db->execute();
$row = $db->fetch_row(true);
if ($row) {
    $db->query("UPDATE grpgusers SET points = points + 10000 WHERE id = ?");
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
    Send_Event($row['id'], "You won Most Money Mugged Today [+10000 Points]");
}

perform_query("UPDATE `grpgusers` SET `tamt` = '0', `todayskills` = '0', `todaysexp` = '0', `boxes_opened` = '1', `crimeauto` = '0', `csmuggling` = '6', `prayer` = '1', `searchdowntown` = '100', `dailytrains` = '0', `dailymugs` = '0', `spins` = '20', `gameevents` = '0', `voted1`='0', `dailyClockins` = '0', `doors`='5', `rtsmuggling`='7', `slots_left1`='100', `psmuggling2`='5', `roulette`='1', `luckydip`='1', `luckydip2`='1',`chase` = '1'");

$db->query("UPDATE ofthes SET baotd = 0, botd = 0, motd = 0, kotd = 0");
$db->execute();

perform_query("UPDATE jobInfo SET addedPercent = 0 WHERE dailyClockins < 5");
perform_query("UPDATE jobInfo SET addedPercent = addedPercent + 5 WHERE dailyClockins >= 5 AND addedPercent < 50");
perform_query("UPDATE jobInfo SET dailyClockins = 0");
perform_query("UPDATE grpgusers SET apban = apban - 1 WHERE apban > 0");
perform_query("UPDATE grpgusers SET relationshipdays = relationshipdays + 1 WHERE relationship > 0");

perform_query("DELETE FROM votes WHERE 1");
perform_query("DELETE FROM dond WHERE 1");
perform_query("UPDATE grpgusers SET rmdays = GREATEST(rmdays-1,0)");

$db->query("SELECT * FROM grpgusers WHERE ip = ? LIMIT 1");
$db->execute([$_SERVER['REMOTE_ADDR']]);
$user = $db->fetch_row(true);

$linkeduser = $user['username'];
send_event(1, "IP: {$_SERVER['REMOTE_ADDR']} Ran update-is-cancel-runner.php. This IP is linked to $linkeduser. Follow up.", 1);

$db->query("SELECT * FROM grpgusers ORDER BY id ASC");
$allusers = $db->fetch_row();
foreach ($allusers as $line) {
    $person_class = new User($line['id']);
    // Calculate the base interest rate based on remaining membership days
    if ($person_class->rmdays >= 1) {
        $interest = 0.04;  // 4% interest rate if membership days are 1 or more
    } else {
        $interest = 0.02;  // 2% interest rate otherwise
    }

    // Adjust interest rate based on donations
    $addmul = $ptsadd = 0;
    if ($person_class->donations >= 50) {
        $addmul = 0.02;
        $ptsadd = 75;
    }
    if ($person_class->donations >= 100) {
        $addmul = 0.03;
        $ptsadd = 120;
    }
    if ($person_class->donations >= 200) {
        $addmul = 0.05;
        $ptsadd = 150;
    }

    // Increase the interest rate by the adjustments from donations
    $interest += $addmul;

    // Calculate the effective interest amount based on the user's bank balance
    if ($person_class->bank >= 15000000) {
        $interest = ceil(15000000 * $interest);
        if ($person_class->bankboost > 0) {
            $interest += ($interest * ($person_class->bankboost / 10));
        }
    } else {
        $interest = ceil($person_class->bank * $interest);  // Interest based on the actual bank balance
        if ($person_class->bankboost > 0) {
            $interest += ($interest * ($person_class->bankboost / 10));  // Adjusting the interest rate by bankboost
        }
    }
    $newmoney = round($line['bank'] + $interest);

    perform_query("UPDATE grpgusers SET bank = ?, points = points + ? WHERE id = ?", [$newmoney, $ptsadd, $line['id']]);
    Send_Event($line['id'], "You have earned " . prettynum($interest, 1) . " for your bank", $line['id']);
}
perform_query("DELETE FROM rating");

$db->query("SELECT * FROM bans");
$bans = $db->fetch_row();
foreach ($bans as $line) {
    $newbandays = $line['days'] - 1;
    if ($line['days'] > 1)
        perform_query("UPDATE bans SET days= ? WHERE banid = ?", [$newbandays, $line['banid']]);
    else if ($line['days'] == 1) {
        perform_query("UPDATE grpgusers SET `ban/freeze` = 0 WHERE id = ?", [$line['id']]);
        perform_query("DELETE FROM bans WHERE banid = ?", [$line['banid']]);
    }
}

$tickCost = 250000;
$db->query("SELECT SUM(tickets) FROM cashlottery");
$db->execute();
$numlotto = $db->fetch_single();
$amountlotto = $numlotto * $tickCost;

$db->query("SELECT * FROM cashlottery");
$lottery = $db->fetch_row();
$numlotto = count($lottery);
if ($numlotto > 0) {
    $worked = $lottery[array_rand($lottery)];

    $cashlottery_class = new User($worked['userid']);
    $newbank = $cashlottery_class->bank + $amountlotto;
    perform_query("UPDATE grpgusers SET bank = ? WHERE id = ?", [$newbank, $worked['userid']]);
    perform_query("INSERT INTO mlottowinners VALUES ('', ?, ?)", [$worked['userid'], $amountlotto]);
    Send_Event($cashlottery_class->id, "Congratulations! You have won " . prettynum($amountlotto, 1) . " on the lottery!", $cashlottery_class->id);
    perform_query("DELETE FROM `cashlottery`");
    perform_query("UPDATE gameevents SET cashlottery = '<li>There were " . prettynum($numlotto) . " lottery tickets bought yesterday.</li><li>The jackpot was " . prettynum($amountlotto, 1) . ".</li><li>The winner was [-_USER_-].</li>', cashlotteryid = ?", [$cashlottery_class->id]);
} else {
    perform_query("UPDATE gameevents SET cashlottery = '<li>There were $numlotto lottery tickets bought yesterday.</li>'");
}

$tickCost = 50;
$db->query("SELECT SUM(tickets) FROM `ptslottery`");
$db->execute();
$numlotto = $db->fetch_single();
$amountlotto = round($numlotto * $tickCost);

$db->query("SELECT * FROM ptslottery");
$lottery = $db->fetch_row();
$numlotto = count($lottery);
if ($numlotto > 0) {
    $worked = $lottery[array_rand($lottery)];

    $cashlottery_class = new User($worked['userid']);
    $newpoints = $cashlottery_class->points + $amountlotto;
    perform_query("UPDATE grpgusers SET points = ? WHERE id = ?", [$newpoints, $worked['userid']]);
    perform_query("INSERT INTO plottowinners VALUES ('', ?, ?)", [$worked['userid'], $amountlotto]);
    Send_Event($cashlottery_class->id, "Congratulations! You have won " . prettynum($amountlotto) . " points on the lottery!", $cashlottery_class->id);
    perform_query("DELETE FROM ptslottery");
    perform_query("UPDATE gameevents SET ptslottery = '<li>There were " . prettynum($numlotto) . " lottery tickets bought yesterday.</li><li>The jackpot was " . prettynum($amountlotto) . " points.</li><li>The winner was [-_USER_-].</li>', `ptslotteryid` = ?", [$cashlottery_class->id]);
} else {
    perform_query("UPDATE gameevents SET ptslottery = '<li>There were " . $numlotto . " lottery tickets bought yesterday.</li>'");
}

perform_query("UPDATE grpgusers SET boxes_opened = 1, csmuggling = 6, psmuggling = 6, prayer = 1, searchdowntown = 100,spins = 20, todayskills = 0,voted1 = 0, doors = 5, slots_left1 = 100, roulette = 1, luckydip = 1, csmuggling = 6, chase = 1");
perform_query("UPDATE grpgusers SET gndays = gndays - 1 WHERE gndays > 0");
perform_query("UPDATE grpgusers SET blocked = blocked - 1 WHERE blocked > 0");
perform_query("UPDATE grpgusers SET actionpoints = 25 WHERE actionpoints < 25 ");

foreach ($allusers as $line) {
    $newrmupgrade = $line['rmdupgrade'] - 1;
    if ($line['rmupgrade'] >= 1)
        perform_query("UPDATE grpgusers SET rmupgrade = ? WHERE id = ?", [$newrmupgrade, $line['id']]);
}

$db->startTrans();
$db->query("UPDATE rentedproperties SET days = days - 1");
$db->execute();
$db->query("SELECT * FROM rentedproperties WHERE days = 0");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $db->query("INSERT INTO ownedproperties VALUES ('', ?, ?)");
    $db->execute(array(
        $row['owner'],
        $row['houseid']
    ));
}
$db->query("DELETE FROM rentedproperties WHERE days <= 0");
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
$db->execute();

$db->query("UPDATE user_research_type SET duration_in_days = duration_in_days - 1 WHERE duration_in_days > 0");
$db->execute();

$db->query("UPDATE `user_santas_grotto` SET `todays_gifts_found` = 0");
$db->execute();

$db->query("DELETE FROM `events` ORDER BY `timesent` ASC LIMIT 100000");
$db->execute();

$total = cleanOldDBEntries();
Send_Event(1059, 'Daily DB Deleted ' . number_format($total) . ' Entries');
Send_Event(1034, 'Daily DB Deleted ' . number_format($total) . ' Entries');
?>