<?php
include "../database/pdo_class.php";

// Fetch the latest 50 messages
$db->query("SELECT * FROM globalchat ORDER BY id DESC LIMIT 50");
$messages = $db->fetch_row();

echo json_encode($messages);
?>
