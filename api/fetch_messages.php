<?php
include "../database/pdo_class.php";
include "../database/functions.php";
// Fetch the latest 50 messages
$db->query("SELECT * FROM globalchat ORDER BY id DESC LIMIT 50");

// Format the user names using the formatName function
foreach ($messages as &$message) {
    $message['formatted_name'] = formatName($message['playerid']); // Format the name using your custom function
}   
$messages = $db->fetch_row();

echo json_encode($messages);
?>
