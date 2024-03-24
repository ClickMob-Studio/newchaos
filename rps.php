<?php
include 'header.php';



    // Begin the form
    echo "<form action='challenge_user.php' method='post'>";
    // Add an input field for the challenged user's id
    echo "Enter the ID of the user you want to challenge: ";
    echo "<input type='number' name='challenged'>";
    // Add a submit button
    echo "<input type='submit' name='challenge' value='Challenge'>";
    // End the form
    echo "</form>";

    // Fetch all users from the grpgusers table except for the current user
    $currentUserId = $_SESSION['uid'];
    $sql = "SELECT id, username FROM grpgusers WHERE id != $currentUserId";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Begin the form
        echo "<form action='test121.php' method='post'>";
        // Begin the select element
        echo "<select name='challenged'>";
        // For each user in the result
        while($row = mysqli_fetch_assoc($result)) {
            // Create an option element for the user
            echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
        }
        // End the select element
        echo "</select>";
        // Add a submit button
        echo "<input type='submit' name='challenge' value='Challenge'>";
        // End the form
        echo "</form>";
    } else {
        echo "No other users to challenge.";
    }






?>
