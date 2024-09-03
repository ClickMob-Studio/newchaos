<?php

include "../database/pdo_class.php";
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

include_once "../includes/functions.php";
// Fetch the latest 50 messages from the globalchat table
$db->query("SELECT * FROM globalchat ORDER BY id DESC LIMIT 50");
$messages = $db->fetch_row();

// Check if messages are fetched correctly
if (!$messages || empty($messages)) {
    echo "No messages fetched or database query error.";
    exit;
}

// Format the user names using the formatName function
foreach ($messages as &$message) {
    // Check if playerid exists and is valid before calling formatName
    if (isset($message['playerid']) && !empty($message['playerid'])) {
        // Call formatName with playerid to get the formatted name
        $formattedName = formatName($message['playerid']);
        // Add the formatted name to the message array
        $message['formatted_name'] = !empty($formattedName) ? $formattedName : "Unknown User"; // Fallback if formatName returns empty
    } else {
        $message['formatted_name'] = "Unknown User"; // Default name if playerid is missing or invalid
    }
}


// Encode the modified array into JSON format and output it
echo json_encode($messages);
?>
