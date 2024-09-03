<?php
include "../database/pdo_class.php";
// Get data from POST request
$playerid = isset($_POST['playerid']) ? intval($_POST['playerid']) : 0;
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
