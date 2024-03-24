<?php
include 'dbcon.php';
session_start();
// Query to get users online in the last hour
$queryOnline = "SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC";
$statementOnline = $db->prepare($queryOnline);
$statementOnline->execute();
$usersOnline = $statementOnline->rowCount();

// Query to get users online in the last 24 hours
$queryOnline24 = "SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400 ORDER BY lastactive DESC";
$statementOnline24 = $db->prepare($queryOnline24);
$statementOnline24->execute();
$users24 = $statementOnline24->rowCount();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="lang" content="english">
    <meta name="robots" content="All">
    <title>Chaos City - Free Online Web RPG</title>
    <link href="assets/css/login.css" type="text/css" rel="stylesheet" />
    <link href="assets/css/game.css" type="text/css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="favicon.ico">
</head>
<body>
<div id="outer" class="wrap">
    <div id="top_bar" class="wrap">
        <div style="float:left;color:#910503;margin-left:5px;">
            <strong>Users Online: <?php echo $usersOnline; ?></strong> |
            <strong>Users Online in last 24 hours: <?php echo $users24; ?></strong>
        </div>
        <a href="login.php">Homepage</a> <a href="register.php">Create an account</a> <a href="tos.php">Terms of Service</a>
    </div>
    <div id="main" class="row">
        <div id="header" class="row">

        </div>
        <div id="content" class="row">
            <div id="left_side">
                <h2>Welcome to Chaos City!</h2>
                Chaos City is a Massively Multiplayer Online Role Playing Game, MMORPG for short.<br /><br />
                In this game, you play the role of a mobster who is leading a life of crime and deception.
                Join gangs and team up with your friends to kick ass, or go your own way.
                The choices are endless.<br /><br />
                <strong>Contact us at <a style='color:black;text-decoration:none;' href="mailto:support@chaoscityrpg.com">support@chaoscityrpg.com</a></strong><br /><br />

                <!-- <strong style='color:red;'>Countdown To beta Launch (<small>11/11/2016</small>)</strong><br /> -->
                <!-- <strong><span id="countdown" style="font-size:25px;"></span></strong><br /><br />-->
            </div>

            <div id="right_side">
                <div id="error_area">
                    <?php 
                    if(isset($_SESSION['failmessage'])){
                        echo '<div class="warning-msg">
                        <i class="fa fa-warning"></i>
                        '.$_SESSION['failmessage'].'
                      </div>';
                        unset($_SESSION['failmessage']);
                    }
                    ?>
                </div>
                <div id="login_panel">
                    <h3>:: Login Panel ::</h3>

                    <form method="POST"  action="login.php">
                        <input type="text" class="login" value="" name="username" placeholder="Username" />
                        <input type="password" class="login" value=""  name="password" placeholder="Password" />
                        <input type="submit" id="submit" name="submit" class="log_btn" value="Login" />
                    </form>
                    <a href="resend.php">Forgot Password?</a>
                </div>
            </div>
            <div class="spacer"></div>
        </div>


        <div id="bottom_content" class="row"></div>
    </div>
    <div id="footer" class="row">
        <span>Chaos City</span><br />
        &copy; 2015+ . All Rights Reserved.<br />
        <a href="">Privacy Policy.</a> <a href="">Terms of Services.</a> <a href="">Help Tutorial.</a> <a href="">Staff	</a>
    </div>
</div>
</body>
</html>
