<?php
ob_start();
session_start();

$redis = new Redis();
$redis->connect("127.0.1", 6379);

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

include 'dbcon.php';
include 'database/pdo_class.php';
include "classes.php";
include "codeparser.php";
include "pdo.php";


if (!isset($_SESSION['id'])) {
    include('home.php');
    die();
}
$l = mysql_query("SELECT sessionid FROM `sessions` WHERE userid = " . $_SESSION['id']);
if (mysql_num_rows($l) < 1) {
    session_destroy();
    header('Location:index.php');
}

$g = mysql_fetch_assoc($l);
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
if ($user_class->id == 18) {
    logPageView();
}

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
    mysql_query("UPDATE grpgusers SET macro_token = '" . $newMacroToken . "' WHERE id = " . $user_class->id);
}

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
//  if($user_class->id == 18){
//     session_destroy();
//      session_unset();
//      header("Location: index.php");
//  }
$db->query("SELECT type, id FROM bans WHERE type IN ('freeze', 'perm') AND id = ?");
$db->execute(array(
    $user_class->id
));
$row = $db->fetch_row(true);
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

$IP = getRealIpAddress();
setcookie("mu", $user_class->id, time() + (10 * 365 * 24 * 60 * 60));
if ($uid != 0) {
    set_last_active_ip($user_class->id, $IP);
}

$q = mysql_query("SELECT `id` FROM grpgusers WHERE hospital > 0");
$hosp = mysql_num_rows($q);
$e = mysql_query("SELECT viewed FROM events WHERE `to` = $user_class->id AND viewed = 1");
$ev = mysql_num_rows($e);
$q = mysql_query("SELECT `id` FROM grpgusers WHERE jail > 0");
$ja = mysql_num_rows($q);


function callback($buffer)
{
    global $user_class, $db;

    $db->query("SELECT count(id) FROM grpgusers WHERE hospital <> 0");
    $db->execute();
    $hosCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM pets WHERE hospital <> 0");
    $db->execute();
    $pHosCount = $db->fetch_single();

    $db->query("SELECT count(viewed) FROM pms WHERE `to` = ? AND viewed = 1");
    $db->execute(array($user_class->id));
    $mailCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
    $db->execute();
    $userJailCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM pets WHERE jail <> 0");
    $db->execute();
    $petJailCount = $db->fetch_single();

    $db->query("SELECT lastClockin, dailyClockins FROM jobinfo WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
    $jinfo = $db->fetch_row(true);
    $toset = ($jinfo['dailyClockins'] < 8 && $jinfo['lastClockin'] < time() - 3600) ? 1 : 0;

    $db->query("SELECT count(viewed) FROM events WHERE `to` = ? AND viewed = 1");
    $db->execute(array(
        $user_class->id
    ));
    $eveCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM hitlist");
    $db->execute();
    $hitCount = $db->fetch_single();

    $db->query("SELECT count(*) FROM votes WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
    $votes = ($db->fetch_single() == 0) ? 'notify' : 'null';


    if (!$user_class->admin && !$user_class->gm) {
        $referrals = 0;
        $tickets = 0;
    }
    $hospital = "[" . $hosCount . "]";
    $hospital = ($hosCount > 0) ? "<span style='color:red;'>$hospital</span>" : $hospital;
    $j = mysql_query("SELECT `id` FROM grpgusers WHERE jail > 0");
    $jail = mysql_num_rows($j);
    $petJailDisplay = "[" . $petJailCount . "]";
    $petJailDisplay = $petJailCount > 0 ? "<span style='color:red;'>$petJailDisplay</span>" : $petJailDisplay;
    $phos = "[" . $pHosCount . "]";
    $phos = ($pHosCount > 0) ? "<span style='color:red;'>$phos</span>" : $phos;
    $mail = $mailCount;
    $events = $eveCount;
    $hitlist = $hitCount;
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

if ($user_class->gangmail > 0) {
    $gmailCount = 'New';
} else {
    $gmailCount = '';
}
if ($user_class->globalchat > 0) {
    $globalchat = 'New';
} else {
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
    'event' => $ev,
    'mail' => '<!_-mail-_!>',
    'hospital' => $hosp,
    'jail' => $ja,
    'gangmail' => $gmailCount,
    'updates' => $user_class->game_updates,
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
?>
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="x-dns-prefetch-control" content="off">
    <meta charset="UTF-8">
    <?php

    if ($user_class->view_preference === '1') { ?>
        <meta name="viewport" content="width=1024">
    <?php } else { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <?php }
    $q = mysql_query("SELECT `id` FROM grpgusers WHERE hospital > 0");
    $hosp = mysql_num_rows($q);
    ?>

    <?php if ($ev > 0) {
        $eve = "(" . $ev . ")";
    } else {
        $eve = "";
    } ?>
    <title><?php echo $eve; ?> ChaosCity</title>

    <!-- NEW -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.js"></script>

    <!-- OLD -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/ca284bbf02.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script src="js/java.js?v=12" type="text/javascript"></script>
</head>

<div class="bg-[#181818] min-h-screen">
    <div class="min-h-screen">

        <header class="mainHeader">
            <div class="row mx-auto mainHeaderContent d-none d-md-block">
                <?php require 'nc_navbar.php'; ?>
            </div>
        </header>

        <?php if (!$user_class->is_ads_disabled): ?>
            <div class="max-w-7xl mx-auto mt-2 mb-4 px-2 md:px-6 lg:px-8">

                <div class="w-full flex flex-row gap-x-2 text-white bg-black/40 rounded-lg p-2 px-4 items-center">
                    <?php
                    $now = time();

                    $db->query("SELECT * FROM ads WHERE `timestamp` + (`displaymins` * 60) > ? ORDER BY RAND() LIMIT 1");
                    $db->execute([$now]);
                    $advertisement = $db->fetch_row(true);
                    if (!empty($advertisement)) {
                        $ads_user = read_user_for_advertisement($advertisement["poster"], $advertisement["displaymins"] * 60);

                        $avatar = $ads_user->avatar ?: "/images/no-avatar.png";
                        $formattedname = formatName($ads_user->id);

                        echo '<img src="assets/images/svg/Ad.svg" class="size-6 mr-2" alt="Advertisement">';
                        echo '<span class="flex items-center pr-2">' . $formattedname . '</span>';
                        echo '<span class="flex items-center">' . $advertisement['message'] . '</span>';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="ajax-message-holder" style="min-height: 60px; display: none;"></div>