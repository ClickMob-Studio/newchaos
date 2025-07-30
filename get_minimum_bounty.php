<?php
include "ajax_header.php";

include_once "classes.php";
include_once "database/pdo_class.php";

$targetID = $_GET['targetID'];

// Query the database to get the user details
$db->query("SELECT * FROM `grpgusers` WHERE `id` = ?");
$db->execute([$targetID]);
$user_data = $db->fetch_row(true);

// Calculate the minimum bounty based on the user's level
$target_level = $user_data['level'];
$min_bounty = max(50000, $target_level * 1000);

echo $min_bounty; // Return the minimum bounty to the AJAX request
?>