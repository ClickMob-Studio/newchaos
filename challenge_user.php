<?php
    // challenge_user.php
    require 'header.php';  // assuming header.php sets up your database connection and session

    if (isset($_POST['challenge'])) {
        $challenger = $_SESSION['uid'];
        $challenged = $_POST['challenged'];
        $state = 'issued';
        
        $sql = "INSERT INTO rps_challenges (challenger, challenged, state) VALUES (?, ?, ?)";
        
        // Create a prepared statement
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bind_param('iis', $challenger, $challenged, $state);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "Challenge issued successfully.";
            $message = "You have been challenged to a game of Rock, Paper, Scissors by user id $challenger. <a href='accept_challenge.php?id=$challenger'>Click here</a> to accept.";
            send_event($challenged, $message);
        } else {
            echo "Error issuing challenge: " . $stmt->error;
        }
    }
?>
