<?php

error_reporting(E_ALL);

// Error log to capture any issues
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');

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
?>
