<?php

require_once 'config.php';
$dbConfig = Config::db();
$db = new mysqli($dbConfig->host, $dbConfig->username, $dbConfig->password, $dbConfig->database);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
if (!$db->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $db->error);
}

date_default_timezone_set('UTC');

try {
    $db = new PDO("mysql:host=" . $dbConfig->host . ";dbname=" . $dbConfig->database, $dbConfig->username, $dbConfig->password);
    // Set PDO to throw exceptions on error
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle connection errors gracefully
    die("Connection failed: " . $e->getMessage());
}

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=" . $dbConfig->host . ";dbname=" . $dbConfig->database, $dbConfig->username, $dbConfig->password);

    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, display error message and exit
    die("Database connection failed: " . $e->getMessage());
}