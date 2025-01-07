<?php
session_start();

include "dbcon.php";

// Function to redirect on error with message
function errorRedirect($errorMessage) {
    $_SESSION['failmessage'] = $errorMessage;
    header("Location: home.php");
    exit;
}

// Input validation and sanitization
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['pass']; // Consider further validating and sanitizing
$conpass = $_POST['conpass'];
$gender = $_POST['gender']; // Ensure this is one of the expected values
$cap = $_POST['cap'];

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
$stmt = $pdo->prepare("INSERT INTO grpgusers (signupip, loginame, username, password, email, signuptime, gender, aprotection) VALUES (?,?, ?, ?, ?, UNIX_TIMESTAMP(), ?, ?)");
$stmt->execute([$IP, $username, $username, $hashedPassword, $email, $gender, time() + 43200]);
$newid = $pdo->lastInsertId();
mysql_query("INSERT INTO referrals (`when`, referrer, referred) VALUES (unix_timestamp(), {$_POST['referer']}, $newid)");
mysql_query("INSERT INTO sessions VALUES($newid, '{$_COOKIE['PHPSESSID']}', 'emptyfornow')");
mysql_query("INSERT INTO ofthes (userid)VALUES($newid)");

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
;
$msgtext = strip_tags($msgtext);
$msgtext = nl2br($msgtext);
$msgtext = addslashes($msgtext);
$result = mysql_query("INSERT INTO `pms` (id,`to`, `from`, timesent, `subject`, msgtext) VALUES ('', $newid, 2, unix_timestamp(), '$subject', '$msgtext')");

header("Location: index.php");
exit;
?>
