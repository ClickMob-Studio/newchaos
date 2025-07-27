<?php
include "ajax_header.php"; // Adjust this path as necessary

// Assuming $db is your database connection
if (isset($_POST['action']) && $_POST['action'] == 'placeBet') {
    $amnt = filter_input(INPUT_GET, 'amnt', FILTER_SANITIZE_NUMBER_INT);
    $curr = htmlspecialchars($_POST['curr'] ?? '', ENT_QUOTES, 'UTF-8');

    $IP = $_SERVER['REMOTE_ADDR'];

    // Further validation here (e.g., check if amount meets the minimum bet requirement)

    // Prepare and execute query
    $query = "INSERT INTO fiftyfifty (userid, amnt, currency, `timestamp`, betterip) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?)";
    if ($stmt = $db->query($query)) { // Assuming $db is a PDO instance
        $stmt->execute(array($user_class->id, $amnt, $curr, $IP)); // Adjust to match your session/user data
        echo "Success";
    } else {
        echo "Error";
    }
}
?>