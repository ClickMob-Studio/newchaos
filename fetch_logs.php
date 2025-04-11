<?php
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";
#include "includes/functions.php";

mysql_select_db('chaoscit_game', mysql_connect('localhost', 'chaoscit_user', '3lrKBlrfMGl2ic14'));

$query = "SELECT * FROM user_logs ORDER BY timestamp DESC LIMIT 10";
$result = mysql_query($query);
if (!$result) {
    die(json_encode(['error' => 'Query failed: ' . mysql_error()]));
}

echo "<table border='1'>";
echo "<tr><th>Timestamp</th><th>User</th><th>Description</th></tr>";
while ($log = mysql_fetch_assoc($result)) {
    $username = formatName($log['user_id']);
    $timeAgo = howlongago($log['timestamp']);
    echo "<tr><td>{$timeAgo}</td><td>{$username}</td><td>{$log['description']}</td></tr>";
}
echo "</table>";
?>