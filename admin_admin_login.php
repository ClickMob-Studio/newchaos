<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'includes/functions.php';

start_session_guarded();

require_once 'database/pdo_class.php';

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

    echo json_encode(["success" => true, "user" => $result, "token" => session_id()]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}
?>