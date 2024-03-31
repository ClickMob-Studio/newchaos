<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Database connection
$dbHost = 'localhost';
$dbUser = 'chaoscity_co';
$dbPass = '3lrKBlrfMGl2ic14';
$dbName = 'game';
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

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
$stmt = $mysqli->prepare("SELECT id FROM grpgusers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    errorRedirect("Username already exists.");
}

// Email uniqueness check
$stmt = $mysqli->prepare("SELECT id FROM grpgusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    errorRedirect("Email already in use.");
}

// Password hashing using SHA-1
$hashedPassword = sha1($password);

// IP handling
$IP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$IP = filter_var($IP, FILTER_VALIDATE_IP);

// Insert user into database
$stmt = $mysqli->prepare("INSERT INTO grpgusers (signupip, username, password, email, signuptime, gender) VALUES (?, ?, ?, ?, UNIX_TIMESTAMP(), ?)");
$stmt->bind_param("sssss", $IP, $username, $hashedPassword, $email, $gender);
if ($stmt->execute()) {
    $_SESSION['id'] = $mysqli->insert_id;
    header("Location: index.php");
    exit;
} else {
    errorRedirect("An error occurred during registration.");
}
?>
