<?php
include_once "classes.php";
include_once "codeparser.php";
include_once "database/pdo_class.php";
include_once "dbcon.php";

$query = "SELECT * FROM user_logs ORDER BY timestamp DESC LIMIT 10";
$db->query($query);
$db->execute();
$logs = $db->fetch_row();

echo "<table border='1'>";
echo "<tr><th>Timestamp</th><th>User</th><th>Description</th></tr>";
foreach ($logs as $log) {
    $username = formatName($log['user_id']);
    $timeAgo = howlongago($log['timestamp']);
    echo "<tr><td>{$timeAgo}</td><td>{$username}</td><td>{$log['description']}</td></tr>";
}
echo "</table>";