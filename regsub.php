<?php
require_once 'includes/functions.php';

start_session_guarded();

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
$stmt = $pdo->prepare("SELECT id FROM grpgusers WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->rowCount() > 0) {
    errorRedirect("Username already exists.");
}

// Email uniqueness check
$stmt = $pdo->prepare("SELECT id FROM grpgusers WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
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
$stmt = $pdo->prepare("INSERT INTO grpgusers (ip, signupip, loginame, username, password, email, signuptime, gender, aprotection) VALUES (?, ?,?, ?, ?, ?, UNIX_TIMESTAMP(), ?, ?)");
$stmt->execute([$IP, $IP, $username, $username, $hashedPassword, $email, $gender, time() + 43200]);
$newid = $pdo->lastInsertId();

if (isset($_POST['referer'])) {
    $stmt = $pdo->prepare("INSERT INTO referrals (`when`, referrer, referred) VALUES (UNIX_TIMESTAMP(), ?, ?)");
    $stmt->execute([$_POST['referer'], $newid]);
}

$stmt = $pdo->prepare("INSERT INTO sessions VALUES (?, ?, 'emptyfornow')");
$stmt->execute([$newid, $_COOKIE['PHPSESSID']]);

$stmt = $pdo->prepare("INSERT INTO ofthes (userid) VALUES (?)");
$stmt->execute([$newid]);

// Add 1 to the total user count in cache
add_to_user_count();

// Redirect upon successful registration
$_SESSION['id'] = $pdo->lastInsertId();
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
$queryInsertOrUpdate = "INSERT INTO sessions (userid, sessionid) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE sessionid = VALUES(sessionid)";

$statementInsertOrUpdate = $db->prepare($queryInsertOrUpdate);

$statementInsertOrUpdate->execute([$_SESSION['id'], $randomKey]);
$_SESSION['token'] = $randomKey;
$newid = $_SESSION['id'];
$parent = ($_POST['parent'] != 0) ? $_POST['parent'] : floor(time() / (uniqid(rand(1, 20), true) + uniqid(rand(1, 200))) - rand(100, 1000));
$subject = "Welcome to Chaos City - <font color=ywllow>Please Read</font>";
$msgtext = strip_tags($msgtext);
$msgtext = nl2br($msgtext);
$msgtext = addslashes($msgtext);

$stmt = $pdo->prepare("INSERT INTO `pms` (id,`to`, `from`, timesent, `subject`, msgtext) VALUES ('', ?, 2, unix_timestamp(), ?, ?)");
$stmt->execute([$newid, $subject, $msgtext]);

header("Location: index.php");
exit;
?>