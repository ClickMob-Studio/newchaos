<?php
include 'header.php';

// Logic to fetch available businesses and purchase a business goes here...

?>

<div class="info-box">
    <h2>Available Businesses</h2>
    <p>Below are the businesses available for purchase:</p>
</div>

<div class="business-container">
    <?php
    // Sample loop, in reality this would fetch data from the database
    $available_businesses = []; // Fetch businesses from database that don't have an owner
    foreach($available_businesses as $business) {
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