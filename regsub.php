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
$stmt = $pdo->prepare("INSERT INTO grpgusers (signupip, loginame, username, password, email, signuptime, gender) VALUES (?,?, ?, ?, ?, UNIX_TIMESTAMP(), ?)");
$stmt->execute([$IP, $username, $username, $hashedPassword, $email, $gender]);
$newid = $pdo->lastInsertId();
mysql_query("INSERT INTO referrals (`when`, referrer, referred) VALUES (unix_timestamp(), {$_POST['referer']}, $newid)");
mysql_query("INSERT INTO sessions VALUES($newid, '{$_COOKIE['PHPSESSID']}', 'emptyfornow')");

// Redirect upon successful registration
$_SESSION['id'] = $pdo->lastInsertId();
header("Location: index.php");
exit;
?>
