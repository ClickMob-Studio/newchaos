<?php
session_start();
register_shutdown_function('ob_end_flush');
$starttime = microtime_float();
include 'dbcon.php';
include 'database/pdo_class.php';
include "classes.php";
include "codeparser.php";
if (empty($ignoreslashes)) {
    if (get_magic_quotes_gpc() == 0) {
        foreach ($_POST as $k => $v)
            $_POST[$k] = addslashes($v);
        foreach ($_GET as $k => $v)
            $_GET[$k] = addslashes($v);
    }
}
if (!isset($_SESSION['id'])) {
    include('home.php');
    die();
}
$db->query("SELECT * FROM sessions WHERE userid = ?");
$db->execute(array(
    $_SESSION['id']
));
$IP = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$row = $db->fetch_row(true);
if (!$row) {
    session_destroy();
    header('Location: index.php');
}
if ($row['sessionid'] != $_COOKIE['PHPSESSID'] && $_SESSION['id'] != 0) {
    $sessid = $_SESSION['id'];
    session_unset();
    session_destroy();
    header('Location: index.php');
}
$file = '/var/www/logs/actlog.txt';
$current = "$IP|-{$_SESSION['id']}|-|-|{$_SERVER['REQUEST_URI']}|-|-|" . serialize($_POST) . "|-|-|" . time() . ";\n";
file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
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

// if ($user_class->id == 174) {
//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
// }
if($_SESSION['anticheat'] == 1 && pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'human') {
    $_SESSION['last_page'] = $_SERVER['REQUEST_URI'];
    header('Location: human.php');
}

$db->query("SELECT * FROM eject WHERE `user_id` = ? AND done = 0");
$db->execute([$user_class->id]);
$rows = $db->fetch_single();
if ($rows) {
    $db->query("UPDATE eject SET done = 1 WHERE `user_id` = ?");
    $db->execute([$user_class->id]);
    session_destroy();
    header('Location: login.php');
}

if ($user_class->gang == 0 && $user_class->cur_gangcrime != 0) {
    $db->query("UPDATE grpgusers SET cur_gangcrime = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
}
if (!$m->get('cities')) {
    $m->set('cities', 'woot', false, 300);
    $db->query("SELECT * FROM cities");
    $db->execute();
    $rows = $db->fetch_row();
    foreach ($rows as $row)
        $m->set('cities.' . $row['id'], false, $row['name']);
}
$m->set('lastpageload.' . $user_class->id, false, time());
if ($user_class->lastpayment < time() - 86400) {
    $db->query("UPDATE grpgusers SET points = points + 250, lastpayment = unix_timestamp() WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    Send_event($user_class->id, "Daily Login Bonus: <font color=yellow><b>250 Points</b></font>");
}
if (isset($_GET['spend'])) {
    if ($_GET['spend'] == "refenergy") {
        manual_refill('e');
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://dev.TheMafiaLife.com/');
    }
    if ($_GET['spend'] == "refawake") {
        $cost = 100 - floor(100 * ($user_class->directawake / $user_class->directmaxawake));
        if ($user_class->awakepercent != 100 && $user_class->points >= $cost) {
            $user_class->points -= $cost;
            $user_class->directawake = $user_class->directmaxawake;
            mysql_query("UPDATE grpgusers SET awake = $user_class->directmaxawake, points = points - $cost WHERE id = $user_class->id");
        }
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://dev.TheMafiaLife.com/');
    }
    if ($_GET['spend'] == "refnerve") {
        manual_refill('n');
        if (isset($_GET['crime']))
            header('Location: crime.php');
        elseif ($_SERVER['HTTP_REFERER'])
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        else
            header('Location: https://dev.TheMafiaLife.com/');
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
        foreach ($rows as $row)
            $total += $row['total'];
        $db->query("UPDATE gangs SET tmstats = ? WHERE id = ?");
        $db->execute(array(
            $total,
            $user_class->gang
        ));
    }
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
$time = date("F d, Y g:i:sa", time());
if (isset($_COOKIE['tu'])) {
    if ($_COOKIE['tu'] != $user_class->id) {
        $db->query("INSERT INTO multi (acc1, acc2, `time`) VALUES (?, ?, ?)");
        $db->execute(
            array(
                $user_class->id,
                $_COOKIE['tu'],
                time(),
            )
        );
    }
}
setcookie("tu", $user_class->id, time() + (10 * 365 * 24 * 60 * 60));
if ($uid != 0) {
    $db->query("UPDATE grpgusers SET lastactive = unix_timestamp(), ip = ? WHERE id = ?");
    $db->execute(array(
        $IP,
        $user_class->id
    ));
}
function callback($buffer)
{
    global $user_class, $db, $m;
    if (!$m->get('hosCount')) {
        $db->query("SELECT count(id) FROM grpgusers WHERE hospital <> 0");
        $db->execute();
        $m->set('hosCount', $db->fetch_single(), false, 15);
    }
    if (!$m->get('v2jailCount')) {
        $db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
        $db->execute();
        $m->set('jailCount', $db->fetch_single(), false, 1);
    }
    if (!$m->get('pJailCount')) {
        $db->query("SELECT count(id) FROM pets WHERE jail <> 0");
        $db->execute();
        $m->set('pJailCount', $db->fetch_single(), false, 1);
    }
    if (!$m->get('pHosCount')) {
        $db->query("SELECT count(id) FROM pets WHERE hospital <> 0");
        $db->execute();
        $m->set('pHosCount', $db->fetch_single(), false,  1);
    }
    if (!$m->get('pHosCount.' . $user_class->id)) {
        $db->query("SELECT count(viewed) FROM pms WHERE `to` = ? AND viewed = 1");
        $db->execute(array(
            $user_class->id
        ));
        $m->set('mailCount.' . $user_class->id, $db->fetch_single(), false, 3);
    }
    if (!$m->get('clockin.' . $user_class->id)) {
        $db->query("SELECT lastClockin, dailyClockins FROM jobInfo WHERE userid = ?");
        $db->execute(array(
            $user_class->id
        ));
        $jinfo = $db->fetch_row(true);
        $toset = ($jinfo['dailyClockins'] < 5 && $jinfo['lastClockin'] < time() - 3600) ? 1 : 0;
        $m->set('clockin.' . $user_class->id, $toset, false, 60);
    }
    if (!$m->get('eveCount.' . $user_class->id)) {
        $db->query("SELECT count(viewed) FROM events WHERE `to` = ? AND viewed = 1");
        $db->execute(array(
            $user_class->id
        ));
        $m->set('eveCount.' . $user_class->id, $db->fetch_single(),  false, 3);
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
    $jail = "[" . $m->get('v2jailCount') . "]";
    $jail = ($m->get('v2jailCount') > 0) ? "<span style='color:red;'>$jail</span>" : $jail;
    $pjail = "[" . $m->get('pJailCount') . "]";
    $pjail = ($m->get('pJailCount') > 0) ? "<span style='color:red;'>$pjail</span>" : $pjail;
    $phos = "[" . $m->get('pHosCount') . "]";
    $phos = ($m->get('pHosCount') > 0) ? "<span style='color:red;'>$phos</span>" : $phos;
    $mail = "[" . $m->get('mailcount') . "]";
    $mail =   $m->get('mailCount.' . $user_class->id);
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
    $buffer = str_replace("<!_-pjail-_!>", $pjail, $buffer);
    $buffer = str_replace("<!_-phos-_!>", $phos, $buffer);
    $buffer = str_replace("<!_-thecardvalue-_!>", $user_class->cardvalue, $buffer);
    $buffer = str_replace("<!_-thecardtype-_!>", $user_class->cardtype, $buffer);
    $buffer = str_replace("<!_-forumnoti-_!>", ($user_class->forumnoti) ? "<span style='color:#00ff00;font-weight:bold;'>$user_class->forumnoti</span>" : "0", $buffer);
    $buffer = str_replace("<!_-genBars-_!>", genBars(), $buffer);
    $hossyjail = ($user_class->hospital) ? "<img width='20px' height='20px' src='images/hossy.png' /> " . ($user_class->hospital / 60) . " Mins" : "";
    $hossyjail .= ($user_class->jail) ? "<img width='20px' height='20px' src='images/jailtop.png' /> " . ($user_class->jail / 60) . " Mins" : "";
    $buffer = str_replace("<!_-hossyjail-_!>", $hossyjail, $buffer);
    $buffer = str_replace("<!_-votes-_!>", $votes, $buffer);
    if ($hitlist > 0)
        $buffer = str_replace("<!_-hitlist-_!>", "<span class='notify'>[" . prettynum($hitlist) . "]</span>", $buffer);
    else
        $buffer = str_replace("<!_-hitlist-_!>", "[" . prettynum($hitlist) . "]", $buffer);
    if ($mail > 0)
        $buffer = str_replace("<!_-mail-_!>", "<span class='notify'>" . prettynum($mail) . "</span>", $buffer);
    else
        $buffer = str_replace("<!_-mail-_!>", prettynum($mail), $buffer);


    if ($user_class->forumnoti > 0)
        $buffer = str_replace("<!_-forum-_!>", "<span class='notify'>New</span>", $buffer);
    else
        $buffer = str_replace("<!_-forum-_!>", "0", $buffer);

    if ($user_class->gmail > 0)
        $buffer = str_replace("<!_-gmail-_!>", "<span class='notify'>New</span>", $buffer);
    else
        $buffer = str_replace("<!_-gmail-_!>", "0", $buffer);
    if ($user_class->globalchat > 0)
        $buffer = str_replace("<!_-gchat-_!>", "<span class='notify'>New</span>", $buffer);
    else
        $buffer = str_replace("<!_-gchat-_!>", "0", $buffer);
    if ($user_class->news > 0)
        $buffer = str_replace("<!_-news-_!>", "<span class='notify'>New</span>", $buffer);
    else
        $buffer = str_replace("<!_-news-_!>", "0", $buffer);
    if ($user_class->game_updates > 0)
        $buffer = str_replace("<!_-gupdates-_!>", "<span class='notify'>$user_class->game_updates</span>", $buffer);
    else
        $buffer = str_replace("<!_-gupdates-_!>", "$user_class->game_updates", $buffer);
    if ($user_class->jail > 0)
        $buffer = str_replace("<!_-jail-_!>", "<span class='notify jailed'>" . prettynum($jail) . "</span>", $buffer);
    else
        $buffer = str_replace("<!_-jail-_!>", prettynum($jail), $buffer);
    if ($events > 0)
        $buffer = str_replace("<!_-events-_!>", "<span class='notify'>" . prettynum($events) . "</span>", $buffer);
    else
        $buffer = str_replace("<!_-events-_!>", prettynum($events), $buffer);
    if ($tickets > 0)
        $buffer = str_replace("<!_-tickets-_!>", "<font color='yellow'><b>" . prettynum($tickets) . "</b></font>", $buffer);
    else
        $buffer = str_replace("<!_-tickets-_!>", prettynum($tickets), $buffer);
    if ($referrals > 0)
        $buffer = str_replace("<!_-referrals-_!>", "<font color='red'><b>" . prettynum($referrals) . "</b></font>", $buffer);
    else
        $buffer = str_replace("<!_-referrals-_!>", prettynum($referrals), $buffer);
    $buffer = str_replace("<!_-cityname-_!>", $user_class->mycityname, $buffer);
    $clockin = ($m->get('clockin.' . $user_class->id)) ? "<a href='jobs.php?clockin' style='color:red;'>Clockin for Job</a>" : "";
    $buffer = str_replace("<!_-clockin-_!>", $clockin, $buffer);
    $et = ($user_class->admin || $user_class->eo ? "<a href='subet.php'>Send ET Prize</a>" : "");
    $buffer = str_replace("<!_-entertain-_!>", $et, $buffer);
    $buffer = str_replace("<!_-emcount-_!>", $emcount, $buffer);
    return $buffer;
}
ob_start("callback");
$cet = filemtime('/var/www/html/css/stylemm.css');
$jet = filemtime('/var/www/html/js/java.js');
/*
if(!$friends = $m->get('friends.count.'.$user_class->id)){
	$db->query("SELECT COUNT(*) FROM contactlist WHERE playerid = $user_class->id AND type = 1");
	$friends = $db->fetch_single();
	$m->set('friends.count.'.$user_class->id, $friends, false, 60);
}
if(!$enemies = $m->get('enemies.count.'.$user_class->id)){
	$db->query("SELECT COUNT(*) FROM contactlist WHERE playerid = $user_class->id AND type = 2");
	$enemies = $db->fetch_single();
	$m->set('enemies.count.'.$user_class->id, $enemies, false, 60);
}
if(!$ignore = $m->get('ignore.count.'.$user_class->id)){
	$db->query("SELECT COUNT(*) FROM ignorelist WHERE blocker = $user_class->id");
	$ignore = $db->fetch_single();
	$m->set('ignore.count.'.$user_class->id, $ignore, false, 60);
}
*/
if (empty($metatitle)) $metatitle = 'TheMafiaLife';

echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<meta name="description" content="Mafia Based Browser Game" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
echo '<title><!_-emcount-_!>' . $metatitle . '</title>';
echo '<link rel="stylesheet" type="text/css" href="css/stylemm.css?' . $cet . '">';
echo '<link rel="stylesheet" type="text/css" href="css/_misc.css?' . filemtime('/var/www/html/css/_misc.css') . '">';
echo '<link href="fa/css/all.css" rel="stylesheet">';
echo '<link rel="stylesheet" href="https://unpkg.com/balloon-css/balloon.min.css">';
echo '<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>';
echo '<script src="js/jquery.tipsy.min.js" type="text/javascript"></script>';
echo '<script src="js/java.js?12" type="text/javascript"></script>';
echo '<script type="text/javascript" src="https://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>';
echo '<link href="https://fonts.googleapis.com/css?family=Chewy|Concert+One|Boogaloo|Germania+One|Bebas+Neue|Creepster" rel="stylesheet">';
echo '<script src="js/jquery.ui.touch-punch.min.js"></script>';
echo '<script src="js/main.js"></script>';
?>


<script type="text/javascript">
    var playerid = '<?php echo $user_class->id ?>';
    $(document).ready(function() {

        $("#sortable li").css("cursor", "pointer")

            .click(function() {
                window.location = $("a", this).attr("href");
                return false;
            });

        setInterval(function() {
            $.get("notiupdates.php", function(result) {
                var results = result.split("|");
                $(".mailbox").html(results[0]);
                $(".events").html(results[1]);

                if (results[3] > 0) {
                    $(".jailed").html("[" + results[3] + "]");
                    $(".jailed").css('color', 'red');
                } else {
                    $(".jailed").html("[" + results[3] + "]");
                    $(".jailed").css('color', '#ffffffbf');
                }

                if (results[4] > 0) {
                    $(".hospi").html("[" + results[4] + "]");
                    $(".hospi").css('color', 'red');
                } else {
                    $(".hospi").html("[" + results[4] + "]");
                    $(".hospi").css('color', '#ffffffbf');
                }

                //$('.progress-bar-heart').attr('style', 'width:' + results[5]);

                if (results[2] > 0)
                    document.title = "(" + results[2] + ") TheMafiaLife";
            });
        }, 2000);
    });
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-215734796-1">
</script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-215734796-1');
</script>

<!-- PushAlert -->
<script type="text/javascript">
    (function(d, t) {
        var g = d.createElement(t),
            s = d.getElementsByTagName(t)[0];
        g.src = "https://cdn.pushalert.co/integrate_197dd892bed436ee67fad9c4f76121e3.js";
        s.parentNode.insertBefore(g, s);
    }(document, "script"));
</script>

<!-- End PushAlert -->

</head>

<body>





<style>
.pulsate {
    -webkit-animation: pulsate 1s ease-out;
    -webkit-animation-iteration-count: infinite;
    opacity: 0.5;
}
@-webkit-keyframes pulsate {
    0% {
        opacity: 0.5;
    }
    50% {
        opacity: 1.0;
    }
    100% {
        opacity: 0.5;
    }
}
</style>





<style>

    </style>
    <?php
    echo '<center>';
    echo '<div id="topContent">';
    echo '<div id="outer" class="wrap">';
    echo '<div id="top_bar" class="row">';
    echo '<a href="online.php">' . get_users_online() . ' Players Online</a>';
    //echo '<a href="https://discord.gg/5QdKwnmWWQ" target="_blank"><img src="images/discord.png" width="10%" style="position: fixed;top: 0%;width: 10%;left: 15%;"></a>';
    echo '</div>';
    echo '<div id="header" class="row">';
    echo '<div id="logo"></div>';
    echo '<div id="stats" class="genBars">';
    echo '<!_-genBars-_!>';
    echo '</div>';
    echo '<div id="profile">';
    echo '<div id="avatar"><img width="95px" height="97px" src="[:AVATAR:]"></div>';
    echo '<div id="details">';
    echo '<a href="profiles.php?id=' . $user_class->id . '">' . $user_class->formattedname . '</a><br />';
    echo 'Money: <a href="bank.php?dep"><span class="money">$<!_-money-_!></span> (<!_-banked-_!>)</a><br />';
    echo 'Level: <span class="level"><!_-level-_!></span><br />';
    echo '<a href="rmstore.php">Points: <span class="points"><!_-points-_!></a> (<!_-pbanked-_!>)</span><br />';
    echo '<a href="rmstore.php">Credits: <span class="credits"><!_-credits-_!></a></span><br />';
    echo '<a href="index.php?action=logout" class="btn">Logout</a>';
    echo '</div>';
    echo '<div class="spacer"></div>';
    echo '</div>';
    echo '<div class="spacer"></div>';
    if ($user_class->id == 4) {
        echo '<div>Test</div>';
    }
    echo '</div>';
    echo '<div class="spacer"></div>';
    echo '<div id="main" class="row">';
    echo '<div class="top row">';
    echo '<div class="pad">';

    echo '</form>';
    echo '<div class="spacer"></div>';
    echo '</div>';
    echo '<div id="info">';
    $serverTime = date("D, F d, g:i:sa", time());
    echo '<span class="clock"><span id="servertime"> ' . $serverTime . ' </span></span>';
    echo '<span class="page_hospital"><a href="hospital.php">Hospital <span class="hospi"><!_-hospital-_!></span></a></span>';
    echo '<span class="page_jail"><a href="jail.php">Jail <span class="jailed"><!_-jail-_!></span></a></span>';
    echo '<span class="page_gameupdates"><a href="gameupdates.php">Game Updates <span class="gameupdates">[<!_-gupdates-_!>]</span></a></span>';
    echo '<span class="page_gamechat"><a href="globalchat.php">Game Chat <span class="gamechat">[<!_-gchat-_!>]</span></a></span>';
    echo '<span class="page_mail"><a href="pms.php?view=inbox">Mailbox [<span class="mailbox"><!_-mail-_!></span>]</a></span>';
    echo '<span class="page_events"><a href="events.php">Events [<span class="events"><!_-events-_!></span>]</a></span>';
    echo '<span class="location"><a href="city.php"><!_-cityname-_!></a></span>';
    echo '<span class="page_news"><a href="news.php">Game News [<!_-news-_!>]</a></span>';
    echo '<div class="spacer"></div>';
    echo '</div>';
    echo '<div class="spacer"></div>';
    echo '</div>';
    echo '</div> ';
    echo '<div class="middle row">';
    echo '<div id="left">';
    echo '<div id="menu" class="sortMenu">';
    /*echo '<div class="menu"><a class="menu" href="index.php">Home</a></div>';
						echo '<div class="menu"><a class="menu" href="dailies.php"><font color=red><b>Daily Jobs</b></font></a></div>';
						echo '<div class="menu"><a class="menu" href="missions.php"><font color=red><b>Missions</b></font></a></div>';
						echo '<div class="menu"><a class="menu" href="backalley.php"><font color=red><b>Backalley</b></font></a></div>';
						echo '<div class="menu"><a class="menu" href="search.php"><font color=chartreuse style="font-size:14px">Search Player</font></a></div>';
                        echo '<div class="menu"><a class="menu" href="online.php">Online [' . get_users_online() . ']</a></div>';
						echo '<div class="menu"><a class="menu" href="globalchat.php">Game Chat[<!_-gchat-_!>]</a></div>';
						echo '<div class="menu"><a class="menu" href="gameupdates.php">Game Updates [<!_-gupdates-_!>]</a></div>';
						echo '<div class="menu"><a class="menu" href="inventory.php">Inventory</a></div>';
						echo '<div class="menu"><a class="menu" href="city.php"><font color=orange><!_-cityname-_!></font></a></div>';
						echo '<div class="menu"><a class="menu" href="bank.php">Bank</a></div>';
						echo '<div class="menu"><a class="menu" href="gym.php">Gym</a></div>';
						echo '<div class="menu"><a class="menu" href="crime.php">Crimes</a></div>';
						echo '<div class="menu"><a class="menu" href="' . ($user_class->gang ? 'gang.php' : 'creategang.php') . '">Your Gang</a></div>';
						echo ($user_class->gang) ? '<div class="menu"><a class="menu" href="gangmail.php">Gang Mail [<!_-gmail-_!>]</a></div>' : '';
						echo '<div class="menu"><a class="menu" href="portfolio.php">Your Properties</a></div>';
						echo '<div class="menu"><a class="menu" href="forum.php">Forums</a></div>';
						echo '<div class="menu"><a class="menu" href="preferences.php">Edit Account</a></div>';
						echo '<div class="menu"><a class="menu" href="rmstore.php"><font color=chartreuse style="font-size:14px">Donate</font> x2</a></div>';
						echo '<div class="menu"><a class="menu" href="refer.php"><font color=chartreuse style="font-size:14px">Refer</font></a></div>';
                        echo '<div class="menu"><a class="menu" href="vote.php"><span class="<!_-votes-_!>">Vote for AA</span></a></div>';
						if ($user_class->petMenu == 'yes'){
							echo'<div class="menu"><a class="menu" href="mypets.php">My Pet</a></div>';
							echo'<div class="menu"><a class="menu" href="petcrime.php">Pet Crimes</a></div>';
							echo'<div class="menu"><a class="menu" href="petgym.php">Pet Gym</a></div>';
							echo'<div class="menu"><a class="menu" href="pethouse.php">Pet House</a></div>';
							echo'<div class="menu"><a class="menu" href="pethof.php">Pet HOF</a></div>';
                        }*/

    // if ($user_class->id == 150) {
    //     echo '<div class="menu"><a class="menu" style="text-indent: unset!important;
    //     color: #7eff11;
    //     font-size: 14px;
    //     text-transform: uppercase;
    //     font-weight: 600;" href="patricks.php">St Patricks</a></div>';
    // }

    $gangURL = ($user_class->gang) ? 'gang.php' : 'creategang.php';

    $menus = array(
        0 => array(
            'title' => 'Home',
            'url'   => 'index.php'
        ),
        1 => array(
            'title' => 'Bloodbath <span class="notify">[Active]</span>',
            'url' => 'bloodbath.php'
        ),
        2 => array(
            'title' => 'Daily Jobs',
            'url'   => 'dailies.php'
        ),
        3 => array(
            'title' => 'Missions',
            'url'   => 'missions.php'
        ),
        4 => array(
            'title' => 'Hall Of Fame',
            'url'   => 'halloffame.php',
        ),
        5 => array(
            'title' => 'Backalley',
            'url'   => 'backalley.php'
        ),
        6 => array(
            'title' => '<font color=chartreuse style="font-size:14px">Search Player</font>',
            'url'   => 'search.php'
        ),
        7 => array(
            'title' => 'Online [' . get_users_online() . ']',
            'url'   => 'online.php'
        ),
        8 => array(
            'title' => 'Game Chat [<!_-gchat-_!>]',
            'url'   => 'globalchat.php'
        ),
        9 => array(
            'title' => 'Game Updates [<!_-gupdates-_!>]',
            'url'   => 'gameupdates.php'
        ),
        10 => array(
            'title' => 'Inventory',
            'url'   => 'inventory.php'
        ),
        11 => array(
            'title' => '<font color=orange><!_-cityname-_!></font>',
            'url'   => 'city.php'
        ),
        12 => array(
            'title' => 'Bank',
            'url'   => 'bank.php'
        ),
        13 => array(
            'title' => 'Gym',
            'url'   => 'gym.php'
        ),
        14 => array(
            'title' => 'Crimes',
            'url'   => 'crime.php'
        ),
        15 => array(
            'title' => 'Your Gang',
            'url'   => $gangURL
        ),
        16 => array(
            'title' => 'Gang Mail [<!_-gmail-_!>]',
            'url'   => 'gangmail.php'
        ),
        17 => array(
            'title' => 'Your Properties',
            'url'   => 'portfolio.php'
        ),
        18 => array(
            'title' => 'Forums [<!_-forum-_!>]',
            'url'   => 'forum.php'
        ),
        19 => array(
            'title' => 'Edit Account',
            'url'   => 'preferences.php'
        ),
        20 => array(
            'title' => '<font color=red style="font-size:14px">Donate</font>',
            'url'   => 'rmstore.php'
        ),
        21 => array(
            'title' => '<font color=chartreuse style="font-size:14px">Refer</font>',
            'url'   => 'refer.php'
        ),
        22 => array(
            'title' => '<span class="<!_-votes-_!>">Vote for TML!</span>',
            'url'   => 'vote.php'
        ),
        23 => array(
            'title' => 'My Pet',
            'url'   => 'mypets.php'
        ),
        24 => array(
            'title' => 'Pet Crimes',
            'url'   => 'petcrime.php'
        ),
        25 => array(
            'title' => 'Pet Gym',
            'url'   => 'petgym.php'
        ),
        26 => array(
            'title' => 'Pet House',
            'url'   => 'pethouse.php'
        ),
        27 => array(
            'title' => 'Pet HOF',
            'url'   => 'pethof.php'
        ),
        // 28 => array(
        //     'title' => 'MINI-Bloodbath',
        //     'url'   => 'bloodbath.php'
        // ),
        29 => array(
            'title' => '<font color=cadetblue style="font-size:14px">Shoutbox</font>',
            'url'   => 'shoutbox.php'
        ),
        30 => array(
            'title' => 'Travel',
            'url'   => 'bus.php'
        ),
    );


    if ($user_class->sortablemenu == 1) {
        echo '<ul id="sortable">';
    } else {
        echo '<ul>';
    }
    $menuorder = explode(',', $user_class->menuorder);

    $disabledPages = array(4, 7, 8, 9, 17);

    foreach ($menuorder as $morder) {
        if ($morder == 16 && !$user_class->gang) {
            continue;
        }

        if (in_array($morder, $disabledPages))
            continue;

        // Show Pet Menu
        if (($morder >= 23 && $morder <= 28) && $user_class->petMenu == "no") {
            continue;
        }
        if ($morder == 31 || $morder == 28)
            continue;
        echo '<li class="ui-state-default" id="' . $morder . '"><a href="' . $menus[$morder]['url'] . '">' . $menus[$morder]['title'] . '</a></li>';
    }

    // } else {

    //     foreach($menus as $key => $value)
    //     {
    //         if($key == 14 && !$user_class->gang)
    //         {
    //             continue;
    //         }

    //         if($key >= 21 && $user_class->petMenu == "no")
    //         {
    //             continue;
    //         }
    //         echo '<li class="ui-state-default" id="'.$key.'"><a href="'.$value['url'].'">'.$value['title'].'</a></li>';
    //     }

    // }

    echo '</ul>';

    //echo '<div class="spacer"></div>';
    //echo '<div class="menu forumhover" id="resetmenuorder">Reset Menu Order</div>';
    echo '<div class="spacer"></div>';
    echo '<div id="staff_panel">';
    echo '<div class="pad">';
    echo '<span class="title">Staff Online</span>';
    echo display_online_staff();
    echo '<div class="spacer"></div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';


    $time = time();
    $array = array();
    if ($user_class->aprotection > $time) {
        $rtn = howlongtil($user_class->aprotection);
        $array['Attack Protection'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
    if ($user_class->mprotection > $time) {
        $rtn = howlongtil($user_class->mprotection);
        $array['Mug Protection'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
    if ($user_class->exppill > $time) {
        $rtn = howlongtil($user_class->exppill);
        $array['Double EXP Pill'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
    if ($user_class->hospital > $time) {
        $rtn = howlongtil($user_class->hospital);
        $array['Hospital'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
    if ($user_class->jail > $time) {
        $rtn = howlongtil($user_class->jail);
        $array['Jail'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
    if ($user_class->hospital > 0) {
        echo '<a href="hospital.php"><span style="color:red;">You are currently in hospital for ' . $user_class->hospital . ' seconds.</span></a><br />';
    }
    if ($user_class->jail > 0) {
        echo '<a href="jail.php"><span style="color:red;">You are currently in jail for ' . $user_class->jail . ' seconds.</span></a><br />';
    }
    foreach ($array as $sub => $in) {
        echo '<span style="color:white;">&bull; ' . $sub . ' : <span style="color:red;">' . $in . '</span></span> &nbsp;';
    }
    if (!empty($array)) {
        echo '<br />';
    }
    $db->query("SELECT * FROM ganginvites WHERE playerid = ?");
    $db->execute(array(
        $user_class->id
    ));
    if ($db->num_rows())
        print "<a href='ganginvites.php'><span style='color:red;'>You have gang invites!</span></a><br />";
    echo '<br />';


    // COUNTDOWN TIMER
    // ADD 000 TO END OF UNIX TIMESTAMP

    echo '<script>
    var countDownDate = new Date(1661727600000);

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

    //$("#countdown").html(" Ends In " + days + "d " + hours + "h " + minutes + "m " + seconds + "s ");

    echo '<div id="maincontent">';
    if (time() <  1661727600)
        //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Bank Holiday! - DOUBLE CRIME EXP ACTIVE</div>';
        echo '<br><div class="pulsate" style="font-family:Creepster;font-size: 1.5em;color:1e7b00;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href=newcrimes.php>BANK HOLIDAY! - DOUBLE CRIME EXP ACTIVE</a>
            <span id="countdown">Ends In ' . countdown(1661727600) . '</span></div>';

    if (time() <  1661122799)
        //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</div>';
        echo '<div class="pulsate" style="font-family:Creepster;font-size: 1.5em;color:1e7b00;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href=bustcontest.php>Our BUST COMPETITION IS currently active! </a>
            <span id="countdown">Ends In ' . countdown(1661122799) . '</span></div>';



    if ($user_class->claimed == 0)                    //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</br></div>';
        echo '<div style="font-family:Creepster;font-size: 2.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href="rmstore.php?buy=freebie">Happy Weekend Claim Your Free Double EXP Pill + 5,000 Free Points by <div class="pulsate">Clicking Here!!</div></a>
                                                </div>';



    if ($user_class->id < 0)                    //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</div>';
        echo '<div style="font-family:Creepster;font-size: 2.5em;color:green;text-align: center;margin-bottom: 20px;margin-top: -20px;">Get Double Exp on all Crimes/Kills is Active!
                                                </div>';





    // if ($user_class->id == 174) {
    //}

        // LOTTERY DISPLAY

    // $db->query("SELECT SUM(tickets) FROM ptslottery WHERE userid = $user_class->id");
    // $db->execute();
    // $ptscount = $db->fetch_single();
    // $ptscount = ($ptscount > 0) ? $ptscount : 0;
    // $db->query("SELECT SUM(tickets) FROM cashlottery WHERE userid = $user_class->id");
    // $db->execute();
    // $cashcount = $db->fetch_single();
    // $cashcount = ($cashcount > 0) ? $cashcount : 0;

    // $tickCost = 250000;
    // $db->query("SELECT SUM(tickets) FROM cashlottery");
    // $db->execute();
    // $numlotto = $db->fetch_single();
    // $camountlotto = $numlotto * $tickCost;

    // $tickCost = 50;
    // $db->query("SELECT SUM(tickets) FROM ptslottery");
    // $db->execute();
    // $numlotto = $db->fetch_single();
    // $pamountlotto = $numlotto * $tickCost;

    // if ($cashcount < 50 || $ptscount < 25) {
    //     echo '<div class="floaty" style="margin-top:-10px;font-family:\'Bebas Neue\';font-size:2em;"><i style="margin-right: 5px; color:#37ff50" class="fas fa-dollar-sign"></i><a href="cashlottery.php">Cash Lottery: <span style="color:#37ff50">$' . number_format_short($camountlotto) . '</span></a>  | <i style="margin-right: 5px; color:#038bff" class="fab fa-product-hunt"></i> <a href="ptslottery.php">Points Lottery <span style="color:#37ff50">' . number_format_short($pamountlotto) . '</span></a></div>';
    // }
    // }
    ?>

    <div class="vertical-text-slider">
        <div class="slider-icon">
            <a href="/shoutbox.php"><img width="16" height="16" src="/css/images/icons/loudspeaker_32.png" alt="Smart Ads" /></a>
        </div>
        <div class="slider-frame">
            <ul class="slides">

                <?php
                $now = time();
                // $result = mysql_query("SELECT * FROM `ads` WHERE TIMESTAMPDIFF(MINUTE, NOW(), `timestamp`) + `displaymins` > 0 AND `flagcount` < 3 ORDER BY `timestamp` DESC");
                $result = mysql_query("SELECT a.* FROM ads a WHERE ( SELECT (`timestamp` +(`displaymins` * 60)) FROM ads WHERE ads.id = a.id ) > UNIX_TIMESTAMP()");
                if (!mysql_num_rows($result)) {
                ?>
                    <li class="slide">
                        <div class="slide-content">
                            <span>Remember - All Referrals using your referral ID will reward you with 50 Credits! Help Spread the word of our launch!</span>
                        </div>
                    </li>
                    <?php
                } else {
                    while ($row = mysql_fetch_array($result)) {
                        $user_ads = new User($row['poster']);
                        if ($user_ads->avatar == "")
                            $user_ads->avatar = "/images/no-avatar.png";
                    ?>
                        <li class="slide">
                            <div class="slide-content">
                                <span><?php echo $user_ads->formattedname ?>: <?php echo $row['message'] ?></span>
                            </div>
                            <div class="slide-action">
                                <a href="#" onClick="reportAd(<?php echo $row['id'] ?>); return false;"><img width="16" height="16" src="/css/images/icons/exclamation-mark_16.png" alt="Report" /></a>
                            </div>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>



    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>
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
            }).done(function(msg) {
                alert("Ad report successful");
            });
        }
    </script>

    <?php

    //if ($user_class->id == 150) {
    // echo '<div class="text-center"><h3>Player Advertisements</h3></div><div class="floaty" style="margin-top:20px;font-family:Chewy;font-size:1.75em;text-decoration:underline;"><a style="color:darkorange;"href="forum.php?id=7">Got a suggestion? Let us know!</a></div>';

    $width = ($user_class->epoints / 120) * 100;

    echo '<style>
                    .container {
                        margin: auto;
                        width: 100%;
                        text-align: center;
                      }

                      .container .progress {
                        margin: 0 auto;
                      }

                      .progress {
                        padding: 4px;
                        background: rgba(0, 0, 0, 0.25);
                        border-radius: 6px;
                        -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25), 0 1px rgba(255, 255, 255, 0.08);
                        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25), 0 1px rgba(255, 255, 255, 0.08);
                      }

                      .progress-bar-heart {
                        height: 16px;
                        border-radius: 4px;
                          background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
                        background-image: -moz-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
                        background-image: -o-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
                        background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
                        -webkit-transition: 0.4s linear;
                        -moz-transition: 0.4s linear;
                        -o-transition: 0.4s linear;
                        transition: 0.4s linear;
                        -webkit-transition-property: width, background-color;
                        -moz-transition-property: width, background-color;
                        -o-transition-property: width, background-color;
                        transition-property: width, background-color;
                        -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.25), inset 0 1px rgba(255, 255, 255, 0.1);
                        box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.25), inset 0 1px rgba(255, 255, 255, 0.1);
                      }
                    .progress > .progress-bar-heart {
                        width: ' . $width . '%;
                        line-height: 20px;
                        background-color: #ff0909;
                        color: white;
                        font-size: 14px;
                        height: 20px;
                        white-space: nowrap;
                    </style>';

    // if ($user_class->id == 150) {
    //     echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';

    //     echo '<button id="myBtn">Open Modal</button>';
    //     echo '<div id="myModal" class="modal">
    //     <div class="modal-content">
    //     <form action="?" method="POST">
    //     <div class="g-recaptcha" data-sitekey="your_site_key"></div>
    //     <br/>
    //     <input type="submit" value="Submit">
    //   </form>
    //     </div>
    //   </div>';

    //   echo '<style>.modal {
    //     display: none; /* Hidden by default */
    //     position: fixed; /* Stay in place */
    //     z-index: 99999; /* Sit on top */
    //     left: 0;
    //     top: 0;
    //     width: 100%; /* Full width */
    //     height: 100%; /* Full height */
    //     overflow: auto; /* Enable scroll if needed */
    //     background-color: rgb(0,0,0); /* Fallback color */
    //     background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    //   }

    //   /* Modal Content/Box */
    //   .modal-content {
    //     background-color: #fefefe;
    //     margin: 15% auto; /* 15% from the top and centered */
    //     padding: 20px;
    //     text-align: center;
    //     border: 1px solid #888;
    //     width: 16%; /* Could be more or less, depending on screen size */
    //   }</style>';

    //   if ($_SESSION['anticheat'] == 1) {
    //     echo '<script>var modal = document.getElementById("myModal");
    //     modal.style.display = "block";
    //     </script>';
    //   }
    // }

    // if ($user_class->id == 174) {
        if (time() <= 1662159600) {
            if (pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'valentines') {
                echo '<div class="container">';

                echo '<div class="progress">
                    <div class="progress-bar-heart"><a href="summer.php">Earn Rayz this Summer by being active!! ' . number_format($width, 2) . '%</a></div>
                </div>
            </div>';
            }
        }

        // echo '<script>
        // var countDownDate = new Date("Feb 02, 2022 00:00:00");

        // var x = setInterval(function() {

        // var now = new Date();
        // var distance = countDownDate - now;

        // var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        // var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        // var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        // var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // $(".progress-bar-heart").html("<a href=\'valentines.php\'>Heart</a> 2x Speed Boost Active - " + hours + "h " + minutes + "m " + seconds + "s ");

        // if (distance < 0) {
        //     clearInterval(x);
        //     $(".progress-bar-heart").html("<a href=\'valentines.php\'>Heart ' . number_format($width, 2) . '%</a>");
        // }
        // }, 1000);
        // </script>';
    //}

    //if (pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'forum' && pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'valentines')
    // echo '<div class="floaty" style="margin-top:20px;font-family:Chewy;font-size:1.5em;text-decoration:underline;"><a style="color:darkorange;"href="forum.php?id=7">Got a suggestion? Let us know!</a></div>';

    // echo '<div class="floaty" style="margin-top:20px;font-size:1.5em;text-decoration:underline;"><a style="color:#0090ff;"href="forum.php?topic=153">Future Gang Changes<br/>Please take a moment to read and leave comments</a></div>';

    // if($user_class->id == 4)
    // {

    //     $question = "Which feature shall we introduce next?";
    //     $answers = array("Farms", "Shops", "Merits", "Bees");
    //     $pollId = 1;

    //     echo '<div class="floaty headerpoll">
    //     <h3>TheMafiaLife Poll</h3>
    //     <p>' . $question . '</p>
    //     <form id="poll">
    //         <input type="hidden" id="pollid" value="' . $pollId . '">
    //         <div class="radiobuttons">';

    //         $i = 0;
    //         foreach($answers as $answer)
    //         {
    //             echo '<label><input type="radio" name="radioq" id="a'.$i.'">' . $answer . '</label>';
    //             $i++;
    //         }

    //     echo '
    //     </div>
    //     <div class="clear"></div>
    //     <button id="pollSubmit">Submit</button>
    //     <div class="clear"></div>
    //     </form>
    //     </div>';
    // }
    function microtime_float()
    {
        $time = microtime();
        return (float) substr($time, 11) + (float) substr($time, 0, 8);
    }

    anticheat();
