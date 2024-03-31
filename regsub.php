<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Database connection
$dbHost = 'localhost';
$dbUser = 'chaoscity_co';
$dbPass = '3lrKBlrfMGl2ic14';
$dbName = 'game';

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);

    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, display error message and exit
    die("Database connection failed: " . $e->getMessage());
}

