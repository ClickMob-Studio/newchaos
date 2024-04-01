<?php
$targetIp = '154.47.113.178';

// Retrieve the IP address of the client
$clientIp = $_SERVER['REMOTE_ADDR'];

// Compare the client IP with the target IP
if ($clientIp != $targetIp) {
echo "be back soon!";
exit;
}
date_default_timezone_set('UTC');
$conn = mysql_connect("localhost", "chaoscity_co", '3lrKBlrfMGl2ic14') or die("<b>SQL ERROR:&nbsp;</b>" . mysql_error());
$db = mysql_select_db("game");
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);
if (!isset($_SESSION['id']) || $_SESSION['id'] != 1) {
    error_reporting(0);
}


$db_host = 'localhost';
$db_name = 'game';
$db_user = 'chaoscity_co';
$db_pass = '3lrKBlrfMGl2ic14';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
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
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, display error message and exit
    die("Database connection failed: " . $e->getMessage());
}