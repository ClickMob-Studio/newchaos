<?php
include 'header.php';
$user = new User();

// Checking if the user is logged in
if(!$user->get_session()) {
    header("location:login.php");
}

// Logic to purchase a business goes here...

?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Businesses</title>
    <!-- Include any other CSS files or meta tags here -->
</head>
<body>

<!-- Styles from auction_house.php -->
<style>
    /* Styles from auction_house.php goes here... */
</style>

<div class="info-box">
    <h2>Available Businesses</h2>
    <p>Below are the businesses available for purchase:</p>
</div>

<div class="auction-container">
    <!-- Loop to display businesses -->
    <?php
    // Sample loop, in reality, this would fetch data from the database
    $businesses = []; // fetch businesses from the database
    foreach($businesses as $business) {
        echo "<div class='business-entry'>";
        echo "<p><strong>Business Name:</strong> " . $business['business_name'] . "</p>";
        echo "<p><strong>Type:</strong> " . $business['business_type'] . "</p>";
        echo "<p><strong>Price:</strong> $" . $business['business_price'] . "</p>";
        echo "<p><strong>Rating:</strong> " . $business['rating'] . " stars</p>";
        echo "<button>Purchase</button>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>