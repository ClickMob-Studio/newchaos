<?php
include "ajax_header.php";

$db->query("SELECT * FROM user_logs ORDER BY timestamp DESC LIMIT 10");
$db->execute();

if (!$db->num_rows) {
    die();
}

echo "<table border='1'>";
echo "<tr><th>Timestamp</th><th>User</th><th>Description</th></tr>";
$result = $db->fetch_row();
foreach ($result as $log) {
    $username = formatName($log['user_id']);
    $timeAgo = howlongago($log['timestamp']);
    echo " <tr><td>{$timeAgo}</td><td>{$username}</td><td>{$log['description']}</td></tr>";
}
echo "</table>";
?>
