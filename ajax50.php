<?php
include "ajax_header.php"; // Adjust this path as necessary

// Assuming $db is your database connection
if(isset($_POST['action']) && $_POST['action'] == 'placeBet') {
    $amnt = filter_input(INPUT_POST, 'amnt', FILTER_SANITIZE_NUMBER_INT);
    $curr = filter_input(INPUT_POST, 'curr', FILTER_SANITIZE_STRING);
    $IP = $_SERVER['REMOTE_ADDR'];

    // Further validation here (e.g., check if amount meets the minimum bet requirement)

    // Prepare and execute query
    $query = "INSERT INTO fiftyfifty (userid, amnt, currency, timestamp, betterip) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?)";
    if($stmt = $db->prepare($query)) { // Assuming $db is a PDO instance
        $stmt->execute([$_SESSION['user_id'], $amnt, $curr, $IP]); // Adjust to match your session/user data
        echo "Success";
    } else {
        echo "Error";
    }
}
?>
