<?php
include 'header.php';
 // ...
    // ...
    if (isset($_POST['challenge'])) {
        $challenger = $_SESSION['uid'];
        $challenged = $_POST['challenged'];
        $state = 'issued';
        $sql = "INSERT INTO rps_challenges (challenger, challenged, state) VALUES ($challenger, $challenged, $state)";
        if (mysqli_query($conn, $sql)) {
            echo "Challenge issued successfully.";
            $message = "You have been challenged to a game of Rock, Paper, Scissors by user id $challenger.";
            send_event($challenged, $message);
        } else {
            echo "Error issuing challenge: " . mysqli_error($conn);
        }
    }
    // ...


 // ...
    $uid = $_SESSION['uid'];
    $sql = "SELECT * FROM rps_challenges WHERE challenged=$uid AND state='issued'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "You have been challenged by user id " . $row['challenger'] . ". ";
            echo "<a href='accept_challenge.php?id=" . $row['id'] . "'>Accept</a> ";
            echo "<a href='decline_challenge.php?id=" . $row['id'] . "'>Decline</a>";
        }
    } else {
        echo "You have no pending challenges.";
    }
    // ...

    // ...
?>
