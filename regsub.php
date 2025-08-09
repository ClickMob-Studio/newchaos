<?php
require_once 'includes/functions.php';

start_session_guarded();

include_once "database/pdo_class.php";
include_once "dbcon.php";

// Function to redirect on error with message
function errorRedirect($errorMessage)
{
    $_SESSION['failmessage'] = $errorMessage;
    header("Location: home.php");
    exit;
}

// Input validation and sanitization
$username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = htmlspecialchars($_POST['pass'] ?? '', ENT_QUOTES, 'UTF-8');
$conpass = htmlspecialchars($_POST['conpass'] ?? '', ENT_QUOTES, 'UTF-8');
$gender = htmlspecialchars($_POST['gender'] ?? '', ENT_QUOTES, 'UTF-8'); // Ensure this is one of the expected values
$cap = htmlspecialchars($_POST['cap'] ?? '', ENT_QUOTES, 'UTF-8');

if ($cap != $_SESSION['cap']) {
    errorRedirect("Invalid Captcha.");
}

if (strlen($password) < 6 || strlen($password) > 20 || $password !== $conpass) {
    errorRedirect("Password validation failed.");
}

if (!$email) {
    errorRedirect("Invalid email address.");
}

// Username uniqueness check
$db->query("SELECT COUNT(*) FROM grpgusers WHERE username = ?");
$db->execute([$username]);
$count = $db->fetch_single();
if ($count > 0) {
    errorRedirect("Username already exists.");
}

// Email uniqueness check
$db->query("SELECT COUNT(*) FROM grpgusers WHERE email = ?");
$db->execute([$email]);
$count = $db->fetch_single();
if ($count > 0) {
    errorRedirect("Email already in use.");
}

// Password hashing using SHA-1
$hashedPassword = sha1($password);

// IP handling
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $IP = $_SERVER['REMOTE_ADDR'];
}
$IP = filter_var($IP, FILTER_VALIDATE_IP);

// Insert user into database
perform_query("INSERT INTO grpgusers (ip, signupip, loginame, username, password, email, signuptime, gender, aprotection) VALUES (?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), ?, ?)", [$IP, $IP, $username, $username, $hashedPassword, $email, $gender, time() + 43200]);
$newid = $db->insert_id();
if (isset($_POST['referer'])) {
    perform_query("INSERT INTO referrals (`when`, referrer, referred) VALUES (UNIX_TIMESTAMP(), ?, ?)", [$_POST['referer'], $newid]);
}

perform_query("INSERT INTO sessions VALUES (?, ?, 'emptyfornow')", [$newid, $_COOKIE['PHPSESSID']]);
perform_query("INSERT INTO ofthes (userid) VALUES (?)", [$newid]);

// Add 1 to the total user count in cache
add_to_user_count();

// Redirect upon successful registration
$_SESSION['id'] = $newid;
$msgtext = "
Welcome to [b][color=yellow]Chaos City![/color][/b]

To get you started we recommend reading our quick start guide: https://chaoscity.co.uk/gameguide.php

You have been credited with 3 free VIP days and 12-hours of protection to get you started!

Be sure to join our discord community: <a href='https://discord.gg/Pb7sTfhCnm'>https://discord.gg/Pb7sTfhCnm</a>

Thank you for choosing to play Chaos City. 

CC Staff.";
session_regenerate_id();

$bytes = openssl_random_pseudo_bytes(16);
$randomKey = bin2hex($bytes);

$db->query("INSERT INTO sessions (userid, sessionid) VALUES (?, ?) ON DUPLICATE KEY UPDATE sessionid = VALUES(sessionid)");
$db->execute([$_SESSION['id'], $randomKey]);

$_SESSION['token'] = $randomKey;
$newid = $_SESSION['id'];
$subject = "Welcome to Chaos City - <font color=ywllow>Please Read</font>";
$msgtext = strip_tags($msgtext);
$msgtext = nl2br($msgtext);
$msgtext = addslashes($msgtext);

perform_query("INSERT INTO `pms` (id, `to`, `from`, timesent, `subject`, msgtext) VALUES ('', ?, 2, unix_timestamp(), ?, ?)", [$newid, $subject, $msgtext]);

header("Location: index.php");
exit;
?>