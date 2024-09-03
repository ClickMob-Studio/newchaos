<?php
include "../database/pdo_class.php";
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

include_once "../includes/functions.php";
include_once "../codeparser.php"; 

// Fetch the latest 50 messages from the globalchat table
$db->query("SELECT * FROM globalchat ORDER BY id DESC LIMIT 50");
$messages = $db->fetch_row();

if (!$messages || empty($messages)) {
    echo json_encode(['error' => 'No messages fetched or database query error.']);
    exit;
}

// Format the user names using the formatName function and parse BBCode
foreach ($messages as &$message) {
    if (isset($message['playerid']) && !empty($message['playerid'])) {
        $formattedName = formatName($message['playerid']);
        $message['formatted_name'] = !empty($formattedName) ? $formattedName : "Unknown User";
    } else {
        $message['formatted_name'] = "Unknown User";
    }

    // Parse the body of the message using the BBCode parser
    $message['body'] = BBCodeParse($message['body']); // Apply BBCode parsing to the message body
}

// Encode the modified array into JSON format and output it
echo json_encode(array_values($messages));