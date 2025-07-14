<?php

require_once 'config.php';

$conn = new mysqli(Config::db()->host, Config::db()->username, Config::db()->password, Config::db()->database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}