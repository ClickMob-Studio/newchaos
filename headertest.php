<?php

session_start();
header('Content-Type: text/html; charset=utf-8');


// Get the name of the current script and the full request URI to check for specific query parameters
$current_page = basename($_SERVER['PHP_SELF']); // Gets the name of the current script
$current_uri = $_SERVER['REQUEST_URI']; // Gets the full request URI
// function security($h){

// }
register_shutdown_function('ob_end_flush');
//ini_set('memcached.sess_prefix', 'memc.sess.ml2.key.1');
$starttime = microtime_float();
include 'dbcon.php';
include 'database/pdo_class.php';
include "classes.php";
include "codeparser.php";
include "pdo.php";
if (empty($ignoreslashes)) {
    if (get_magic_quotes_gpc() == 0) {
        foreach ($_POST as $k => $v) {
            $_POST[$k] = addslashes($v);
        }
        foreach ($_GET as $k => $v) {
            $_GET[$k] = addslashes($v);
        }
    }
}


if (!isset($_SESSION['id'])) {
    include('home.php');
    die();
}
$l = mysql_query("SELECT sessionid FROM `sessions` WHERE userid = ".$_SESSION['id']);
if(mysql_num_rows($l) < 1)
{
    session_destroy();
    header('Location:index.php');
}
$g = mysql_fetch_assoc($l);
if($g['sessionid'] != $_SESSION['token']){
    session_destroy();
    header('Location:index.php');
}

// $db->query("SELECT * FROM sessions WHERE userid = ?");
// $db->execute(array(
//     $_SESSION['id']
// ));
if (isset($_GET['action']) && $_GET['action'] == "logout") {
    session_destroy();
    header("Location: index.php");
    exit();
}
$uid = $_SESSION['id'];
$user_class = new User($uid);

if ($uid == 1) {
    $user_class->admin = 1;
}

// Define a function to check and log request frequency
function logHighFrequencyRequests() {
    global $user_class;
    $ipAddress = $_SERVER['REMOTE_ADDR']; // Get client IP address
    $requestURI = $_SERVER['REQUEST_URI']; // Get the request URI
   
    $currentTime = time();
    $timeLimit = 1; // Time window in seconds
    $requestLimit = 11; // Maximum number of requests allowed in the time window

    // Path to the log file
   

    // Initialize session storage for request timestamps
    if (!isset($_SESSION['request_log'])) {
        $_SESSION['request_log'] = [];
    }

    // Initialize storage for the current IP if not set
    if (!isset($_SESSION['request_log'][$ipAddress])) {
        $_SESSION['request_log'][$ipAddress] = [];
    }

    // Filter out old requests for the IP
    $_SESSION['request_log'][$ipAddress] = array_filter($_SESSION['request_log'][$ipAddress], function($timestamp) use ($currentTime, $timeLimit) {
        return ($currentTime - $timestamp) <= $timeLimit;
    });

    // Add the current request timestamp for the IP
    $_SESSION['request_log'][$ipAddress][] = $currentTime;

    // Check if the number of requests exceeds the limit
    if (count($_SESSION['request_log'][$ipAddress]) > $requestLimit) {
        $uris = array_column($_SESSION['request_log'][$ipAddress], 'uri');
        $uniqueUris = array_unique($uris); // Optional: Filter to unique URIs
        $uriList = implode(', ', $uniqueUris);
        
        $logEntry = sprintf("[%s] IP %s Userid: ". $user_class->id ." exceeded the limit with %d requests to %s in %d second(s).\n", 
                            date('Y-m-d H:i:s'), 
                            $ipAddress, 
                            count($_SESSION['request_log'][$ipAddress]), 
                            $uriList, 
                            $timeLimit);
                             Send_Event(1, $logEntry);
        Send_Event(2, $logEntry);
    }
}


if ($user_class->gang == 0 && $user_class->cur_gangcrime != 0) {
    $db->query("UPDATE grpgusers SET cur_gangcrime = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
}

if (empty($user_class->macro_token)) {
    $newMacroToken = generateMacroToken(10);
    mysql_query("UPDATE grpgusers SET macro_token = '" . $newMacroToken ."' WHERE id = " . $user_class->id);
}

// if (!$m->get('cities')) {
//     $m->set('cities', 'woot', false, 300);
//     $db->query("SELECT * FROM cities");
//     $db->execute();
//     $rows = $db->fetch_row();
//     foreach ($rows as $row) {
//         $m->set('cities.' . $row['id'], false, $row['name']);
//     }
// }

$m->set('lastpageload.' . $user_class->id, false, time());
if ($user_class->lastpayment < time() - 86400) {
    $db->query("UPDATE grpgusers SET points = points + 250, lastpayment = unix_timestamp() WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    Send_event($user_class->id, "Daily Login Bonus: <font color=red><b>250 Points</b></font>");
}
if (isset($_GET['spend'])) {
    if ($_GET['spend'] == "refenergy") {
        manual_refill('e');
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://chaoscity.co.uk/');
    }
    if ($_GET['spend'] == "refawake") {
        $cost = 100 - floor(100 * ($user_class->directawake / $user_class->directmaxawake));
        if ($user_class->awakepercent != 100 && $user_class->points >= $cost) {
            $user_class->points -= $cost;
            $user_class->directawake = $user_class->directmaxawake;
            mysql_query("UPDATE grpgusers SET awake = $user_class->directmaxawake, points = points - $cost WHERE id = $user_class->id");
        }
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://chaoscity.co.uk/');
    }
    if ($_GET['spend'] == "refnerve") {
        manual_refill('n');
        if (isset($_GET['crime'])) {
            header('Location: crime.php');
        } elseif ($_SERVER['HTTP_REFERER']) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: https://chaoscity.co.uk/');
        }
    }
}

$browser = getBrowser();
$browser = serialize($browser);
if ($browser != $user_class->browser) {
    $db->query("UPDATE grpgusers SET browser = ? WHERE id = ?");
    $db->execute(array(
        $browser,
        $user_class->id
    ));
}
if ($user_class->outofjail > 0) {
    $db->query("UPDATE grpgusers SET jail = 0 WHERE id = ?");
    $db->execute(array(
                $user_class->id
    ));
}



if ($user_class->strength + $user_class->defense + $user_class->speed != $user_class->total) {
    $user_class->total = $user_class->strength + $user_class->defense + $user_class->speed;
    $db->query("UPDATE grpgusers SET total = ? WHERE id = ?");
    $db->execute(array(
        $user_class->total,
        $user_class->id
    ));
}
if ($user_class->gang != 0) {
    if (!$m->get('gangtotal.' . $user_class->gang)) {
        $m->set('gangtotal.' . $user_class->gang, 'set', false, 300);
        $db->query("SELECT total FROM grpgusers WHERE gang = ?");
        $db->execute(array(
            $user_class->gang
        ));
        $rows = $db->fetch_row();
        $total = 0;
        foreach ($rows as $row) {
            $total += $row['total'];
        }
        $db->query("UPDATE gangs SET tmstats = ? WHERE id = ?");
        $db->execute(array(
            $total,
            $user_class->gang
        ));
    }
}
//if($user_class->id == 5){
//    exit();
//}
$db->query("SELECT type, id FROM bans WHERE type IN ('freeze', 'perm') AND id = ?");
$db->execute(array(
    $user_class->id
));
row = $db->fetch_row(true);
if (!empty($row)) {
    session_destroy();
    die('<meta http-equiv="refresh" content="0;url=home.php">');
}


$time = date("F d, Y g:i:sa", time());
if (isset($_COOKIE['mu'])) {
    if ($_COOKIE['mu'] != $user_class->id) {
        $db->query("INSERT INTO multi (acc1, acc2, `time`) VALUES (?, ?, ?)");
        $db->execute(
            array(
                $user_class->id,
                $_COOKIE['mu'],
                time(),
            )
        );
    }
}
function getRealIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // IP from shared internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // IP passed from the proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // Standard way to get visitor IP
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
$IP = getRealIpAddress();
setcookie("mu", $user_class->id, time() + (10 * 365 * 24 * 60 * 60));
if ($uid != 0) {
    $db->query("UPDATE grpgusers SET lastactive = unix_timestamp(), ip = ? WHERE id = ?");
    $db->execute(array(
        $IP,
        $user_class->id
    ));
}
$q = mysql_query("SELECT `id` FROM grpgusers WHERE hospital > 0");
$hosp = mysql_num_rows($q);
$e = mysql_query("SELECT viewed FROM events WHERE `to` = $user_class->id AND viewed = 1");
$ev = mysql_num_rows($e);
$q = mysql_query("SELECT `id` FROM grpgusers WHERE jail > 0");
$ja = mysql_num_rows($q);
function callback($buffer)
{
    global $user_class, $db, $m;
    if (!$m->get('hosCount')) {
        $db->query("SELECT count(id) FROM grpgusers WHERE hospital <> 0");
        $db->execute();
        $m->set('hosCount', $db->fetch_single(), false, 15);
    }
       if (!$m->get('pHosCount')) {
        $db->query("SELECT count(id) FROM pets WHERE hospital <> 0");
        $db->execute();
        $m->set('pHosCount', $db->fetch_single(), false, 1);
    }
    $db->query("SELECT count(viewed) FROM pms WHERE `to` = ? AND viewed = 1");
$db->execute(array($user_class->id));
$mailCount = $db->fetch_single();
$m->set('mailCount.' . $user_class->id, $mailCount, false, 3);

// Attempt to retrieve user jail count from cache
$userJailCount = $m->get('v2jailCount');
if (!$userJailCount) {
    $db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
    $db->execute();
    $userJailCount = $db->fetch_single();
    $m->set('v2jailCount', $userJailCount, false, 1);
}

// Attempt to retrieve pet jail count from cache
$petJailCount = $m->get('pJailCount');
if (!$petJailCount) {
    $db->query("SELECT count(id) FROM pets WHERE jail <> 0");
    $db->execute();
    $petJailCount = $db->fetch_single();
    $m->set('pJailCount', $petJailCount, false, 1);
}



    if (!$m->get('clockin.' . $user_class->id)) {
        $db->query("SELECT lastClockin, dailyClockins FROM jobinfo WHERE userid = ?");
        $db->execute(array(
            $user_class->id
        ));
        $jinfo = $db->fetch_row(true);
        $toset = ($jinfo['dailyClockins'] < 8 && $jinfo['lastClockin'] < time() - 3600) ? 1 : 0;
        $m->set('clockin.' . $user_class->id, $toset, false, 60);
    }
    if (!$m->get('eveCount.' . $user_class->id)) {
        $db->query("SELECT count(viewed) FROM events WHERE `to` = ? AND viewed = 1");
        $db->execute(array(
            $user_class->id
        ));
        $m->set('eveCount.' . $user_class->id, $db->fetch_single(), false, 3);
    }
    if (!$m->get('hlCount')) {
        $db->query("SELECT count(id) FROM hitlist");
        $db->execute();
        $m->set('hlCount', $db->fetch_single(), false, 5);
    }
    if (!$votes = $m->get('votes.' . $user_class->id)) {
        $db->query("SELECT count(*) FROM votes WHERE userid = ?");
        $db->execute(array(
            $user_class->id
        ));
        $votes = ($db->fetch_single() == 0) ? 'notify' : 'null';
        $m->set('votes.' . $user_class->id, $votes, false, 5);
    }
    if ($user_class->admin || $user_class->gm) {
        if (!$m->get('refCount')) {
            $db->query("SELECT count(viewed) FROM referrals WHERE viewed = 0");
            $db->execute();
            $m->set('refCount', $db->fetch_single(), false, 5);
        }
        if (!$m->get('tickCount')) {
            $db->query("SELECT count(viewed) FROM tickets WHERE status <> 'CLOSED'");
            $db->execute();
            $m->set('tickCount', $db->fetch_single(), false, 5);
        }
        $referrals = $m->get('refCount');
        $tickets = $m->get('tickCount');
    } else {
        $referrals = 0;
        $tickets = 0;
    }
    $hospital = "[" . $m->get('hosCount') . "]";
    $hospital = ($m->get('hosCount') > 0) ? "<span style='color:red;'>$hospital</span>" : $hospital;
    $j = mysql_query("SELECT `id` FROM grpgusers WHERE jail > 0");
    $jail = mysql_num_rows($j);
    $petJailDisplay = "[" . $petJailCount . "]";
$petJailDisplay = $petJailCount > 0 ? "<span style='color:red;'>$petJailDisplay</span>" : $petJailDisplay;
    $phos = "[" . $m->get('pHosCount') . "]";
    $phos = ($m->get('pHosCount') > 0) ? "<span style='color:red;'>$phos</span>" : $phos;
    //$mail = "[" . $mailount . "]";
    $mail =   $mailCount;
    $events = $m->get('eveCount.' . $user_class->id);
    $hitlist = $m->get('hlCount');
    $emcount = $mail + $events;
    $emcount = ($emcount) ? "(" . $emcount . ") " : "";
    $buffer = str_replace("[:USERNAME:]", strip_tags($user_class->username), $buffer);
    $buffer = str_replace("[:EMAIL:]", strip_tags($user_class->email), $buffer);
    $buffer = str_replace("[:AVATAR:]", strip_tags($user_class->avatar), $buffer);
    $buffer = str_replace("[:QUOTE:]", strip_tags($user_class->quote), $buffer);
    $buffer = str_replace("[:MUSIC:]", $user_class->promusic, $buffer);
    $buffer = str_replace("[:VOLUME:]", $user_class->volume, $buffer);
    $buffer = str_replace("[:GENDER:]", $user_class->gender, $buffer);
    $buffer = str_replace("[:SIGNATURE:]", strip_tags($user_class->sig), $buffer);
    $buffer = str_replace("[:NOTEPAD:]", strip_tags($user_class->notepad), $buffer);
    $buffer = str_replace("<!_-money-_!>", prettynum($user_class->money), $buffer);
    $buffer = str_replace("<!_-bank-_!>", prettynum($user_class->bank), $buffer);
    $buffer = str_replace("<!_-banked-_!>", number_format_short($user_class->bank), $buffer);
    $buffer = str_replace("<!_-points-_!>", prettynum(floor($user_class->points)), $buffer);
    $buffer = str_replace("<!_-pbanked-_!>", prettynum(floor($user_class->pbank)), $buffer);
    $buffer = str_replace("<!_-money2-_!>", prettynum(floor($user_class->money)), $buffer);
    $buffer = str_replace("<!_-formhp-_!>", prettynum($user_class->formattedhp), $buffer);
    $buffer = str_replace("<!_-hpperc-_!>", $user_class->hppercent, $buffer);
    $buffer = str_replace("<!_-formenergy-_!>", prettynum($user_class->formattedenergy), $buffer);
    $buffer = str_replace("<!_-energyperc-_!>", $user_class->energypercent, $buffer);
    $buffer = str_replace("<!_-formawake-_!>", prettynum($user_class->formattedawake2forbar), $buffer);
    $buffer = str_replace("<!_-awakeperc-_!>", $user_class->awakepercent, $buffer);
    $buffer = str_replace("<!_-formnerve-_!>", prettynum($user_class->formattednerve), $buffer);
    $buffer = str_replace("<!_-nerveperc-_!>", $user_class->nervepercent, $buffer);
    $buffer = str_replace("<!_-formexp-_!>", prettynum($user_class->formattedexp), $buffer);
    $buffer = str_replace("<!_-expperc-_!>", $user_class->exppercent, $buffer);
    $buffer = str_replace("<!_-points-_!>", prettynum($user_class->points), $buffer);
    $buffer = str_replace("<!_-credits-_!>", prettynum($user_class->credits), $buffer);
    $buffer = str_replace("<!_-level-_!>", $user_class->level, $buffer);
    $buffer = str_replace("<!_-mprotection-_!>", $user_class->mprotection, $buffer);
    $buffer = str_replace("[:FORMAT.NAME:]", $user_class->formattedname, $buffer);
    $buffer = str_replace("<!_-hospital-_!>", prettynum($hospital), $buffer);
    $buffer = str_replace("<!_-jail-_!>", $jail, $buffer);
    $buffer = str_replace("<!_-phos-_!>", $phos, $buffer);
    $buffer = str_replace("<!_-thecardvalue-_!>", $user_class->cardvalue, $buffer);
    $buffer = str_replace("<!_-thecardtype-_!>", $user_class->cardtype, $buffer);
    $buffer = str_replace("<!_-forumnoti-_!>", ($user_class->forumnoti) ? "<span style='color:#00ff00;font-weight:bold;'>$user_class->forumnoti</span>" : "0", $buffer);
    $buffer = str_replace("<!_-genBars-_!>", genBars(), $buffer);
    $hossyjail = ($user_class->hospital) ? "<img width='20px' height='20px' src='images/hossy.png' /> " . ($user_class->hospital / 60) . " Mins" : "";
    $hossyjail .= ($user_class->jail) ? "<img width='20px' height='20px' src='images/jailtop.png' /> " . ($user_class->jail / 60) . " Mins" : "";
    $buffer = str_replace("<!_-hossyjail-_!>", $hossyjail, $buffer);
    $buffer = str_replace("<!_-votes-_!>", $votes, $buffer);
    if ($hitlist > 0) {
        $buffer = str_replace("<!_-hitlist-_!>", "<span class='notify'>[" . prettynum($hitlist) . "]</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-hitlist-_!>", "[" . prettynum($hitlist) . "]", $buffer);
    }
    if ($mail > 0) {
        $buffer = str_replace("<!_-mail-_!>", "<span class='notify'>" . prettynum($mail) . "</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-mail-_!>", prettynum($mail), $buffer);
    }


    if ($user_class->forumnoti > 0) {
        $buffer = str_replace("<!_-forum-_!>", "<span class='notify'>New</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-forum-_!>", "0", $buffer);
    }

    if ($user_class->gmail > 0) {
        $buffer = str_replace("<!_-gmail-_!>", "<span class='notify'>New</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-gmail-_!>", "0", $buffer);
    }
    if ($user_class->globalchat > 0) {
        $buffer = str_replace("<!_-gchat-_!>", "<span class='notify'>New</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-gchat-_!>", "0", $buffer);
    }
    if ($user_class->news > 0) {
        $buffer = str_replace("<!_-news-_!>", "<span class='notify'>New</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-news-_!>", "0", $buffer);
    }
    if ($user_class->game_updates > 0) {
        $buffer = str_replace("<!_-gupdates-_!>", "<span class='notify'>$user_class->game_updates</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-gupdates-_!>", "$user_class->game_updates", $buffer);
    }
    if ($user_class->jail > 0) {
        $buffer = str_replace("<!_-
-_!>", "<span class='notify jailed'>" . prettynum($jail) . "</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-jail-_!>", prettynum($jail), $buffer);
    }
    if ($events > 0) {
        $buffer = str_replace("<!_-events-_!>", "<span class='notify'>" . prettynum($events) . "</span>", $buffer);
    } else {
        $buffer = str_replace("<!_-events-_!>", prettynum($events), $buffer);
    }
    if ($tickets > 0) {
        $buffer = str_replace("<!_-tickets-_!>", "<font color='red'><b>" . prettynum($tickets) . "</b></font>", $buffer);
    } else {
        $buffer = str_replace("<!_-tickets-_!>", prettynum($tickets), $buffer);
    }
    if ($referrals > 0) {
        $buffer = str_replace("<!_-referrals-_!>", "<font color='red'><b>" . prettynum($referrals) . "</b></font>", $buffer);
    } else {
        $buffer = str_replace("<!_-referrals-_!>", prettynum($referrals), $buffer);
    }
    $buffer = str_replace("<!_-cityname-_!>", $user_class->mycityname, $buffer);
    $clockin = ($m->get('clockin.' . $user_class->id)) ? "<a href='jobs.php?clockin' style='color:red;'>Clockin for Job</a>" : "";
    $buffer = str_replace("<!_-clockin-_!>", $clockin, $buffer);
    $et = ($user_class->admin || $user_class->eo ? "<a href='subet.php'>Send ET Prize</a>" : "");
    $buffer = str_replace("<!_-entertain-_!>", $et, $buffer);
    $buffer = str_replace("<!_-emcount-_!>", $emcount, $buffer);
    return $buffer;



}
ob_start("callback");

$currencies = array(
	'money'    => array(
		'icon'  => 'fas fa-dollar-sign',
		'value' => '$' . number_format( $user_class->money ),
	),
	'bank'     => array(
		'icon'  => 'fas fa-piggy-bank',
		'value' => '$' . number_format( $user_class->bank),
	),
	'points' => array(
		'icon'  => 'far fa-gem',
		'value' => number_format( $user_class->points ),
	),
	'credits'   => array(
		'icon'  => 'fab fa-medium-m',
		'value' => number_format( $user_class->credits ) . ( ( 1 === $user_class->credits ) ? ' gold' : ' gold' ),
	),
);
$stats = array(
	'health' => array(
		'title'   => 'Health',
		'current' => $user_class->hp,
		'max'     => $user_class->maxhp,
	),
	'energy' => array(
		'title'   => 'Energy',
		'current' => $user_class->energy,
		'max'     => $user_class->maxenergy,
	),
	'brave'  => array(
		'title'   => 'Nerve',
		'current' => $user_class->nerve,
		'max'     => $user_class->maxnerve,
	),
	'will'   => array(
		'title'   => 'Awake',
		'current' => $user_class->awake,
		'max'     => $user_class->maxawake,
	),
	'exp'    => array(
		'title'   => 'Exp.',
		'current' => $user_class->exp,
		'max'     => $user_class->maxexp,
	),
);

if($user_class->gangmail > 0){
    $gmailCount = 'New';
}else{
    $gmailCount = '';
}
if($user_class->globalchat > 0){
    $globalchat = 'New';
}else{
    $globalchat = '';
}
$gang_raid_query = "
SELECT 
    ar.raid_type, ar.summoned_by, g.gang 
FROM 
    active_raids ar                        
    LEFT JOIN grpgusers g ON ar.summoned_by = g.id 
WHERE 
    g.gang = " . $user_class->gang . " AND
    ar.completed = 0 AND
    ar.raid_type = 'Gang'
";
$gang_raid_count = mysql_num_rows(mysql_query($gang_raid_query));
$counts = array(
	'event'         => $ev,
	'mail'          => '<!_-mail-_!>',
	'hospital'      => $hosp,
	'jail'          => $ja,
    'gangmail'      => $gmailCount,
    'updates'       => $user_class->game_updates,
    'gchat' => $globalchat,
    'gang_raid_count' => $gang_raid_count,
);
$queryOnline = mysql_query("SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");

$usersOnline = mysql_num_rows($queryOnline);

$activeRaidsQuery = "SELECT COUNT(*) AS activeRaidsCount FROM active_raids WHERE completed = 0"; // Replace 'end_time' with the actual column name that represents when the raid ends
$activeRaidsResult = mysql_query($activeRaidsQuery);
$activeRaidsData = mysql_fetch_assoc($activeRaidsResult);
$activeRaidsCount = $activeRaidsData['activeRaidsCount'];

$nogame2 = mysql_query("SELECT * FROM numbergame WHERE userid=$user_class->id");
$no2 = mysql_num_rows($nogame2);

echo '<script src="js/java.js?12" type="text/javascript"></script>';
?><!doctype html>
<html lang="en">
<meta charset="UTF-8">
<head>

<?php 

if ($user_class->view_preference === '1') { ?>
            <meta name="viewport" content="width=1024">
        <?php } else { ?>
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
        <?php } 
        $q = mysql_query("SELECT `id` FROM grpgusers WHERE hospital > 0");
        $hosp = mysql_num_rows($q);
        ?>
	<title>ChaosCity</title>

    <script src="js/java.js?12" type="text/javascript"></script><!doctype html>
<html lang="en">
<head>
    <title>ChaosCity</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/ca284bbf02.js" crossorigin="anonymous"></script>
    <link href="newassets/css/style.css?v=1714569ss35a" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/java.js?v=12" type="text/javascript"></script>


	
	
<style>
	a{
		text-decoration: none;
	}
	.floaty{
		color:white !important;
	}
    @media (max-width: 768px) {
        .mainHeader {
    background: #111;
    margin-top: -84px;
    margin-bottom:10px;
    position: static;
        }
    }
  
	</style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate a unique tab identifier if it doesn't exist
            if (!sessionStorage.tabId) {
                sessionStorage.tabId = Math.random().toString(36).substr(2, 9);
            }

            // Send the tab ID to the server
            fetch('tab_tracker.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'tabId=' + sessionStorage.tabId
            }).then(response => response.text())
              .then(data => console.log(data));
        });
    </script>
</head>
<body>
	<header class="mainHeader">
		<div class="row mx-auto mainHeaderContent d-none d-md-block">
		<?php 
        
        require 'navbar1.php'; ?>
		</div>

	</header>
<style>
.carousel-item {
  display: flex;
  flex-wrap: nowrap;
  overflow-x: auto;
}

.carousel-item > div {
  flex: 0 0 auto;
  width: 50%; /* Adjust the width for how many you want to show */
}

.carousel-item a {
  display: block;
  text-align: center;
}

/* Optional: hide scrollbars */
.carousel-item::-webkit-scrollbar {
  display: none;
}

.carousel-item {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
.carousel{
    background-color: #000;
}

.tran-middle {
    transform: translate(-4%, -66%) !important;
}
/* Custom styling for the modal */
.modal-content {
            background-color: #333; 
            color: #fff;  
        }
        .dropdown-menu{
            background-color: #333; 
            color: #fff;  
            position: absolute !important;
        }
        .dropdown-item{
            color: #fff;
            font-size:18px;
        }
        .modal-header, .modal-body {
            border-bottom: none;  
        }
        .fa-solid, .fas {
         font-weight: 900;
        }
        .dragging {
            opacity: 0.7;
            border: 1px solid red;
        }


/* Ensure it doesn't interfere with the carousel controls if any */
#edit-button.text-button {
  z-index: 1000; /* Higher z-index to make sure it's clickable */
}
        
</style>
<div class="container clearfix d-block d-md-none">
  <div class="d-flex justify-content-between align-items-center">
    <div class="logo pe-3" role="banner">
      <a href="/" class="d-flex align-items-center text-decoration-none">
        <img src="asset/img/logo1.png" style="width:30px"/>
        <h1 class="h3 ms-2">ChaosCity</h1>
      </a>
   
    </div>
    <div class="d-flex justify-content-end align-items-center">
        <a href="#" data-bs-toggle="modal" data-bs-target="#timeModal">
            <i class="fa-solid fa-clock"></i>
        </a>

        <!-- Dropdown -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-list"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?php if ($user_class->cityturns > 0): ?>
                    <li><a class="dropdown-item" href="/maze.php"><i class="fa-solid fa-puzzle-piece"></i> Maze</a></li>
                <?php endif; ?>
                <?php if ($user_class->searchdowntown > 0): ?>
                    <li><a class="dropdown-item" href="/thecity.php"><i class="fa-solid fa-road"></i> Streets</a></li>
                <?php endif; ?>
                <?php if ($no2 < 1): ?>
                    <li><a class="dropdown-item" href="/numbergame.php"><i class="fa-solid fa-dice"></i> Number Game</a></li>
                <?php endif; ?>
                <?php if ($user_class->luckydip > 0): ?>
                    <li><a class="dropdown-item" href="/luckydip.php"><i class="fa-solid fa-sack-dollar"></i> Lucky Dip</a></li>
                <?php endif; ?>
                <?php if ($user_class->doors > 0): ?>
                    <li><a class="dropdown-item" href="/thedoors.php"><i class="fa-solid fa-dungeon"></i> The Doors</a></li>
                <?php endif; ?>
                <?php if ($user_class->psmuggling > 0): ?>
                    <li><a class="dropdown-item" href="/psmuggling.php"><i class="fa-solid fa-person-through-window"></i> Point Smuggling</a></li>
                <?php endif; ?>
                <?php if ($user_class->rtsmuggling > 0): ?>
                    <li><a class="dropdown-item" href="/raidtokensmuggling.php.php"><i class="fa-solid fa-person-through-window"></i> Raid Token Smuggling</a></li>
                <?php endif; ?>

            </ul>
        </div>

        <!-- Dropdown -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="/settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                <li><a class="dropdown-item" href="/profiles.php?id=<?php echo $user_class->id;?>"><i class="fa-solid fa-address-card"></i> Profile</a></li>
                <li><a class="dropdown-item" href="/online.php"><i class="fa-solid fa-globe"></i> Online</a></li>
                <li><a class="dropdown-item" href="index.php?action=logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>
    </div>

  </div>
</div>
<?php
$db->query("SELECT carousel_order FROM user_preferences WHERE user_id = :user_id");
$db->bind(':user_id', $user_class->id);
$orderResult = $db->fetch_row(true);
$carouselData = $orderResult['carousel_order'];
    $carouselData = stripslashes($orderResult['carousel_order']);
$carouselData = str_replace('"\,', '\"', $carouselData);

$carousel_order = json_decode($carouselData, true);

if (empty($carousel_order)) {
    $carousel_order = array("city",
    "updates",
    "gang",
    "gmail",
    "pms",
    "chat",
    "events",
    "crimes",
    "gym",
    "jail",
    "hospital",
    "inventory",
    "missions",
    "raids",
    "search",
    "maze",
    "backalley",)
    ;
}

?>
<div id="carouselExample" class="carousel slide d-lg-none" data-bs-ride="carousel">
<?php
      $check = mysql_query("SELECT * FROM missions WHERE userid=$user_class->id AND completed='no'");
      function shorthandNumber($number) {
        if ($number >= 1000000000) { // Check if the number is at least a billion
            $shorthand = round($number / 1000000000, 2) . 'B'; // Convert to billions, round to 2 decimal places, and append 'B'
            return $shorthand;
        } elseif ($number >= 1000000) { // Check if the number is at least a million
            $shorthand = round($number / 1000000, 2) . 'M'; // Convert to millions, round to 2 decimal places, and append 'M'
            return $shorthand;
        } elseif ($number >= 1000) { // Check if the number is at least a thousand
            $shorthand = round($number / 1000, 1) . 'k'; // Convert to thousands, round to 1 decimal place, and append 'k'
            return $shorthand;
        }
        return number_format($number); // Return the original number if it's less than 1000
    }
    
      if (mysql_num_rows($check)) {
            $show = true;
          $usermission = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid=$user_class->id AND completed='no'"));
          $miss = mysql_fetch_array(mysql_query("SELECT * FROM mission WHERE id={$usermission['mid']}"));
          $kills = ($miss['kills'] > $usermission['kills']) ? "<font color='red'>" . shorthandNumber($usermission['kills']) . "/".shorthandNumber($miss['kills'])."</font>" : "<font color='green'>" . shorthandNumber($miss['kills']) . "/".shorthandNumber($miss['kills'])."</font>";
          $crimes = ($miss['crimes'] > $usermission['crimes']) ? "<font color='red'>" . shorthandNumber($usermission['crimes']) . "/".shorthandNumber($miss['crimes'])."</font>" : "<font color='green'>" . shorthandNumber($miss['crimes']) . "/".shorthandNumber($miss['crimes'])."</font>";
          $mugs = ($miss['mugs'] > $usermission['mugs']) ? "<font color='red'>" . shorthandNumber($usermission['mugs']) . "/".shorthandNumber($miss['mugs'])."</font>" : "<font color='green'>" . shorthandNumber($miss['mugs']) . "/".shorthandNumber($miss['mugs'])."</font>";
          $busts = ($miss['busts'] > $usermission['busts']) ? "<font color='red'>" . shorthandNumber($usermission['busts']) . "/".shorthandNumber($miss['busts'])."</font>" : "<font color='green'>" . shorthandNumber($miss['busts']) . "/".shorthandNumber($miss['busts'])."</font>";
          $backalleys = ($miss['backalleys'] > $usermission['backalleys']) ? "<font color='red'>" . shorthandNumber($usermission['backalleys']) . "/".shorthandNumber($miss['backalleys'])."</font>" : "<font color='green'>" . shorthandNumber($miss['backalleys']) . "/" .shorthandNumber($miss['backalleys'])."</font>";
          $currenttime = time();
          $timeleft = ($miss['time'] + $usermission['timestamp']) - $currenttime;
      }else{
        $show = false;
      }
        ?>
<style>
       
        .daily-jobs .card-header {
            background-color: #ff5722;
            color: white;
        }
        .job-item {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #ddd;
        }
        .job-item:last-child {
            border-right: none;
        }
        .job-container {
            overflow-x: auto;
            white-space: nowrap;
        }
        .job-item {
            display: inline-block;
            white-space: normal;
            width: 200px; /* Adjust as necessary */
        }
        
    </style>
    <?php if($show == true): ?>
        <div class="daily-jobs d-md-none d-lg-none">
    <div class="card">
        <div class="card-header d-flex" data-bs-toggle="collapse" data-bs-target="#dailyJobsContent" aria-expanded="false" aria-controls="dailyJobsContent">
            Mission<span class="ms-auto"><i class="fa-solid fa-angles-down"></i></span>
        </div>
        <div id="dailyJobsContent" class="collapse">
            <div class="card-body job-container d-flex">
                <div class="job-item">Kills: <?= $kills; ?></div>
                <div class="job-item">Crimes: <?= $crimes; ?></div>
                <div class="job-item">Busts: <?= $busts; ?></div>
                <div class="job-item">Mugs: <?= $mugs; ?></div>
                <div class="job-item">BA: <?= $backalleys; ?></div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
  <div class="carousel-inner pl-1 pt-2">
    <div class="carousel-item active">
      <div class="d-flex" id="sortable-container">
        <?php foreach ($carousel_order as $item_id) {
            include 'menu_items/' . $item_id . '.php';
        } ?>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    var isEditable = false;  // Flag to track whether sorting should be enabled

    function initializeSortable() {
        $('#sortable-container').sortable({
            axis: 'x',
            delay: 20,
            start: function(event, ui) {
                ui.item.addClass('dragging');
            },
            stop: function(event, ui) {
                ui.item.removeClass('dragging');
            },
            update: function(event, ui) {
                var newOrder = $(this).sortable('toArray', { attribute: 'data-id' });
                $.ajax({
                    url: '/ajax_changemenu.php',
                    type: 'POST',
                    data: { order: JSON.stringify(newOrder) },
                    success: function(response) {
                        
                    },
                    error: function() {
                        alert('Error saving order.');
                    }
                });
            }
        });
    }

    function destroySortable() {
        if ($('#sortable-container').hasClass('ui-sortable')) {
            $('#sortable-container').sortable('destroy');
        }
    }

    $('#edit-button').click(function() {
        window.scrollTo({
        top: 0,
        behavior: 'smooth'  // This makes the scroll smoothly glide to the top rather than a sudden jump
        });
        isEditable = !isEditable;
        if (isEditable) {
            initializeSortable();  // Initialize sortable if entering edit mode
        } else {
            destroySortable();  // Destroy sortable if exiting edit mode
        }
    });
});

</script>
<style>
  /* For the Time Modal */
  #timeModal .modal-content {
    background-color: #333; 
    color: #fff;  
  }
  #timeModal .modal-header, 
  #timeModal .modal-body {
    border-bottom: none;  
  }
  #timeModal .fa-clock {
    font-size: 2rem;
  }
</style>

<!-- Time Modal -->
<div class="modal fade" id="timeModal" tabindex="-1" aria-labelledby="timeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="timeModalLabel">Current Server Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <i class="fa-solid fa-clock"></i>
        <span id="serverTime"></span>
      </div>
    </div>
  </div>
</div>

<!-- Add script to update the server time -->
<script>
  function updateServerTime() {
    var now = new Date();
    var timeString = now.toLocaleTimeString();
    document.getElementById('serverTime').textContent = timeString;
  }
  setInterval(updateServerTime, 1000); // Update every second
  updateServerTime(); // Initial call to set the time immediately
</script>
<style>
.carousel-inner {
    border: 2px solid #fff; 
    padding: 10px; 
}
</style>
	<div class="container">
		<div class="row mx-auto">
			<div class="col-12 col-md-9">
				<?php 
if ($current_page == 'profiles.php') {
    include('profile-content.php'); 
}
else {
    include('main-content.php');
} 
?>
			</div>
			<div class="col-12 col-md-3">
				<?php include('sidebar.php'); ?>
			</div>
		</div>
	</div>
	<footer>
		<?php require 'footer.php'; ?>
	</footer>
	<script src="newassets/js/jquery.min.js"></script>
	<script src="newassets/js/bootstrap.bundle.min.js"></script>
	<script src="newassets/js/main.js"></script>
	<script src="newassets/js/chart.min.js"></script>
	<script src="newassets/js/utils.js"></script>
	<script src="newassets/js/anime.min.js"></script>
	<script src="newassets/js/d3.min.js"></script>
	<script src="newassets/js/topojson.min.js"></script>
	<script src="newassets/js/datamaps.world.min.js"></script>
	<script src="newassets/js/datamaps.usa.min.js"></script>
</body>
</html>
<?php 
ob_end_flush();?>
