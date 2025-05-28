<?php
require 'header.php';

$uid = $_SESSION['uid'];

$db->query("SELECT * FROM rps_challenges WHERE challenged= ? AND state = 'issues'");
$db->execute($uid);
$result = $db->fetch_row();

foreach ($result as $row) {
    echo "You have been challenged by user id " . $row['challenger'] . ". <a href='accept_challenge_action.php?id=" . $row['id'] . "'>Accept</a>";
}
?>