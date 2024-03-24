<?php
    // accept_challenge.php
    require 'header.php';  // assuming header.php sets up your database connection and session

    $uid = $_SESSION['uid'];
    $sql = "SELECT * FROM rps_challenges WHERE challenged=$uid AND state='issued'";
    
    // Execute the statement
    $result = mysql_query($sql, $conn);

    // Fetch values
    while ($row = mysql_fetch_assoc($result)) {
        echo "You have been challenged by user id " . $row['challenger'] . ". <a href='accept_challenge_action.php?id=" . $row['id'] . "'>Accept</a>";
    }
?>
