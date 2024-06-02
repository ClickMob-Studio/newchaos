<?php
require "ajax_header.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$user_class = new User($_SESSION['id']);


echo json_encode(["success" => true, "user" => $user_class]);
?>
