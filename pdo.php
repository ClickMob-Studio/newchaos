<?php
$db_host = 'localhost';
$db_name = 'game';
$db_user = 'chaoscity_co';
$db_pass = '3lrKBlrfMGl2ic14';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set PDO to throw exceptions on error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle connection errors gracefully
    die("Connection failed: " . $e->getMessage());
}