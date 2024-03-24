<?php
//ini_set('display_errors', 1);
 //ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();

register_shutdown_function('ob_end_flush');
 //ini_set('memcached.sess_prefix', 'memc.sess.ml2.key.1');
$starttime = microtime_float();
include 'dbcon.php';
include 'database/pdo_class.php';
include "classes.php";
include "codeparser.php";
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
$file = '/var/www/s2.themafialife.co.uk/actlog.txt';
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

if ($user_class->id == 174) {
    //print_r($_SERVER);
//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
}


// $rows = $m->get('eject.' . $user_class->id);
// if (!$rows) {
//     $db->query("SELECT * FROM eject WHERE `user_id` = ? AND done = 0 LIMIT 1");
//     $db->execute([$user_class->id]);
//     $rows = $db->fetch_single();
//     $m->set('eject.' . $row['user_id'], false, 60);
// }
// if ($rows) {
//     $db->query("UPDATE eject SET done = 1 WHERE `user_id` = ?");
//     $db->execute([$user_class->id]);
//     session_destroy();
//     header('Location: login.php');
// }

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
    foreach ($rows as $row) {
        $m->set('cities.' . $row['id'], false, $row['name']);
    }
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
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://dev.themafialife.com/');
    }
    if ($_GET['spend'] == "refawake") {
        $cost = 100 - floor(100 * ($user_class->directawake / $user_class->directmaxawake));
        if ($user_class->awakepercent != 100 && $user_class->points >= $cost) {
            $user_class->points -= $cost;
            $user_class->directawake = $user_class->directmaxawake;
            mysql_query("UPDATE grpgusers SET awake = $user_class->directmaxawake, points = points - $cost WHERE id = $user_class->id");
        }
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://dev.themafiaLife.com/');
    }
    if ($_GET['spend'] == "refnerve") {
        manual_refill('n');
        if (isset($_GET['crime'])) {
            header('Location: crime.php');
        } elseif ($_SERVER['HTTP_REFERER']) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: https://dev.themafialife.com/');
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
setcookie("mu", $user_class->id, time() + (10 * 365 * 24 * 60 * 60));
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
       if (!$m->get('pHosCount')) {
        $db->query("SELECT count(id) FROM pets WHERE hospital <> 0");
        $db->execute();
        $m->set('pHosCount', $db->fetch_single(), false, 1);
    }
    if (!$m->get('pHosCount.' . $user_class->id)) {
        $db->query("SELECT count(viewed) FROM pms WHERE `to` = ? AND viewed = 1");
        $db->execute(array(
            $user_class->id
        ));
        $m->set('mailCount.' . $user_class->id, $db->fetch_single(), false, 3);
    }
if (!$m->get('v2jailCount')) {
        $db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
        $db->execute();
        $m->set('v2jailCount', $db->fetch_single(), false, 1);
    }
    if (!$m->get('pJailCount')) {
        $db->query("SELECT count(id) FROM pets WHERE jail <> 0");
        $db->execute();
        $m->set('pJailCount', $db->fetch_single(), false, 1);
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
        $buffer = str_replace("<!_-tickets-_!>", "<font color='yellow'><b>" . prettynum($tickets) . "</b></font>", $buffer);
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
$cet = filemtime('/var/www/html/css/newgamecss.css');
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
if (empty($metatitle)) {
    $metatitle = 'TheMafiaLife';
}

$activeRaidsQuery = "SELECT COUNT(*) AS activeRaidsCount FROM active_raids WHERE completed = 0"; // Replace 'end_time' with the actual column name that represents when the raid ends
$activeRaidsResult = mysql_query($activeRaidsQuery);
$activeRaidsData = mysql_fetch_assoc($activeRaidsResult);
$activeRaidsCount = $activeRaidsData['activeRaidsCount'];


echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>

<style>
.glow-text {
    text-shadow: 0 0 5px #FF0000, 0 0 10px #FF0000, 0 0 15px #FF0000, 0 0 20px #FF0000, 0 0 25px #FF0000, 0 0 30px #FF0000, 0 0 35px #FF0000;
}
</style>
';
echo '<meta name="description" content="Mafia Based Browser Game" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
echo '<title><!_-emcount-_!>' . $metatitle . '</title>';
echo '<link rel="stylesheet" type="text/css" href="css/newgamecss1.css?' . $cet . '">';
echo '<link href="fa/css/all.css" rel="stylesheet">';
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



<script charset="UTF-8" src="//web.webpushs.com/js/push/de58354f23d94a09330ade135a5a999a_1.js" async></script>

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
        @import url('https://fonts.googleapis.com/css2?family=Dosis&display=swap');
        @import url('https://fonts.cdnfonts.com/css/apocalypse-grunge');

        .new {
            animation: blinker 3s linear infinite;
        }

        .item { filter: invert(100%); }

        #small_card {
            width: 630px;
            margin: 0 auto;
        }

        h4 {
            text-transform: uppercase;
            margin-top: 3px;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
            font-family: 'Dosis', sans-serif;
        }

        .text-dark {
            color: #000 !Important;
        }

        .bg-darker {
            background-color: #343a40!important;
        }

        .icon-container {
            width: 50px;
            height: 50px;
            position: relative;
        }

        .avatar {
            height: 100%;
            width: 100%;
            border-radius: 50%;
            border: 1px solid #000;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        body,html,main {
            background: #333 !important;
            font-weight: 230 !important;
            font-style: normal !important;
        }

        .pad-top {
            margin-top: 10px !important;
        }

        body::-webkit-scrollbar {
            width: 8px;
        }

        body::-webkit-scrollbar-track {
            background: transparent;
        }

        body::-webkit-scrollbar-thumb {
            cursor: pointer !important;
            background-color: #000;
            background-color: rgba(0,0,0,.4);
            filter: "alpha(opacity=40)";-ms-filter:"alpha(opacity=40)";
        }

        .pull-right {
            float: right !important;
            padding: 5px;
        }

        @font-face {
            font-family: Apocalypse Grunge;
            src: url(../css/Apocalypse Grudge.ttf);
        }

        .slogan {
            line-height: 30px;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
            text-align: center;
            margin: 0 auto !important;
            margin-left: -10px !Important;
            font-size: 16px !important;
            padding: 10px;
            font-family: Apocalypse Grunge !important;
        }

        .btn, button, [type="submit"] {
            position: relative;
            display: inline-block;
            cursor: pointer;
            -webkit-transition: background 0.3s, border-color 0.3s;
            -moz-transition: background 0.3s, border-color 0.3s;
            transition: background 0.3s, border-color 0.3s;
            border-radius: 4px;
            background: #555;
            border: 1px solid transparent;
            font-weight: normal;
            color: white !important;
            font-size: 14px;
            text-decoration: none !important;
            text-align: center;
            overflow: hidden;
        }

        .btn:hover, button:hover, [type="submit"]:hover {
            background: #777;
            color: white;
            border: 1px solid transparent;
        }

        a.btn:active, a.btn:focus {
            color: white;
            border: 1px solid transparent;
        }

        .fa:hover {
            color: #ad7945 !important;
        }

     .fa {
            color: #c08f5e !important;
        }

        @media (max-width: 767px) {
            .hidden-xs {
                display: none !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .hidden-sm {
                display: none !important;
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .hidden-md {
                display: none !important;
            }
        }

        @media (min-width: 1200px) {
            .hidden-lg {
                display: none !important;
            }
        }

        small {
            margin-top: 0px !important;
            margin-left: 10px;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .nav-button {
            z-index: 999;
            position: fixed;
            float: right;
            top: 20px;
            right: 20px;
        }

        .sidebar-wrapper .sidebar-menu ul li a i {
            border-radius: 5px;
        }

        @media (min-width: 347px) and (max-width: 767px) {
            .container-fluid {
                margin-top: 80px !important;
                margin-bottom: 100px !important;
            }

            #BackTop {
                display: none;
                position: fixed;
                bottom: 60px !important;
                right: -7px;
                z-index: 99;
                font-size: 40px;
                border: none;
                outline: none;
                background-color: transparent;
                color: white !important;
                cursor: pointer;
                padding: 15px;
            }

            [class*="col-"] {
                background-clip: padding-box;
                border: 10px solid transparent;
            }

            #sidebar, .page-content {
                margin-top: 5px !important;
            }

            #small_card {
                width: 100% !important;
                margin: 0 auto;
            }
        }

        .container-fluid {
            margin-bottom: 10px;
        }

        .card-body {
            padding: 5px;
        }

        .table th {
            font-weight: 500;
            border-top: none;
            padding: 5px;
        }

        .img__wrap {
            position: relative;
            height: 75px;
            width: 100%;
        }

        .img__description {
            position: absolute;
            bottom: -16px;
            right: -5px;
            font-size: 11px;
            background: #000;
            color: #fff;
            visibility: hidden;
            opacity: 0.7;
            height: 20px;
            width: 75px;
        }

        .img__wrap:hover .img__description {
            visibility: visible;
            opacity: 0.7;
            cursor: pointer;
        }

        .img-rounded {
            border-radius: 5px !important;
        }

        #santa_hat {
            width: 25px;
            height: 25px;
            position: absolute;
            background: url(img/santa_hat.png) no-repeat;
            left: 39.8px;
            top: 10px;
        }

        .navi_bot, a, i {
            font-variant: small-caps;
            color: black;
        }

        #stats_bar {
            margin-left: -20px;
            padding-bottom: 30px;
        }

        .new_mail {
            animation: blinker 3s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        #topNavbar {
            padding: 0 10px;
            border-bottom: 1px solid #000;
            background: #111;
            z-index: 9999;
        }

        #sidebar, .page-content {
            margin-top: 38px;
        }
    </style>
    <?php
    echo '<center>';
echo '<div class="spacer"></div>';
echo '</div>';
echo '<div class="spacer"></div>';
echo '<div class="spacer"></div>';

echo '<body font-weight: normal !important;>';
echo '    <div class="page-wrapper default-theme sidebar-bg bg2 toggled">';
echo '	<nav class="navbar d-none d-lg-block navbar-expand-lg navbar-dark fixed-top" id="topNavbar">';
echo '    <div class="collapse navbar-collapse" id="navbarCollapse">';
echo '        <ul class="navbar-nav mr-auto">';
echo '         <a href="updates" class="nav-link">Game Updates</a>';
echo '         <a href="leaderboards" class="nav-link">Leaderboards</a>';
echo '         <a href="forum" class="nav-link">Game Forum</a>';
echo '         <li><a href="helpdesk" class="nav-link">Help Desk</a></li>';
echo '         <li><a href="wiki" class="nav-link" target="_blank">Wiki</a></li>';
echo '        </ul>';
echo '    </div>';
echo '    <span style="float:right;margin-top: -34px;font-size: 20px;" onclick="window.location.href="../logout""><i class="fa fa-sign-out-alt"></i></span>';
echo '</nav>';

echo'<div class="hidden-md hidden-lg fixed-top" style="z-index: 9999 !important;background: #333;color:#fff;padding: 15px;margin: 0 auto !important;">';
echo'<div class="row" align="center">';
echo'<div class="col" style="padding: 0px !important;">Scrap: 100</div>';
echo'<div class="col" style="padding: 0px !important;">Level: 1</div>';
echo'<div class="col" style="padding: 0px !important;">Exp: 0</div>';
echo'</div>';
echo'<div class="row" align="center">';
echo'<div class="col" style="padding: 0px !important;">';
echo'Energy';
echo'<div class="progress" style="height: 8px;width: 95%;">';
echo'<div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>';
echo'</div></div>';
echo'<div class="col" style="padding: 0px !important;">';
echo'Hunger';
echo'<div class="progress" style="height: 8px;width: 95%;">';
echo'<div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>';
echo'</div></div>';
echo'<div class="col" style="padding: 0px !important;">';
echo'Hydration';
echo'<div class="progress" style="height: 8px;width: 95%;">';
echo'<div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>';
echo'</div></div>';
echo'</div>';
 echo'</div>';
       echo' <nav id="sidebar" class="sidebar-wrapper  hidden-xs hidden-sm">';
          echo'  <div class="sidebar-content">';
            echo'    <div class="sidebar-item sidebar-brand slogan">';
              echo'      <div id="santa_hat_offline"></div>';
              echo'      <a href="#">Ruin Society</a>';
              echo'  </div>';

              echo'  <div class="sidebar-item sidebar-header d-flex flex-nowrap">';
                echo'  <div class="img__wrap user-pic" style="width: 120px;height:75px;">';

               echo'     <img class="img-responsive img-rounded img__img" id="image" style="border: 1px solid #000;" src="' . $user_class->avatar . '" alt="User picture">';
                    echo'<p class="img__description" align="center"><a href="settings">Change?</a></p>';
               echo'     </div>';
          echo'          <div class="user-info" style="width: 100%">';
     echo'                   <span class="user-status" style="margin-bottom: -2px;">' . $user_class->formattedname . '</span>';
   echo'                     <span class="user-status" style="margin-bottom: -2px;"><font color=yellow>Status:</font> <font color=orange><strong>' . $user_class->rmdays . ' Days Left</strong></font><b style="float:right;color:#5cb85c"><!_-hpperc-_!> HP</b><div class="progress" style="height: 5px;">';
echo'<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div></div></span>';
     echo'                   <span class="user-status" style="margin-bottom: -2px;">Level: <strong><!_-level-_!></strong><b style="float:right;color:#5cb85c"><font color=red><!_-expperc-_!> Exp</b></font>';
echo'<div class="progress" style="height: 5px;">';
  echo'<div class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>';
echo'</div></span>';
               echo'     </div>';
               echo' </div>';


               echo' <div class=" sidebar-item sidebar-menu">';
                   echo' <ul>';
                    echo'    <li class="header-menu" style="margin-bottom: -6px;margin-top: -5px;">';
                    echo'    <span>Energy - 100%</span>';
                    echo'    </li>';
                    echo'    <li>'; 
                   echo'     <div class="progress" style="height: 5px;width: 85% !important;margin: 0 auto !important;">';
                   echo'     <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>';
                  echo'      </div>';
                 echo'       </li>';
                 echo'       <li class="header-menu" style="margin-bottom: -6px;margin-top: -10px;">';
                 echo'       <span>Hunger - 100%</span>';
                echo'        </li>';
                echo'        <li>';
                 echo'       <div class="progress" style="height: 5px;width: 85% !important;margin: 0 auto !important;">';
                 echo'       <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>';
                  echo'      </div>';
                  echo'      </li>';
                   echo'     <li class="header-menu" style="margin-bottom: -6px;margin-top: -10px;">';
                    echo'    <span>Hydration - 100%</span>';
                    echo'    </li>';
                    echo'    <li>'; 
                     echo'   <div class="progress" style="height: 5px;width: 85% !important;margin: 0 auto !important;">';
                     echo'   <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>';
                     echo'   </div>';
                     echo'   </li>';
                  echo'  </ul>';
               echo' </div>';

               echo' <div class=" sidebar-item sidebar-menu">';
                   echo' <ul>';
                        echo'<li class="header-menu">';
                            echo'<span>Game Navigation</span>';
                       echo' </li>';
                       echo' <li><a href="gfx_uploader"><i class="fa fa-file-upload"></i><span class="menu-text">GFX Uploader</span></a></li>';
                  echo'      <li><a href="city"><i class="fa fa-map-marked-alt"></i><span class="menu-text">City</span></a></li>';
             echo'           <li><a href="survivors"><i class="fa fa-users"></i><span class="menu-text">Survivors</span></a></li>';                     
        echo'                <li><a href="tasks"><i class="fa fa-truck"></i><span class="menu-text">Army Base</span></a></li>';
   echo'                     <li><a href="scavenge"><i class="fa fa-search"></i><span class="menu-text">Scavenge</span></a></li>';                    
                        echo'<li><a href="crafting"><i class="fa fa-screwdriver"></i><span class="menu-text">Crafting</span></a></li>';
                     echo'   <li><a href="inventory"><i class="fa fa-box-open"></i><span class="menu-text">Inventory</span></a></li>';
                echo'        <li><a href="medic_tent"><i class="fa fa-h-square"></i><span class="menu-text">Medical Tent</span></a></li>';               
           echo'             <li><a href="trading_post"><i class="fa fa-shopping-cart"></i><span class="menu-text">Trading Post</span></a></li>';
      echo'                  <li><a href="exercise"><i class="fa fa-dumbbell"></i><span class="menu-text">Exercise</span></a></li>';
 echo'                   </ul>';
      echo'          </div>';
 echo'           </div>';
 echo'       </nav>';




echo '<div id="main" class="row">';
echo '<div class="top row">';
echo '<div class="pad">';

echo '</form>';

echo '<div class="spacer"></div>';
echo '</div>';
echo '</div> ';
echo '<div class="middle row">';
echo '<div id="left">';
                

echo '<div class="spacer"></div>';
echo '</div>';


$time = time();
$array = array();
if ($user_class->aprotection > $time) {
    $rtn = howlongtil($user_class->aprotection);
    $array['Attack Protection'] = ($rtn == 'NOW') ? '@None@' : $rtn;
}
if ($user_class->bustpill > 0) {
    $rtn = ($user_class->bustpill);
    $array['Police Badge'] = ($rtn == 'NOW') ? '@None@' : $rtn;
}

if ($user_class->outofjail > 0) {
    $rtn = ($user_class->outofjail);
    $array['Jail Card'] = ($rtn == 'NOW') ? '@None@' : $rtn;
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
if ($user_class->nightvision > 0) {
    echo '<span style="color:red;">Your currently have ' . $user_class->nightvision . ' minutes of Night Vision left.</span><br />';
}

if ($user_class->fbi > 0) {
    echo '<a href="jail.php"><span style="color:green;">You are currently being watched over by the FBI for ' . $user_class->fbi . ' Minutes.</span></a><br />';
}

if ($user_class->fbitime > 0) {
    echo '<a href="home.php"><span style="color:red;">You are currently in FBI Jail for ' . $user_class->fbitime . ' minutes.</span></a><br />';
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
if ($db->num_rows()) {
    print "<a href='ganginvites.php'><span style='color:red;'>You have gang invites!</span></a><br />";
}
echo '<br />';


// COUNTDOWN TIMER
// ADD 000 TO END OF UNIX TIMESTAMP

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

//$("#countdown").html(" Ends In " + days + "d " + hours + "h " + minutes + "m " + seconds + "s ");

echo '<div id="maincontent">';
if (time() <  1673827199) {
    //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Double EXP ACTIVE on all Crimes!</div>';
    echo '<br><div class="pulsate" style="font-family:Creepster;font-size: 2em;color:1e7b00;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href=newcrimes.php><font colour=purple>Double EXP ACTIVE on all Crimes!</a>
            <span id="countdown">Ends In ' . countdown(1673827199) . '</span></div>';
}

if (time() <  1661122799) {
    //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</div>';
    echo '<div class="pulsate" style="font-family:Creepster;font-size: 1.5em;color:1e7b00;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href=bustcontest.php>Our BUST COMPETITION IS currently active! </a>
            <span id="countdown">Ends In ' . countdown(1662965999) . '</span></div>';
}



if ($user_class->claimed == 0 && basename($_SERVER['PHP_SELF']) != 'rmstore.php') {    // The original echo statement for the claim message should be commented out or removed
    // echo '<div style="font-family:Creepster;font-size: 2.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href="rmstore.php?buy=freebie">...</div>';

    // Insert the modal code here
    ?>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
      <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Congratulations!!</h2>

    <p>Here is a nice little bonus. Come back Every Hour To Receive Bonus Points!</p>
    <ul class="gift-list">
        <li><strong>2,500 Points</strong> to boost your score</li>
        <li><strong>+1 Raid Token</strong> for extra raids</li>
    </ul>
    <button onclick="window.location.href='rmstore.php?buy=freebie'" class="claim-button">Claim Gift</button>
</div>

    </div>

   <style>



    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 9999; /* Higher than anything else on the page */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.6); /* Darker black w/ opacity */
    }

    /* Modal Content */
   .modal-content {
    background-color: #333; /* Dark grey background */
    color: #fff; /* White text */
    padding: 20px;
    border: 1px solid #888;
    width: auto; /* Auto width based on content size */
    max-width: 600px; /* Maximum width of the modal */
    box-shadow: 0 0 10px 3px rgba(0, 150, 0, 0.4); /* Green glow */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

    /* The Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* The Claim Link */
    .claim-link {
        color: #04AA6D; /* Adjust if needed */
        text-decoration: underline;
    }

/* Modal content heading and list */
.modal-content h2 {
    color: #FFD700; /* Gold color for the heading */
    text-align: center;
}

.gift-list {
    list-style: none; /* Remove default list styling */
    padding: 0;
    margin: 20px 0; /* Some space above and below the list */
    text-align: left;
}

.gift-list li {
    margin-bottom: 10px; /* Space between list items */
    font-size: 18px; /* Slightly larger font size for readability */
    line-height: 1.6; /* More readable line spacing */
}

/* Styling for the claim button, as previously defined */

.claim-button {
    background-color: #04AA6D; /* Green background */
    color: white; /* White text */
    border: none;
    padding: 15px 30px;
    text-align: center;
    text-decoration: none;
    display: block; /* Make the button a block to fill the modal */
    font-size: 18px;
    margin: 20px auto; /* Center the button */
    cursor: pointer;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2); /* subtle shadow for depth */
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

.claim-button:hover {
    background-color: #037d54; /* Darker green on hover */
}
</style>

    <script>
        // When the user loads the page, open the modal 
        window.onload = function() {
            document.getElementById('myModal').style.display = "block";
        };

        // When the user clicks on <span> (x), close the modal
        document.getElementsByClassName("close")[0].onclick = function() {
            document.getElementById('myModal').style.display = "none";
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = "none";
            }
        };
    </script>
    <?php
}

  $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);

    $debug['worked'] = $bonus_row;



if ($bonus_row['Time'] > 0) {

    $_tt = secondsToHumanReadable($bonus_row['Time'] * 60);
    echo '<div style="font-family:Creepster;font-size: 2.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href="bonuspot.php"><font color=green>Server Wide Double EXP Active: </font>  <font color=white>' . $_tt . '</font> </a>
                                                </div>';
}













if ($user_class->id < 0) {                    //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</div>';
    echo '<div style="font-family:Creepster;font-size: 2.5em;color:green;text-align: center;margin-bottom: 20px;margin-top: -20px;">Get Double Exp on all Crimes/Kills is Active!
                                                </div>';
}





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

    $width = ($user_class->epoints / 1000) * 100;

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
if (time() <= 1703577599) {
    if (pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'valentines') {
        echo '<div class="container">';

        echo '<div class="progress">
                    <div class="progress-bar-heart"><a href="home.php">Activity Reward (' . number_format($width, 1) . '%)</a></div>
                </div>
            </div>';
    }
}

if ($user_class->id == 0) {
    if (pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'valentines') {
        echo '<div class="container">';

        echo '<div class="progress">
                    <div class="progress-bar-heart"><a href="activitycontest.php">Earn Rayzz by playing the game!! ' . number_format($width, 2) . '%</a></div>
                </div>
            </div>';
    }
}

 // echo '<div class="floaty" style="margin-top:20px;font-family:Chewy;font-size:2em;letter-spacing: 6px;text-decoration:none;"><a style="color:#e91137;"href="attackcontest.php">?? TheMafiaLife Attack Contest ??</a></div>';

echo '<script>
var countDownDate = new Date("Jan 30, 2024 23:59:00");

var x = setInterval(function() {

var now = new Date();
var distance = countDownDate - now;

var days = Math.floor(distance / (1000 * 60 * 60 * 24));
var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
var seconds = Math.floor((distance % (1000 * 60)) / 1000);

$(".progress-bar-heart").html("<a href=\'home.php\'>Activity Reward (' . number_format($width, 1) . '%)</a> 2x Speed Boost Active - " + hours + "h " + minutes + "m " + seconds + "s ");

if (distance < 0) {
            clearInterval(x);
            $(".progress-bar-heart").html("<a href=\'home.php\'>Activity Reward (' . number_format($width, 1) . '%)</a>");
}
}, 1000);
</script>';

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

function secondsToHumanReadable($seconds, $requiredParts = null)
{
    $from     = new \DateTime('@0');
    $to       = new \DateTime("@$seconds");
    $interval = $from->diff($to);
    $str      = '';

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
