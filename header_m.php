<?php
// ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);
session_start();
register_shutdown_function('ob_end_flush');
 //ini_set('memcached.sess_prefix', 'memc.sess.ml2.key.1');
//$starttime = microtime_float();
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
        $db->query("SELECT lastClockin, dailyClockins FROM jobInfo WHERE userid = ?");
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
    if ($mail >0) {
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
if (empty($metatitle)) {
    $metatitle = 'TheMafiaLife';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="https://www.w3.org/1999/xhtml">
	
	<head>
<meta https-equiv='cache-control' content='no-cache'>
<meta https-equiv='expires' content='0'>
<meta https-equiv='pragma' content='no-cache'>
	<meta name="viewport" content="width=device-width, user-scalable=yes, minimum-scale=1.0, maximum-scale=1.0"> 
	<title><?php echo $metatitle; ?></title>
    <?php $cet = filemtime('/var/www/html/css/stylemm.css');
echo '<link rel="stylesheet" type="text/css" href="css/stylemm.css?' . $cet . '">';
?>	
	<link href="css/stylem.css" rel="stylesheet" type="text/css" />
    echo '<link rel="stylesheet" href="https://unpkg.com/balloon-css/balloon.min.css">';
echo '<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>';
echo '<script src="js/jquery.tipsy.min.js" type="text/javascript"></script>';
echo '<script src="js/java.js?12" type="text/javascript"></script>';
echo '<script type="text/javascript" src="https://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>';
echo '<link href="https://fonts.googleapis.com/css?family=Chewy|Concert+One|Boogaloo|Germania+One|Bebas+Neue|Creepster" rel="stylesheet">';
echo '<script src="js/jquery.ui.touch-punch.min.js"></script>';
echo '<script src="js/main.js"></script>';
	<link rel="stylesheet" href="stylesheets/classic-upgraded-theme.min.css" />
	<link rel="stylesheet" href="stylesheets/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="stylesheets/jquery.mobile.structure-1.4.5.min.css" /><link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Chewy|Concert+One|Boogaloo|Germania+One|Bebas+Neue|Creepster" rel="stylesheet">

<style>table {
  overflow: scroll;
}
</style>
	<?php if($user_class->globalchat > 0){
        ?>
<style>
  /* Add a red background behind the image */
  a[href="globalchat.php"] img {
    background-color: red;
    padding: 5px; /* Adjust padding as needed */
    border-radius: 5px; /* Optional: Add rounded corners */
  }
</style>
<?php
    }
    ?>
	<script type="text/javascript">
	$(document).on("pagechange", function (toPage, info) {
                if ($.mobile.firstPage && info.options.fromPage && info.toPage && ($.mobile.firstPage == info.options.fromPage) && !$.mobile.firstPage.is('[data-dom-cache="true"]') && (info.toPage.attr('data-url') != info.options.fromPage.attr('data-url'))) {
                    jQuery.mobile.firstPage.remove();
                    $(document).off("pagechange", this);
                }
            });
            </script>
</head>
<style>
</style>
<body>
    <div data-role="page" data-cache="pagechange" data-dom-cache="false">
        <div style="width:100%; margin: 0 auto;">
	<table width="100%" style="height:40px;">
                                  <tr>
                              <td class="box55">
                        <img src="images/creditcoin.png"> Credits: <!_-credits-_!> &nbsp;
	      <a href="rmstore.php" rel="external" style='color:#fff;'>    <div class="buttoncityd">Store</div></a>

                    </td>
                            
                               <td  class="box55" style="text-align:center;">
                        <a href="pms.php?view=inbox" rel="external"><img src="https://s2.themafialife.com/imageicons/email2.png">
                            <?php
                            $qmail = mysql_query("SELECT viewed FROM pms WHERE `to` = ".$user_class->id." AND viewed = 1");
                            $mail = mysql_num_rows($qmail);
                            if($mail > 0){
                                ?>
                            <span class="current">
                                <span class='notif'><!_-mail-_!></span>
                            </span>
                        <?php } ?>
                        </a>

         <?php $equery = mysql_query("SELECT viewed FROM events WHERE `to` = ".$user_class->id." AND viewed = 1");
                            $events = mysql_num_rows($equery);
                            ?>
                        <a href="events.php" rel="external"><img src="imageicons/error.png">
                            <?php if($events > 0){ ?>
                            <span class="current"><span class='notif'><!_-events-_!></span> </span>
                          <?php } ?>
                            </a>
                            
                            <a href="globalchat.php" rel="external"><img src="imageicons/comments.png"></a>
                            <a href="gameupdates.php"><img src="imageicons/newspaper.png">
                            <?php if ($user_class->game_updates > 0) {
                                ?>
                                <span class="current"><span class='notif'><!_-gupdates-_!></span> </span>
                                <?php } ?>
                            </a>

                    </td>
                        </tr>
                    
                        </table>
                        
                  <table width="100%" class="graytable">
                        <tr>
                    <td align="center">
                            <a href="preferences.php" rel="external">
<?php	     

	      if($user_class->avatar)
{
print "
<img src='{$user_class->avatar}' width='40px' height='40px' style='border: 1px #777 solid;' alt='User Display Pic' title='User Display Pic' />";
}
else
{
print "
<img src='images/defaultmale.jpg' width='40px' style='border: 1px #777 solid;' height='40px' alt='Default Male' title='Default Male' />";
}     
	
	
?>	      </a>
                    </td>
                    <td class="box1">
                    <center>
                        
                        <?php if($ir['vip']>0) {echo'  <a href="gym.php" rel="external"><img src="images/energy.gif">';}else {echo'  <a href="gym.php" rel="external"><img src="images/energy.gif">';} ?><div class="levelbarexp" style="width: 50px;height: 5px;">
				<div class="experiencebare" style="width: <!_-energyperc-_!>%;height: 5px;"></div></div><span class="current"><!_-formenergy-_!></span></center> </a></b> 

                           </td>
                           

                     <td class="box1">
                    <center><a href="house.php" rel="external"><img src="images/icon-will.png"><div class="levelbarexp" style="width: 50px;height: 5px;">
				<div class="experiencebar2" style="width:<!_-awakeperc-_!>%;height: 5px;"></div></div><span class="current"><!_-formawake-_!></span></center> </a></b> 

                           </td>
                           
                            <td class="box1">
                    <center><a href="crime.php" rel="external"><img src="images/gun-icon.png"><div class="levelbarexp" style="width: 50px;height: 5px;">
				<div class="experiencebar3" style="width: <!_-nerveperc-_!>%;height: 5px;"></div></div> <span class="current"><!_-formnerve-_!></span></center> </a></b> 

                           </td>
                  
                   
</tr>
</table>
  <table width="100%" class="graytable2" align="center">
                <tr>
                    
                    <td>
                            <?php if(isset($gn)){echo $gn;}
                           ?>
                             <?php echo formatName($user_class->id); ?>

<img src="imageicons/heart.png"> <span class="current"><!_-hpperc-_!></span>
                    </td>
                   
      
                    <td>
	         
				
	          <span class="darkfont"><img src='images/icons-png/star-white.png'> Level</span> <?php echo $user_class->level;?>
  <div class="levelbarexp" style="width: 100px;height: 5px;">
				<div class="experiencebar" style="width: <!_-expperc-_!>%;height: 5px;"></div></div>
				 XP <span class="current"><!_-formexp-_!></span> 
	      </td> 
	      
	      <td>
                        <?php
      echo"<center><img src='images/icons-png/location-white.png'> <!_-cityname-_!> <a href='travel.php' rel='external'> <div class='buttoncity'>Travel</div></a>"; ?></center></td>
      
                        </tr>
                        </table>
                        
                        <table width="100%">
                        <tr class="box55">
                              <td>
   	      
 
          <?php 

        echo'<img src="images/cash_icon.gif"> Cash: <font color=limegreen>$'.number_format($user_class->money).'</font> <a href="bank.php">[depo]</a><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <small>Bank: <font color=green>$'.number_format($user_class->bank).'</font></small></td>
        <td>
        <img src="imageicons/ruby.png"> Points: '.number_format($user_class->points).' <a href="spendpoints.php">[use]</a><br /><img src="imageicons/chart_bar.png">
        '.get_users_online().' <a href="online.php">Users Online</a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        
        '; ?>

      </td>
                        </tr>
                    
                        </table>
  
<?php
echo'<div class="mainmenu" valign="top">';
	//}
	?>
    <a href="index.php" data-role="button" data-icon="home" data-mini="true" data-theme="a" class="bluediv" rel="external" data-iconpos="left">Home</a>
<a href="city.php" data-role="button" data-icon="location" data-mini="true" data-theme="a" class="bluediv" rel="external" data-iconpos="left">Explore</a>
<a href="inventory.php" data-role="button" rel="external" data-icon="grid" data-theme="a" class="bluediv2" data-iconpos="left" data-mini="true">Inventory</a>	
<?php
if($user_class->id == 9){
    echo '<a class="open-button" data-theme="a"  data-icon="grid" popup-open="popup-1" href="javascript:void(0)"> Popup 
        Preview</a>';
}
?>
<script>
$(function() {
    // Open Popup
    $('[popup-open]').on('click', function() {
        var popup_name = $(this).attr('popup-open');
 $('[popup-name="' + popup_name + '"]').fadeIn(300);
    });
 
    // Close Popup
    $('[popup-close]').on('click', function() {
 var popup_name = $(this).attr('popup-close');
 $('[popup-name="' + popup_name + '"]').fadeOut(300);
    });
 
    // Close Popup When Click Outside
    $('.popup').on('click', function() {
 var popup_name = $(this).find('[popup-close]').attr('popup-close');
 $('[popup-name="' + popup_name + '"]').fadeOut(300);
    }).children().click(function() {
 return false;
    });
 
});
</script>
<style>
  .responsive {
    width: 100%;
    border-collapse: collapse;
    overflow-x: auto;
  }

  /* Style the table header */
  .responsive th, .responsive td {
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #ddd;
  }

  /* Apply media query for small screens */
  @media only screen and (max-width: 750px) {
    .avatar{
        max-width: 80px;
        max-height: 80px;
    }
    .bottom.row {
    background: none;
}
table td{
    border:0;
}
table th{
    border:0;
}


    /* Add more responsive styling as needed */
  }
    .open-button{
    color:#FFF;
    background:#0066CC;
    padding:10px;
    text-decoration:none;
    border:1px solid #0157ad;
    border-radius:3px;
}
 
.open-button:hover{
    background:#01478e;
}
 
.popup {
    position:fixed;
    top:0px;
    left:0px;
    background:rgba(0,0,0,0.75);
    width:100%;
    height:100%;
    display:none;
}
 
/* Popup inner div */
.popup-content {
    width: 500px;
    margin: 0 auto;
    box-sizing: border-box;
    padding: 40px;
    margin-top: 20px;
    box-shadow: 0px 2px 6px rgba(0,0,0,1);
    border-radius: 3px;
    background: #fff;
    position: relative;
}
 
/* Popup close button */
.close-button {
    width: 25px;
    height: 25px;
    position: absolute;
    top: -10px;
    right: -10px;
    border-radius: 20px;
    background: rgba(0,0,0,0.8);
    font-size: 20px;
    text-align: center;
    color: #fff;
    text-decoration:none;
}
 
.close-button:hover {
    background: rgba(0,0,0,1);
}
 
@media screen and (max-width: 720px) {
.popup-content {
    width:90%;
    } 
}
</style>
 
<div class="popup" popup-name="popup-1">
            <div class="popup-content">
            <h2>Model </h2>
        <p>Model content will be here. Lorem ipsum dolor sit amet, 
        consectetur adipiscing elit. Aliquam consequat diam ut tortor 
        dignissim, vel accumsan libero venenatis. Nunc pretium volutpat 
        convallis. Integer at metus eget neque hendrerit vestibulum. 
        Aenean vel mattis purus. Fusce condimentum auctor tellus eget 
        ullamcorper. Vestibulum sagittis pharetra tellus mollis vestibulum. 
        Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <a class="close-button" popup-close="popup-1" href="javascript:void(0)">x</a>
            </div>
        </div>  
</div>

	<?php
	//Main game starts
	print'<div class="maingame" valign="top" align="center">';
    
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
if ($user_class->cityturns >= 50) {
    echo '<span class="help3"><a href="maze.php">You have MAX Turns to search the maze!.</a></span>';
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

}
$db->query("SELECT * FROM ganginvites WHERE playerid = ?");
$db->execute(array(
    $user_class->id
));
if ($db->num_rows()) {
    print "<a href='ganginvites.php'><span style='color:red;'>You have gang invites!</span></a><br />";
}

    if ($userInfo['dailyClockins'] < 8 && $jobInfo['lastClockin'] < time() - 3600) {
        echo '<div class="help">You have not Clocked in this hour! </font> </a>
        </div>';   


        
      print "<div class='feed' style='border-bottom:2px solid #222;'>";
                
             echo'</div>';
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
        <h2>Congratulations! - Black Friday Special!</h2>
        <p>With all new Black Friday Packages in store + 2 for 1 on all credits!:</p>
    
        <p>You've received a free gift package:</p>
        <ul class="gift-list">
            <li><strong>10,000 Points</strong> to boost your score</li>
            <li><strong>x1 Double EXP Pill</strong> for rapid advancement</li>
            <li><strong>+1 Raid Token</strong> for extra raids</li>
            <li><strong>+1 Donation Token</strong> to spend in the game store</li>
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
    

    
    
    // Fetch dailyClockins from grpgusers
    $db->query("SELECT dailyClockins FROM grpgusers WHERE id = ?");
    $db->execute(array($user_class->id));
    $userInfo = $db->fetch_row(true);
    
    // Fetch lastClockin from jobInfo
    $db->query("SELECT lastClockin FROM jobInfo WHERE userid = ?");
    $db->execute(array($user_class->id));
    $jobInfo = $db->fetch_row(true);
  
    
    
    if ($user_class->id < 0) {                    //echo '<div class="floaty" style="margin-top:-10px;font-family:Creepster;font-size:2em;text-decoration:underline;color:orange;">Black Friday! - DOUBLE CRIME EXP ACTIVE</div>';
        echo '<div style="font-family:Creepster;font-size: 2.5em;color:green;text-align: center;margin-bottom: 20px;margin-top: -20px;">Get Double Exp on all Crimes/Kills is Active!
                                                    </div>';
    }
    
    
    
// $q=$db->query("SELECT * FROM livefeed ORDER BY evTIME DESC LIMIT 1");
//  if ($db->num_rows($q) == 0)
//   {
//   print "<div class='feed' style='border-bottom:2px solid #222;'>";
// 		  print "<b>No recent news available at this current time!</b>";
// 		  echo"<br /> Players Online <a href='usersonline.php'>[".$online."]</a>";
// 		 echo'</div>';
//   }
//   else
//   {
//     while($ii=$db->fetch_row($q))
//       {
//                $LA1 = time() - $ii['evTIME'];
//                $Unit22 = " secs ago <small><font color=red>NEW!</font></small>";
//                if($LA1 >= 60)
//                {
//                   $LA1 = (int) ($LA1/60);
//                   $Unit22 = " mins ago";
//                }
//                if($LA1 >= 60)
//                {
//                   $LA1 = (int) ($LA1/60);
//                   $Unit22 = " hours ago";
//                   if($LA1 >= 24)
//                   {
//                      $LA1 = (int) ($LA1/24);
//                      $Unit22 = " days ago";
//                   }
//                }
//       print "<div class='feed' style='border-bottom:2px solid #222;'>";
// 		  print "{$ii['evTEXT']}  - <span class='sent'>";
// 		  echo''. @intval($LA1),$Unit22 .'';
// 		  echo"<br /> Players Online <a href='usersonline.php'>[".$online."]</a>";
// 		 echo'</div>';
//       }
//   }		$date = date('F j, Y');
// 	$hour = date('H');
// 	$minute = date('i');
// 	$second = date('s');
// 	$ampm = date('a');
	
// 	echo'<center>
// 		<a href="" class="datetime">'.$date.' <span id="chas">'.$hour.'</span>:<span id="minuti">'.$minute.'</span>:<span id="sekundi">'.$second.'</span></a> </center><br />';
// 			include_once 'shoutbox.php';
// 	if($ir['tutorial']==0)
// {
// 	echo"<a href='tutorial1.php' data-role='button' data-icon='star' rel='external'>Claim <font color=orange>Bonus</font></a>";

// }  
// 			include_once 'checks5.php';
// 			if($ir['hp'] == 0) {
// 		print "<span class='help'>Notice: You are out of health! Buy a Private Nurse to refill it. <a href='bmarket.php?action=redeem&item=2' rel='external' data-theme='b' rel='external' data-inline='true'><img src='https://mafiamobi.com/imageicons/ruby.png'>Buy now</a></span><br />";
// 	  }	
	  
			  
// 				if($ir['hospital'] > 0) {
// 		print "<span class='help'>Notice: You are currently in hospital for {$ir['hospital']} minutes. Use morphine in your <a href='inventory.php' rel='external'>inventory</a> to recover!</span>";
// 	  }	
// 	  if($ir['jail'] > 0) {
// 		print "<span class='help'>Notice: You are currently in jail for {$ir['jail']} minutes. Have <a href='yourgang.php' rel='external'>your gang</a> member bust you out!</span>";


// 	  }
//     	  if($ir['amount'] >0) {
// 		print "<a href='compensation.php' data-role='button' rel='external'  data-theme='e' rel='external' data-icon='star' data-inline='true'> Claim Promo</a><br />";


// 	  }
	 

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
if (time() <= 1690757999) {
if (pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) != 'valentines') {
    echo '<div class="container">';

    echo '<div class="progress">
                <div class="progress-bar-heart"><a href="activitycontest.php">Rayz (' . number_format($width, 1) . '%)</a></div>
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
var countDownDate = new Date("Jan 30, 2023 23:59:00");

var x = setInterval(function() {

var now = new Date();
var distance = countDownDate - now;

var days = Math.floor(distance / (1000 * 60 * 60 * 24));
var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
var seconds = Math.floor((distance % (1000 * 60)) / 1000);

$(".progress-bar-heart").html("<a href=\'activitycontest.php\'>Rayz (' . number_format($width, 1) . '%)</a> 2x Speed Boost Active - " + hours + "h " + minutes + "m " + seconds + "s ");

if (distance < 0) {
        clearInterval(x);
        $(".progress-bar-heart").html("<a href=\'activitycontest.php\'>Rayz (' . number_format($width, 1) . '%)</a>");
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

?>