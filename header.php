<?php

session_start();

// Get the name of the current script and the full request URI to check for specific query parameters
$current_page = basename($_SERVER['PHP_SELF']); // Gets the name of the current script
$current_uri = $_SERVER['REQUEST_URI']; // Gets the full request URI

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
    exit();
}
if ($row['sessionid'] != $_COOKIE['PHPSESSID'] && $_SESSION['id'] != 0) {
    $sessid = $_SESSION['id'];
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

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
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://mafialords.com/');
    }
    if ($_GET['spend'] == "refawake") {
        $cost = 100 - floor(100 * ($user_class->directawake / $user_class->directmaxawake));
        if ($user_class->awakepercent != 100 && $user_class->points >= $cost) {
            $user_class->points -= $cost;
            $user_class->directawake = $user_class->directmaxawake;
            mysql_query("UPDATE grpgusers SET awake = $user_class->directmaxawake, points = points - $cost WHERE id = $user_class->id");
        }
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: https://mafialords.com/');
    }
    if ($_GET['spend'] == "refnerve") {
        manual_refill('n');
        if (isset($_GET['crime'])) {
            header('Location: crime.php');
        } elseif ($_SERVER['HTTP_REFERER']) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: https://mafialords.com/');
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

if (empty($metatitle)) 




$activeRaidsQuery = "SELECT COUNT(*) AS activeRaidsCount FROM active_raids WHERE completed = 0"; // Replace 'end_time' with the actual column name that represents when the raid ends
$activeRaidsResult = mysql_query($activeRaidsQuery);
$activeRaidsData = mysql_fetch_assoc($activeRaidsResult);
$activeRaidsCount = $activeRaidsData['activeRaidsCount'];
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php
		echo ucwords(str_replace($headFind, $headRepl, str_replace('.php', '', $self))); ?> - <?php
		echo $set['game_name']; ?></title>
<link href="assets/css/game.css" type="text/css" rel="stylesheet" />
<link type="text/css" rel="stylesheet" href="assets/css/template.css"  />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//js.pusher.com/2.2/pusher.min.js"></script>
<script type="text/javascript" language="javascript" src="/gradient.js"></script>
<script type="text/javascript" src="/farbtastic/farbtastic.js"></script>
<link rel="stylesheet" href="/farbtastic/farbtastic.css" type="text/css" />
<script type="text/javascript">
	var currenttime = '<?php
		print date("F d, Y H:i:s", time()) ?>' //PHP method of getting server date
	var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
	var serverdate=new Date(currenttime)

	function padlength(what) {
		var output=(what.toString().length==1)? "0"+what : what
		return output
	}
	function displaytime() {
		serverdate.setSeconds(serverdate.getSeconds()+1)
		var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear()
		var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())
		document.getElementById("servertime").innerHTML=datestring+" Server Time: "+timestring
	}
	window.onload=function() {
		setInterval("displaytime()", 1000)
	}
</script>
</head>
<body>
<div id="outer" class="wrap">
	<div id="inner" class="wrap">
		<div id="header" class="row"></div>
		<div id="main_box" class="row">
        <div class="top_level row">
				<div class="left_side">
					<div id="avatar"></div>
				<br />
					<strong><span style='font-size:17px;'>Level <?php
		echo ($ir['level']); ?></span></strong>
				</div>
				<div class="center_side">
					<div id="links">
						<a href="/mailbox.php">Mail (<?php echo $ir['new_mail'] ?>)</a> -
						<a href="/events.php">Events(<?php echo $ir['new_events'] ?>)</a> -
						<a href="/forums.php">Forum</a> -
						<a href="/helptutorial.php">Help</a> -
						<a href="/changelog.php"><strong>Change Log</strong></a>
						<a href="/todo.php"><strong>To Do List</strong></a>
					</div>
					<div id="logo">
						<a href="/usersonline.php"><?php echo $count ?> Online Players</a>
					</div>
				</div>
				<div class="right_side">
					<div class="info_slot">
						<span><?php echo $money
?></span>
						Cash
					</div>
					<div class="info_slot">
						<span><?php echo $crystals
?></span>
						Points
					</div>
					<div class="info_slot">
						<span><?php echo ($ir['bankmoney'] >= 0) ? ($ir['bankmoney']) : 'No Account'; ?></span>
						Bank
					</div>
					<div class="info_slot">
						<span><?php echo $ir['credits'] ?></span>
						Credits
					</div>
					<div class="spacer"></div>
				</div>
				<div class="spacer"></div>
			</div>
			<div class="red_bar row">
				<div id="stat_section">
					<div class="stats">
						<div class="l-text">ENERGY</div><div class="r-text"><?php echo $ir['energy'] ?>/<?php echo $ir['maxenergy'] ?></div>
						<div class="spacer"></div>
						<div style="background: url(../images/stat-bar-bg.png) top center no-repeat;width: 147px;height: 22px;">
							<div class="stat-bar">
								<div style="background: url(../images/yellow-stat-bar.png) no-repeat;height: 8px;width:<?php echo $enperc ?>%;"></div>
							</div>
						</div>
					</div>
					<div class="stats">
						<div class="l-text">NERVE</div><div class="r-text"><?php echo $ir['brave'] ?>/<?php echo $ir['maxbrave'] ?></div>
						<div class="spacer"></div>
						<div style="background: url(../images/stat-bar-bg.png) top center no-repeat;width: 147px;height: 22px;">
							<div class="stat-bar">
								<div style="background: url(../images/yellow-stat-bar.png) no-repeat;height: 8px;width:<?php echo $brperc ?>%;"></div>
							</div>
						</div>
					</div>
					<div class="stats">
						<div class="l-text">HP</div><div class="r-text"><?php echo $ir['hp'] ?>/<?php echo $ir['maxhp'] ?></div>
						<div class="spacer"></div>
						<div style="background: url(../images/stat-bar-bg.png) top center no-repeat;width: 147px;height: 22px;">
							<div class="stat-bar">
								<div style="background: url(../images/yellow-stat-bar.png) no-repeat;height: 8px;width:<?php echo $hpperc ?>%;"></div>
							</div>
						</div>
					</div>
					<div class="stats">
						<div class="l-text">RAGE</div><div class="r-text"><?php echo $wiperc ?>%</div>
						<div class="spacer"></div>
						<div style="background: url(../images/stat-bar-bg.png) top center no-repeat;width: 147px;height: 22px;">
							<div class="stat-bar">
								<div style="background: url(../images/red-stat-bar.png) no-repeat;height: 8px;width:<?php echo $wiperc ?>%;"></div>
							</div>
						</div>
					</div>
					<div class="spacer"></div>
				</div>
				<div id="link_section">
					<div id="link_box">
						<a href="/vote.php">Vote</a> || <a href="/donate.php">Donate</a> || <a href="/logout.php">Logout</a>
					</div>
				</div>
				<div class="spacer"></div>
			</div>
			<div class="content row">
				<div class="menu_side">
					<ul class="mainmenu">

    <?php
    echo '<center>';
echo '<div class="spacer"></div>';
echo '</div>';
echo '<div class="spacer"></div>';
echo '<div class="spacer"></div>';


// Echo out the HTML with fixed navbar
echo '<body style="font-weight: normal !important; padding-top: 0rem;">'; // padding-top to avoid overlap with the fixed top bar
echo '    <div class="page-wrapper default-theme sidebar-bg bg2 toggled">';
echo '        <nav class="top-bar" id="topBar">';
echo '            <div class="top-bar-container">';
// Conditionally add "glow-pulse" class
$classForGameUpdates = $user_class->new_updates > 0 ? 'glow-pulse' : '';
echo '            <a href="gameupdates.php" class="top-bar-link ' . $classForGameUpdates . '" style="color: ' . ($user_class->new_updates > 0 ? 'red' : 'white') . ' !important;">Game Updates</a>';
// Echo other links as they were
echo '                <a href="news.php" class="top-bar-link" style="color: white !important;">Game News[<font color=red><!_-news-_!></font>]</a>';
echo '                <a href="VIPstore.php" class="top-bar-link" style="color: yellow !important;">VIP Store</a>';
echo '                <a href="online.php" class="top-bar-link" style="color: white !important;">' . get_users_online() . ' Players Online</a>';
echo '                <a href="VIPstore.php" class="top-bar-link" style="color: white !important;">Points:<font color=yellow> <span class="points">' . number_format($user_class->points) . '</font></span></a>';
echo '                <a href="bank.php?dep" class="top-bar-link" style="color: white !important;">Money: <font color=green>$<span class="money">' . number_format($user_class->money) . '</font></span></a>';
echo '                <a href="#" class="top-bar-link" style="color: white !important;">Bank: <font color=green>$<span class="bank-amount">' . number_format($user_class->bank) . '</font></span></a>';
echo '                <a href="VIPstore.php" class="top-bar-link" style="color: white !important;"><img src="https://mafialords.com/mafialordsicons/goldbar.png"></img> <span class="gold-amount"><b><font color=yellow>' . number_format($user_class->credits) . '</b></font></span></a>';
echo'<a href="index.php?action=logout" class="logout-button">> Logout <</a>';
echo '            </div>';
echo '        </nav>';




echo'<div class="row" align="center">';





 echo'</div>';
       echo' <nav id="sidebar" class="sidebar-wrapper">';
          echo'  <div class="sidebar-content">';
            echo'    <div class="sidebar-item sidebar-brand slogan">';
              echo'     <a href="#">
  <img src="https://mafialords.com/mlordsimages/smalllogo.png" alt="" />
</a>
';
              echo'  </div>';

              echo'  <div class="sidebar-item sidebar-header d-flex flex-nowrap">';
                echo'  <div class="img__wrap user-pic" style="width: 120px;height:75px;">';

               echo'     <img class="img-responsive img-rounded img__img" id="image" style="border: 1px solid #000;" src="' . $user_class->avatar . '" alt="User picture">';
                    echo'<p class="img__description" align="center"><a href="preferences.php">Edit Account</a></p>';
               echo'     </div>';
          echo'          <div class="user-info" style="width: 100%">';
     echo'                   <span class="user-status" style="margin-bottom: -2px;">' . $user_class->formattedname . '</span>';
   echo'                     <span class="user-status" style="margin-bottom: -2px;"><font color=yellow>Status:</font> <font color=orange><strong>' . $user_class->rmdays . ' Days Left</strong></font><b style="float:right;color:#5cb85c"><!_-hpperc-_!> HP</b><div class="progress" style="height: 5px;">';
echo'<div class="progress-bar bar-health" role="progressbar" style="width: <!_-hpperc-_!>%;" aria-valuemin="0" aria-valuemax="100"></div></div></span>';
     echo'                   <span class="user-status" style="margin-bottom: -2px;">Level: <strong><!_-level-_!></strong><b style="float:right;color:#5cb85c"><font color=red><!_-expperc-_!>% Exp</b></font>';
echo'<div class="progress" style="height: 5px;">';
  echo'<div class="progress-bar bar-exp" role="progressbar" style="width: <!_-expperc-_!>%;" aria-valuemin="0" aria-valuemax="100"></div>';
echo'</div></span>';

               echo'     </div>';
               echo' </div>';
echo '<span id="dateTime">' . date('m/d/Y h:i:s a', time()) . '</span>';

echo '<div class="sidebar-item sidebar-menu" style="position: relative; background-image: url(\'mlordsimages/sidemenu1.png\'); background-size: cover; background-repeat: no-repeat; background-position: center center;">';
echo '<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5);"></div>';
echo '<ul>';

echo '<li class="header-menu menu-item-no-triangle">';
echo '<div class="progress-labels lithograph-text">';
echo '<span onclick="redirectIt(this)" href="?spend=refenergy" class="label-left" id="energy-link">Energy - ' . $user_class->energy . ' / ' . $user_class->maxenergy . '</span>';

echo '<span class="label-right">' . $user_class->energypercent . '%</span>';
echo '</div>';
echo '<div class="progress floaty-background">';
echo '<div class="progress-bar bar-energyy" role="progressbar" style="width: ' . $user_class->energypercent . '%" aria-valuemin="0" aria-valuemax="100"></div>';
echo '</div>';

echo '<div class="progress-labels lithograph-text">';
echo '<span onclick="redirectIt(this)" href="?spend=refnerve" class="label-left" id="nerve-link">Nerve - ' . $user_class->nerve . ' / ' . $user_class->maxnerve . '</span>';
echo '<span class="label-right">' . $user_class->nervepercent . '%</span>';
echo '</div>';
echo '<div class="progress floaty-background">';
echo '<div class="progress-bar bar-nerve1" role="progressbar" style="width: ' . $user_class->nervepercent . '%" aria-valuemin="0" aria-valuemax="100"></div>';
echo '</div>';

echo '<div class="progress-labels lithograph-text">';
echo '<span class="label-left">Awake - ' . $user_class->awake . ' / ' . $user_class->maxawake . '</span>';
echo '<span class="label-right">' . $user_class->awakepercent . '%</span>';
echo '</div>';
echo '<div class="progress floaty-background">';
echo '<div class="progress-bar bar-awake1" role="progressbar" style="width: ' . $user_class->awakepercent . '%" aria-valuemin="0" aria-valuemax="100"></div>';
echo '</div>';
echo '</li>';

echo '</ul>';
echo '</div>';



              echo '<div class="sidebar-item sidebar-menu">';
echo '    <ul>';
echo '<h4>NAVIGATION BAR</h4>';
echo '<li><a href="search.php"><span class="menu-text">Search Players</span></a></li>';
echo '    <li><a href="globalchat.php"><span class="menu-text"><font color=orange>Global Chat <span class="gamechat"> [<!_-gchat-_!>]</font></span></a></li>';
echo '<li><a href="index.php"><span class="menu-text">Home</span></a></li>';

echo '        <li><a href="city.php"><span class="menu-text"><div class="gradient-background"><!_-cityname-_!></div></a></li>';


$classForGameUpdates = $user_class->diamonds > 0 ? 'glow-pulse' : '';

echo '<li class="' . $classForGameUpdates . '"><a href="pms.php?view=inbox"><span class="menu-text">Mail [<span class="mailbox">' . $mail . '</span>]</span></a></li>';


echo '<li><a href="events.php"><span class="menu-text">Events [<span class="events">' . $eventCount . '</span>]</span></a></li>';

$classForGameUpdates = $eventCount >+ 1 ? 'glow-pulse' : ''; // Apply 'glow-pulse' class if there are unviewed events



echo '        <li><a href="missions.php"><span class="menu-text">Missions</span></a></li>';    

echo '        <li><a href="inventory.php"><span class="menu-text">Inventory</span></a></li>';
echo '        <li><a href="raids.php"><span class="menu-text"><font color=yellow>Raids Section</span></a></li>';
echo '        <li><a href="backalley.php"><span class="menu-text"><font color=yellow>Backalley</span></a></li>';
    
// Check if the user is in a gang
if($user_class->gang == 0) {
    echo '        <li><a href="creategang.php"><span class="menu-text">Create Gang</span></a></li>';
} else {
    echo '        <li><a href="gang.php"><span class="menu-text">Your Gang</span></a></li>';
    echo '        <li><a href="gangmail.php"><span class="menu-text">Gang Mail [<!_-gmail-_!>]</span></a></li>';
    echo '        <li><a href="gangcontest.php"><span class="menu-text">Gang Contest</span></a></li>';
}

echo '        <li><a href="bank.php"><span class="menu-text">Bank</span></a></li>';    
echo '<li><a href="jail.php"><span class="menu-text">Jail [' . (int)$userJailCount . ']</span></a></li>';
                 echo '        <li><a href="hospital.php"><span class="menu-text">Hospital </span></a></li>';


echo '        <li><a href="crime.php"><span class="menu-text">Crimes</span></a></li>';
echo '        <li><a href="newcrimes.php"><span class="menu-text">Crimes <font colour=red>(SPEED)</font></span></a></li>';

echo '        <li><a href="gym.php"><span class="menu-text">Gym</span></a></li>';                    
echo '        <li><a href="preferences.php"><span class="menu-text">Edit Account</span></a></li>';               
echo '        <li><a href="forum.php"><span class="menu-text">Forums</span></a></li>';
echo '        <li><a href="support.php"><span class="menu-text">Support Desk</span></a></li>';

// Check if the user is an admin
if ($user_class->admin != 0) {
    echo '        <li><a href="gmpanel.php"><span class="menu-text">Staff Panel</span></a></li>';
}

echo '    </ul>';
echo '</div>';

 
 
 
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


if ($user_class->bustpill > 0) {
    $rtn = ($user_class->bustpill);
    $array['Police Badge'] = ($rtn == 'NOW') ? '@None@' : $rtn;
}

if ($bonus_row['Time'] > 0) {

    $_tt = secondsToHumanReadable($bonus_row['Time'] * 60);
    echo '<div style="font-family:timesnewroman;font-size: 1.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><font color=green>Server Wide Double EXP Active </font>  <font color=white>' . $_tt . '</font> 
                                                </div>';
}

if ($user_class->outofjail > 0) {
    $rtn = ($user_class->outofjail);
    $array['Jail Card'] = ($rtn == 'NOW') ? '@None@' : $rtn;
}

if ($user_class->news > 0) {
    $buffer = str_replace("<!_-news-_!>", "<div class='contenthead floaty'><span style='margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;'><h4 class='notify important'><a href='forum.php?id=1'>You have new game news [<span class='news-count'>$user_class->news</span>]</a></h4></span></div>", $buffer);

} else {
    if ($user_class->mjprotection > $time) {
        $rtn = howlongtil($user_class->mprotection);
        $array['Mug Protection'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
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

$db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);

    $debug['worked'] = $bonus_row;



if ($bonus_row['Time'] > 0) {

    $_tt = secondsToHumanReadable($bonus_row['Time'] * 60);
   $messages[] = 'Attackgfgdgdfgdfgsdfg: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
   
}

$time = time();
$messages = array();


// Attack Protection
if ($user_class->aprotection > $time) {
    $rtn = howlongtil($user_class->aprotection);
    $messages[] = 'Attack Protection: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
}

$db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);

    $debug['worked'] = $bonus_row;



if ($bonus_row['Time'] > 0) {

    $_tt = secondsToHumanReadable($bonus_row['Time'] * 60);
   $messages[] = 'Server Wide Double EXP: ' . (($_tt == 'NOW') ? '@None@' : $_tt);
   
}

if ($user_class->cityturns > 29) {
    $messages[] = '<a href="maze.php">You Have Maze Searches Available</a>';
}
if ($user_class->id > 0) {
    $messages[] = '<a href="contest.php"><font color=red>Raid/Attack Comp Active</font></a>';
}
$db->query("SELECT * FROM ganginvites WHERE playerid = ?");
$db->execute(array($user_class->id));
if ($db->num_rows() > 0) {
    // Adding gang invites message to the $messages array instead of printing directly
    $messages[] = "<a href='ganginvites.php'><span style='color:red;'>You have gang invites!</span></a>";
}

// Bust Pill
if ($user_class->bustpill > 0) {
    $rtn = ($user_class->bustpill);
    $messages[] = 'Police Badge: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
}

// Out of Jail
if ($user_class->outofjail > 0) {
    $rtn = ($user_class->outofjail);
    $messages[] = 'Jail Card: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
}


// Mug Protection
if ($user_class->mprotection > $time) {
    $rtn = howlongtil($user_class->mprotection);
    $messages[] = 'Mug Protection: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
}

// Double EXP Pill
if ($user_class->exppill > $time) {
    $rtn = howlongtil($user_class->exppill);
    $messages[] = 'Double EXP Pill: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
}



// Jail
if ($user_class->jail > $time) {
    $rtn = howlongtil($user_class->jail);
    $messages[] = 'Jail: ' . (($rtn == 'NOW') ? '@None@' : $rtn);
}

// Additional messages based on your previous code snippets
if ($user_class->hospital > 0) {
    $messages[] = 'You are currently in hospital for ' . $user_class->hospital . ' seconds.';
}

if ($user_class->jail > 0) {
    $messages[] = 'You are currently in jail for ' . $user_class->jail . ' seconds.';
}

if ($user_class->nightvision > 0) {
    $messages[] = 'Your currently have ' . $user_class->nightvision . ' minutes of Night Vision left.';
}

if ($user_class->fbi > 0) {
    $messages[] = 'You are currently being watched over by the FBI for ' . $user_class->fbi . ' Minutes.';
}

if ($user_class->fbitime > 0) {
    $messages[] = 'You are currently in FBI Jail for ' . $user_class->fbitime . ' minutes.';
}

if (!empty($messages)) {
    echo '<script type="text/javascript">';
    echo 'document.addEventListener("DOMContentLoaded", function() {';
    // Initialize the messages HTML as an empty string
    $messagesHtml = '';
    foreach ($messages as $index => $message) {
        $escapedMessage = addslashes($message);
        // For all but the first message, add a separator before the message
        if ($index > 0) {
            $messagesHtml .= ' + \' <span style="margin: 0 10px;">&bull;</span> \' + ';
        }
        $messagesHtml .= '\'<span>' . $escapedMessage . '</span>\'';
    }
    if (!empty($messagesHtml)) {
        echo 'var messageContainer = document.getElementById("message-container");';
        echo 'messageContainer.style.display = "flex";'; // Use flexbox for side by side display
        echo 'messageContainer.style.alignItems = "center";'; // Center align items for a better look
        echo 'messageContainer.style.justifyContent = "center";'; // Center the content horizontally
        echo 'var messagesElement = document.createElement("div");'; // Create a new div for messages
        echo 'messagesElement.innerHTML = ' . $messagesHtml . ';'; // Set the inner HTML of the div
        echo 'document.getElementById("messages").appendChild(messagesElement);'; // Append the div to the container
    }
    echo '});';
    echo '</script>';
} else {
    // Hide the container if there are no messages
    echo '<script type="text/javascript">';
    echo 'document.addEventListener("DOMContentLoaded", function() {';
    echo 'var messageContainer = document.getElementById("message-container");';
    echo 'if (messageContainer) messageContainer.style.display = "none";';
    echo '});';
    echo '</script>';
}


if ($user_class->claimed == 0 && basename($_SERVER['PHP_SELF']) != 'VIPstore.php') {    // The original echo statement for the claim message should be commented out or removed
    // echo '<div style="font-family:Creepster;font-size: 2.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href="rmstore.php?buy=freebie">...</div>';

    // Insert the modal code here
    ?>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
      <div class="modal-content">
    <span class="close">&times;</span>
    <h4><font color=red>A Free Gift</font></h4><br>

    <p><font color=white>Here is a free gift on us enjoy the competition</font></p>
    <ul class="gift-list">
        <h4>+50 Raid Tokens</h4>
        <h4>25,000 Points</h4>
        



    </ul>
    <button onclick="window.location.href='VIPstore.php?buy=freebie'" class="claim-button">Claim Gift</button>
</div>

    </div>
<style>


.gradient-background {
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
</style>
   <style>



   /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 9999; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
}

/* Modal Content for "flexele floaty" style */
.modal-content {
    background-color: #333; /* Dark grey background */
    color: #fff; /* White text */
    padding: 20px;
    margin: auto;
    border: 1px solid #888;
    width: 60%; /* Adjust based on your preference */
    box-shadow: 0 4px 8px rgba(255, 0, 0, 0.5), 0 6px 20px rgba(255, 0, 0, 0.5); /* Red glow for floaty look */
    animation: floaty 5s infinite; /* Adds a gentle floaty animation */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 10px; /* Rounded corners for the modal content */
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

/* Style for the Claim Gift button */
/* Style for the Claim Gift button */
.claim-button {
    background-color: red; /* Change to red background */
    color: white; /* White text */
    padding: 10px 20px;
    border: none;
    border-radius: 5px; /* Rounded corners for a softer look */
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s; /* Smooth transition for hover effect */
}

.claim-button:hover {
    background-color: darkred; /* Darker red on hover for a subtle effect */
}

/* Modal content heading and list */
.modal-content h2 {
    color: #FFD700; /* Gold color for the heading */
    text-align: center;
}

/* Animation for floaty effect */
@keyframes floaty {
    0%, 100% { transform: translate(-50%, -50%) translateY(0); }
    50% { transform: translate(-50%, -50%) translateY(-20px); }
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
/* Icon for the ad slider */
.slider-icon img {
    width: 20px; /* Adjust the width as necessary */
    height: auto; /* Keep the aspect ratio of the image */
    vertical-align: middle; /* Aligns the icon with the text */
    margin-right: 8px; /* Space between the icon and the text */
}

/* Frame for the ad slider */
.slider-frame {
    overflow: hidden; /* Hide the overflowed content */
    margin: 0 auto;
}

/* Styles for each slide */
.slides .slide {
    display: flex; /* Use flexbox for alignment */
    align-items: center; /* Center items vertically */
    justify-content: space-between; /* Space between the text and action button */
    padding: 5px 15px; /* Padding within each slide */
}

/* Content within each slide */
.slide-content {
width:100%;
}

/* Styling for the report ad link */
.slide-action a {
    color: #f00; /* Red color for the report link */
    font-size: 0.8em; /* Smaller font size for the report link */
}

/* Hover effect for the report ad link */
.slide-action a:hover {
    text-decoration: underline; /* Underline on hover for better visibility */
}

/* Ensure the slider text is visible and legible */
.slide-content span {
    color: #fff; /* White color for visibility */
    font-size: 1em; /* Appropriate font size for readability */
    line-height: 1.5; /* Line height for better reading experience */
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
?>

    <div class="vertical-text-slider floaty12">
    <div class="flex-container">
            <div class="slider-icon">
                <a href="/shoutbox.php"><img width="16" height="16" src="/css/images/icons/loudspeaker_32.png" alt="Smart Ads" /></a>
            </div>



            <div class="slider-frame">
                <ul class="slides" style="list-style-type: none; width:100%">

                    <?php
                    $now = time();
                    $result = mysql_query("SELECT a.* FROM ads a WHERE ( SELECT (`timestamp` +(`displaymins` * 60)) FROM ads WHERE ads.id = a.id ) > UNIX_TIMESTAMP()");
                    if (!mysql_num_rows($result)) {

                        $_messages = ['Invite your friends to play and receive <font color=yellow>50 Gold</font> for every friend that plays. Hurry and start inviting now!',
                            'For every friend you successfully refer, you\'ll earn <font color=yellow>50 Gold</font> Spread the word and let\'s play together!',
                            'Attention all players! Invite your friends to join in on the fun. <font color=yellow>50 Gold</font> reward for every successful referral'
                        ];

                        $ref_message = $_messages[array_rand($_messages)];

                        ?>
                        <li class="slide">
                            <div class="slide-content">
                                <!-- <span>Remember - All Referrals using your referral ID will reward you with 50 Credits! Help Spread the word of our launch!</span> -->
                                <span><a href="refer.php"><?= $ref_message ?></a></span>
                            </div>
                        </li>
                        <?php
                    } else {
                        while ($row = mysql_fetch_array($result)) {
                            $user_ads = new User($row['poster']);
                            if ($user_ads->avatar == "") {
                                $user_ads->avatar = "/images/no-avatar.png";
                            }  
                            ?>
                            <li class="slide" style="width:80% !important;">
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
        </div>
    </div>
</div>

<div class="vertical-text-slider floaty12" id="message-container" style="display: block;">
    <ul id="messages" style="list-style-type: none;">
       
    </ul>
</div>


<style>
   
   .floaty12 {
    background-color: #292929; /* Dark background */
    color: white; /* Light text */
    padding: 20px;
    border-radius: 10px; /* Rounded corners */
box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
    padding: 4px 5px 4px;
    margin-bottom:20px;
}
   
    .flex-container {
    display: flex;
    align-items: center; /* Vertical alignment */
    justify-content: flex-start; /* Horizontal alignment */
    font-size: 11px; /* Base font size */
    white-space: nowrap; /* Prevent text wrapping */
    padding: 0; /* Remove padding */
}

.slider-icon {
    flex-shrink: 0; /* Prevent icon from shrinking */
    margin-right: 10px; /* Space between icon and text */
}

.slider-frame {
    width: 100%; /* Ensure it takes up the remaining space */
    overflow: hidden; /* Hide overflowing content */
}

.slides {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

.slide {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.slide-content {
    color: white; /* Text color */
    padding: 2px 10px; /* Tight padding around the text */
}

@media (max-width: 768px) {
    .flex-container {
        font-size: 12px; /* Slightly larger font for small screens */
    }
}

@media (max-width: 480px) {
    .flex-container {
        font-size: 14px; /* Even larger font for very small screens */
    }
}


</style>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>
    
    <script>document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('energy-link').addEventListener('click', function() {
        // Replace 'your_link_here' with the URL you want to navigate to
        window.location.href = '?spend=refenergy';
    });
});
</script>
 <script>document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nerve-link').addEventListener('click', function() {
        // Replace 'your_link_here' with the URL you want to navigate to
        window.location.href = '?spend=refnerve';
    });
});
</script>
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
