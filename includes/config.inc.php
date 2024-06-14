<?php
// Enable error reporting for debugging (comment out in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection setup
$host    = 'localhost';
$ln      = 'chaoscit_user';
$pw      = '3lrKBlrfMGl2ic14';
$db      = 'chaoscit_game';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $ln, $pw, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Unable to connect to database: " . $e->getMessage());
}

