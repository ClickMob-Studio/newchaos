<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['confirm_buy'])) {
    $business_id = (int) $_GET['confirm_buy'];
    echo '<form action="business.php" method="post">';
    echo 'Enter Business Name: <input type="text" name="custom_name" required>';
    echo '<input type="hidden" name="business_id" value="' . $business_id . '">';
    echo '<input type="submit" name="confirm_purchase" value="Confirm Purchase">';
    echo '</form>';
} elseif (isset($_POST['confirm_purchase'])) {
    $business_id = (int) $_POST['business_id'];
    $custom_name = $_POST['custom_name'];
    $user_id = $_SESSION['id'];

    // Fetch the user's details
    $user_result = mysql_query("SELECT * FROM grpgusers WHERE id = '$user_id'");
    if (mysql_error()) {
        echo "MySQL Error: " . mysql_error();
        exit;
    }

    $user = mysql_fetch_assoc($user_result);

    // Fetch the business details
    $business_result = mysql_query("SELECT * FROM businesses WHERE id = '$business_id'");
    $business = mysql_fetch_assoc($business_result);

    // Check if the user has enough money
    if ($user['money'] < $business['cost']) {
        echo "You don't have enough money to buy this business.";
    } else {
        // Deduct the cost from the user's money
        perform_query("UPDATE grpgusers SET money = money - ? WHERE id = ?", [$business['cost'], $user_id]);

        // Insert the purchase into the ownedbusinesses table with the custom name
        perform_query("INSERT INTO ownedbusinesses (user_id, business_id, purchase_date, status, name, rating, employees, intelligence, cost) VALUES (?, ?, NOW(), 'Active', ?, ?, ?, ?)", [$user_id, $business_id, $custom_name, $business['rating'], $business['employees'], $business['intelligence'], $business['cost']]);

        // Fetch the ownership_id of the last inserted business
        $ownership_id = mysql_insert_id();
        perform_query("UPDATE grpgusers SET current_employer = ? WHERE id = ?", [$ownership_id, $user_id]);

        // Display the success message
        echo "You have successfully purchased and named your business!";
    }
}

// Fetching the businesses from the database
$business_result = mysql_query("SELECT * FROM businesses ORDER BY id ASC");
$rows = array();
while ($row = mysql_fetch_assoc($business_result)) {
    $rows[] = $row;
}

echo '<div class="auction-container">'; // Opening the new container div
echo '<h3 class="auction-title">Businesses</h3><hr>';

foreach ($rows as $row) {
    $imageName = str_replace(array(' ', '*'), '', strtolower($row['name'])) . '.png';
    $ratingStars = str_repeat('&#9733;', $row['rating']) . str_repeat('&#9734;', 5 - $row['rating']);

    echo '<div class="floaty flexcont" style="width:85%;margin:2px;">';
    echo '<div class="flexele" style="border-right:thin solid #333;">';
    echo '<img src="images/' . $imageName . '" />';
    echo '</div>';
    echo '<div class="flexele">';
    echo '<h4>' . $row['name'] . '</h4>'; // Business name as a header
    echo '</div>';
    echo '</div>';

    // Employees
    echo '<div class="floaty flexcont" style="width:85%;margin:2px;">';
    echo '<div class="flexele" style="border-right:thin solid #333;">';
    echo 'Employees:';
    echo '</div>';
    echo '<div class="flexele">';
    echo $row['employees'];
    echo '</div>';
    echo '</div>';

    // Intelligence required
    echo '<div class="floaty flexcont" style="width:85%;margin:2px;">';
    echo '<div class="flexele" style="border-right:thin solid #333;">';
    echo 'Intelligence:';
    echo '</div>';
    echo '<div class="flexele">';
    echo $row['intelligence'];
    echo '</div>';
    echo '</div>';

    // Cost of the business
    echo '<div class="floaty flexcont" style="width:85%;margin:2px;">';
    echo '<div class="flexele" style="border-right:thin solid #333;">';
    echo 'Cost:';
    echo '</div>';
    echo '<div class="flexele">';
    echo '$' . number_format($row['cost'], 2);
    echo '</div>';
    echo '</div>';

    // Rating
    echo '<div class="floaty flexcont" style="width:85%;margin:2px;">';
    echo '<div class="flexele" style="border-right:thin solid #333;">';
    echo 'Rating:';
    echo '</div>';
    echo '<div class="flexele">';
    echo '<p class="rating">' . $ratingStars . '</p>';
    echo '</div>';
    echo '</div>';

    // Buy button
    echo '<div class="floaty flexcont" style="width:85%;margin:2px;">';
    echo '<div class="flexele" style="border-right:thin solid #333;">';
    echo '</div>';
    echo '<div class="flexele">';
    echo '<a href="business.php?confirm_buy=' . $row['id'] . '" class="buy-button">Buy</a>';
    echo '</div>';
    echo '</div>';
}
echo '</div>'; // Closing the auction-container div

include 'footer.php';
?>