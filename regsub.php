<?php
session_start();
if(!empty($_POST['tos'])) die();

mysql_select_db('game', mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14'));
$password = $_POST['pass'];
$conpass = $_POST['conpass'];
$username = mysql_real_escape_string($_POST['username']);
$email = mysql_real_escape_string($_POST['email']);
$gender = $_POST['gender'];
$cap = $_POST['cap'];
if($cap != $_SESSION['cap'])
    die("Invalid Captcha.");
$_SESSION['cap'] = '';
if (strlen($password) < 6)
    error();
if (strlen($username) < 1)
    error();
if (strlen($username) > 20)
    error();
if ($password != $conpass)
    error();

if(empty($username))
    die("Invalid Details");

$email = strip_tags($email);
$email = addslashes($email);
//if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
    //error();
$q = mysql_query("SELECT id FROM grpgusers WHERE email LIKE '$email'");
$r = mysql_fetch_array($q);
if (!empty($r))
    error();
$IP = ($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$IP = addslashes($IP);
$IP = mysql_real_escape_string($IP);
$IP = strip_tags($IP);
$username = strip_tags($username);
$username = addslashes($username);
$q = mysql_query("SELECT id FROM grpgusers WHERE username LIKE '$username' OR loginame LIKE '$username' OR signupip = '$IP' OR ip = '$IP'");
$r = mysql_fetch_array($q);
if (!empty($r))
    error();
if ($gender != "Male" && $gender != "Female")
    error();
$pass = sha1($password);
$aprotection = time() + 86400;
$activationCode = substr(md5(mt_rand()), 0, 7);


mysql_query("INSERT INTO grpgusers (signupip, username, password, email, signuptime, loginame, gender, activate, mprotection, aprotection, activation_code) VALUES ('$IP', '$username', '$pass', '$email', unix_timestamp(), '$username', '$gender', 0, '$aprotection', '$aprotection', '$activationCode')");
$newid = mysql_insert_id();
mysql_query("INSERT INTO referrals (`when`, referrer, referred) VALUES (unix_timestamp(), {$_POST['referer']}, $newid)");
mysql_query("INSERT INTO sessions VALUES($newid, '{$_COOKIE['PHPSESSID']}', 'emptyfornow')");
mysql_query("INSERT INTO ofthes (userid)VALUES($newid)");
$_SESSION['id'] = $newid;
$msgtext = "
[center]Welcome to [b][color=red]Chaos City![/color][/b]

You have been credited 3 free T1 VIP Days

Hello!
Thank you for choosing us at CC. 
Please pop any game suggestions into our new Suggestions box!

Good luck and a warm welcome to Chaos City! [/center]

CC Staff.";
$parent = ($_POST['parent'] != 0) ? $_POST['parent'] : floor(time() / (uniqid(rand(1, 20), true) + uniqid(rand(1, 200))) - rand(100, 1000));
$subject = "Welcome to Chaos City - <font color=red>Please Read</font>";
;
$msgtext = strip_tags($msgtext);
$msgtext = nl2br($msgtext);
$msgtext = addslashes($msgtext);
$result = mysql_query("INSERT INTO `pms` (id,`to`, `from`, timesent, subject, msgtext) VALUES ('', $newid, 1, unix_timestamp(), '$subject', '$msgtext')");
header("Location: index.php");
function error() {
    header("Location: register.php");
}
?>
