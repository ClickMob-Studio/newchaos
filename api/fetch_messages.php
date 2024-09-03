<?php
include "../database/pdo_class.php";
include "../database/functions.php";

// Fetch the latest 50 messages from the globalchat table
$db->query("SELECT * FROM globalchat ORDER BY id DESC LIMIT 50");
$messages = $db->fetch_row();

// Format the player name using the formatName function
foreach ($messages as &$message) {
    // Ensure the formatName function is correctly called with playerid
    $message['formatted_name'] = formatName($message['playerid']);
}

echo json_encode($messages);
?>
