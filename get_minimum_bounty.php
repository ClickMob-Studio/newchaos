<?php
include "ajax_header.php";
mysql_select_db('ml2', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));

$targetID = $_GET['targetID'];

// Query the database to get the user details
$user_query = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = '$targetID'");
$user_data = mysql_fetch_array($user_query);

// Calculate the minimum bounty based on the user's level
$target_level = $user_data['level'];
$min_bounty = max(50000, $target_level * 1000);

echo $min_bounty; // Return the minimum bounty to the AJAX request
?>