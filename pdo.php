<?php
$db_host = 'localhost';
$db_name = 'game';
$db_user = 'chaoscity_co';
$db_pass = '3lrKBlrfMGl2ic14';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}