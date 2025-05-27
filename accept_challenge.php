<?php
// accept_challenge.php
require 'header.php';  // This should set up $db and session

$uid = $_SESSION['uid'];

// Prepare the query using placeholders
$db->query("SELECT * FROM rps_challenges WHERE challenged = :uid AND state = 'issued'");

// Bind the UID to the query
$db->bind(':uid', $uid);

// Execute the query
$db->execute();

// Loop through the results using fetch_row()
while ($row = $db->fetch_row()) {
    echo "You have been challenged by user id " . htmlspecialchars($row['challenger']) .
         ". <a href='accept_challenge_action.php?id=" . urlencode($row['id']) . "'>Accept</a><br>";
}
?>
