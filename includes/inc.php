<?php
$includedailies = 1;
$starttime = microtime_float();
$time_start = microtime(true);
if (!isset($_SESSION['id'])) {
    require_once DIRNAME(__DIR__) . '/home.php';
    exit;
}
require_once __DIR__ . '/dbcon.php';
require_once __DIR__ . '/class_main.php';
require_once DIRNAME(__DIR__) . '/codeparser.php';
require_once __DIR__ . '/class_mtg_functions.php';
if (isset($_GET['action']) && $_GET['action'] == "logout") {
    session_unset();
    session_destroy();
    header("Location: index.php");
}
date_default_timezone_set('Europe/London');
$user_class = new User($_SESSION['id']);
if (isset($cap)) {
    header("Location: cap.php?page=$cap");
}
$_SESSION['lastpageload'] = time();
if (isset($_GET['removeprotection'])) {
    $db->query("UPDATE grpgusers SET protection = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    $user_class->protectionact = 0;
}
if (isset($_GET['spend']) && $_GET['spend'] == "energy") {
    if ($user_class->points >= 10 && $user_class->energy < $user_class->maxenergy) {
        $user_class->points -= 10;
        $user_class->energy = $user_class->maxenergy;
        $db->query("UPDATE `grpgusers` SET `energy` = ?, `points`= points - 10 WHERE `id` = ?");
        $db->execute(array(
            $user_class->maxenergy,
            $user_class->id
        ));
        $_SESSION['refilled'] = array(
            'type' => 'energy',
            'cost' => 10
        );
    }
}
if (isset($_GET['spend']) && $_GET['spend'] == "awake") {
    $points_to_use = floor(($user_class->points > (100 - $user_class->awakepercent)) ? (100 - $user_class->awakepercent) : $user_class->points);
    if ($user_class->awakepercent < 100 && $user_class->points >= $points_to_use) {
        $awake_to_digits = floor($user_class->directmaxawake * ($points_to_use / 100));
        $newawake = floor($user_class->directawake + $awake_to_digits);
        $user_class->awake = ($newawake > $user_class->directmaxawake) ? $user_class->directmaxawake : $newawake;
        $user_class->points -= $points_to_use;
        $db->query("UPDATE grpgusers SET awake = ?, points = ? WHERE id = ?");
        $db->execute(array(
            $user_class->awake,
            $user_class->points,
            $user_class->id
        ));
        $_SESSION['refilled'] = array(
            'type' => 'awake',
            'cost' => $points_to_use
        );
    }
}
if (isset($_GET['spend']) && $_GET['spend'] == "nerve") {
    if ($user_class->points >= 10 && $user_class->nerve < $user_class->maxnerve) {
        $user_class->points -= 10;
        $user_class->nerve = $user_class->maxnerve;
        $db->query("UPDATE grpgusers SET nerve = ?, points = points - 10 WHERE id = ?");
        $db->execute(array(
            $user_class->maxnerve,
            $user_class->id
        ));
        $_SESSION['refilled'] = array(
            'type' => 'nerve',
            'cost' => 10
        );
    }
}

if ($user_class->bf) {
    $db->query("SELECT days FROM bans WHERE id = ? AND type IN ('freeze','perm')");
    $db->execute(array(
        $user_class->id
    ));
    if ($db->num_rows()) {
        if ($db->fetch_single() >= 1) {
            session_unset();
            session_destroy();
            exit(header("Location: http://onemorecupof-coffee.com/wp-content/uploads/2013/08/access-denied-300x300.jpg"));
        }
    }
}
function microtime_float()
{
    $time = microtime();
    return (double) substr($time, 11) + (double) substr($time, 0, 8);
}
$time = date("F d, Y g:i:sa", time());
$db->query("UPDATE grpgusers SET lastactive = ?, ip = ? WHERE id = ?");
$db->execute(array(
    time(),
    $mtg->_ip(),
    $user_class->id
));
function callback($buffer)
{
    global $user_class, $db, $includepets;
    $db->query("SELECT * FROM (
        (SELECT COUNT(id) as hits FROM hitlist) AS hits,
        (SELECT COUNT(petid) AS animals FROM pets WHERE jail <> 0) AS animals,
                                (SELECT COUNT(id) AS refs FROM referrals WHERE credited = 0) AS refs,
        (SELECT COUNT(ticketid) AS tick FROM tickets WHERE status != 'closed') AS tick
    )");

    $db->execute();
    $info = $db->fetch_row(true);
    $hitlist = $info['hits'];
    $animals = $info['animals'];
    $referrals = $info['refs'];
    $tickets = $info['tick'];

    $db->query("SELECT COUNT(id) FROM pms WHERE `to` = ? AND viewed = 1 AND deleted = 0");
    $db->execute(array(
        $user_class->id
    ));
    $mail = $db->fetch_single();

    $db->query("SELECT COUNT(id) FROM events WHERE `to` = ? AND viewed = 1");
    $db->execute(array(
        $user_class->id
    ));
    $events = $db->fetch_single();

    $db->query("SELECT COUNT(id) FROM grpgusers WHERE hospital > 0");
    $db->execute();
    $hospital = $db->fetch_single();

    $db->query("SELECT COUNT(id) FROM grpgusers WHERE jail > 0");
    $db->execute();
    $prison = $db->fetch_single();

    $hospital = "[$hospital]";
    $jail = "[" . ($prison ? "<span style='color:lime;'>$prison</span>" : '0') . "]";
    $jail2 = "[" . ($animals ? "<span style='color:lime;'>$animals</span>" : '0') . "]";

    $db->query("SELECT COUNT(id) FROM missions_in_progress WHERE user = ?");
    $db->execute(array(
        $user_class->id
    ));
    $inProgress = $db->num_rows() ? 1 : 0;

    $db->query("SELECT COUNT(userID) FROM ajax_chat_online");
    $db->execute();
    $chatUsers = $db->num_rows() ? $db->fetch_single() : 0;

    $dxp = ($user_class->dxp > 0) ? "<span style='color:yellow;'>[Double EXP: $user_class->dxp Mins]</span>" : "";
    $buffer = str_replace("<!_-dxp-_!>", $dxp, $buffer);
    $buffer = str_replace("<!_-money-_!>", prettynum($user_class->money), $buffer);
    $buffer = str_replace("<!_-bank-_!>", prettynum($user_class->bank), $buffer);
    $buffer = str_replace("<!_-credits-_!>", prettynum($user_class->credits), $buffer);
    $buffer = str_replace("<!_-points-_!>", "<span class='points'>" . prettynum($user_class->points) . "</span>", $buffer);
    $buffer = str_replace("<!_-level-_!>", $user_class->level, $buffer);
    $buffer = str_replace("<!_-hospital-_!>", prettynum($hospital), $buffer);
    $buffer = str_replace("<!_-jail-_!>", prettynum($jail), $buffer);
    $buffer = str_replace("<!_-crimepage-_!>", $user_class->crimepage, $buffer);
    $buffer = str_replace("<!_-jail2-_!>", $jail2, $buffer);
    $buffer = str_replace("<!_-CHAT_USERS-_!>", '[' . ($chatUsers ? "<span style='color:lime;'>" . prettynum($chatUsers) . "</span>" : '0') . ']', $buffer);
    $buffer = str_replace("<!_-GCLINK-_!>", ($user_class->globalchat ? "<span style='color:lime;'>The Tavern [" . prettynum($user_class->globalchat) . "]</span>" : 'The Tavern [0]'), $buffer);
    $buffer = str_replace("<!_-hitlist-_!>", $hitlist ? "<font color='yellow'><strong>" . prettynum($hitlist) . "</strong></font>" : 0, $buffer);
    $buffer = str_replace("<!_-missions-_!>", $inProgress ? "<font color='yellow'><strong>" . prettynum($inProgress) . "</strong></font>" : 0, $buffer);
    $buffer = str_replace("<!_-mail-_!>", $mail ? "<font color='yellow'><strong>" . $mail . "</strong></font>" : 0, $buffer);
    $buffer = str_replace("<!_-gmail-_!>", $user_class->gmail ? "<font color='yellow'><strong>" . prettynum($user_class->gmail) . "</strong></font>" : 0, $buffer);
    $buffer = str_replace("<!_-events-_!>", $events ? "<font color='yellow'><strong>" . prettynum($events) . "</strong></font>" : 0, $buffer);
    if ($user_class->admin) {
        $buffer = str_replace("<!_-tickets-_!>", $tickets ? "<font color='yellow'><strong>" . prettynum($tickets) . "</strong></font>" : 0, $buffer);
        $buffer = str_replace("<!_-referrals-_!>", $referrals ? "<font color='yellow'><strong>" . prettynum($referrals) . "</strong></font>" : 0, $buffer);
    }
    $buffer = str_replace("<!_-cityname-_!>", $user_class->cityname, $buffer);
    $buffer = str_replace("<!_-genBars-_!>", genBars(), $buffer);
    return $buffer;
}
ob_start("callback");

$db->query("SELECT MAX(forumid) FROM ftopics WHERE sectionid = 1");
$db->execute();
$lastpost = $db->fetch_single();

$votes = array_sum(explode("|", $user_class->votes));
$vote = ($votes == 6) ? "Vote" : "<span style='color:lime;'>Vote</span>";
$cet = filemtime('/usr/share/nginx/html/css/mainstyle.css');
?><!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" type="text/css" media="screen" href="css/mainstyle.css?<?php echo $cet ?>" />
    <script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(
            function () {
                setInterval(function () {
                    $.get("ajax_updates.php", function (result) {
                        var results = result.split("|");
                        $(".inmates").html(results[0]);
                        $(".mail").html(results[1]);
                        $(".events").html(results[2]);
                        $(".hitlist").html(results[3]);
                        $(".animals").html(results[4]);
                        $(".genBars").html(results[5]);
                    });
                }, 5000);
            });
    </script>
    <link rel="shortcut icon" href="images/favicon.ico" />
    <title>Mafia Thug</title>
</head>

<body>
    <table class='wholescreen' width="100%">
        <tr>
            <td>
                <table class="top" width='100%'>
                    <tr>
                        <td align="left">
                            <!_-dxp-_!>
                        </td>
                        <td align="right">
                            <a href='vote.php'><?php echo $vote ?></a> |
                            <a href='refer.php'>Refer</a> |
                            <a href='gameupdates.php'>Updates<?php
                            echo $user_class->updates ? " [<span style='color:lime;'>" . $user_class->updates . '</span>]' : '';
                            ?></a> |
                            <a href="news.php">News<?php
                            echo $user_class->news ? " [<span style='color:lime;'>" . $user_class->news . '</span>]</a> [<a href="forum.php?topic=' . $lastpost . '">DL</a>]' : '</a> [<a href="forum.php?topic=' . $lastpost . '">DL</a>]';
                            ?> |
                                <a href="comps.php">Comps</a><?php
                                echo $user_class->comps ? " [<span style='color:lime;'>" . $user_class->comps . '</span>]</a>' : '';
                                ?> |
                                <a href="gameguide.php">Game Guide</a> |
                                <a href="itempedia.php">Itempedia</a> |
                                <a href="tos.php">TOS</a> |
                                <a href="rmstore.php" style="color:gold;">Upgrade Account</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="pos1" valign="top">
                <div class="topbox">
                    <table>
                        <tr>
                            <td colspan='2' width="99%"
                                style="font-weight:700;text-align:left;vertical-align:top;padding:0;" class="resize">
                                <div
                                    style="background:rgba(0,0,0,.75);width:200px;height:150px;margin:0;padding-left:25px;">
                                    <div style="margin:0 auto;white-space:nowrap;line-height:18.5px;">
                                        <br />
                                        &nbsp;<strong>ID: </strong> <?php
                                        echo $user_class->id;
                                        ?><br />
                                        &nbsp;<strong>Name: </strong> <?php
                                        echo $user_class->formattedname;
                                        ?><br />
                                        &nbsp;<strong>Level: </strong> <span class="level"><?php
                                        echo $user_class->level;
                                        ?></span><br />
                                        &nbsp;<strong>Money: </strong> <span style='color:gold;font-weight:700;'
                                            class='money'>$<!_-money-_!></span> <a href='bank.php?dep'><img
                                                src='images/deposit.gif' title='Deposit' alt='Deposit' /></a></span> <a
                                            href='sendmoney.php'><img src='images/send.jpg' title='Send'
                                                alt='Send' /></a><br />
                                        &nbsp;<strong>Points: </strong> <a href='spendpoints.php'
                                            style='color:white;font-weight:700;'><!_-points-_!></a> <a
                                            href='sendpoints.php'><img src='images/send.jpg' title='Send'
                                                alt='Send' /></a><br />
                                        &nbsp;<strong>Credits: </strong> <a href='rmstore.php'
                                            style='color:yellow;'><!_-credits-_!></a> <a href='sendcredits.php'><img
                                                src='images/send.jpg' title='Send' alt='Send' /></a>
                                    </div>
                                </div>
                            </td>
                            <td colspan='2' width="1%">
                                <div class='genBars' style="background:rgba(0,0,0,.75);height:150px;">
                                    <!_-genBars-_!>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <table class='top'>
                    <tr>
                        <td align="left">
                            <?php
                            echo $time;
                            ?>
                        </td>
                        <td align="right"><span class="resizethug"><?php
                        $db->query("SELECT userid FROM ofthes WHERE thugoth > 0 ORDER BY thugoth DESC, userid ASC LIMIT 1");
                        $db->execute();
                        $toth = $db->fetch_single();
                        if ($toth) {
                            echo '<a href="koth.php"><strong>Thug of the Hour:</strong></a> ' . formatName($toth) . '&nbsp;&nbsp;&middot;&nbsp;&nbsp;';
                        }

                        $db->query("SELECT id FROM gangs ORDER BY oth DESC, id ASC LIMIT 1");
                        $db->execute();
                        $goth = $db->fetch_single();
                        if ($goth) {
                            $gi = new formatGang($goth);
                            echo '<a href="goth.php"><strong>Gang of the Hour:</strong></a> ' . $gi->formatTag() . '&nbsp;&nbsp;&middot;&nbsp;&nbsp;';
                        }

                        if (bbrunning()) {
                            $pos = array(
                                array(
                                    'level',
                                    'Levels Gained'
                                ),
                                array(
                                    'attackswon',
                                    'Attacks Won'
                                ),
                                array(
                                    'attackslost',
                                    'Attacks Lost'
                                ),
                                array(
                                    'busts',
                                    'Busts'
                                ),
                                array(
                                    'crimes',
                                    'Crimes'
                                )
                            );
                            $rand = rand(0, 4);
                            $db->query("SELECT userid FROM bbusers ORDER BY {$pos[$rand][0]} DESC LIMIT 1");
                            $db->execute();
                            if ($db->num_rows()) {
                                $koth = $db->fetch_single();
                                $rtn = '<a href="thugwar.php"><strong>Thug Wars(' . $pos[$rand][1] . '):</strong></a> ' . formatName($koth);
                                print $rtn;
                            }
                        }
                        ?></span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width:100%;border-spacing:0px;margin-top:5px;">
                    <tr>
                        <td valign="top" width="175">
                            <div class="contenthead">Menu</div>
                            <a class="leftmenu" href="index.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Home</a>
                            <a class="leftmenu" href="dailies.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Check Dailies</a>
                            <a class="leftmenu" href="tavern.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> <!_-GCLINK-_!></a>
                            <a class="leftmenu" href="inventory.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Inventory</a>
                            <a class="leftmenu" href="city.php" style="font-weight:700;"><img src="images/point.png"
                                    width="16" height="16" border="0" /> <!_-cityname-_!> </a>
                            <a class="leftmenu" href="bank.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Bank</a>
                            <a class="leftmenu" href="gym.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Gym </a>
                            <a class="leftmenu" href="<!_-crimepage-_!>.php"><img src="images/point.png" width="16"
                                    height="16" border="0" /> Crime</a>
                            <a class="leftmenu" href="<?php
                            echo !$user_class->gang ? "creategang.php" : "gang.php";
                            ?>"><img src="images/point.png" width="16" height="16" border="0" /> Your Gang</a>
                            <a class="leftmenu" href="gangmail.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Gang Mail [<!_-gmail-_!>]</a>
                            <a class="leftmenu" href="pms.php?view=inbox"><img src="images/point.png" width="16"
                                    height="16" border="0" /> Mailbox [<span class='mail'><!_-mail-_!></span>]</a>
                            <a class="leftmenu" href="events.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Events [<span class='events'><!_-events-_!></span>]</a>
                            <a class="leftmenu" href="jail.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Jail <span class='inmates'><!_-jail-_!></span></a>
                            <a class="leftmenu" href="hospital.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Hospital <!_-hospital-_!></a>
                            <a class="leftmenu" href="petjail.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Dog Pound <span class='animals'><!_-jail2-_!></span></a>
                            <a class="leftmenu" href="hitlist.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Hitlist [<span class='hitlist'><!_-hitlist-_!></span>]</a>
                            <a class="leftmenu" href="missions.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Missions [<!_-missions-_!>]</a>
                            <a class="leftmenu" href="backalley.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> <span style="color:yellow;">Back Alley</span></a>
                            <a class="leftmenu" href="smartads.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Smart Ads</a>
                            <a class="leftmenu" href="spylog.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Spy Log</a>
                            <a class="leftmenu" href="notepad.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Notepad</a>
                            <a class="leftmenu" href="search.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Thug Search</a>
                            <a class="leftmenu" href="classifieds.php"><img src="images/point.png" width="16"
                                    height="16" border="0" /> Newspaper</a>
                            <a class="leftmenu" href="chat.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Thug Chat <!_-CHAT_USERS-_!></a>
                            <a class="leftmenu" href="forum.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Forums</a>
                            <a class="leftmenu" href="index.php?action=logout" style="font-weight:700;"><img
                                    src="images/point.png" width="16" height="16" border="0" /> Logout</a><?php
                                    if ($user_class->gm) {
                                        ?>
                                <div class="space"></div>
                                <div class="contenthead">Staff Panel</div>
                                <a class="leftmenu" href="gmpanel.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> GM Panel</a>
                                <a class="leftmenu" href="managetickets.php"><img src="images/point.png" width="16"
                                        height="16" border="0" /> Support Desk [<!_-tickets-_!>]</a><?php
                                    }
                                    if ($user_class->fm) {
                                        ?>
                                <div class="space"></div>
                                <div class="contenthead">Staff Panel</div>
                                <a class="leftmenu" href="fmpanel.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> FM Panel</a><?php
                                    }
                                    if ($user_class->admin) {
                                        ?>
                                <div class="space"></div>
                                <div class="contenthead">Staff Panel</div>
                                <a class="leftmenu" href="gmpanel.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> GM Panel</a>
                                <a class="leftmenu" href="fmpanel.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> FM Panel</a>
                                <a class="leftmenu" href="managetickets.php"><img src="images/point.png" width="16"
                                        height="16" border="0" /> Support Desk [<!_-tickets-_!>]</a>
                                <div class="space"></div>
                                <div class="contenthead">Admin Panel</div>
                                <a class="leftmenu" href="staff_rtstore.php"><img src="images/point.png" width="16"
                                        height="16" border="0" /> Manage RTStore</a>
                                <a class="leftmenu" href="staff_missions.php"><img src="images/point.png" width="16"
                                        height="16" border="0" /> Manage Missions</a>
                                <a class="leftmenu" href="control.php?page=referrals"><img src="images/point.png" width="16"
                                        height="16" border="0" /> Referrals [<!_-referrals-_!>]</a>
                                <a class="leftmenu" href="paypal_log.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> Paypal Logs</a>
                                <a class="leftmenu" href="gmpanel.php?page_action=maillog"><img src="images/point.png"
                                        width="16" height="16" border="0" /> Mail log</a>
                                <a class="leftmenu" href="statcounts.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> Stat Counts</a>
                                <a class="leftmenu" href="control.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> Admin Stuff</a>
                                <a class="leftmenu" href="editfeatures.php"><img src="images/point.png" width="16"
                                        height="16" border="0" /> Edit crime/house</a>
                                <a class="leftmenu" href="massmail.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> Mass Mail</a>
                                <a class="leftmenu" href="staff_mail.php"><img src="images/point.png" width="16" height="16"
                                        border="0" /> Mass Emailer</a><?php
                                    }
                                    ?>
                            <div class="space"></div>
                            <div class="contenthead">Pet</div>
                            <a class="leftmenu" href="pethof.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Pet Hall Of Fame</a>
                            <a class="leftmenu" href="petshop.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Pet Shop</a>
                            <a class="leftmenu" href="petgym.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Pet Gym</a>
                            <a class="leftmenu" href="petcrime.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Pet Crime</a>
                            <a class="leftmenu" href="pettrack.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> <span style="color:yellow;">Pet Track</span></a>
                            <a class="leftmenu" href="mypets.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> My Pets</a>
                            <a class="leftmenu" href="petspylog.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Pet Spy Log</a>
                            <div class="space"></div>
                            <div class="contenthead">Support</div>
                            <a class="leftmenu" href="tickets.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Support Desk</a>
                            <a class="leftmenu" href="bbcode.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> BBCode Help</a>
                            <a class="leftmenu" href="forum.php?id=8"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Help Forum</a>
                            <div class="space"></div>
                            <div class="contenthead">Account</div>
                            <a class="leftmenu" href="preferences.php"><img src="images/point.png" width="16"
                                    height="16" border="0" /> Edit Account</a>
                            <a class="leftmenu" href="cpassword.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Change Password</a>
                            <a class="leftmenu" href="contactlist.php"><img src="images/point.png" width="16"
                                    height="16" border="0" /> Contact List</a>
                            <a class="leftmenu" href="ignorelist.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Ignore List</a>
                            <a class="leftmenu" href="rmstore.php"><img src="images/point.png" width="16" height="16"
                                    border="0" /> Upgrade Account</a>
                        </td>
                        <td valign="top">
                            <table style="width:100%;border-spacing:0px;">
                                <tr>
                                    <td>
                                        <table class="content"
                                            style="border-spacing:0px;align:right;margin-left:3px;margin-top:-1px;">
                                            <?php
                                            if (array_key_exists('harbinge', $_GET) && $user_class->admin) {
                                                if ($_GET['harbinge'] == $user_class->id)
                                                    $mtg->error("You can't take over your own account");
                                                $db->query("SELECT id, admin FROM grpgusers WHERE id = ?");
                                                $db->execute(array(
                                                    $_GET['harbinge']
                                                ));
                                                if (!$db->num_rows())
                                                    $mtg->error("That player doesn't exist");
                                                $user = $db->fetch_row(true);
                                                if ($user['admin'])
                                                    $mtg->error("You can't take over another administrator's account");
                                                Send_Event($_GET['harbinge'], '[-_USERID_-] has temporarily taken control of your account. Feel free to continue playing as normal. You\'ll receive another event when full control has been returned to you', $user_class->id);
                                                $_SESSION['harbinged'] = $_SESSION['id'];
                                                $_SESSION['id'] = $_GET['harbinge'];
                                                $user_class = new User($_SESSION['id']);
                                                $mtg->success("Switched to " . $user_class->formattedname);
                                            }
                                            if (array_key_exists('harbinged', $_SESSION)) {
                                                ?>
                                                <h4><a href='?switch=back'>Switch back</a></h4><?php
                                            }
                                            if (array_key_exists('switch', $_GET) && $_GET['switch'] == 'back') {
                                                Send_Event($user_class->id, 'Control of your account has been returned to you');
                                                $_SESSION['id'] = $_SESSION['harbinged'];
                                                unset($_SESSION['harbinged']);
                                                $user_class = new User($_SESSION['id']);
                                                $mtg->success("You've switched back");
                                            }
                                            if (isset($set['admin'])) {
                                                ?>
                                                <tr>
                                                    <td class='contenthead'>Admin Notification</td>
                                                </tr>
                                                <tr>
                                                    <td class='contentcontent center'><?php
                                                    echo $mtg->format($set['admin'], true);
                                                    ?></td>
                                                </tr><?php
                                            }
                                            if (isset($_SESSION['refilled'])) {
                                                echo Message("You've refilled your " . $_SESSION['refilled']['type'] . " for " . $_SESSION['refilled']['cost'] . " point" . $mtg->s($_SESSION['refilled']['cost']));
                                                unset($_SESSION['refilled']);
                                            }
                                            $unifinished = -1;
                                            $gcfinished = 0;
                                            $warfinished = -1;

                                            $db->query("SELECT finish FROM uni WHERE playerid = ?");
                                            $db->execute(array(
                                                $user_class->id
                                            ));
                                            if ($db->num_rows())
                                                if (time() >= $db->fetch_single())
                                                    $unifinished = 1;

                                            if ($user_class->gang) {
                                                $db->query("SELECT crime FROM gangs WHERE id = ?");
                                                $db->execute(array(
                                                    $user_class->gang
                                                ));
                                                $crime = $db->fetch_single();
                                                if ($crime) {
                                                    $rank = new GangRank($user_class->grank);
                                                    if ($rank->crime) {
                                                        $db->query("SELECT ending FROM gangs WHERE id = ?");
                                                        $db->execute(array(
                                                            $user_class->gang
                                                        ));
                                                        if (time() >= $db->fetch_single())
                                                            $gcfinished = 1;
                                                    }
                                                }

                                                $db->query("SELECT timeending FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1");
                                                $db->execute(array(
                                                    $user_class->gang,
                                                    $user_class->gang
                                                ));
                                                if ($db->num_rows())
                                                    if (time() >= $db->fetch_single())
                                                        $warfinished = 1;
                                            } else {
                                                $db->query("SELECT id FROM ganginvites WHERE playerid = ?");
                                                $db->execute(array(
                                                    $user_class->id
                                                ));
                                                if ($db->num_rows())
                                                    echo Message("<a href='ganginvites.php'>You have new gang invites</a>");
                                            }
                                            $rtn = "none";
                                            $db->query("SELECT reqid FROM rel_requests WHERE player = ?");
                                            $db->execute(array(
                                                $user_class->id
                                            ));
                                            if ($db->num_rows())
                                                $rtn = "<a href='rel_requests.php'>You have new relationship requests</a>";

                                            if ($rtn != "none")
                                                echo Message($rtn);
                                            $db->query("SELECT userid, `desc` FROM smartads WHERE `timestamp` >= ? ORDER BY RAND() LIMIT 1");
                                            $db->execute(array(
                                                time() - 900
                                            ));
                                            if ($db->num_rows()) {
                                                $row = $db->fetch_row(true);
                                                require_once __DIR__ . '/jbbcode/Parser.php';
                                                $ad_parser = new jBBCode\Parser;
                                                $ad_parser->loadDefaultCodes();
                                                $ad_parser->parse($mtg->format($row['desc']));
                                                echo Message("<span style='color:red;font-weight:700;'>Ad:</span> " . $ad_parser->getAsHTML() . " - " . formatName($row['userid']), 'Player Advertisement');
                                            }
                                            if (isset($_GET['collect']) && !$user_class->hourlyClaim) {
                                                $db->query("UPDATE grpgusers SET points = points + 25, hourlyClaim = 1 WHERE id = ?");
                                                $db->execute(array(
                                                    $user_class->id
                                                ));
                                                $user_class->points += 25;
                                                $user_class->hourlyClaim = 1;
                                            }
                                            $messages = array();
                                            if (!$user_class->hourlyClaim)
                                                $messages[] = "<a href='?collect'><font color='red'><strong>Click here to collect 25 points.</strong></font></a>";
                                            if ($user_class->invincible > time())
                                                $messages[] = "<font color='red'><strong>You're invincible for a further " . howlongleft($user_class->invincible) . ".</strong></font>";
                                            if ($user_class->protectionact > time())
                                                $messages[] = "<strong>You are currently protected from being attacked for another " . howlongleft($user_class->protectionact) . ". <a href='?removeprotection'>[Remove Protection]</a></strong>";
                                            if ($user_class->news)
                                                $messages[] = "<a href='forum.php?id=1'>You have unread news!</a>";
                                            if ($user_class->hospital)
                                                $messages[] = "<a href='hospital.php'>You're in the hospital for " . $mtg->time_format($user_class->hospital) . ".</a>";
                                            if ($user_class->jail)
                                                $messages[] = "<a href='jail.php'>You're in the jail for " . $mtg->time_format($user_class->jail) . ".</a>";
                                            if ($user_class->drugused)
                                                $messages[] = "<a href='inventory.php'>You are under the influence of drugs [" . howlongleft($user_class->drugtime) . " left]</a>";
                                            if ($unifinished == 1)
                                                $messages[] = "<a href='completeuni.php'><strong>Click here to complete your course at the university.</strong></a>";
                                            if ($gcfinished)
                                                $messages[] = "<a href='completegc.php'><strong>Click here to complete your gang crime.</strong></a>";
                                            if ($warfinished == 1)
                                                $messages[] = "<a href='completegw.php'><strong>Click here to complete your gang war.</strong></a>";
                                            if (count($messages))
                                                echo Message(implode('<br />', $messages));
                                            function formatOnline($la)
                                            {
                                                return (time() - $la < 900) ? "<span style='color:green;padding:2px;font-weight:700;'>[online]</span>" : "<span style='color:red;padding:2px;'>[offline]</span>";
                                            }
                                            $complete = 1;
                                            ?>