<?php

include "ajax_header.php";

$user_class = new User($_SESSION['id']);

// Calculate the remaining time for Pack 1
$currentTime = time();
$remainingTime = $user_class->pack1time - $currentTime;

// Return the remaining time in seconds as JSON data
header("Content-Type: application/json");
echo json_encode(array("remainingTime" => $remainingTime));