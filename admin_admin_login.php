<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

session_start(); // Start the session to use session_id()

// Include the database class
require 'database/pdo_class.php';


// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$password = $data['password'];

// Hash the password using SHA-1
$hashed_password = sha1($password);

// Prepare and execute the query securely
$sql = "SELECT id, username FROM grpgusers WHERE username = :username AND password = :password";
$db->query($sql);
$db->bind(':username', $username);
$db->bind(':password', $hashed_password);
$result = $db->fetch_row(true);

// Check if a user was found
if ($result) {
    echo json_encode(["success" => true, "user" => $result, "token" => session_id()]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}
?>
