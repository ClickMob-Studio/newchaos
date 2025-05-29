<?php
require_once 'includes/functions.php';

start_session_guarded();

if (!isset($_SESSION['id'])) {
    include('home.php');
    die();
}
include_once 'dbcon.php';
include_once 'gradient.class.php';
include_once 'colourgradient.class.php';
include_once 'classes.php';
include_once 'codeparser.php';

if ($_GET['action'] == "logout") {
    session_destroy();
    die('<meta http-equiv="refresh" content="0;url=index.php">');
}
$user_class = new User($_SESSION['id']);
if ($user_class->admin != 1 && $user_class->gm != 1 && $user_class->fm != 1) {
    message("You are not allowed here.");
    include("footer.php");
    die();
}
//Change Server Time to London
putenv("TZ=Europe/London");
//End of Change Server Time
// Update Total Stats
$result2 = mysql_query("SELECT * FROM `grpgusers` WHERE `id`='" . $user_class->id . "'");
$worked2 = mysql_fetch_array($result2);
$total = $worked2['strength'] + $worked2['defense'] + $worked2['speed'];
perform_query("UPDATE `grpgusers` SET `total` = ? WHERE `id`= ?", [$total, $user_class->id]);
//End of Update Total Stats
//Check for Bans
$user_class = new User($_SESSION['id']);
$resultban = mysql_query("SELECT * FROM `bans` WHERE `type` = 'freeze'");
$workedban = mysql_fetch_array($resultban);
$resultban2 = mysql_query("SELECT * FROM `bans` WHERE `type` = 'perm'");
$workedban2 = mysql_fetch_array($resultban2);
if ($user_class->id == $workedban['id'] || $user_class->id == $workedban2['id']) {
    session_destroy();
    die('<meta http-equiv="refresh" content="0;url=index.php">');
}
//End Check for Bans
function microtime_float()
{
    $time = microtime();
    return (double) substr($time, 11) + (double) substr($time, 0, 8);
}
microtime_float();
$starttime = microtime_float();
$user_class = new User($_SESSION['id']);
$result = mysql_query("SELECT * FROM `serverconfig`");
$worked = mysql_fetch_array($result);
if ($worked['serverdown'] != "" && $user_class->admin == 1) {
    die("<h1><font color='red'>SERVER DOWN<br><br>" . $worked['serverdown'] . "</font></h1>");
}
$time = date(F . " " . d . ", " . Y . " " . g . ":" . i . ":" . sa, time());
perform_query("UPDATE `grpgusers` SET `lastactive` = ?, `ip` = ? WHERE `id`= ?", [time(), $_SERVER['REMOTE_ADDR'], $_SESSION['id']]);
function callback($buffer)
{
    $user_class = new User($_SESSION['id']);
    $checkhosp = mysql_query("SELECT * FROM `grpgusers` WHERE `hospital`!='0'");
    $nummsgs = mysql_num_rows($checkhosp);
    $hospital = "[" . $nummsgs . "]";
    $checkjail = mysql_query("SELECT * FROM `grpgusers` WHERE `jail`!='0'");
    $nummsgs = mysql_num_rows($checkjail);
    $jail = "[" . $nummsgs . "]";
    $checkmail = mysql_query("SELECT * FROM `pms` WHERE `to`='$user_class->id' and `viewed`='1'");
    $nummsgs = mysql_num_rows($checkmail);
    $mail = "[" . $nummsgs . "]";
    $checkmail2 = mysql_query("SELECT * FROM `grpgusers` WHERE `id`='$user_class->id' and `gangmail`='1'");
    $numgmail = mysql_num_rows($checkmail2);
    $gmail = $numgmail;
    $checkmail = mysql_query("SELECT * FROM `events` WHERE `to`='$user_class->id' and `viewed` = '1'");
    $numevents = mysql_num_rows($checkmail);
    $events = "[" . $numevents . "]";
    $checkmail = mysql_query("SELECT * FROM `referrals` WHERE `viewed` = '0'");
    $numrefs = mysql_num_rows($checkmail);
    $referrals = "[" . $numrefs . "]";
    $checkmail = mysql_query("SELECT * FROM `tickets` WHERE `viewed` = '0'");
    $numtickets = mysql_num_rows($checkmail);
    $tickets = "[" . $numtickets . "]";
    $result = mysql_query("SELECT * from `effects` WHERE `userid`='" . $user_class->id . "'");
    if (mysql_num_rows($result) != 0) {
        $effects = '<div class="headbox">Current Effects</div>';
        while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
            $effects .= '<a class="leftmenu" href="effects.php?view=' . $line['effect'] . '">' . $line['effect'] . " (" . floor($line['timeleft']) . ")" . '</a></ul><br />';
        }
    }
    $out = $buffer;
    $out = str_replace("<!_-money-_!>", prettynum($user_class->money), $out);
    $out = str_replace("<!_-money2-_!>", $user_class->money, $out);
    $out = str_replace("<!_-formhp-_!>", prettynum($user_class->formattedhp), $out);
    $out = str_replace("<!_-hpperc-_!>", $user_class->hppercent, $out);
    $out = str_replace("<!_-formenergy-_!>", prettynum($user_class->formattedenergy), $out);
    $out = str_replace("<!_-energyperc-_!>", $user_class->energypercent, $out);
    $out = str_replace("<!_-formawake-_!>", prettynum($user_class->formattedawake2forbar), $out);
    $out = str_replace("<!_-awakeperc-_!>", $user_class->awakepercent, $out);
    $out = str_replace("<!_-formnerve-_!>", prettynum($user_class->formattednerve), $out);
    $out = str_replace("<!_-nerveperc-_!>", $user_class->nervepercent, $out);
    $out = str_replace("<!_-formexp-_!>", prettynum($user_class->formattedexp), $out);
    $out = str_replace("<!_-expperc-_!>", $user_class->exppercent, $out);
    $out = str_replace("<!_-points-_!>", prettynum($user_class->points), $out);
    $out = str_replace("<!_-credits-_!>", prettynum($user_class->credits), $out);
    $out = str_replace("<!_-level-_!>", $user_class->level, $out);
    $out = str_replace("<!_-hospital-_!>", prettynum($hospital), $out);
    $out = str_replace("<!_-jail-_!>", prettynum($jail), $out);
    $out = str_replace("<!_-mail-_!>", prettynum($mail), $out);
    if ($gmail > "0") {
        $out = str_replace("<!_-gmail-_!>", "&nbsp;[new]", $out);
    } else {
        $out = str_replace("<!_-gmail-_!>", "", $out);
    }
    $out = str_replace("<!_-events-_!>", prettynum($events), $out);
    $out = str_replace("<!_-tickets-_!>", prettynum($tickets), $out);
    $out = str_replace("<!_-referrals-_!>", prettynum($referrals), $out);
    $out = str_replace("<!_-effects-_!>", $effects, $out);
    $out = str_replace("<!_-cityname-_!>", $user_class->cityname, $out);
    return $out;
}
ob_start("callback");
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <title>Mafia Town</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <LINK REL=StyleSheet HREF="mainstyle.css" TYPE="text/css" MEDIA=screen>
    <link REL="SHORTCUT ICON" HREF="images/favicon.ico">
    <script type="text/javascript" src="java.js"></script>
</head>

<body background="images/background.gif">
    <table bgcolor="#000000" border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td>
                <!--<img src="images/forconor[1]-1.png" />-->
                <table class="top">
                    <tr>
                        <td align="right">
                            <a href="news.php">News</a>&nbsp; | &nbsp;<a href="forum.php?id=8">Help Forum</a>
                            &nbsp;|&nbsp; <a href="forum.php">Forums</a>&nbsp; |&nbsp; <a href="gameguide.php">Game
                                Guide</a>&nbsp; | &nbsp;<a href="itempedia.php">Itempedia</a>&nbsp; |&nbsp; <a
                                href="tickets.php">Support</a>&nbsp; |&nbsp; <a href="tos.php">TOS</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="pos1" valign="top">
                <div class="topbox">
                    <table width='805' height="143px" border="0" style="table-layout:fixed; word-wrap:break-word;">
                        <tr>
                            <td width="15%">&nbsp;<b>ID:</b>&nbsp;<?php echo $user_class->id; ?></td>
                            <td width="19%"></td>
                            <td width="36%"></td>
                            <td width="30%"></td>
                        </tr>
                        <tr valign="top">
                            <td align="left" width="15%">
                                <?php
                                if ($user_class->avatar !== "") {
                                    $avatar = $user_class->avatar;
                                } else {
                                    $avatar = "images/no-avatar.png";
                                }
                                ?>
                                <a href='profiles.php?id=<?php echo $_SESSION['id'] ?>'><img height="115" width="115"
                                        style="border:1px solid #000000" src="<?php echo $avatar ?>"></a>
                            </td>
                            <td width="19%"><a
                                    href='profiles.php?id=<?php echo $_SESSION['id'] ?>'><?php echo $user_class->formattedname; ?></a><br />
                                Level:
                                <!_-level-_!>
                                    <br />
                                    Money: $<!_-money-_!><br />
                                        <a href="bank.php?dep=<!_-money2-_!>">Bank</a>:
                                        $<?php echo prettynum($user_class->bank); ?><br />
                                        Points:
                                        <!_-points-_!><br />
                                            Credits:
                                            <!_-credits-_!>
                                                <br />[<a href="spendpoints.php">Use</a>] [<a
                                                    href="rmstore.php">Buy</a>]
                            </td>
                            <td width="36%"></td>
                            <td width="30%">
                                <table>
                                    <tr>
                                        <td width="5%">
                                            HP:&nbsp;
                                        </td>
                                        <td width="95%">
                                            <div class="bar_a" title="<!_-formhp-_!>">
                                                <div class="bar_b" style="width: <!_-hpperc-_!>%;"
                                                    title="<!_-formhp-_!>">&nbsp;</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%">
                                            <a href="spendpoints.php?spend=energy">Energy</a>:
                                        </td>
                                        <td width="95%">
                                            <div class="bar_a" title="<!_-formenergy-_!>">
                                                <div class="bar_b" style="width: <!_-energyperc-_!>%;"
                                                    title="<!_-formenergy-_!>">&nbsp;</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%">
                                            <a href="spendpoints.php?spend=awake">Awake</a>:
                                        </td>
                                        <td width="95%">
                                            <div class="bar_a" title="<!_-formawake-_!>">
                                                <div class="bar_b" style="width: <!_-awakeperc-_!>%;"
                                                    title="<!_-formawake-_!>">&nbsp;</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%">
                                            <a href="spendpoints.php?spend=nerve">Nerve</a>:
                                        </td>
                                        <td width="95%">
                                            <div class="bar_a" title="<!_-formnerve-_!>">
                                                <div class="bar_b" style="width: <!_-nerveperc-_!>%;"
                                                    title="<!_-formnerve-_!>">&nbsp;</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%">
                                            EXP:
                                        </td>
                                        <td width="95%">
                                            <div class="bar_a" title="<!_-formexp-_!>">
                                                <div class="bar_b" style="width: <!_-expperc-_!>%;"
                                                    title="<!_-formexp-_!>">&nbsp;</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <table class='topbar'>
                    <tr>
                        <td align="left">
                            <?php echo $time; ?>&nbsp;&nbsp;|&nbsp;&nbsp;Update Time: 12:00am
                        </td>
                        <td align="right">
                            <a href="vote.php">Vote</a> | <a href="userpoll.php">Poll</a> | <a
                                href="refer.php">Refer</a> | <a href="rmstore.php">Upgrade Account</a> | <a
                                href="gameevents.php">Game Events</a>
                        </td>
                    </tr>
                </table>
                <?php
                $result = mysql_query("SELECT * from `serverconfig`");
                $worked = mysql_fetch_array($result);
                $messagetext = BBCodeParse($worked['messagefromadmin']);
                echo ($messagetext != "") ? "<table class='topbar'><tr><td><marquee scrollamount='3'>" . $messagetext . "</marquee>		</td></tr></table>" : "";
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top" width="150">
                            <div style="height:7px;"></div>
                            <div>
                                <?php
                                $result1 = mysql_query("SELECT * FROM `ftopics` WHERE `reported` = '1'");
                                $threads = mysql_num_rows($result1);
                                $result2 = mysql_query("SELECT * FROM `freplies` WHERE `reported` = '1'");
                                $posts = mysql_num_rows($result2);
                                ?>
                                <div class="headbox">Back</div>
                                <a class="leftmenu" href="index.php"><img src="images/point.png" width=16 height=16
                                        border=0> Back to Game</a>
                                <div class="space"></div>
                                <div class="headbox">Guide</div>
                                <a class="leftmenu" href="fmpanel.php"><img src="images/point.png" width=16 height=16
                                        border=0> FM Guide</a>
                                <div class="space"></div>
                                <div class="headbox">Bans</div>
                                <a class="leftmenu" href="fmpanel.php?page=bans"><img src="images/point.png" width=16
                                        height=16 border=0> Forum Bans</a>
                                <div class="space"></div>
                                <div class="headbox">Forum Reports</div>
                                <a class="leftmenu" href="fmpanel.php?page=reportthread"><img src="images/point.png"
                                        width=16 height=16 border=0> Threads [<?php echo $threads; ?>]</a>
                                <a class="leftmenu" href="fmpanel.php?page=reportpost"><img src="images/point.png"
                                        width=16 height=16 border=0> Posts [<?php echo $posts; ?>]</a>
                                <div class="space"></div>
                        </td>
                        <td valign="top">
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td width="4"></td>
                                    <td valign="top" class="mainbox">
                                        <table class="content" border="0" align="right" cellspacing="0">
                                            <?php
                                            $resultadmin = mysql_query("SELECT * FROM `serverconfig`");
                                            $workedadmin = mysql_fetch_array($resultadmin);
                                            $result123 = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = '" . $user_class->id . "'");
                                            $worked123 = mysql_fetch_array($result123);
                                            $resultginvite = mysql_query("SELECT * FROM `ganginvites` WHERE `playerid` = '$user_class->id'");
                                            $resultrelation = mysql_query("SELECT * FROM `rel_requests` WHERE `player` = '$user_class->id'");
                                            if ($workedadmin['admin'] != "") {
                                                echo "
<tr><td class='contentspacer'></td></tr><tr><td class='contenthead'>Admin Notification</td></tr>
<tr><td class='contentcontent'>
<center>" . $workedadmin['admin'] . "</center>
<?phptd></tr>";
                                            }
                                            //UNI
                                            $check12 = mysql_query("SELECT * FROM `uni` WHERE `playerid` = '" . $user_class->id . "'");
                                            $check1 = mysql_num_rows($check12);
                                            if ($check1 > 0) {
                                                $uni = mysql_fetch_array($check12);
                                                if (time() >= $uni['finish']) {
                                                    $unifinished = 1;
                                                }
                                            }
                                            //END
//GC
                                            $gang = new Gang($user_class->gang);
                                            if ($user_class->gang != 0 && $gang->crime != 0) {
                                                $result = mysql_query("SELECT * FROM `gangs` WHERE `id` = '" . $user_class->gang . "'");
                                                $worked = mysql_fetch_array($result);
                                                if (time() >= $worked['ending']) {
                                                    $gang_rank = new GangRank($user_class->grank);
                                                    if ($gang_rank->crime == 1) {
                                                        $gcfinished = 1;
                                                    }
                                                }
                                            }
                                            //END
//GANG WARS
                                            $result = mysql_query("SELECT * FROM `gangwars` WHERE (`gang1` = '" . $user_class->gang . "' OR `gang2` = '" . $user_class->gang . "') AND `accepted` = '1' LIMIT 1");
                                            $atwar = mysql_num_rows($result);
                                            $query = mysql_fetch_array($result);
                                            if ($user_class->gang != 0 && $atwar > 0 && time() >= $query['timeending']) {
                                                $warfinished == 1;
                                            }
                                            //END
                                            if ($user_class->voted1 == 0 || $user_class->voted2 == 0 || $user_class->voted3 == 0 || $user_class->voted4 == 0 || $worked123['news'] == 1 || $user_class->gameevents == 0 || $user_class->polled1 == 0 && $user_class->polled1active == "active" || mysql_num_rows($resultginvite) > 0 || mysql_num_rows($resultrelation) > 0 || $user_class->hospital > 0 || $user_class->jail > 0 || $user_class->protectionact > time() || $user_class->drugused > 0 || $unifinished == 1 || $gcfinished == 1 || $warfinished == 1) {
                                                ?>
                                                <tr>
                                                    <td class='contentspacer'></td>
                                                </tr>
                                                <tr>
                                                    <td class='contenthead'>Important Message</td>
                                                </tr>
                                                <tr>
                                                    <td class='contentcontent'>
                                                        <?php
                                                        if ($user_class->invincible > time()) {
                                                            echo "<center><font color='red'><b>You are currently invincible for another " . howlongleft($user_class->invincible) . ".</b></font></center>";
                                                        }
                                                        if ($user_class->protectionact > time()) {
                                                            echo "<center><font color='red'><b>You are currently protected from being attacked for another " . howlongleft($user_class->protectionact) . ".</b></font></center>";
                                                        }
                                                        if ($user_class->voted1 == 0 || $user_class->voted2 == 0 || $user_class->voted3 == 0 || $user_class->voted4 == 0) {
                                                            echo "<center><a href='vote.php'>You haven't voted today! Click here to vote!</a></center>";
                                                        }
                                                        if ($worked123['news'] == 1) {
                                                            echo "<center><a href='news.php'>You have unread news!</a></center>";
                                                        }
                                                        if ($user_class->gameevents == 0) {
                                                            echo "<center><a href='gameevents.php'>You haven't viewed todays game events yet!</a></center>";
                                                        }
                                                        if ($user_class->polled1 == 0 && $user_class->polled1active == "active") {
                                                            echo "<center><a href='userpoll.php'>You haven't voted for the poll yet!</a></center>";
                                                        }
                                                        if (mysql_num_rows($resultginvite) > 0) {
                                                            echo "<center><a href='ganginvites.php'>You have new gang invites!</a></center>";
                                                        }
                                                        if (mysql_num_rows($resultrelation) > 0) {
                                                            echo "<center><a href='rel_requests.php'>You have new relationship requests.</a></center>";
                                                        }
                                                        if ($user_class->hospital > 0) {
                                                            echo "<center><a href='hospital.php'>You are in the hospital for " . howlongleft($user_class->hospital) . ".</a></center>";
                                                        }
                                                        if ($user_class->jail > 0) {
                                                            echo "<center><a href='jail.php'>You are currenty in prison for " . jailleft($user_class->jail) . ".</a></center>";
                                                        }
                                                        if ($pet_class22->jail > 0) {
                                                            echo "<center><a href='jail.php'>Your pet is currenty in prison for " . jailleft($pet_class22->jail) . ".</a></center>";
                                                        }
                                                        if ($user_class->drugused > 0) {
                                                            echo "<center><a href='inventory.php'>You are under the influence of drugs [" . howlongleft($user_class->drugtime) . " left]</a></center>";
                                                        }
                                                        if ($unifinished == 1) {
                                                            echo "<center><a href='completeuni.php'><b>Click here to complete your course at the university.</b></a></center>";
                                                        }
                                                        if ($gcfinished == 1) {
                                                            echo "<center><a href='completegc.php'><b>Click here to complete your gang crime.</b></a></center>";
                                                        }
                                                        if ($warfinished == 1) {
                                                            echo "<center><a href='completegw.php'><b>Click here to complete your gang war.</b></a></center>";
                                                        }
                                                        ?>
                                                </tr>
                                        </td>
                                        <?php
                                            }
                                            ?>