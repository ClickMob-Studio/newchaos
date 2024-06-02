<?php
require "ajax_header.php";

session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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
