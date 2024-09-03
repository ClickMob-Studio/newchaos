<?php
include "../database/pdo_class.php";
include "../includes/functions.php";
// Fetch the latest 50 messages
$db->query("SELECT * FROM globalchat ORDER BY id DESC LIMIT 50");

// Format the user names using the formatName function
foreach ($messages as &$message) {
    $message['formatted_name'] = formatName($message['playerid']); // Use formatName with playerid
}  
$messages = $db->fetch_row();

echo json_encode($messages);
?>
