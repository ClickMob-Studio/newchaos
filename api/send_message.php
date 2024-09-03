<?php
session_start(); // Start the session to access session variables

include "../database/pdo_class.php";

// Get the player ID from the session
$playerid = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
$body = isset($_POST['body']) ? trim($_POST['body']) : '';
$timesent = time(); // Current timestamp

if ($playerid > 0 && !empty($body)) {
    // Insert the message into the database
    $db->query("INSERT INTO globalchat (playerid, timesent, body) VALUES (:playerid, :timesent, :body)");
    $db->bind(':playerid', $playerid);
    $db->bind(':timesent', $timesent);
    $db->bind(':body', $body);
    if ($db->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Message sending failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
}
?>
