<?php
include 'header.php';  // Assuming header.php sets up the database connection and session

// Check if the user is logged in
if (!$user_class) {
    die("You must be logged in to access the Auction House.");
}

// Add item to auction house
if (isset($_POST['submit_auction'])) {
    $itemid = intval($_POST['itemid']);
    $min_bid = floatval($_POST['min_bid']);
    $duration = intval($_POST['duration']);  // in hours

    if ($min_bid <= 0 || $duration <= 0) {
        echo "Invalid bid or duration.";
        exit;
    }

    $end_time = date("Y-m-d H:i:s", strtotime("+$duration hours"));

    // Insert auction details into auction_house table
    $query = "INSERT INTO auction_house (item_id, seller_id, min_bid, end_time) VALUES (?, ?, ?, ?)";
    $db->prepare($query)->execute([$itemid, $user_class->id, $min_bid, $end_time]);

    echo "Item added to Auction House successfully!";
}

// Display form to add item to auction house
?>

<form action="auction_house.php" method="post">
    <!-- Display user's inventory items in a dropdown -->
    <select name="itemid">
        <!-- This is a placeholder; you'll fetch user's inventory items from the database -->
        <option value="1">Item 1</option>
        <option value="2">Item 2</option>
    </select>

    <label>Minimum Bid:</label>
    <input type="number" name="min_bid" required>

    <label>Duration (in hours):</label>
    <input type="number" name="duration" required>

    <input type="submit" name="submit_auction" value="Add to Auction">
</form>

