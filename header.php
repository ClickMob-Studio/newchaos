<?php
// TODO(Mathais): Remove before releasing to production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);

ob_start();

require_once 'includes/cache.php';
include_once 'includes/functions.php';

start_session_guarded();

$now = time();

header('Content-Type: text/html; charset=utf-8');
function getUserIP()
{
    // Check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
        // Check for IPs passing through proxies
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        // Use the remote address
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


// Function to log the page view
function logPageView()
{
    // Get the user's IP address
    $ip = getUserIP();

    // Get the current page URL
    $pageURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Get the current date and time
    $dateTime = date("Y-m-d H:i:s");

    // Create a log entry
    $logEntry = "IP: $ip | Page: $pageURL | DateTime: $dateTime\n";

    // Specify the log file path
    $logFilePath = 'page_views.log';

    // Write the log entry to the log file
    file_put_contents($logFilePath, $logEntry, FILE_APPEND);
}

// Get the name of the current script and the full request URI to check for specific query parameters
$current_page = basename($_SERVER['PHP_SELF']); // Gets the name of the current script
$current_uri = $_SERVER['REQUEST_URI']; // Gets the full request URI

register_shutdown_function('ob_end_flush');
$starttime = microtime_float();

include_once 'dbcon.php';
include_once 'database/pdo_class.php';
include_once "classes.php";
include_once "codeparser.php";
include_once "pdo.php";

if (empty($ignoreslashes)) {
    foreach ($_POST as $k => $v) {
        $_POST[$k] = addslashes($v);
    }
    foreach ($_GET as $k => $v) {
        $_GET[$k] = addslashes($v);
    }
}

$ip = getUserIP();
if (isIPBanned($ip)) {
    die("You have been banned from this site. If you think this is a mistake, then you are mistaken. Please do not contact us about this, as we will not respond. If you are using a VPN, please disable it and try again.");
}

if (!isset($_SESSION['id'])) {
    include('home.php');
    die();
}

$db->query("SELECT sessionid FROM `sessions` WHERE userid = ?");
$db->execute(array(
    $_SESSION['id']
));
if ($db->num_rows() < 1) {
    session_destroy();
    header('Location:index.php');
}
$g = $db->fetch_row(true);
if ($g['sessionid'] != $_SESSION['token']) {
    session_destroy();
    header('Location:index.php');
}

if (isset($_GET['action']) && $_GET['action'] == "logout") {
    session_destroy();
    header("Location: index.php");
    exit();
}
$uid = $_SESSION['id'];
$user_class = new User($uid);

$_SESSION['username'] = $user_class->username;

// Define a function to check and log request frequency

function logHighFrequencyRequests()
{
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
    $_SESSION['request_log'][$ipAddress] = array_filter($_SESSION['request_log'][$ipAddress], function ($timestamp) use ($currentTime, $timeLimit) {
        return ($currentTime - $timestamp) <= $timeLimit;
    });

    // Add the current request timestamp for the IP
    $_SESSION['request_log'][$ipAddress][] = $currentTime;

    // Check if the number of requests exceeds the limit
    if (count($_SESSION['request_log'][$ipAddress]) > $requestLimit) {
        $uris = array_column($_SESSION['request_log'][$ipAddress], 'uri');
        $uniqueUris = array_unique($uris); // Optional: Filter to unique URIs
        $uriList = implode(', ', $uniqueUris);

        $logEntry = sprintf(
            "[%s] IP %s Userid: " . $user_class->id . " exceeded the limit with %d requests to %s in %d second(s).\n",
            date('Y-m-d H:i:s'),
            $ipAddress,
            count($_SESSION['request_log'][$ipAddress]),
            $uriList,
            $timeLimit
        );
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
    $db->query("UPDATE grpgusers SET macro_token = ? WHERE id = ?");
    $db->execute(array(
        $newMacroToken,
        $user_class->id
    ));
}
$_SESSION['lastpageload'] = $now;
if ($user_class->lastpayment < $now - 86400) {
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

            $db->query("UPDATE grpgusers SET awake = ?, points = points - ? WHERE id = ?");
            $db->execute(array(
                $user_class->directmaxawake,
                $cost,
                $user_class->id
            ));
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
    $db->query("SELECT total FROM grpgusers WHERE gang = ?");
    $db->execute([$user_class->gang]);
    $rows = $db->fetch_row();

    $total = 0;
    foreach ($rows as $row) {
        $total += $row['total'];
    }

    $db->query("UPDATE gangs SET tmstats = ? WHERE id = ?");
    $db->execute([
        $total,
        $user_class->gang
    ]);
}

$db->query("SELECT type, id FROM bans WHERE type IN ('freeze', 'perm') AND id = ?");
$db->execute(array(
    $user_class->id
));
$row = $db->fetch_row(true);
if (!empty($row)) {
    session_destroy();
    die('<meta http-equiv="refresh" content="0;url=home.php">');
}

if (isset($_COOKIE['mu'])) {
    if ($_COOKIE['mu'] != $user_class->id) {
        $db->query("INSERT INTO multi (acc1, acc2, `time`) VALUES (?, ?, ?)");
        $db->execute(
            array(
                $user_class->id,
                $_COOKIE['mu'],
                $now,
            )
        );
    }
}

function getRealIpAddress()
{
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

setcookie("mu", $user_class->id, $now + (10 * 365 * 24 * 60 * 60));

$IP = getRealIpAddress();
if ($uid != 0) {
    set_last_active_ip($user_class->id, $IP);
}

// Hospital count
$db->query("SELECT count(id) FROM grpgusers WHERE hospital > 0");
$db->execute();
$hosp = $db->fetch_single();

// Event count
$ev = $cache->get("eveCount_" . $user_class->id);
if (empty($ev)) {
    $db->query("SELECT count(viewed) FROM events WHERE `to` = ? AND viewed = 1");
    $db->execute(array(
        $user_class->id
    ));
    $ev = $db->fetch_single();
    $cache->setEx("eveCount_" . $user_class->id, 3, $ev);
}

// Jail count
$db->query("SELECT COUNT(id) FROM grpgusers WHERE jail > 0");
$db->execute();
$ja = $db->fetch_single();

function callback($buffer)
{
    global $user_class, $db, $cache;

    $now = time();

    $hosCount = $cache->get("hosCount");
    if (empty($hosCount)) {
        $db->query("SELECT count(id) FROM grpgusers WHERE hospital <> 0");
        $db->execute();
        $hosCount = $db->fetch_single();
        $cache->setEx("hosCount", 15, $hosCount);
    }

    $pHosCount = $cache->get("pHosCount");
    if (empty($pHosCount)) {
        $db->query("SELECT count(id) FROM pets WHERE hospital <> 0");
        $db->execute();
        $pHosCount = $db->fetch_single();
        $cache->setEx("pHosCount", 15, $pHosCount);
    }

    $jailCount = $cache->get("jailCount");
    if (empty($jailCount)) {
        $db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
        $db->execute();
        $jailCount = $db->fetch_single();
        $cache->setEx("jailCount", 1, $jailCount);
    }

    $pJailCount = $cache->get("pJailCount");
    if (empty($pJailCount)) {
        $db->query("SELECT count(id) FROM pets WHERE jail <> 0");
        $db->execute();
        $pJailCount = $db->fetch_single();
        $cache->setEx("pJailCount", 5, $pJailCount);
    }

    $toset = $cache->get("clockin_" . $user_class->id);
    if (empty($toset)) {
        $db->query("SELECT lastClockin, dailyClockins FROM jobinfo WHERE userid = ?");
        $db->execute([$user_class->id]);
        $jinfo = $db->fetch_row(true);
        if (!empty($jinfo)) {
            $toset = ($jinfo['dailyClockins'] < 8 && $jinfo['lastClockin'] < $now - 3600) ? 1 : 0;
            $cache->setEx("clockin_" . $user_class->id, 60, $toset);
        }
    }

    $eveCount = $cache->get("eveCount_" . $user_class->id);
    if (empty($eveCount)) {
        $db->query("SELECT count(viewed) FROM events WHERE `to` = ? AND viewed = 1");
        $db->execute(array(
            $user_class->id
        ));
        $eveCount = $db->fetch_single();
        $cache->setEx("eveCount_" . $user_class->id, 3, $eveCount);
    }

    $hitCount = $cache->get("hitCount");
    if (empty($hitCount)) {
        $db->query("SELECT count(id) FROM hitlist");
        $db->execute();
        $hitCount = $db->fetch_single();
        $cache->setEx("hitCount", 5, $hitCount);
    }

    $votes = $cache->get("votes_" . $user_class->id);
    if (empty($votes)) {
        $db->query("SELECT count(*) FROM votes WHERE userid = ?");
        $db->execute([$user_class->id]);
        $votes = ($db->fetch_single() == 0) ? 'notify' : 'null';
        $cache->setEx("votes_" . $user_class->id, 60, $votes);
    }

    if (!$user_class->admin && !$user_class->gm) {
        $referrals = 0;
        $tickets = 0;
    }

    $hospital = "[" . $hosCount . "]";
    $hospital = ($hosCount > 0) ? "<span style='color:red;'>$hospital</span>" : $hospital;

    $db->query("SELECT count(id) FROM grpgusers WHERE jail > 0");
    $db->execute();
    $jail = $db->fetch_single();

    $petJailDisplay = "[" . $pJailCount . "]";
    $petJailDisplay = $pJailCount > 0 ? "<span style='color:red;'>$petJailDisplay</span>" : $petJailDisplay;
    $phos = "[" . $pHosCount . "]";
    $phos = ($pHosCount > 0) ? "<span style='color:red;'>$phos</span>" : $phos;
    $events = $eveCount;
    $hitlist = $hitCount;
    $emcount = $events;
    $emcount = ($emcount) ? "(" . $emcount . ") " : "";
    $buffer = str_replace("[:USERNAME:]", strip_tags($user_class->username), $buffer);
    $buffer = str_replace("[:EMAIL:]", strip_tags($user_class->email), $buffer);
    $buffer = str_replace("[:AVATAR:]", strip_tags($user_class->avatar), $buffer);
    if (isset($user_class->quote)) {
        $buffer = str_replace("[:QUOTE:]", strip_tags($user_class->quote), $buffer);
    }
    $buffer = str_replace("[:MUSIC:]", $user_class->promusic, $buffer);
    $buffer = str_replace("[:VOLUME:]", $user_class->volume, $buffer);
    $buffer = str_replace("[:GENDER:]", $user_class->gender, $buffer);

    if (isset($user_class->sig)) {
        $buffer = str_replace("[:SIGNATURE:]", strip_tags($user_class->sig), $buffer);
    }

    if (isset($user_class->notepad)) {
        $buffer = str_replace("[:NOTEPAD:]", strip_tags($user_class->notepad), $buffer);
    }

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
    if (isset($tickets) && $tickets > 0) {
        $buffer = str_replace("<!_-tickets-_!>", "<font color='red'><b>" . prettynum($tickets) . "</b></font>", $buffer);
    } else if (isset($tickets)) {
        $buffer = str_replace("<!_-tickets-_!>", prettynum($tickets), $buffer);
    }
    if (isset($referrals) && $referrals > 0) {
        $buffer = str_replace("<!_-referrals-_!>", "<font color='red'><b>" . prettynum($referrals) . "</b></font>", $buffer);
    } else if (isset($referrals)) {
        $buffer = str_replace("<!_-referrals-_!>", prettynum($referrals), $buffer);
    }
    $buffer = str_replace("<!_-cityname-_!>", $user_class->mycityname, $buffer);
    $clockin = $toset ? "<a href='jobs.php?clockin' style='color:red;'>Clockin for Job</a>" : "";
    $buffer = str_replace("<!_-clockin-_!>", $clockin, $buffer);
    $et = ($user_class->admin || $user_class->eo ? "<a href='subet.php'>Send ET Prize</a>" : "");
    $buffer = str_replace("<!_-entertain-_!>", $et, $buffer);
    $buffer = str_replace("<!_-emcount-_!>", $emcount, $buffer);
    return $buffer;
}

ob_start("callback");

$currencies = array(
    'money' => array(
        'icon' => 'fas fa-dollar-sign',
        'value' => '$' . number_format($user_class->money),
    ),
    'bank' => array(
        'icon' => 'fas fa-piggy-bank',
        'value' => '$' . number_format($user_class->bank),
    ),
    'points' => array(
        'icon' => 'far fa-gem',
        'value' => number_format($user_class->points),
    ),
    'credits' => array(
        'icon' => 'fab fa-medium-m',
        'value' => number_format($user_class->credits) . ((1 === $user_class->credits) ? ' gold' : ' gold'),
    ),
);
$stats = array(
    'health' => array(
        'title' => 'Health',
        'current' => $user_class->hp,
        'max' => $user_class->maxhp,
    ),
    'energy' => array(
        'title' => 'Energy',
        'current' => $user_class->energy,
        'max' => $user_class->maxenergy,
    ),
    'brave' => array(
        'title' => 'Nerve',
        'current' => $user_class->nerve,
        'max' => $user_class->maxnerve,
    ),
    'will' => array(
        'title' => 'Awake',
        'current' => $user_class->awake,
        'max' => $user_class->maxawake,
    ),
    'exp' => array(
        'title' => 'Exp.',
        'current' => $user_class->exp,
        'max' => $user_class->maxexp,
    ),
);

$gmailCount = '';
if ($user_class->gangmail > 0) {
    $gmailCount = 'New';
}

$globalchat = '';
if ($user_class->globalchat > 0) {
    $globalchat = 'New';
}

$db->query("SELECT ar.raid_type, ar.summoned_by, g.gang FROM active_raids ar LEFT JOIN grpgusers g ON ar.summoned_by = g.id WHERE g.gang = ? AND ar.completed = 0 AND ar.raid_type = 'Gang'");
$db->execute([$user_class->gang]);
$gang_raid_count = $db->num_rows();

$mailCount = get_pm_count($user_class->id);
$counts = array(
    'event' => $ev,
    'mail' => $mailCount,
    'hospital' => $hosp,
    'jail' => $ja,
    'gangmail' => $gmailCount,
    'updates' => $user_class->game_updates,
    'gchat' => $globalchat,
    'gang_raid_count' => $gang_raid_count,
);

$usersOnline = $cache->get('usersOnline');
if (empty($usersOnline) || !$usersOnline) {
    $db->query("SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
    $db->execute();
    $queryOnline = $db->num_rows();
    $cache->setEx("usersOnline", 60, $queryOnline);
}

$activeRaidsCount = $cache->get("activeRaidsCount");
if (empty($activeRaidsCount) || !$activeRaidsCount) {
    $db->query("SELECT COUNT(*) AS activeRaidsCount FROM active_raids WHERE completed = 0");
    $db->execute();
    $activeRaidsData = $db->fetch_row(true);
    $activeRaidsCount = $activeRaidsData['activeRaidsCount'];
    $cache->setEx("activeRaidsCount", 10, $activeRaidsCount);
}

$db->query("SELECT * FROM numbergame WHERE userid = ?");
$db->execute([$user_class->id]);
$no2 = $db->num_rows();

echo '<script src="js/java.js?12" type="text/javascript"></script>';
?><!doctype html>
<html lang="en">
<meta http-equiv="x-dns-prefetch-control" content="off">

<meta charset="UTF-8">

<head>
    <meta http-equiv="x-dns-prefetch-control" content="off">
    <?php

    if ($user_class->view_preference === '1') { ?>
        <meta name="viewport" content="width=1024">
    <?php } else { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <?php }

    $db->query("SELECT count(id) FROM grpgusers WHERE hospital > 0");
    $db->execute();
    $hosp = $db->fetch_single();
    ?>

    <?php if ($ev > 0) {
        $eve = "(" . $ev . ")";
    } else {
        $eve = "";
    } ?>
    <title><?php echo $eve; ?> ChaosCity</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/ca284bbf02.js" crossorigin="anonymous"></script>
    <link href="newassets/css/style.css?v=1714569ss35a" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="js/java.js?12" type="text/javascript"></script>
    <!doctype html>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/java.js?v=12" type="text/javascript"></script>

    <script src="/assets/js/cc-countdown.js"></script>

    <style>
        a {
            text-decoration: none;
        }

        .floaty {
            color: white !important;
        }

        @media (max-width: 768px) {
            .mainHeader {
                background: #111;
                margin-top: -84px;
                margin-bottom: 10px;
                position: static;
            }
        }
    </style>
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-CRVCJ66JV7"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-CRVCJ66JV7');
</script>

<body>
    <header class="mainHeader">
        <div class="row mx-auto mainHeaderContent d-none d-md-block">
            <?php

            require 'navbar1.php'; ?>
        </div>

    </header>
    <style>
        .countdown-text {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .carousel-item {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .carousel-item>div {
            flex: 0 0 auto;
            width: 50%;
            /* Adjust the width for how many you want to show */
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
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .carousel {
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

        .dropdown-menu {
            background-color: #333;
            color: #fff;
            position: absolute !important;
        }

        .dropdown-item {
            color: #fff;
            font-size: 18px;
        }

        .modal-header,
        .modal-body {
            border-bottom: none;
        }

        .fa-solid,
        .fas {
            font-weight: 900;
        }

        .dragging {
            opacity: 0.7;
            border: 1px solid red;
        }


        /* Ensure it doesn't interfere with the carousel controls if any */
        #edit-button.text-button {
            z-index: 1000;
            /* Higher z-index to make sure it's clickable */
        }
    </style>
    <div class="container clearfix d-block d-md-none">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo pe-3" role="banner">
                <a href="/index.php" class="d-flex align-items-center text-decoration-none">
                    <img src="asset/img/logo1.png" style="width:30px" />
                    <!-- <img src="asset/halloween.png" style="width:30px"/> -->
                    <h1 class="h3 ms-2">ChaosCity</h1>
                </a>

            </div>
            <div class="d-flex justify-content-end align-items-center" style="gap: 4px;">
                <div class="mx-2">
                    <a href="store.php#VIP">
                        <?= ($user_class->rmdays > 0 ? $user_class->rmdays . ' VIP days' : 'Not VIP') ?>
                    </a>
                </div>

                <a href="#" data-bs-toggle="modal" data-bs-target="#timeModal">
                    <i class="fa-solid fa-clock"></i>
                </a>

                <!-- Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-list"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php if ($user_class->cityturns > 0): ?>
                            <li><a class="dropdown-item" href="/maze.php"><i class="fa-solid fa-puzzle-piece"></i> Maze</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($user_class->searchdowntown > 0): ?>
                            <li><a class="dropdown-item" href="/thecity.php"><i class="fa-solid fa-road"></i> Streets</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($no2 < 1): ?>
                            <li><a class="dropdown-item" href="/numbergame.php"><i class="fa-solid fa-dice"></i> Number
                                    Game</a></li>
                        <?php endif; ?>
                        <?php if ($user_class->luckydip > 0): ?>
                            <li><a class="dropdown-item" href="/luckydip.php"><i class="fa-solid fa-sack-dollar"></i> Lucky
                                    Dip</a></li>
                        <?php endif; ?>
                        <?php if ($user_class->doors > 0): ?>
                            <li><a class="dropdown-item" href="/thedoors.php"><i class="fa-solid fa-dungeon"></i> The
                                    Doors</a></li>
                        <?php endif; ?>
                        <?php if ($user_class->psmuggling > 0): ?>
                            <li><a class="dropdown-item" href="/psmuggling.php"><i
                                        class="fa-solid fa-person-through-window"></i> Point Smuggling</a></li>
                        <?php endif; ?>
                        <?php if ($user_class->rtsmuggling > 0): ?>
                            <li><a class="dropdown-item" href="/raidtokensmuggling.php"><i
                                        class="fa-solid fa-person-through-window"></i> Raid Token Smuggling</a></li>
                        <?php endif; ?>

                    </ul>
                </div>

                <!-- Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="/settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
                        </li>
                        <li><a class="dropdown-item" href="/profiles.php?id=<?php echo $user_class->id; ?>"><i
                                    class="fa-solid fa-address-card"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="/online.php"><i class="fa-solid fa-globe"></i> Online</a>
                        </li>
                        <li><a class="dropdown-item" href="index.php?action=logout"><i
                                    class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    <?php
    $db->query("SELECT carousel_order FROM user_preferences WHERE user_id = :user_id");
    $db->bind(':user_id', $user_class->id);
    $orderResult = $db->fetch_row(true);

    $carouselData = isset($orderResult['carousel_order']) ? stripslashes($orderResult['carousel_order']) : '';
    $carouselData = str_replace('"\,', '\"', $carouselData);
    $carousel_order = json_decode($carouselData, true);

    $requiredItems = array(
        "city",
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
        "backalley",
        "store"
    );

    if (empty($carousel_order)) {
        $carousel_order = $requiredItems;
    } else {

        foreach ($requiredItems as $item) {
            if (!in_array($item, $carousel_order)) {
                $carousel_order[] = $item;
            }
        }
    }

    $updatedCarouselData = json_encode($carousel_order);
    $db->query("UPDATE user_preferences SET carousel_order = :carousel_order WHERE user_id = :user_id");
    $db->bind(':carousel_order', $updatedCarouselData);
    $db->bind(':user_id', $user_class->id);
    $db->execute();


    ?>
    <div id="carouselExample" class="carousel slide d-lg-none" data-bs-ride="carousel">
        <?php
        function shorthandNumber($number)
        {
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

        $metrics = ['kills', 'crimes', 'mugs', 'busts', 'backalleys', 'raids'];

        $currenttime = $now;
        $showmission = false;
        $usermission = get_user_mission(($user_class->id));
        if (!empty($usermission)) {
            $miss = get_mission($usermission['mid']);
            if (!empty($miss)) {
                $showmission = true;
                foreach ($metrics as $metric) {
                    $userVal = $usermission[$metric] ?? 0;
                    $missVal = $miss[$metric] ?? 0;

                    $color = ($missVal > $userVal) ? 'red' : 'green';
                    ${'m' . $metric} = "<font color='$color'>" . shorthandNumber(($missVal > $userVal) ? $userVal : $missVal) . "/" . shorthandNumber($missVal) . "</font>";
                }
            }
        }

        $showoperation = false;
        $currentUserOperation = get_current_operation($user_class->id);
        if (!empty($currentUserOperation)) {
            $operation = get_operation($currentUserOperation['operations_id']);
            if (!empty($currentUserOperation) && !empty($operation)) {
                $showoperation = true;
                if (!empty($currentUserOperation) && !empty($operation)) {
                    $showoperation = true;
                    foreach ($metrics as $metric) {
                        $userVal = $currentUserOperation[$metric] ?? 0;
                        $targetVal = $operation[$metric] ?? 0;

                        $color = ($targetVal > $userVal) ? 'red' : 'green';
                        ${'p' . $metric} = "<font color='$color'>" . shorthandNumber(($targetVal > $userVal) ? $userVal : $targetVal) . "/" . shorthandNumber($targetVal) . "</font>";
                    }
                }

            }
        }

        ?>
        <style>
            .daily-jobs .card-header {
                background-color: #ff5722;
                color: white;
                font-size: 1rem !important;
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
                width: 200px;
                /* Adjust as necessary */
            }
        </style>
        <?php if ($showmission || $showoperation): ?>
            <div class="daily-jobs d-md-none d-lg-none">
                <?php if ($showmission): ?>
                    <div class="card">
                        <div class="card-header d-flex" data-bs-toggle="collapse" data-bs-target="#dailyJobsContent"
                            aria-expanded="false" aria-controls="dailyJobsContent">
                            Mission<span class="ms-auto"><i class="fa-solid fa-angles-down"></i></span>
                        </div>
                        <div id="dailyJobsContent" class="collapse">
                            <div class="card-body job-container d-flex">
                                <div class="job-item">Kills: <br /> <?= $mkills; ?></div>
                                <div class="job-item">Crimes: <br /> <?= $mcrimes; ?></div>
                                <div class="job-item">Busts: <br /> <?= $mbusts; ?></div>
                                <div class="job-item">Mugs: <br /> <?= $mmugs; ?></div>
                                <div class="job-item">BA: <br /> <?= $mbackalleys; ?></div>
                                <div class="job-item">Raids: <br /> <?= $mraids; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($showoperation): ?>
                    <div class="card">
                        <div class="card-header d-flex" data-bs-toggle="collapse" data-bs-target="#dailyOpsContent"
                            aria-expanded="false" aria-controls="dailyOpsContent">
                            Operation<span class="ms-auto"><i class="fa-solid fa-angles-down"></i></span>
                        </div>
                        <div id="dailyOpsContent" class="collapse">
                            <div class="card-body job-container d-flex">
                                <div class="job-item">Kills: <br /> <?= $pkills; ?></div>
                                <div class="job-item">Crimes: <br /> <?= $pcrimes; ?></div>
                                <div class="job-item">Busts: <br /> <?= $pbusts; ?></div>
                                <div class="job-item">Mugs: <br /> <?= $pmugs; ?></div>
                                <div class="job-item">BA: <br /> <?= $pbackalleys; ?></div>
                                <div class="job-item">Raids: <br /> <?= $praids; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
        $(document).ready(function () {
            var isEditable = false;  // Flag to track whether sorting should be enabled

            function initializeSortable() {
                $('#sortable-container').sortable({
                    axis: 'x',
                    delay: 20,
                    start: function (event, ui) {
                        ui.item.addClass('dragging');
                    },
                    stop: function (event, ui) {
                        ui.item.removeClass('dragging');
                    },
                    update: function (event, ui) {
                        var newOrder = $(this).sortable('toArray', { attribute: 'data-id' });
                        $.ajax({
                            url: '/ajax_changemenu.php',
                            type: 'POST',
                            data: { order: JSON.stringify(newOrder) },
                            success: function (response) {

                            },
                            error: function () {
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

            $('#edit-button').click(function () {
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
                $(this).text(isEditable ? 'Finish Editing' : 'Edit');
            });
        });


    </script>

    <div class="container d-block d-md-none p-3 dcPanel dcAvatarPanel">
        <!-- This container is visible only on xs screens -->
        <div class="row">
            <!-- Energy Bar -->
            <div class="col-3">
                <p class="text-center"><i class="fa-solid fa-heart-pulse" style="color:#ff6218"></i>
                    <?= $user_class->hppercent; ?>%</p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="background-color: #ff6218 !important; width: <?= $user_class->hppercent; ?>%"
                        aria-valuenow="<?= $user_class->hppercent; ?>" aria-valuemin="0"
                        aria-valuemax="<?= $user_class->hppercent; ?>"></div>
                </div>

            </div>

            <div class="col-3">
                <p class="text-center"><a href='index.php?spend=refenergy'><i class="fa-solid fa-bolt-lightning"
                            style="color:#ff6218"></i>
                        <?= $user_class->energypercent; ?>%</a></p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="background-color: #ff6218 !important; width: <?= $user_class->energypercent; ?>%"
                        aria-valuenow="<?= $user_class->energypercent; ?>" aria-valuemin="0"
                        aria-valuemax="<?= $user_class->energypercent; ?>"></div>
                </div>

            </div>
            <div class="col-3">
                <p class="text-center"><a href='index.php?spend=refnerve'><i class="fa-brands fa-brave"
                            style="color:#ff6218"></i>
                        <?= $user_class->nervepercent; ?>%</p></a>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="background-color: #ff6218 !important; width: <?= $user_class->nervepercent; ?>%"
                        aria-valuenow="<?= $user_class->nervepercent; ?>" aria-valuemin="0"
                        aria-valuemax="<?= $user_class->nervepercent; ?>"></div>
                </div>

            </div>
            <div class="col-3">
                <p class="text-center"><i class="fa-solid fa-bed" style="color:#ff6218"></i>
                    <?= $user_class->awakepercent; ?>%</p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="background-color: #ff6218 !important; width: <?= $user_class->awakepercent; ?>%"
                        aria-valuenow="<?= $user_class->awakepercent; ?>" aria-valuemin="0"
                        aria-valuemax="<?= $user_class->awakepercent; ?>"></div>
                </div>

            </div>
        </div>

        <!-- Additional Information (Money, Points, Merits) -->
        <div class="row mt-3">
            <div class="col-3">
                <!-- Money -->
                <div class="text-center">
                    <span class="badge bg-success mb-money">$<?= shorthandNumber($user_class->money); ?></span>
                    <p>Money</p>
                </div>
            </div>
            <div class="col-3">
                <!-- Points -->
                <div class="text-center">
                    <a href="bank.php?h_deposit=cash" style="text-decoration: none;"><span
                            class="badge bg-info">$<?= shorthandNumber($user_class->bank); ?></span>
                        <p>Bank</p>
                    </a>
                </div>
            </div>
            <div class="col-3">
                <!-- Merits -->
                <div class="text-center">
                    <span class="badge bg-danger mb-points"><?= shorthandNumber($user_class->points); ?></span>
                    <p>Points</p>
                </div>
            </div>
            <div class="col-3">
                <p class="text-center">Level:
                <div class="level"><?= $user_class->level; ?></div>
                </p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="background-color: #ff6218 !important; width: <?= $user_class->exppercent; ?>%"
                        aria-valuenow="<?= $user_class->exppercent; ?>" aria-valuemin="0"
                        aria-valuemax="<?= $user_class->exppercent; ?>"></div>
                </div>

            </div>
        </div>
    </div>

    <div class="row mx-auto my-3 mainContent">
        <div class="d-none d-lg-block col-2 dcLeftNavContainer p-0">
            <?php require 'leftnav.php'; ?>
        </div>

        <div class="col-12 col-lg-10">
            <header class="row">
                <?php
                $eventMessage = getEventsMessage();
                $showEvents = false;
                if (isset($eventMessage) && !empty($eventMessage)) {
                    $showEvents = true;
                }
                ?>
                <?php if ($showEvents): ?>
                    <div style="padding-right: 24px;">
                        <div class="dcPanel col-12 mb-3 p-3 mx-3 d-flex flex-column gap-3 text-center"
                            style="background-color:#ff5d0033;">
                            <?= $eventMessage; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-none d-md-block col-12 col-lg-8 mt-3 mt-lg-0">
                    <div class="dcPanel h-100">
                        <div class="text-center">
                            <div style="display:flex;justify-content:space-between;">
                                <div class="col-6 col-md-4" style="justify-items: center;">
                                    <div class="p-1 dcPanel dcAvatarPanel"
                                        style="width: 150;margin-top: 10px;margin-left: 6px">
                                        <div class="row mb-3 mission">
                                            <h3 class='box_top'>Mission</h3>
                                        </div>

                                        <div class="row heroTop heroTop2">
                                            <div class="col-12 col-lg-7 offset-lg-1 row realMission">
                                                <?php if ($showmission): ?>
                                                    <div class=" missionDiv">
                                                        <p class="missionTo">Kills:</p>
                                                        <p style="font-size: 10px;"><?= $mkills; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">Crimes:</p>
                                                        <p style="font-size: 10px;" class="mission-crime-counter"
                                                            data-value="<?= $usermission['crimes'] ?>"><?= $mcrimes; ?>
                                                        </p>
                                                    </div>
                                                    <div class=" missionDiv">
                                                        <p class="missionTo">Busts:</p>
                                                        <p style="font-size: 10px;"><?= $mbusts; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">Mugs:</p>
                                                        <p style="font-size: 10px;"><?= $mmugs; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">BA:</p>
                                                        <p style="font-size: 10px;"><?= $mbackalleys; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">Raids:</p>
                                                        <p style="font-size: 10px;"><?= $mraids; ?></p>
                                                    </div>
                                                <?php else: ?>
                                                    <a href="missions.php" class="dcSecondaryButton my-3">Start
                                                        Mission</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4" style="justify-items: center;">
                                    <div class="p-1 dcPanel dcAvatarPanel" style="width: 155px;margin-top: 10px">
                                        <div class="row mb-3 mission">
                                            <h3 class='box_top'>Operation</h3>
                                        </div>

                                        <div class="row heroTop heroTop2">
                                            <div class="col-12 col-lg-7 offset-lg-1 row realMission">
                                                <?php if ($showoperation): ?>
                                                    <div class=" missionDiv">
                                                        <p class="missionTo">Kills:</p>
                                                        <p style="font-size: 10px;"><?= $pkills; ?></p>
                                                    </div>
                                                    <div class="missionDiv">

                                                        <p class="missionTo">Crimes:</p>
                                                        <p style="font-size: 10px;"><?= $pcrimes; ?></p>
                                                    </div>
                                                    <div class=" missionDiv">
                                                        <p class="missionTo">Busts:</p>
                                                        <p style="font-size: 10px;"><?= $pbusts; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">Mugs:</p>
                                                        <p style="font-size: 10px;"><?= $pmugs; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">BA:</p>
                                                        <p style="font-size: 10px;"><?= $pbackalleys; ?></p>
                                                    </div>
                                                    <div class="missionDiv">
                                                        <p class="missionTo">Raids:</p>
                                                        <p style="font-size: 10px;"><?= $praids; ?></p>
                                                    </div>
                                                <?php else: ?>

                                                    <a href="user_operations.php" class="dcSecondaryButton my-3">Start
                                                        Operation</a>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dcBannerButtonsContainer2 d-none d-md-block col-4 col-lg-4">
                                    <a href="vote.php" class="dcSecondaryButton my-2 mt-3">Vote for <i
                                            class="far fa-gem"></i></a>

                                    <a href="refer.php" class="dcSecondaryButton my-2">Refer for <i
                                            class="far fa-gem"></i></a>
                                    <a href="store.php" class="dcSecondaryButton my-2">Upgrades <i
                                            class="fas fa-level-up-alt"></i></a>
                                    <a href="redeem_code.php" class="dcSecondaryButton my-2">Redeem <i
                                            class="fa-solid fa-gift"></i></a>
                                </div>
                            </div>

                            <div class="d-md-none">
                                <div style="display:flex">
                                    <a href="vote.php" class="dcSecondaryButton my-3" style="margin: 0 5px">
                                        Vote for <i class="far fa-gem"></i>
                                    </a>
                                    <a href="refer.php" class="dcSecondaryButton my-3" style="margin: 0 5px">
                                        Refer for <i class="far fa-gem"></i>
                                    </a>
                                    <a href="store.php" class="dcSecondaryButton my-3" style="margin: 0 5px">
                                        Upgrades <i class="fas fa-level-up-alt"></i>
                                    </a>
                                    <a href="redeem_code.php" class="dcSecondaryButton my-3" style="margin: 0 5px">
                                        Redeem <i class="fa-solid fa-gift"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="vertical-text-slider floaty dcPanel p-1" style="margin:4px;min-height:40px;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="d-flex align-items-center justify-content-center mb-0">
                                        <div class="flex-grow-1 text-center">
                                            <ul class="list-unstyled d-flex flex-row align-items-center justify-content-left"
                                                style="margin:0!important;">
                                                <?php

                                                $db->query("SELECT * FROM ads WHERE `timestamp` + (`displaymins` * 60) > ? ORDER BY RAND() LIMIT 1");
                                                $db->execute([$now]);
                                                $row = $db->fetch_row(true);
                                                if (empty($row)) {
                                                    $_messages = [
                                                        'Invite your friends to play and receive <strong class="text-warning">50 Gold</strong> for every friend that plays. Hurry and start inviting now!',
                                                        'For every friend you successfully refer, you\'ll earn <strong class="text-warning">50 Gold</strong>. Spread the word and let\'s play together!',
                                                        'Attention all players! Invite your friends to join in on the fun. <strong class="text-warning">50 Gold</strong> reward for every successful referral.'
                                                    ];
                                                    $ref_message = $_messages[array_rand($_messages)];
                                                    ?>
                                                    <?php if (!$user_class->is_ads_disabled): ?>
                                                        <li class="headerSvg">
                                                            <a href="refer.php"><?= $ref_message ?></a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php
                                                } else {
                                                    $row = $db->fetch_row(true);
                                                    $user_ads = new User($row['poster']);
                                                    $user_ads->avatar = $user_ads->avatar ?: "/images/no-avatar.png";
                                                    ?>

                                                    <li class="flex-grow-1">
                                                        <?php if (!$user_class->is_ads_disabled): ?>
                                                            <span><?= $user_ads->formattedname ?>:
                                                                <?php echo $row['message'] ?></span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php } ?> <?php if (!$user_class->is_ads_disabled): ?>
                                                    <li class="headerSvg">
                                                        <a href="/shoutbox.php">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                fill="#ff6218" class="bi bi-megaphone-fill"
                                                                viewBox="0 0 16 16">
                                                                <path
                                                                    d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0zm-1 .724c-2.067.95-4.539 1.481-7 1.656v6.237a25 25 0 0 1 1.088.085c2.053.204 4.038.668 5.912 1.56zm-8 7.841V4.934c-.68.027-1.399.043-2.008.053A2.02 2.02 0 0 0 0 7v2c0 1.106.896 1.996 1.994 2.009l.496.008a64 64 0 0 1 1.51.048m1.39 1.081q.428.032.85.078l.253 1.69a1 1 0 0 1-.983 1.187h-.548a1 1 0 0 1-.916-.599l-1.314-2.48a66 66 0 0 1 1.692.064q.491.026.966.06" />
                                                            </svg>
                                                        </a>

                                                        <a href="#" onClick="reportAd(27); return false;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                fill="#ff6218" class="bi bi-flag-fill" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001" />
                                                            </svg>
                                                        </a>

                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="p-3 dcPanel dcAvatarPanel d-none d-md-block">
                        <div class="row mb-3">
                            <div class="col-5 dcUserName">
                                <span class="dcHeaderUsername">
                                    <?= $user_class->formattedname; ?>
                                </span>
                                <div class="mt-1" style="font-size: 1.2rem">
                                    <a
                                        href="store.php#VIP"><?= ($user_class->rmdays > 0 ? $user_class->rmdays . ' VIP days' : 'Not VIP') ?></a>
                                </div>

                                <div class='time d-none d-lg-block mt-2' style='text-align: left' ;>
                                    <span style="font-size: 12px;"><?php echo date('m/d h:i a', $now); ?></span>
                                </div>
                            </div>
                            <div class="col-7 text-center new_avarta">
                                <div class="col-3 d-flex align-items-center">
                                    <span>Level <div class="level"><?= $user_class->level; ?></div></span>
                                </div>
                                <div class="col-9 d-flex align-items-center d-lg-none align-items-center2 progress dcStatsBars stat-bar"
                                    data-toggle="tooltip" title="<?= $user_class->formattedexp; ?>">
                                    <div class="progress-bar stat-bar" role="progressbar"
                                        style="width:<?= $user_class->exppercent; ?>%"></div>
                                </div>
                                <div class="d-none d-lg-block col-3">
                                    <img style="width: 50px;" src="<?= $user_class->avatar; ?>" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="row heroTop">
                            <div class="col-5 col-lg-12 row mb-0 mb-lg-3 newTimeHolder">
                                <div class="col-8 col-lg-7 g-0 row" style="margin-left:4px;">
                                    <div class="row my-1 g-0">
                                        <div class="col-2 d-flex align-items-center"><i
                                                class="mx-auto fas fa-dollar-sign"></i></div>
                                        <div class="col-10 d-flex align-items-center">$<div class="money">
                                                <?= shorthandNumber($user_class->money); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-1 g-0">

                                        <div class="col-2 d-flex align-items-center"><i
                                                class="mx-auto fas fa-piggy-bank"></i></div>
                                        <div class="col-10 d-flex align-items-center"><a href="bank.php?h_deposit=cash"
                                                style="text-decoration: none;">$<?= shorthandNumber($user_class->bank); ?>
                                        </div>
                                        </a>
                                    </div>
                                    <div class="row my-1 g-0">
                                        <div class="col-2 d-flex align-items-center"><i class="mx-auto far fa-gem"></i>
                                        </div>
                                        <div class="col-10 d-flex align-items-center points">
                                            <?= shorthandNumber($user_class->points); ?>
                                        </div>
                                    </div>
                                    <div class="row my-1 g-0">
                                        <div class="col-2 d-flex align-items-center"><i
                                                class="mx-auto fab fa-medium-m"></i>
                                        </div>
                                        <div class="col-10 d-flex align-items-center credits"><a href="store.php"
                                                style="text-decoration: none;"><?= shorthandNumber($user_class->credits); ?>
                                                credits</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7 col-lg-12 g-0 row dcStatsPanel">
                                <div class="row my-0 my-lg-1 dcStatContainer-health">
                                    <div class="col-3 d-flex align-items-center">Health</div>
                                    <div class="col-9 d-flex align-items-center align-items-center2">
                                        <div class="progress dcStatsBars stat-bar" data-toggle="tooltip"
                                            title="<?= $user_class->formattedhp; ?>">
                                            <div class="progress-bar" role="progressbar stat-bar"
                                                style="width:<?= $user_class->hppercent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-0 my-lg-1 dcStatContainer-energy">
                                    <div class="col-3 d-flex align-items-center"><a href='?spend=refenergy'>Energy</a>
                                    </div>
                                    <div class="col-9 d-flex align-items-center align-items-center2">
                                        <div class="progress dcStatsBars stat-bar" data-toggle="tooltip"
                                            title="<?= $user_class->formattedenergy; ?>">
                                            <div class="progress-bar" role="progressbar stat-bar"
                                                style="width:<?= $user_class->energypercent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-0 my-lg-1 dcStatContainer-brave">
                                    <div class="col-3 d-flex align-items-center"><a href='?spend=refnerve'>Nerve</a>
                                    </div>
                                    <div class="col-9 d-flex align-items-center align-items-center2">
                                        <div class="progress dcStatsBars stat-bar" data-toggle="tooltip"
                                            title="<?= $user_class->formattednerve; ?>">
                                            <div class="progress-bar" role="progressbar stat-bar"
                                                style="width:<?= $user_class->nervepercent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-0 my-lg-1 dcStatContainer-will">
                                    <div class="col-3 d-flex align-items-center">Awake</div>
                                    <div class="col-9 d-flex align-items-center align-items-center2">
                                        <div class="progress dcStatsBars stat-bar" data-toggle="tooltip"
                                            title="<?= $user_class->formattedawake; ?>">
                                            <div class="progress-bar" role="progressbar stat-bar"
                                                style="width:<?= $user_class->awakepercent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-0 my-lg-1 dcStatContainer-exp">
                                    <div class="col-3 d-flex align-items-center">Exp.</div>
                                    <div class="col-9 d-flex align-items-center align-items-center2">
                                        <div class="progress dcStatsBars stat-bar" data-toggle="tooltip"
                                            title="<?= $user_class->formattedexp; ?>">
                                            <div class="progress-bar" role="progressbar stat-bar"
                                                style="width:<?= $user_class->exppercent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </header>
            <?php if (!$user_class->is_ads_disabled): ?>
                <div class="vertical-text-slider d-md-none d-lg-none floaty dcPanel p-3"
                    style="width: 99%;margin-top: 10px;">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="me-3">
                                <a href="/shoutbox.php">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ff6218"
                                        class="bi bi-megaphone-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0zm-1 .724c-2.067.95-4.539 1.481-7 1.656v6.237a25 25 0 0 1 1.088.085c2.053.204 4.038.668 5.912 1.56zm-8 7.841V4.934c-.68.027-1.399.043-2.008.053A2.02 2.02 0 0 0 0 7v2c0 1.106.896 1.996 1.994 2.009l.496.008a64 64 0 0 1 1.51.048m1.39 1.081q.428.032.85.078l.253 1.69a1 1 0 0 1-.983 1.187h-.548a1 1 0 0 1-.916-.599l-1.314-2.48a66 66 0 0 1 1.692.064q.491.026.966.06" />
                                    </svg>
                                </a>
                            </div>
                            <div class="flex-grow-1 text-center">
                                <ul class="list-unstyled d-flex flex-row align-items-center justify-content-center">
                                    <?php
                                    $db->query("SELECT * FROM ads WHERE `timestamp` + (`displaymins` * 60) > ? ORDER BY RAND() LIMIT 1");
                                    $db->execute([$now]);
                                    $row = $db->fetch_row(true);
                                    if (empty($row)) {
                                        $_messages = [
                                            'Invite your friends to play and receive <strong class="text-warning">50 Gold</strong> for every friend that plays. Hurry and start inviting now!',
                                            'For every friend you successfully refer, you\'ll earn <strong class="text-warning">50 Gold</strong>. Spread the word and let\'s play together!',
                                            'Attention all players! Invite your friends to join in on the fun. <strong class="text-warning">50 Gold</strong> reward for every successful referral.'
                                        ];
                                        $ref_message = $_messages[array_rand($_messages)];
                                        ?>
                                        <li class="flex-grow-1">
                                            <a href="refer.php"><?= $ref_message ?></a>
                                        </li>
                                        <?php
                                    } else {
                                        $row = $db->fetch_row(true);
                                        $user_ads = new User($row['poster']);
                                        $user_ads->avatar = $user_ads->avatar ?: "/images/no-avatar.png";
                                        ?>
                                        <li class="flex-grow-1">
                                            <span><?= $user_ads->formattedname ?>: <?php echo $row['message'] ?></span>
                                        </li>
                                        <li>
                                            <a href="#" onClick="reportAd(<?= $row['id'] ?>); return false;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff6218"
                                                    class="bi bi-flag-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001" />
                                                </svg>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row mt-4">
                <main>
                    <div class="dcPanel p-3">

                        <?php

                        $time = time();
                        if ($user_class->nightvision > 0) {
                            echo '<span style="color:red;">Your currently have ' . $user_class->nightvision . ' minutes of Night Vision left.</span><br />';
                        }

                        if ($user_class->fbi > 0) {
                            echo '<a href="jail.php"><span style="color:green;">You are currently being watched over by the FBI for ' . $user_class->fbi . ' Minutes.</span></a><br />';
                        }

                        if ($user_class->fbitime > 0) {
                            echo '<a href="home.php"><span style="color:red;">You are currently in FBI Jail for ' . $user_class->fbitime . ' minutes.</span></a><br />';
                        }

                        echo '<br />';

                        echo '<script>
    var countDownDate = new Date(1673827199000);

    var x = setInterval(function() {

    var now = new Date();
    var distance = countDownDate - now;

    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    $("#countdown").html("Ends In " + (days === 0 ? "" : days + "d ") + (hours === 0 ? "" : hours + "h ") + (minutes === 0 ? "" : minutes + "m ") + seconds + "s");

    if (distance < 0) {
        clearInterval(x);
        $("#countdown").html("");
    }
    }, 1000);
    </script>';

                        echo '<div id="maincontent">';
                        if (time() < 1673827199) {
                            //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Double EXP ACTIVE on all Crimes!</div>';
                            echo '<br><div class="pulsate" style="font-family:Creepster;font-size: 2em;color:1e7b00;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href=newcrimes.php><font colour=purple>Double EXP ACTIVE on all Crimes!</a>
            <span id="countdown">Ends In ' . countdown(1673827199) . '</span></div>';
                        }

                        if (time() < 1661122799) {
                            //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</div>';
                            echo '<div class="pulsate" style="font-family:Creepster;font-size: 1.5em;color:1e7b00;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href=bustcontest.php>Our BUST COMPETITION IS currently active! </a>
            <span id="countdown">Ends In ' . countdown(1662965999) . '</span></div>';
                        }

                        $time = time();
                        $messages = array();

                        // Attack Protection
                        if ($user_class->aprotection > $now) {
                            $rtn = howlongtil($user_class->aprotection);
                            $messages[] = '[ Attack Protection: ' . (($rtn == 'NOW') ? '@None@' : $rtn) . ' ]';
                        }

                        $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
                        $db->execute();
                        $bonus_row = $db->fetch_row(true);
                        if (isset($bonus_row)) {
                            $debug['worked'] = $bonus_row;
                        }
                        if (isset($bonus_row['Time']) && $bonus_row['Time'] > 0) {
                            $_tt = secondsToHumanReadable($bonus_row['Time'] * 60);
                            $messages[] = '[ Server Wide Double EXP: ' . (($_tt == 'NOW') ? '@None@' : $_tt) . ' ]';
                        }

                        $db->query("SELECT * FROM gamebonus WHERE ID = 2 LIMIT 1");
                        $db->execute();
                        $gymbonus_row = $db->fetch_row(true);

                        if (isset($gymbonus_row) && $gymbonus_row['Time'] > 0) {
                            $_tt = secondsToHumanReadable($gymbonus_row['Time'] * 60);
                            $messages[] = '[ Server Wide Double Gym Gains: ' . (($_tt == 'NOW') ? '@None@' : $_tt) . ' ]';
                        }

                        $db->query("SELECT * FROM ganginvites WHERE playerid = ?");
                        $db->execute(array($user_class->id));
                        if ($db->num_rows() > 0) {
                            // Adding gang invites message to the $messages array instead of printing directly
                            $messages[] = "<a href='ganginvites.php'><span style='color:red;'>[ You have gang invites! ]</span></a>";
                        }

                        // Bust Pill
                        if ($user_class->bustpill > 0) {
                            $messages[] = '<li class="event-countdown" data-end="' . (($user_class->bustpill * 60) + $time) . '">[ Police Badge: <span class="countdown-text">' . secondsToTime(60 * $user_class->bustpill) . '</span> ]</li>';
                        }

                        // Out of Jail
                        if ($user_class->outofjail > 0) {
                            $rtn = ($user_class->outofjail);
                            $messages[] = '<li class="event-countdown" data-end="' . (($user_class->outofjail * 60) + $time) . '">[ Jail Card: <span class="countdown-text">' . secondsToTime(time() - (60 * $user_class->outofjail)) . '</span> ]</li>';
                        }

                        // Mug Protection
                        if ($user_class->mprotection > $time) {
                            $rtn = howlongtil($user_class->mprotection);
                            $messages[] = '<li class="event-countdown" data-end="' . $user_class->mprotection . '">[ Mug Protection: <span class="countdown-text">' . secondsToTime($user_class->mprotection - $time) . '</span> ]</li>';
                        }

                        // Double EXP Pill
                        if ($user_class->exppill > $now) {
                            $rtn = howlongtil($user_class->exppill);
                            $messages[] = '<li class="event-countdown" data-end="' . $user_class->exppill . '">[ Double EXP Pill: <span class="countdown-text">' . secondsToTime($user_class->exppill - $time) . '</span> ]</li>';
                        }

                        $tempItemUse = getItemTempUse($user_class->id);
                        // Crime Potion
                        if ($tempItemUse['crime_potion_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['crime_potion_time'] . '">[ Crime Potion: <span class="countdown-text">' . secondsToTime($tempItemUse['crime_potion_time'] - $time) . '</span> ]</li>';
                        }

                        // Crime Booster
                        if ($tempItemUse['crime_booster_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['crime_booster_time'] . '">[ Crime Booster: <span class="countdown-text">' . secondsToTime($tempItemUse['crime_booster_time'] - $time) . '</span> ]</li>';
                        }

                        // Nerve Vial
                        if ($tempItemUse['nerve_vial_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['nerve_vial_time'] . '">[ Nerve Vial: <span class="countdown-text">' . secondsToTime($tempItemUse['nerve_vial_time'] - $time) . '</span> ]</li>';
                        }

                        // Gang Double EXP Time
                        if ($tempItemUse['gang_double_exp_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['gang_double_exp_time'] . '">[ Gang Double EXP: <span class="countdown-text">' . secondsToTime($tempItemUse['gang_double_exp_time'] - $time) . '</span> ]</li>';
                        }

                        // 10x GYM
                        if ($tempItemUse['gym_10_multiplier_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['gym_10_multiplier_time'] . '">[ 10x Gym: <span class="countdown-text">' . secondsToTime($tempItemUse['gym_10_multiplier_time'] - $time) . '</span> ]</li>';
                        }

                        // 15x Crimes
                        if ($tempItemUse['crime_15_multiplier_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['crime_15_multiplier_time'] . '">[ 75x Crimes: <span class="countdown-text">' . secondsToTime($tempItemUse['crime_15_multiplier_time'] - $time) . '</span> ]</li>';
                        }

                        // Super Crime
                        if ($tempItemUse['supercrime_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['supercrime_time'] . '">[ Super Crime: <span class="countdown-text">' . secondsToTime($tempItemUse['supercrime_time'] - $time) . '</span> ]</li>';
                        }

                        // Protein Bar
                        if ($tempItemUse['gym_protein_bar_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['gym_protein_bar_time'] . '">[ Protein Bar: <span class="countdown-text">' . secondsToTime($tempItemUse['gym_protein_bar_time'] - $time) . '</span> ]</li>';
                        }

                        // Super Pills
                        if ($tempItemUse['gym_super_pills_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['gym_super_pills_time'] . '">[ Gym Super Pills: <span class="countdown-text">' . secondsToTime($tempItemUse['gym_super_pills_time'] - $time) . '</span> ]</li>';
                        }

                        // Ghost Vacuum
                        if ($tempItemUse['ghost_vacuum_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['ghost_vacuum_time'] . '">[ Ghost Vacuum: <span class="countdown-text">' . secondsToTime($tempItemUse['ghost_vacuum_time'] - $time) . '</span> ]</li>';
                        }

                        // Trick or Treat Pass
                        if ($tempItemUse['trick_or_treat_pass_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['trick_or_treat_pass_time'] . '">[ Trick or Treat Pass: <span class="countdown-text">' . secondsToTime($tempItemUse['trick_or_treat_pass_time'] - $time) . '</span> ]</li>';
                        }

                        // Double Gym
                        if ($tempItemUse['double_gym_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['double_gym_time'] . '">[ Double Gym: <span class="countdown-text">' . secondsToTime($tempItemUse['double_gym_time'] - $time) . '</span> ]</li>';
                        }

                        // Love Potion
                        if ($tempItemUse['love_potions_time'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['love_potions_time'] . '">[ Love Potion: <span class="countdown-text">' . secondsToTime($tempItemUse['love_potions_time'] - $time) . '</span> ]</li>';
                        }

                        // Easter Bead
                        if ($tempItemUse['easter_bead'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['easter_bead'] . '">[ Easter Bead (Maze): <span class="countdown-text">' . secondsToTime($tempItemUse['easter_bead'] - $time) . '</span> ]</li>';
                        }

                        // Maze Boost
                        if ($tempItemUse['maze_boost'] > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $tempItemUse['maze_boost'] . '">[ Maze Boost: <span class="countdown-text">' . secondsToTime($tempItemUse['maze_boost'] - $time) . '</span> ]</li>';
                        }

                        // Jail
                        if ($user_class->jail > $time) {
                            $messages[] = '<li class="event-countdown" data-end="' . $user_class->jail . '">[ Jail: <span class="countdown-text">' . secondsToTime($user_class->jail - $time) . '</span> ]</li>';
                        }

                        // Additional messages based on your previous code snippets
                        if ($user_class->hospital > 0) {
                            $messages[] = '<li class="event-countdown" data-end="' . ($user_class->hospital + $time) . '">[ Hospital: <span class="countdown-text">' . secondsToTime($user_class->hospital) . '</span> ]</li>';
                        }

                        if ($user_class->jail > 0) {
                            $messages[] = '<li class="event-countdown" data-end="' . ($user_class->jail + $time) . '">[ Jail: <span class="countdown-text">' . secondsToTime($user_class->jail) . '</span> ]</li>';
                        }

                        if ($user_class->nightvision > 0) {
                            $messages[] = '<li class="event-countdown" data-end="' . (($user_class->nightvision * 60) + $time) . '">[ Night Vision: <span class="countdown-text">' . secondsToTime($user_class->nightvision * 60) . '</span> ]</li>';
                        }

                        if ($user_class->fbi > 0) {
                            $messages[] = '<li class="event-countdown" data-end="' . (($user_class->fbi * 60) + $time) . '">[ FBI Watch: <span class="countdown-text">' . secondsToTime($user_class->fbi * 60) . '</span> ]</li>';
                        }

                        if ($user_class->fbitime > 0) {
                            $messages[] = '<li class="event-countdown" data-end="' . (($user_class->fbitime * 60) + $time) . '">[ FBI Jail: <span class="countdown-text">' . secondsToTime($user_class->fbitime * 60) . '</span> ]</li>';
                        }

                        // Raid Pass
                        if ($tempItemUse['raid_pass'] > 0) {
                            $messages[] = '<li class="full-width-message">[ ' . $tempItemUse['raid_pass'] . 'x Raid Pass(es) Active ]</li>';
                        }

                        // Raid Booster
                        if ($tempItemUse['raid_booster'] > 0) {
                            $messages[] = '<li class="full-width-message">[ ' . $tempItemUse['raid_booster'] . 'x Raid Booster(s) Active ]</li>';
                        }

                        if ($user_class->gang > 0) {
                            $db->query("SELECT * FROM gang_territory_zone_battle WHERE attacking_gang_id = " . $user_class->gang . " AND (is_complete IS NULL OR is_complete = 0)");
                            $db->execute();
                            $attackingGangTerritoryBattles = $db->fetch_row();

                            if (count($attackingGangTerritoryBattles) > 0) {
                                $messages[] = "<a href='gang_territories.php'><span style='color:red;'>[ Protection Racket Attack! ]</span></a>";
                            }

                            $db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id = " . $user_class->gang);
                            $db->execute();
                            $ownedGangTerritoryZones = $db->fetch_row();

                            $isDefense = false;
                            foreach ($ownedGangTerritoryZones as $ownedGangTerritoryZone) {
                                if (getActiveGangTerritoryZoneBattle($ownedGangTerritoryZone)) {
                                    $isDefense = true;
                                }
                            }

                            if ($isDefense) {
                                $messages[] = "<a href='gang_territories.php'><span style='color:red;'>[ Protection Racket Defense! ]</span></a>";
                            }
                        }

                        //if ($user_class->claimed == 0 && basename($_SERVER['PHP_SELF']) != 'store.php') {    // The original echo statement for the claim message should be commented out or removed
                        // echo '<div style="font-family:Creepster;font-size: 2.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href="rmstore.php?buy=freebie">...</div>';
                        
                        // Insert the modal code here
                        ?>



                        <?php if (!empty($messages)): ?>
                            <script type="text/javascript">
                                document.addEventListener("DOMContentLoaded", function () {
                                    var messagesHTML = <?php echo json_encode(implode('', $messages)); ?>;
                                    var ul = document.getElementById("messages");
                                    ul.innerHTML = messagesHTML;
                                });
                            </script>
                        <?php else: ?>
                            <script type="text/javascript">
                                document.addEventListener("DOMContentLoaded", function () {
                                    var container = document.getElementById("message-container");
                                    if (container) container.style.display = "none";
                                });
                            </script>
                        <?php endif; ?>

                        <!-- The Modal -->
                        <!-- <div id="myModal" class="modal"> -->
                        <!-- Modal content -->
                        <!-- <div class="modal-content"> -->
                        <!-- <span class="close">&times;</span> -->
                        <!-- <h4><font color=red>A Free Gift</font></h4><br> -->

                        <!-- <p><font color=white>Here is a free gift on us enjoy the competition</font></p> -->
                        <!-- <ul class="gift-list"> -->
                        <!-- <h4>+50 Raid Tokens</h4> -->
                        <!-- <h4>25,000 Points</h4> -->




                        <!-- </ul> -->
                        <!-- <button onclick="window.location.href='store.php?buy=freebie'" class="claim-button">Claim Gift</button> -->
                        <!-- </div> -->

                        <!-- </div> -->
                        <!-- <style> -->


                        <!-- .gradient-background {
    background: linear-gradient(to right, #484848, #303030, #181818);
    color: white; /* Ensures text is readable on dark background */
    padding: 20px;
    text-align: center;
}


    @keyframes pulseGlow {
        0% {
            box-shadow: 0 0 5px red;
        }
        50% {
            box-shadow: 0 0 20px red;
        }
        100% {
            box-shadow: 0 0 5px red;
        }
    }
    .glow-pulse {
        animation: pulseGlow 2s infinite;
        color: red !important;
    }
</style> -->

                        <?php
                        //}
                        ?>
                        <style>
                            .floaty12 {
                                margin: 0 auto;
                                margin-right: 10px;
                                color: #FFF;
                                width: 72%;
                                text-align: center;
                                background-color: #fff;
                                border-radius: 10px;
                                box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
                                padding: 5px 5px 4px;
                                margin-bottom: 10px;
                            }

                            .floaty12 a:link {
                                color: #000;
                            }

                            .full-width-message {
                                flex-basis: 100%;
                                text-align: center;
                            }
                        </style>


                        <div class="dcPanel p-3" style="text-align:center" id="message-container">
                            <ul id="messages"
                                style="list-style-type: none; display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; padding: 0; margin: 0;">
                            </ul>
                        </div>



                        <script type="text/javascript"
                            src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>

                        <script type="text/javascript">
                            $('.slides').slick({
                                vertical: true,
                                autoplay: true,
                                autoplaySpeed: 3000,
                                arrows: false,
                                speed: 300
                            });

                            function reportAd(id) {
                                $.ajax({
                                    type: "POST",
                                    url: "/ajax_reportad.php",
                                    data: {
                                        id: id
                                    }
                                }).done(function (msg) {
                                    alert("Ad report successful");
                                });
                            }

                        </script>

                        <div class="modal fade" id="timeModal" tabindex="-1" aria-labelledby="timeModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="timeModalLabel">Current Time</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo date('m/d h:i a', $now); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        var timeModal = document.getElementById('timeModal');
                        timeModal.addEventListener('show.bs.modal', function () {
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', 'server_time.php', true);
                            xhr.onreadystatechange = function () {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    var serverTime = new Date(parseInt(xhr.responseText) * 1000);
                                    document.getElementById('timeDisplay').textContent = serverTime.toLocaleString();
                                }
                            };
                            xhr.send();
                        });
                    </script>
                    <?php

                    function secondsToHumanReadable($seconds, $requiredParts = null)
                    {
                        $from = new \DateTime('@0');
                        $to = new \DateTime("@$seconds");
                        $interval = $from->diff($to);
                        $str = '';

                        $parts = [
                            'y' => 'year',
                            'm' => 'month',
                            'd' => 'D',
                            'h' => 'H',
                            'i' => 'M',
                            's' => 'second',
                        ];

                        $includedParts = 0;

                        foreach ($parts as $key => $text) {
                            if ($requiredParts && $includedParts >= $requiredParts) {
                                break;
                            }

                            $currentPart = $interval->{$key};

                            if (empty($currentPart)) {
                                continue;
                            }

                            if (!empty($str)) {
                                $str .= ' ';
                            }

                            $str .= sprintf('%d%s', $currentPart, $text);

                            if ($currentPart > 1) {
                                // handle plural
                                //$str .= 's';
                            }

                            $includedParts++;
                        }

                        return $str;
                    }

                    function microtime_float()
                    {
                        $time = microtime();
                        return (float) substr($time, 11) + (float) substr($time, 0, 8);
                    }

                    anticheat();
                    ?>

                    <div class="ajax-message-holder" style="min-height: 60px; display: none;"></div>