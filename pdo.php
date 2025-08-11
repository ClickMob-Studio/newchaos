<?php

require_once 'config.php';

$dbConfig = Config::db();
$conn = new mysqli($dbConfig->host, $dbConfig->username, $dbConfig->password, $dbConfig->database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}