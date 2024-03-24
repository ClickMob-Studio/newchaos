<?php

if (isset($_POST['submit'])) {
    $username = strip_tags($_POST["newname"]);
    $signuptime = time();
    $password = $_POST["newpass"];
    $password2 = $_POST["newpassagain"];
    $email = $_POST["email"];
    if ($_POST["gender"] == "1") { $gender = "Male"; }
        elseif ($_POST["gender"] == "2") { $gender = "Female"; }

    $captcha_number = $_REQUEST["number"];
    $key=substr($_SESSION['key'],0,5);

    $checkuser = $mysql->query("SELECT * FROM `grpgusers` WHERE `username` = '$username' AND `username` != ''");
    $username_exist = mysql_num_rows($checkuser);

    $checkname = $mysql->query("SELECT * FROM `grpgusers` WHERE `gamename` = '$username' AND `gamename` != ''");
    $name_exist = mysql_num_rows($checkname);

    $checkemail = $mysql->query("SELECT * FROM `grpgusers` WHERE `email` = '$email' AND `email` != ''");
    $email_exist = mysql_num_rows($checkemail);

//     if (($_SERVER['HTTP_CF_CONNECTING_IP'] != "86.35.52.97") && ($_SERVER['HTTP_CF_CONNECTING_IP'] != "193.230.232.35")) {
//         $checkip = $mysql->query("SELECT * FROM `grpgusers` WHERE `ip`='".$_SERVER['HTTP_CF_CONNECTING_IP']."'");
//         $ip_exist = mysql_num_rows($checkip);
//         if ($ip_exist >= 1) {
//             $message .= "<div>You're only allowed ONE account!</div>";
//             echo $_SERVER['HTTP_CF_CONNECTING_IP'];
// //            Send_Event(1, "Too many accounts for IP (register): ".$_SERVER['REMOTE_ADDR'].".");
//         }
//     }

  $preg = '/[^0-9a-zA-Z\.\:\-\_]/';
  if (preg_match($preg, $username)){
    $message .= "<li>Username can only contain alpha-numeric characters and a few special characters. (0-9, a-z, A-Z, '.', ':', '-', '_')</li>";
  }
  if($username_exist > 0){
    $message .= "<li>Username already used.</li>";
  }
  if($email_exist > 0){
    $message .= "<li>E-mail already used.</li>";
  }
  if(strlen($username) < 4 or strlen($username) > 15){
    $message .= "<li>Username must be between 4 and 15 characters.</li>";
  }
  if(strlen($password) < 4 or strlen($password) > 15){
    $message .= "<li>Password must be between 4 and 15 characters.</li>";
  }
  if($password != $password2){
    $message .= "<li>Your passwords don't match. Please try again.</li>";
  }
  if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
    $message .= "<li>The e-mail address you entered was invalid.</li>";
  }
  if ($captcha_number != $key) {
    $message .= "<li>Invalid image verification code.</li>";
  }
  if ($_POST["gender"] == "0") {
    $message .= "<li>Please select your Gender.</li>";
  }
  if ($_POST["agree"] != "yes") {
    $message .= "<li>You must agree to the Terms of Service.</li>";
  }

  //insert the values
  if (!isset($message)){
    $vercode = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
    $vermessage = "Hi $username and welcome to worldofmobsters.com\n\nPlease click the link below to activate your account on worldofmobsters.com\n\n http://worldofmobsters.com/verify.php?code=" . $vercode . "\n\nOr simply go to http://worldofmobsters.com/verify.php and enter the code: ".$vercode." \n\n\n Regards,\nworldofmobsters Staff\nhttp://worldofmobsters.com";
    $title = "worldofmobsters account activation";
    mail($email,$title,$vermessage,"From: worldofmobsters <webmaster@worldofmobsters.com>\r\n");

    $result= $mysql->query("INSERT INTO `grpgusers` (ip, username, gamename, password, rmdays, email, signuptime, lastactive, lasttravel, jail, hospital, gender, ref_site, campaign_id, active)".
    "VALUES ('".$_SERVER['REMOTE_ADDR']."', '$username', '$username', '$password', '3', '$email', '$signuptime', '$signuptime', '$signuptime', '$signuptime', '$signuptime', '$gender', '".$_COOKIE["_SGrefgangstahost"]."', '".$_SESSION['cid']."', '$vercode')");
    $result01 = $mysql->query("INSERT INTO `grpgusers_extra` VALUES ('".mysql_insert_id()."', '', '', '', '', '', '')");
    echo Message('Your account has been created successfully!.<br>You will be redirected to the account activation page in 10 seconds. Please login using your credentials. <meta http-equiv="refresh" content="5;url=https://worldofmobsters.com/">');
    echo '
            <!-- Game-Advertising-Online.com Action Tracker -->
            <img src="http://www.game-advertising-online.com/tracker.php?id=3321" width="0" height="0">
            <img src="http://www.advertiseyourgame.com/publish/pixel.php?id=870" height=0 width=0>
         ';

    $resultcheck = $mysql->query("SELECT `id` from `grpgusers` WHERE `username` = '".$username."'");
    $worked = mysql_fetch_array($resultcheck);
    $id = $worked['id'];

    $result22 = $mysql->query("INSERT INTO `inventory` VALUES ('$id', '204', '25', '0'), ('$id', '205', '25', '0')");
    $result3 = $mysql->query("INSERT INTO `events` (`to`, `timesent`, `text`) ".
    "VALUES ('$id', '$signuptime', 'Welcome to worldofmobsters.com.<br><br>To help you get started we are giving you a welcome pack:<br><font color=yellow><b>3 Respected Gangsta days</b></font><br><b>25 Newbie Prison Keys</b><br><b>25 Newbie Medical Certificates</b><br>(can be found in your <u>Inventory</u> in the <u>Misc</u> tab).<br><br>Before you start playing, make sure you read: <a href=''http://worldofmobsters.com/rules.php'' target=''_blank''>The Rules</a> and the <a href=''http://worldofmobsters.com/tos.php'' target=''_blank''>Terms of Service</a>.<br><br>Enjoy the game!<br><br>')");

    if ($_POST['referer'] != "") {
        $result = $mysql->query("INSERT INTO `referrals` (`when`, `referrer`, `referred`, `ref_site`)".
        "VALUES ('$signuptime', '".$_POST['referer']."', '".$id."', '".$_COOKIE["_SGrefgangstahost"]."')");
    } elseif ($_COOKIE["_SGrefgangstauser"] != "") {
        $result2 = $mysql->query("INSERT INTO `referrals` (`when`, `referrer`, `referred`, `ref_site`)".
        "VALUES ('$signuptime', '".$_COOKIE["_SGrefgangstauser"]."', '".$id."', '".$_COOKIE["_SGrefgangstahost"]."')");
    }
	die();
  }
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>FatalPlay - FREE Online Browser Based MMORPG Game</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta content="MSHTML 6.00.2900.2180" name=GENERATOR>
<meta name="description" content="Free Mobster MMORPG game. Sign up and play free today!">
<meta name="keywords" content="mobster game,free mobster game,free mmorpg,mmorpg,free mmo,free online mmorpg,free mmo games,free,game,mmog,rpg,free online rpg,online rpg,free rpg">
<link rel="stylesheet" href="css/style_main.css" type="text/css">
<script src="includes/jquery-1.7.2.js" type="text/javascript"></script>
<script src="includes/check.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#usernameLoading').hide();
    $('#username').blur(function(){
      $('#usernameLoading').show();
      $.post("check_reg.php", {
        username: $('#username').val()
      }, function(response){
        $('#usernameResult').fadeOut();
        setTimeout("finishAjax('usernameResult', '"+escape(response)+"')", 400);
      });
        return false;
    });
});

function finishAjax(id, response) {
  $('#usernameLoading').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}

function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=640,height=385,scrollbars=no');
return false;
}
</script>

<style>
    .dark-table-container {
        background-color: #1a1a1a;  /* Darker background */
        border-radius: 5px;
        box-shadow: 0 0 10px red;  /* Red glow */
        padding: 20px; /* Padding around the table */
    }
</style>
</head>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>FatalPlay - FREE Online Browser Based MMORPG Game</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta content="MSHTML 6.00.2900.2180" name=GENERATOR>
<meta name="description" content="Free Mobster MMORPG game. Sign up and play free today!">
<meta name="keywords" content="mobster game,free mobster game,free mmorpg,mmorpg,free mmo,free online mmorpg,free mmo games,free,game,mmog,rpg,free online rpg,online rpg,free rpg">
<link rel="stylesheet" href="css/style_main.css" type="text/css">
<script src="includes/jquery-1.7.2.js" type="text/javascript"></script>
<script src="includes/check.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#usernameLoading').hide();
    $('#username').blur(function(){
      $('#usernameLoading').show();
      $.post("check_reg.php", {
        username: $('#username').val()
      }, function(response){
        $('#usernameResult').fadeOut();
        setTimeout("finishAjax('usernameResult', '"+escape(response)+"')", 400);
      });
        return false;
    });
});

function finishAjax(id, response) {
  $('#usernameLoading').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}

function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=640,height=385,scrollbars=no');
return false;
}
</script>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="menu">
                <ul>
                    <li>
                        <a href="index.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home</a>
                    </li>
                    <li>
                        <a href="register.php">&nbsp;&nbsp;&nbsp;<span style="color: #e30303;">Register</span></a>
                    </li>
                    <li>
                        <a href="tos.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ToS</a>
                    </li>
                </ul>
            </div><!--menu-->
            
            <div class="loginsection">
                <form name='login' method='post' action='home.php'>
                <div class="fldname">
                    <div class="usertxt">
                        <p>Username</p>
                    </div><!--usertxt-->
                    <div class="usertxt" style="margin-left:7px;">
                        <p style="">Password (<a href='forgot.php' title='Reset Password'>Reset</a>)</p>
                    </div><!--usertxt-->
                <div class="flds">
                    <input type="text" name="username" class="ttx" style="font-size: 14px;" />
                    <input type="password" name="password" class="ttx" style="margin-left:3px; font-size: 14px;" />
                    <input type="hidden" name="submit" value="Submit">
                    <input type="image" name="submit" class="butt" value="Login" src="images/login_button.png" border="0" />
                </div><!--flds-->
                </form>
            </div><!--loginsection-->
        </div><!--container-->
    </div><!--header-->


<script type="text/javascript" src="includes/wz_tooltip.js"></script>

    .
    <div class="container">
        <div class="content">
            <div class="introtxt">
                                <div style="margin-left: 50px;" class="desc">
                    <p style="margin-top: 10px;">
                            <form name="form1" method="post" action="">
                            <div style="position: relative; width: 650px; height: 500px;">
                                <div style="background: #000; filter:alpha(opacity=60); /* IE */ -moz-opacity:0.6; /* Mozilla */ opacity: 0.6; /* CSS3 */ position: absolute; top: 0; left: 0; height: 100%; width:100%;"></div>
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                 <style>
    .content-table {
        width: 100%;
        border-collapse: collapse;
    }
    .content-table td {
        padding: 10px;
        border: 1px solid #ddd;
        font-family: 'Times New Roman', Times, serif;
        font-size: 14px;  /* Reducing font size */
        font-weight: normal;  /* Removing bold text */
    }
    .content-table h1, .content-table h2 {
        font-style: italic;  /* Making headers fancy with italic */
        font-weight: normal;  /* Removing bold text */
    }
</style>

<table class="content-table">
    <tr>
        <td>
            <h1><center>Blood Rivals: Pre-Registration</center></h1>
            
            <h2 style="background-color: #222; color: #fff; padding: 10px; border: 1px solid #fff; border-radius: 5px; font-size: 1.5em; box-shadow: 0 0 10px #fff;">Initial Release Date:</h2>
            <ul>
                <center>13th August 2023</center>
            </ul>
            
            <h2 style="background-color: #222; color: #fff; padding: 10px; border: 1px solid #fff; border-radius: 5px; font-size: 1.5em; box-shadow: 0 0 10px #fff;">Why Pre-Register?</h2>
            <ul>
                <li>Exclusive Rewards: Get exclusive in-game items and currency only available for pre-registered players.</li>
                <li>First Look: Be among the first to experience the streets, allies, and enemies in Blood Rivals.</li>
                <li>Join a Family Early: Form alliances, strategize, and plan your rise to power before the game goes live to the public.</li>
            </ul>
            
            <h2 style="background-color: #222; color: #fff; padding: 10px; border: 1px solid #fff; border-radius: 5px; font-size: 1.5em; box-shadow: 0 0 10px #fff;">Game Features:</h2>
            <ul>
                <li>Dive deep into compelling storylines, filled with intrigue, betrayal, and suspense.</li>
                <li>Control territories, manage illegal businesses, and grow your empire.</li>
                <li>Engage with players worldwide, form families, and take down rival gangs.</li>
                <li>Participate in daily missions, challenges, and events.</li>
                <li>Customize your mobster, upgrade your arsenal, and set your mark in the mafia world.</li>
            <td>Mickey</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Godfather</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Lucky</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Krampus</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Kane</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>HitGirl</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MrsKxD</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Tarja</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Sylvie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>remob</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>iNeedKrackMoney</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Masher</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>M3RKY</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>coco</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Lucy</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Archangel</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Smokey</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Coont</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>PixieKat</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Oakster</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Bloodaxe</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>rajesh</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>chLone</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Jxe78</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Cowbell</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Angel_lover</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Demon</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Wolverine</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>IrisRose</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Reckless</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Grumpy</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>BigPapi</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Glitch</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Gota</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>JaymanMDev</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>PeroxideBride</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Strykervenom</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Evil</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>G_Money</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Opie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>P3ac3bob</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MONSTER</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MojoFuzz</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Rianna</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Sabres</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Keesha</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>CyrusTheVirus</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>CaptAs</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Zoalfekar</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Killer</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Ohgren</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Negan</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Raven</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Groucho</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Corubachic</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Villarreal</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Octavia</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Soul</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Moist</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MAJOR</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>TwoGlocks</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Damon</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>SeductiveMisery</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>thebondsman</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>womwomwom</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Lolgoats</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>test</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Distructor</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>PowerBand</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Bot1</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Th3-JoK3r</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>RickSanchez</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Ragina</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>SuckMyPniz</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>ScarMoon</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Unkown</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Gomer</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Angel-Kiss</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>ChristmasDog</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Shark</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>shittalker</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Babyface</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Mrs-Smith</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Billylee</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MrSmith</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Bot2</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>mamaRyan</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Mizzieb</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Foxus</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Hellish</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Bonkers</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>GodofWar</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Piper</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>XANAX</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Masse</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>FrankCastle</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>tripsikh</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>A_Saviour</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>PAX1</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Penny:Pistols</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>gerald1</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>PepperMint</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>kruug</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Daemon</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Saniax</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Geralt_of_Rivia</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>scrungle</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Dott</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>____</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>happy_gilmore</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Levy</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Mazie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Superman4466</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Malachai25</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Enoch</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Ghost</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Towelie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Themanjake</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Celestial</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>abbiesmith05</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>CowboyFromHell</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>PickleRick</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Junior</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>meursault</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Tyrannosaurus</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>noman</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>allen</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>testotorko</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Yahhhhh</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MzTempleBlonde</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Fatal</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Harambe</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Kushik</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>WiCkEd_GoOd</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>iiTranquility</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Roasties</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>TOMUCHFUN</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>A1fie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Slacker</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Jack</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>rohit</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Nolzyy</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Robbie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Lykaon</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Jezebel</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>test123</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Shocker</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>RoyJR</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Anna</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>LADYWOLF</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Lemon</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>hello</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Nitrored</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>DEMO</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>GeeGee</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>ginge</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Venox606</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>IXpoison</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>BEE_OTCH</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>HippieChick</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Ekolu</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Fkndumb</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Chunkz</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>zkiller</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Gotti</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Chellie</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>aimee</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Grumpeh</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Giotto</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Dobbs</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>dann</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Shazam</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Henrix</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Invincible</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Nabeel</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Nabeeel</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>huncho</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Hillerbilly</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>TheKxd</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>KING</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Degdatar</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Dude</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Dqlova</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Dqlova54</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Luxe</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>cowboyintexas</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>JLindsay27</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>MrOdge</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>De_Master</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>moosehead</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>catesjets</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Kliuty</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>RICH</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>jayhaze</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Exclusive</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>TheTester</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Lazarus</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Cuatro</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Satine</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Godson</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>ToxicV</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Dudette</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>TheDevilChild</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>StinkyPinky</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            <td>Grunt</td> <!-- Rival name -->            <td>...</td> <!-- Call function to get Date joined -->            <td>...</td> <!-- Call function to get Reward -->        </tr>            </ul>
            
                   </td>
    </tr>
</table>



 </div>
                            </div>
                          </form>
                    </p>
                </div><!--desc-->
            </div><!--introtxt-->
            
            <div class="footer" style="margin-top:90px;">
                <h2 style="color:#FFFFFF;">Copyright &copy; <a href="http://www.mob-gamez.com/" target="_blank"><font color="#e43b02">Mob-Gamez.com</font></a>. All Rights Reserved.</h2>
            <div style="margin-top: 10px;"></div>
            </div><!--footer-->
        </div><!--content-->
    </div><!--container-->
</div>


<!-- Quantcast Tag -->
<script type="text/javascript">
var _qevents = _qevents || [];

(function() {
var elem = document.createElement('script');
elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
elem.async = true;
elem.type = "text/javascript";
var scpt = document.getElementsByTagName('script')[0];
scpt.parentNode.insertBefore(elem, scpt);
})();

_qevents.push({
qacct:"p-9665Jicul1f0U"
});
</script>

<noscript>
<div style="display:none;">
<img src="//pixel.quantserve.com/pixel/p-9665Jicul1f0U.gif" border="0" height="1" width="1" alt="Quantcast"/>
</div>
</noscript>
<!-- End Quantcast tag -->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38872056-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>
