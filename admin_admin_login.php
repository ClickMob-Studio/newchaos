<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");


// Custom log file for debugging
$log_file = '/home/chaoscit/api_error.log'; // Update this path as needed

session_start();
file_put_contents($log_file, "Session ID: " . session_id() . "\n", FILE_APPEND);

require 'database/pdo_class.php';
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$password = $data['password'];

$hashed_password = sha1($password);

$sql = "SELECT id, username FROM grpgusers WHERE username = :username AND password = :password";
$db->query($sql);
$db->bind(':username', $username);
$db->bind(':password', $hashed_password);
$result = $db->fetch_row(true);

if ($result) {
    $_SESSION['user_id'] = $result['id'];
    $_SESSION['username'] = $result['username'];
    $_SESSION['loggedin'] = true;

    file_put_contents($log_file, "Login successful. Session data: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

    echo json_encode(["success" => true, "user" => $result, "token" => session_id()]);
} else {
    file_put_contents($log_file, "Invalid credentials\n", FILE_APPEND);
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}
?>
