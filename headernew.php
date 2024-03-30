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
		'value' => number_format( $user_class->credits ) . ( ( 1 === $user_class->credits ) ? ' credit' : ' credits' ),
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
		'max'     => $ir['maxenergy'],
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

$counts = array(
	'event'         => $ev,
	'mail'          => '<!_-mail-_!>',
	'hospital'      => $hosp,
	'jail'          => $ja,
);
$queryOnline = mysql_query("SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");

$usersOnline = mysql_num_rows($queryOnline);

$activeRaidsQuery = "SELECT COUNT(*) AS activeRaidsCount FROM active_raids WHERE completed = 0"; // Replace 'end_time' with the actual column name that represents when the raid ends
$activeRaidsResult = mysql_query($activeRaidsQuery);
$activeRaidsData = mysql_fetch_assoc($activeRaidsResult);
$activeRaidsCount = $activeRaidsData['activeRaidsCount'];
?><!doctype html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>ChaosCity</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
	<link href="asset/css/style.css?v=<?php echo time()?>" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="asset/js/app.<?php echo CACHEBURST?>.js"></script>
<style>
	a{
		text-decoration: none;
	}
	</style>
</head>
<body>
	<header class="mainHeader">
		<div class="row mx-auto mainHeaderContent">
		<?php require 'navbar.php'; ?>
		</div>
	</header>
	<div class="row mx-auto my-3 mainContent">
		<div class="d-none d-lg-block col-2 dcLeftNavContainer p-0">
			<?php require 'leftnav.php'; ?>
		</div>
		<div class="col-12 col-lg-10">
			<header class="row">
				<div class="col-12 col-lg-4">
					<div class="p-3 dcPanel dcAvatarPanel">
						<div class="row mb-3">
							<div class="col-9 dcUserName">
							
									<span class="dcHeaderUsername"><?php echo $user_class->formattedname; ?></span>
								<img class="d-lg-none dcAvatarMobile" style="width: 50px;" src="<?php echo $user_class->avatar; ?>">
							</div>
							<div class="col-3 text-center">
								Level <?php echo $user_class->id; ?>
								<div class="d-flex d-lg-none progress dcStatsBars" data-toggle="tooltip" title="<?php echo $user_class->exp . '/' . $user_class->maxexp; ?>">
									<div class="progress-bar" role="progressbar" style="width:<?php echo ( $user_class->exp / $user_class->maxexp * 100 ); ?>%"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-4 col-lg-12 row mb-0 mb-lg-3">
								<div class="d-none d-lg-block col-4">
									<img style="width: 50px;" src="<?php echo $user_class->avatar; ?>" alt="">
								</div>
								<div class="col-12 col-lg-7 offset-lg-1 g-0 row">
									<?php foreach ( $currencies as $key => $currency ) : ?>
										<div class="row my-1 g-0">
											<div class="col-3 d-flex align-items-center"><i class="mx-auto <?php echo $currency['icon']; ?>"></i></div>
											<div class="col-9 d-flex align-items-center"><?php echo $currency['value']; ?></div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<div class="col-8 col-lg-12 g-0 row dcStatsPanel">
								<?php foreach ( $stats as $key => $stat ) : ?>
									<div class="row my-0 my-lg-1 <?php echo 'dcStatContainer-' . $key; ?>">
										<div class="col-3 d-flex align-items-center"><?php echo $stat['title']; ?></div>
										<div class="col-9 d-flex align-items-center">
											<div class="progress dcStatsBars" data-toggle="tooltip" title="<?php echo $stat['current'] . '/' . $stat['max']; ?>">
												<div class="progress-bar" role="progressbar" style="width:<?php echo ( $stat['current'] / $stat['max'] * 100 ); ?>%"></div>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-8 mt-3 mt-lg-0">
					<div class="dcPanel h-100">
						<div class="text-center dcBannerButtonsContainer">
							<a href="voting.php" class="dcSecondaryButton my-3">Vote for <i class="far fa-gem"></i></a>
							<a href="#" class="dcSecondaryButton my-3">Refer for <i class="far fa-gem"></i></a>
							<a href="donate.php" class="dcSecondaryButton my-3">Upgrades <i class="fas fa-level-up-alt"></i></a>
						</div>
					</div>
				</div>
			</header>
			<div class="row mt-4">
				<main>
					<div class="dcPanel p-3">

    <?php


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

// if ($user_class->cityturns > 29) {
//     $messages[] = '<a href="maze.php">You Have Maze Searches Available</a>';
// }
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


//if ($user_class->claimed == 0 && basename($_SERVER['PHP_SELF']) != 'VIPstore.php') {    // The original echo statement for the claim message should be commented out or removed
    // echo '<div style="font-family:Creepster;font-size: 2.5em;color:red;text-align: center;margin-bottom: 20px;margin-top: -20px;"><a href="rmstore.php?buy=freebie">...</div>';

    // Insert the modal code here
    ?>
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
    <!-- <button onclick="window.location.href='VIPstore.php?buy=freebie'" class="claim-button">Claim Gift</button> -->
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
    .floaty12{
        margin: 0 auto;
    margin-right: 10px;
    color:#000;
    width: 72%;
    text-align: center;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
    padding: 5px 5px 4px;
    margin-bottom:10px;
    }
    .floaty12 a:link {
  color: #000;
}
    </style>


                    <?php
                    $now = time();
                    $result = mysql_query("SELECT a.* FROM ads a WHERE ( SELECT (`timestamp` +(`displaymins` * 60)) FROM ads WHERE ads.id = a.id ) > UNIX_TIMESTAMP()");
                    if (!mysql_num_rows($result)) {

                        $_messages = [
                            '<b>Please note this game is currently in BETA before full launch in the first week of April. On relaunch, the database will reset.<br /> If you find any issues, please DM ID 1 or 2.</b>',
                            //'Invite your friends to play and receive <font color=red>50 Gold</font> for every friend that plays. Hurry and start inviting now!',
                            //'For every friend you successfully refer, you\'ll earn <font color=red>50 Gold</font> Spread the word and let\'s play together!',
                            //'Attention all players! Invite your friends to join in on the fun. <font color=red>50 Gold</font> reward for every successful referral'
                        ];

                        $ref_message = $_messages[array_rand($_messages)];

                        ?>
                        
                            <div class="dcPanel p-3">
								<?= $ref_message ?>
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

<div class="vertical-text-slider floaty12" id="message-container">
    <ul id="messages" style="list-style-type: none;">

    </ul>
</div>



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
?>

