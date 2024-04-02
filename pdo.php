<?php
$db_host = 'localhost';
$db_name = 'chaoscit_game';
$db_user = 'chaoscit_user';
$db_pass = '3lrKBlrfMGl2ic14';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}