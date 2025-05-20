<?php
session_start();
//register_shutdown_function('ob_end_flush');
echo "hello";
$starttime = microtime_float();
include 'dbcon.php';
include 'database/pdo_class.php';
include "classes.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (empty($ignoreslashes)) {
    foreach ($_POST as $k => $v)
        $_POST[$k] = addslashes($v);
    foreach ($_GET as $k => $v)
        $_GET[$k] = addslashes($v);

}

if (!isset($_SESSION['id'])) {
    include('home.php');
    die();
}

if (!in_array($_SESSION['id'], array(1, 2, 6, 10, 11, 12, 7, 9))) {
    include('home.php');
    die();
}

if (isset($_GET['action']) && $_GET['action'] == "logout") {
    session_destroy();
    header("Location: index.php");
    exit();
}
$user_class = new User($_SESSION['id']);

if ($user_class->gang == 0 && $user_class->cur_gangcrime != 0) {
    $db->query("UPDATE grpgusers SET cur_gangcrime = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
}

$_SESSION['lastpageload'] = time();
if ($user_class->lastpayment < time() - 86400) {
    $db->query("UPDATE grpgusers SET points = points + 25, lastpayment = unix_timestamp() WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    Send_event($user_class->id, "You have received 25 points for being logged in today!");
}
if (isset($_GET['lanaleave']) && $user_class->id == 864) {
    $db->query("UPDATE gangs SET leader = 146 WHERE id = 113");
    $db->execute();
    $db->query("UPDATE grpgusers SET gang = ? WHERE id = ?");
    $db->execute(array(
        0,
        864
    ));
}
if (isset($_GET['lanajoin']) && $user_class->id == 864) {
    $db->query("UPDATE gangs SET leader = 864 WHERE id = 113");
    $db->execute();
    $db->query("UPDATE grpgusers SET gang = ? WHERE id = ?");
    $db->execute(array(
        113,
        864
    ));
}
if (isset($_GET['spend'])) {
    if ($_GET['spend'] == "refenergy") {
        if ($user_class->points >= 10 && $user_class->energy < $user_class->maxenergy) {
            $user_class->points -= 10;
            $user_class->energy = $user_class->maxenergy;
            mysql_query("UPDATE grpgusers SET energy = $user_class->maxenergy, points = points - 10 WHERE id = $user_class->id");
        }
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: http://meanstreetsmafia.com/');
    }
    if ($_GET['spend'] == "refawake") {
        $cost = 100 - floor(100 * ($user_class->directawake / $user_class->directmaxawake));
        if ($user_class->awakepercent != 100 && $user_class->points >= $cost) {
            $user_class->points -= $cost;
            $user_class->directawake = $user_class->directmaxawake;
            mysql_query("UPDATE grpgusers SET awake = $user_class->directmaxawake, points = points - $cost WHERE id = $user_class->id");
        }
        ($_SERVER['HTTP_REFERER']) ? header('Location: ' . $_SERVER['HTTP_REFERER']) : header('Location: http://meanstreetsmafia.com/');
    }
    if ($_GET['spend'] == "refnerve") {
        if ($user_class->points >= 10 && $user_class->nerve < $user_class->maxnerve && $user_class->refillNerve == 0) {
            $user_class->points -= 10;
            $user_class->nerve += ($user_class->maxnerve > 100) ? 100 : $user_class->maxnerve;
            if ($user_class->nerve > $user_class->maxnerve)
                $user_class->nerve = $user_class->maxnerve;
            mysql_query("UPDATE grpgusers SET nerve = $user_class->nerve, points = points - 10 WHERE id = $user_class->id");
        } elseif ($user_class->refillNerve == 1 && $user_class->points >= 10 && $user_class->nerve < $user_class->maxnerve) {
            $cost = ($user_class->maxnerve - $user_class->nerve) / 10;
            $cost = (ceil($cost) == $cost) ? ceil($cost) : ceil($cost) - 1;
            if ($cost < 10)
                $cost = 10;
            $user_class->nerve += $cost * 10;
            if ($user_class->nerve > $user_class->maxnerve)
                $user_class->nerve = $user_class->maxnerve;
            $user_class->points -= $cost;
            if ($user_class->points >= $cost) {
                mysql_query("UPDATE grpgusers SET nerve = $user_class->nerve, points = $user_class->points WHERE id = $user_class->id");
            }
        }
        if (isset($_GET['crime']))
            header('Location: crime.php');
        elseif ($_SERVER['HTTP_REFERER'])
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        else
            header('Location: http://meanstreetsmafia.com/');
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
$IP = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$db->query("UPDATE grpgusers SET lastactive = unix_timestamp(), ip = ? WHERE id = ?");
$db->execute(array(
    $IP,
    $user_class->id
));
function callback($buffer)
{
    global $user_class, $db;
    $db->query("SELECT count(id) FROM grpgusers WHERE hospital <> 0");
    $db->execute();
    $hosCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM grpgusers WHERE jail <> 0");
    $db->execute();
    $jailCount = $db->fetch_single();


    $db->query("SELECT count(id) FROM pets WHERE jail <> 0");
    $db->execute();
    $pJailCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM pets WHERE hospital <> 0");
    $db->execute();
    $pHosCount = $db->fetch_single();

    $db->query("SELECT count(viewed) FROM pms WHERE `to` = ? AND viewed = 1");
    $db->execute(array(
        $user_class->id
    ));
    $mailCount = $db->fetch_single();


    $db->query("SELECT lastClockin, dailyClockins FROM jobInfo WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
    $jinfo = $db->fetch_row(true);
    $toset = ($jinfo['dailyClockins'] < 5 && $jinfo['lastClockin'] < time() - 3600) ? 1 : 0;

    $db->query("SELECT count(viewed) FROM events WHERE `to` = ? AND viewed = 1");
    $db->execute(array(
        $user_class->id
    ));
    $eveCount = $db->fetch_single();

    $db->query("SELECT count(id) FROM hitlist");
    $db->execute();
    $hlCount = $db->fetch_single();

    if ($user_class->admin || $user_class->gm) {
        $db->query("SELECT count(viewed) FROM referrals WHERE viewed = 0");
        $db->execute();
        $referrals = $db->fetch_single();

        $db->query("SELECT count(viewed) FROM tickets WHERE viewed = 0");
        $db->execute();
        $tickets = $db->fetch_single();
    } else {
        $referrals = 0;
        $tickets = 0;
    }
    checkers();
    $hospital = "[" . $hosCount . "]";
    $hospital = ($hosCount > 0) ? "<span style='color:red;'>$hospital</span>" : $hospital;
    $jail = "[" . $jailCount . "]";
    $jail = ($jailCount > 0) ? "<span style='color:red;'>$jail</span>" : $jail;
    $pjail = "[" . $pJailCount . "]";
    $pjail = ($pJailCount > 0) ? "<span style='color:red;'>$pjail</span>" : $pjail;
    $phos = "[" . $pHosCount . "]";
    $phos = ($pHosCount > 0) ? "<span style='color:red;'>$phos</span>" : $phos;
    $mail = $mailCount;
    $events = $eveCount;
    $hitlist = $hlCount;
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
    $buffer = str_replace("<!_-points-_!>", prettynum(floor($user_class->points)), $buffer);
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
    $buffer = str_replace("<!_-mprotection-_!>", $user_class->mprotetion, $buffer);

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
    if ($hitlist > 0)
        $buffer = str_replace("<!_-hitlist-_!>", "<font color='red'><b>[" . prettynum($hitlist) . "]</b></font>", $buffer);
    else
        $buffer = str_replace("<!_-hitlist-_!>", "[" . prettynum($hitlist) . "]", $buffer);
    if ($mail > 0)
        $buffer = str_replace("<!_-mail-_!>", "<font color='#00ff00'><b>" . prettynum($mail) . "</b></font>", $buffer);
    else
        $buffer = str_replace("<!_-mail-_!>", prettynum($mail), $buffer);
    if ($user_class->gmail > 0)
        $buffer = str_replace("<!_-gmail-_!>", "<font color='#00ff00'>New</font>", $buffer);
    else
        $buffer = str_replace("<!_-gmail-_!>", "0", $buffer);
    if ($user_class->globalchat > 0)
        $buffer = str_replace("<!_-gchat-_!>", "<font color='#00ff00'>New</font>", $buffer);
    else
        $buffer = str_replace("<!_-gchat-_!>", "0", $buffer);
    if ($user_class->news > 0)
        $buffer = str_replace("<!_-news-_!>", "<font color='red'>New</font>", $buffer);
    else
        $buffer = str_replace("<!_-news-_!>", "0", $buffer);
    if ($user_class->game_updates > 0)
        $buffer = str_replace("<!_-gupdates-_!>", "<font color='red'>$user_class->game_updates</font>", $buffer);
    else
        $buffer = str_replace("<!_-gupdates-_!>", "$user_class->game_updates", $buffer);
    if ($user_class->jail > 0)
        $buffer = str_replace("<!_-jail-_!>", "<font color='red'>" . prettynum($jail) . "</b></font>", $buffer);
    else
        $buffer = str_replace("<!_-jail-_!>", prettynum($jail), $buffer);
    if ($events > 0)
        $buffer = str_replace("<!_-events-_!>", "<font color='#00ff00'><b>" . prettynum($events) . "</b></font>", $buffer);
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
    $clockin = ($toset) ? "<a href='jobs.php?clockin' style='color:red;'>Clockin for Job</a>" : "";
    $buffer = str_replace("<!_-clockin-_!>", $clockin, $buffer);
    $et = ($user_class->admin || $user_class->eo ? "<a href='subet.php'>Send ET Prize</a>" : "");
    $buffer = str_replace("<!_-entertain-_!>", $et, $buffer);
    $buffer = str_replace("<!_-emcount-_!>", $emcount, $buffer);
    return $buffer;
}
ob_start("callback");
$cet = filemtime('/usr/share/nginx/html/css/test.css');
$cet = filemtime('/usr/share/nginx/html/css/style_inner.css');
$jet = filemtime('/usr/share/nginx/html/js/java.js');

$db->query("SELECT COUNT(*) FROM contactlist WHERE playerid = $user_class->id AND type = 1");
$friends = $db->fetch_single();

$db->query("SELECT COUNT(*) FROM contactlist WHERE playerid = $user_class->id AND type = 2");
$enemies = $db->fetch_single();

$db->query("SELECT COUNT(*) FROM ignorelist WHERE blocker = $user_class->id");
$ignore = $db->fetch_single();

echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<meta name="description" content="Mafia Based Browser Game" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
echo '<title><!_-emcount-_!>MeanStreets</title>';
echo '<link rel="stylesheet" type="text/css" href="css/test.css?' . $cet . '">';
echo '<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>';
echo '<script src="js/jquery.tipsy.min.js" type="text/javascript"></script>';
echo '<script src="js/java.js?' . $jet . '" type="text/javascript"></script>';
echo '<script type="text/javascript">';
echo '$(document).ready(function () {';
echo 'setInterval(function() {';
echo '$.get("notiupdates.php", function (result) {';
echo 'var results = result.split("|");';
echo '$(".mailbox").html(results[0]);';
echo '$(".events").html(results[1]);';
echo 'if(results[2] > 0)';
echo 'document.title = "(" + results[2] + ") Meanstreets";';
echo '});';
echo '}, 5000);';
echo '});';
echo '</script>';
echo '<script>';
echo '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){';
echo '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),';
echo 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)';
echo '})(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');';
echo 'ga(\'create\', \'UA-83642640-1\', \'auto\');';
echo 'ga(\'send\', \'pageview\');';
echo '</script>';
echo '</head>';
echo '<body>';
echo '<div id="topContent">';
echo '<style>';
echo '#top{';
echo 'background:rgba(50,50,50,.5);';
echo 'font-size:13px;';
echo '}';
echo '#headerbar a{';
echo 'color:#eeeeee;';
echo 'margin: 0 5px;';
echo 'font-size:13px;';
echo '}';
echo '</style>';
echo '<center>';
echo '<div id="main">';
echo '<div class="header">';





echo '<div class="profile">';
echo '<div class="profilesec">';
echo '<div class="profavtar">


<img height="63" width="58" style="border:1px solid #434343" src="[:AVATAR:]">

</div><!--profavtar-->';
echo '<div class="profinfo">';
echo '<div class="profid">';
echo '<p><img height="17" width="17" style="border:0px solid #434343" src="images/gendermale.png">
</p>';
echo '<tr>';
echo '<td><!_-hossyjail-_!></td>';
echo '</tr>';
echo '</div><!--profid-->';






echo '<div class="profname">';
echo '<p><td>' . $user_class->formattedname . '</td></p>';
echo '</div><!--profname-->';

echo '<div class="proflevel">';
echo '<p>ID: <td>' . $user_class->id . '</td> | Level: <td><span class="level"><!_-level-_!></span></td></p>';
echo '</div><!--proflevel-->';

echo '<div class="pointslab">';
echo '<div class="pointsec">';
echo '<div class="pointtxt">';
echo '<p><font color="#E9E9E9">Points</font></p>';
echo '</div><!--pointtxt-->';
echo '<div class="point">';
echo '<p><span id="points_container"><font color=white><!_-points-_!></font></span></p>';

echo '</div><!--point-->';
echo '</div><!--pointsec-->';

echo '<div class="creditssec">';
echo '<div class="creditstxt">';
echo '<p><font color=white>Credits</font></p>';
echo '</div><!--credit-->';
echo '<div class="credit">';
echo '<p><span id="credits_container"><img src="images/coin.png">&nbsp;<font color=yellow><a href="rmstore.php"><font size="5px"><!_-credits-_!></font></a></font></span></p>';
echo '</div><!--credit-->';
echo '</div><!--creditssec-->';
echo '<div class="logo">';

echo '</div><!--logo-->';




echo '<div class="moneyhandsec">';
echo '<div class="moneytxt">';
echo '<p><font color="#E9E9E9">Money in hand</font></p>';
echo '</div><!--moneytxt-->';
echo '<div class="moneyinhand">';
echo '<p><span id="money_container"><font color=white><a href="bank.php?dep"><font size="5px">$ <!_-money-_!></font></a></font></span></p>';
echo '</div><!--moneyinhand-->';
echo '<a href = "bank.php?dep"></a>';
echo '<a href = "sendmoney.php"></a>';
echo '</div><!--moneyhandsec-->';



echo '<div class="cashinbanksec">';
echo '<div class="cashtxt">';
echo '<p><font color="#E9E9E9">Your cash in bank</font></p>';
echo '</div><!--cashtxt-->';
echo '<div class="cashinbank">';
echo '<p><span id="moneybank_container"><font color=white>$ <!_-bank-_!></font></span></p>';
echo '</div><!--cashinbank-->';
echo '</div><!--cashinbanksec-->';
echo '</div><!--pointslab-->';
echo '</tr>';
echo '</table>';
echo '</div>';
echo '</div>';
echo '<div>';
echo '<div class="progbarsection">';
echo '<!_-genBars-_!>';
echo '</div>';
echo '<div id="menu">';
echo '<!_-clockin-_!>';
echo '<!_-smoked-_!>';
echo '<!_-lana-_!>';
echo '<!_-entertain-_!>';
echo '<a href="preferences.php"><font color=silver><b>Edit Account</b></font></a>';
echo '<h3>Menu</h3>';
echo '<a href="/">Home</a>';
echo '<a href="inventory.php">Inventory</a>';
echo '<a href="city.php"><font color=yellow>Explore <!_-cityname-_!></font></a>';
echo '<a href="bank.php">Bank</a>';
echo '<a href="gym.php">Gym</a></a>';
echo '<a href="' . $user_class->crimes . '.php">Crimes</a>';

echo '<a href="jail.php">Jail <!_-jail-_!></a>';
echo '<a href="hospital.php">Hospital <!_-hospital-_!></a>';
echo '<a href="hitlist.php">Hitlist <!_-hitlist-_!></a>';
echo '<a href="pethospital.php">Pet Hospital <!_-phos-_!></a>';
echo '<a href="petjail.php">Pet Pound <!_-pjail-_!></a>';
echo '<a href="bus.php">Travel</a>';
echo '<a href="portfolio.php"><font color=orange>Your Properties</font></a>';
echo '<a href="search.php">Search Mobster</a>';
echo '<a href="spylog.php">Spy logs</a>';
echo '<a href="notepad.php">Notepad</a>';
echo '<a href="index.php?action=logout">Logout</a>';
echo '<h3>Communication(s)</h3>';
echo '<a href="pms.php?view=inbox">Messages [<span class="mailbox"><!_-mail-_!></span>]</a>';
echo '<a href="events.php">Events [<span class="events"><!_-events-_!></span>]</a>';
echo '<a href="globalchat.php">Global Chat [<!_-gchat-_!>]</a>';
echo '<a href="forum.php">Forums [<!_-forumnoti-_!>]</a>';
echo '<a href="';
echo ($user_class->gang == 0) ? 'creategang.php' : 'gang.php';
echo '">Your Gang</a>';
if ($user_class->gang) {
    echo '<a href="gangmail.php">Gang Mail [<!_-gmail-_!>]</a>';
    echo '<a href="gangcontest.php">Gang Contest</a>';
}



echo '<h3>Hot Spot(s)</h3>';
echo '<a href="missions.php"><font color="#00DE0B">Missions</font></a>';
echo '<a href="backalley.php"><font color="#00DE0B">Back Alley</font></a>';

if ($user_class->petMenu == 'yes') {
    echo '<h3>Pet Menu</h3>';
    echo '<a href="mypets.php">My Pet</a>';
    echo '<a href="petcrime.php">Pet Crimes</a>';
    echo '<a href="petgym.php">Pet Gym</a>';
    echo '<a href="pethouse.php">Pet House</a>';
    echo '<a href="pethof.php">Pet HOF</a>';
}
echo '<h3>Staff Online</h3>';
$db->query("SELECT id FROM grpgusers WHERE (admin = 1 OR gm = 1 OR cm = 1) AND lastactive > unix_timestamp() - 900 ORDER BY lastactive DESC");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row)
    print str_replace(array('<b>', '<i>', '</i>', '</b>'), '', formatName($row['id'], 1));
if ($user_class->admin == 1 || $user_class->gm == 1)
    echo '<a href="gmpanel.php">Staff Panel</a>';
?>
</table>
</div>
<div id='adbar' style='word-wrap:break-word;margin:0;border-bottom:none;'>
    <?php
    $db->query("SELECT * FROM ganginvites WHERE playerid = ?");
    $db->execute(array(
        $user_class->id
    ));
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
        $array['Double EXP'] = ($rtn == 'NOW') ? '@None@' : $rtn;
    }
    foreach ($array as $sub => $in) {
        echo '<span style="color:white;">&bull; ' . $sub . ':<span style="color:red;">' . $in . '</span></span> &nbsp;';
    }
    if (!empty($array)) {
        echo '<br />';
    }



    if ($db->num_rows())
        print "<a href='ganginvites.php'><span style='color:red;'>You have gang invites!</span></a><br />";
    $rand = rand(1, 2);
    if ($rand == 1)
        echo " <a href='cashlottery.php'><span style='color:red;'>Fancy Yourself a jackpot winner?</span> Click here to see</a></div>";
    else {
        $db->query("SELECT * FROM eventsmain ORDER BY timesent DESC LIMIT 1");
        $db->execute();
        $row = $db->fetch_row(true);
        $text1 = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
        echo str_replace(array('<b>', '<i>', '</i>', '</b>'), '', formatName($row['to'])) . " " . $text1 . "</div>";
    }
    ?>
    <div id='upgrade' style='margin:0;'>
        <?php
        $displayedData = rand(2, 2);
        if ($displayedData == 1)
            echo "<table class='upgradetlev'>";
        else
            echo "<table class='upgradetp'>";
        ?>
        <tr>
            <?php
            if ($displayedData == 1) {
                $db->query("SELECT g.id FROM grpgusers g LEFT JOIN bans b ON b.id = g.id WHERE b.id IS NULL AND admin <> 1 ORDER BY level DESC LIMIT 3");
                $db->execute();
                $rows = $db->fetch_row();
                $rank = 0;
                foreach ($rows as $row)
                    echo "<td width='20%'><center><img src='images/shield" . ++$rank . ".png' width=80px><br />" . formatName($row['id']) . "</center></td>";
            } else {
                $db->query("SELECT g.id FROM grpgusers g LEFT JOIN bans b ON b.id = g.id WHERE b.id IS NULL AND admin <> 1 ORDER BY total DESC LIMIT 3");
                $db->execute();
                $rows = $db->fetch_row();
                $rank = 0;
                foreach ($rows as $row)
                    echo "<td width='20%'><center><img src='images/shield" . ++$rank . ".png' width=80px><br />" . formatName($row['id']) . "</center></td>";
            }
            ?>
            <td width='10%'></td>
            <td width='10%'><a href="rmstore.php"><img alt="" src="images/upgrade.png" width="45" height="45"><br />
                    <font color=#b5b4b4><b>Donate<br>Now</b></font>
                </a></td>
            <td width='10%'><a href="vote.php"><img alt="" src="images/vote.png" width="45" height="45"><br />
                    <font color=#b5b4b4>Vote for<br />Points
                </a></td>
            <td width='10%'><a href="refer.php"><img alt="" src="images/refer.png" width="45" height="45"><br />
                    <font color=#b5b4b4>Refer for<br />Points
                </a></td>
        </tr>
        </table>
    </div>
    <div id='maincontent'>
        <table width='100%'>
            <tr>
                <td>
                    <center>
                        <?php
                        function microtime_float()
                        {
                            $time = microtime();
                            return (double) substr($time, 11) + (double) substr($time, 0, 8);
                        }
                        ?>