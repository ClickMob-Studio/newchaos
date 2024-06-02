<?php
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$headers = apache_request_headers();
if (isset($headers['Authorization'])) {
    $session_id = str_replace('Bearer ', '', $headers['Authorization']);
    session_id($session_id);
}

session_start();

file_put_contents('php://stderr', print_r($_POST, TRUE));
file_put_contents('php://stderr', print_r($_SESSION, TRUE)); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $data['user_id'];

    // Validate the session token and user ID
    if (!isset($_SESSION['id']) || $_SESSION['id'] !== $user_id) {
        echo json_encode(["success" => false, "message" => "Unauthorized"]);
        exit();
    }

    $user_class = new User($_SESSION['id']);

    echo json_encode(["success" => true, "user" => $user_class]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
