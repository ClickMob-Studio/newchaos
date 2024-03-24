
<style>

table th {
    background-color: #1e1e1e;
    color: #d9d9d9;
    padding: 10px;
    border: none;  /* Ensure no borders */
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}
.previous-auctions-dropdown summary {
    list-style: none;
    cursor: pointer;
    padding: 5px;
    background-color: #1e1e1e;
    border-radius: 5px;
    color: #f2f2f2;
    outline: none;
}

.previous-auctions-dropdown summary::-webkit-details-marker { /* Hide the default arrow in Chrome & Safari */
    display: none;
}

.previous-auctions-dropdown summary::before {  /* Custom arrow */
    content: "?";
    padding-right: 5px;
}

.previous-auctions-dropdown[open] summary::before {  /* Arrow rotates when dropdown is open */
    content: "?";
}

.previous-auctions-dropdown table {
    width: 100%;
    border-collapse: collapse;
}

.previous-auctions-dropdown th, .previous-auctions-dropdown td {
    border: 1px solid #ddd;
    padding: 8px 12px;
    text-align: left;
}

.previous-auctions-dropdown th {
    background-color: #1e1e1e;
}
.bidders-dropdown summary {
    list-style: none;
    cursor: pointer;
    padding: 5px;
    background-color: #2f2f2f;
    border-radius: 5px;
    color: #d9d9d9;
    outline: none;
}

.bidders-dropdown summary::-webkit-details-marker { /* Hide the default arrow in Chrome & Safari */
    display: none;
}

.bidders-dropdown summary::before {  /* Custom arrow */
    content: "?";
    padding-right: 5px;
}

.bidders-dropdown[open] summary::before {  /* Arrow rotates when dropdown is open */
    content: "?";
}

.dropdown-content div {
    padding: 5px;
    border-top: 1px solid #343434;
}

.dropdown-content div:first-child {
    border-top: none;
}

.bidders-dropdown {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 200px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content div {
    padding: 10px;
    text-align: left;
}

 .info-box {
    background-color: #2f2f2f;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    padding: 5px 20px; /* Reduced vertical padding but kept horizontal padding as before */
    margin: 20px 0;
    width: 90%;
    margin-left: auto;
    margin-right: auto;
}

.info-box h2 {
    font-size: 20px; /* Slightly reduced font size */
    margin-bottom: 5px; /* Reduced margin */
    color: #ffb245;
    text-shadow: 1px 0 1px #343434;
}

.info-box p, .info-box ul {
    font-size: 15px; /* Slightly reduced font size */
    color: #d9d9d9;
    margin: 0; /* Removed default margins */
}

.info-box li {
    margin-bottom: 3px; /* Reduced margin */
    color: #d9d9d9;
}


.auction-container {
    background-color: #1e1e1e;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    padding: 20px;
    margin: 20px auto;
    width: 90%;
}

/* Container for the entire Auction House */
.auction-container {
    background-color: #1e1e1e;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    padding: 20px;
    margin: 20px auto;
    width: 90%;
}

.auction-title {
    font-size: 24px;
    text-align: center;
    color: orange;
    font-family: 'HackingTrashed-Regular',sans-serif;
    margin-bottom: 20px;
    text-shadow: 1px 0 1px #343434;
    border-bottom: 2px solid orange;
    padding-bottom: 10px;
}

/* Updated Auction House Styles for auction rows */
.auction-row {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px;
    background-color: #1e1e1e;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    margin-bottom: 20px;
}

.auction-row img {
    flex: 1;
}

.auction-row > div {
    flex: 2;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.auction-row form {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Additional Auction House Styles */
.add-auction {
    background-color: #1e1e1e;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    margin: 20px auto;
    max-width: 800px;
}

.add-auction h3 {
    border-bottom: 1px solid orange;
    padding-bottom: 10px;
}

.add-auction form {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    justify-content: center;
}

.add-auction label {
    flex: 1;
    text-align: right;
    margin-right: 10px;
}

.add-auction input, .add-auction select {
    flex: 2;
}

   /* Auction House Styles */
    h3 {
        text-align: center;
        color: orange;
        font-family: 'HackingTrashed-Regular',sans-serif;
        margin-top: 20px;
        text-shadow: 1px 0 1px #343434;
    }
    table {
        width: 90%;
        margin: 20px auto 30px auto; /* Adding space after each table */
        border-collapse: separate;
        border-spacing: 0;  /* Ensure no space between cells */
    }
    table td {
        background-color: #2f2f2f;
        padding: 10px;
        border: none;  /* Ensure no borders */
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    }
    select {
        width: 100%;
        padding: 5px;
        background-color: #171515;
        border: 1px solid #686767;
        color: #ffffffbf;
        font-family: 'Open Sans';
    }
    input[type="text"], input[type="number"] {
        width: 80%;
        padding: 5px;
        margin-right: 5px;
        border-radius: 5px;
    }
    input[type="submit"] {
        background-color: #171515;
        border: none;
        padding: 5px 10px;
        color: #ffffffbf;
        cursor: pointer;
        border-radius: 5px;
    }
    input[type="submit"]:hover {
        background-color: #444;
    }
    .countdown {
        color: #ffb245;
    }
    img {
        max-width: 100px;
        max-height: 100px;
    }
</style>

<?php
include 'header.php';




if (!$user_class) {
    die("You must be logged in to access the Auction House.");
}

function execute_query($query, $conn) {
    $result = mysql_query($query, $conn);
    if (!$result) {
        die('Query failed: ' . mysql_error() . '. SQL: ' . $query);
    }
    return $result;
}





// Handle adding an item to the auction house
if (isset($_POST['submit_auction'])) {
    $inventory_id = intval($_POST['inventory_id']); 
    $quantity_to_auction = intval($_POST['quantity']);
    $starting_bid = floatval($_POST['starting_bid']);
    $duration_hours = intval($_POST['duration']);
    $end_time = strtotime('+' . $duration_hours . ' hours');

    $query = "SELECT i.*, it.itemname FROM inventory i JOIN items it ON i.itemid = it.id WHERE i.userid = " . $user_class->id . " AND i.id = " . $inventory_id;
$result = execute_query($query, $conn);
$item_data = mysql_fetch_assoc($result);

if (!$item_data || $item_data['quantity'] < $quantity_to_auction) {
    die("Item not found in your inventory or insufficient quantity.");
}
    $query = "INSERT INTO auction_house (item_id, seller_id, starting_bid, current_bid, end_time, quantity) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')";
    execute_query(sprintf($query, $item_data['itemid'], $user_class->id, $starting_bid, $starting_bid, $end_time, $quantity_to_auction), $conn);



   // Insert a new entry into the 'ads' table
    $item_name = $item_data['itemname'];
    $item_quantity = $quantity_to_auction;
    $message = "Has Listed {$item_name} (x{$item_quantity}) in the Auction House";
    $timestamp = time(); // Current timestamp
    $display_mins = 60;

    $ads_query = "INSERT INTO ads (timestamp, poster, message, displaymins) VALUES ('$timestamp', '{$user_class->id}', '$message', '$display_mins')";
    execute_query($ads_query, $conn);



    // Update the user's inventory
    if ($item_data['quantity'] == $quantity_to_auction) {
        $query = "DELETE FROM inventory WHERE id = '%s'";
    } else {
        $query = "UPDATE inventory SET quantity = quantity - $quantity_to_auction WHERE id = '%s'";
    }
    execute_query(sprintf($query, $inventory_id), $conn);

    echo "Item added to the auction house successfully!";
}

// Handle bid submissions
if (isset($_POST['submit_bid'])) {
    $auction_id = intval($_POST['auction_id']);
    $bid_amount = floatval($_POST['bid_amount']);

    // Checking if the user has enough money
    $check_money_query = "SELECT money FROM grpgusers WHERE id = {$user_class->id}";
    $money_result = execute_query($check_money_query, $conn);
    $user_money = mysql_fetch_assoc($money_result)['money'];
    
    if ($user_money < $bid_amount) {
        die("You do not have enough money to place this bid.");
    }

    $query = "SELECT current_bid, seller_id, item_id, quantity FROM auction_house WHERE auction_id = '%s'";
    $result = execute_query(sprintf($query, $auction_id), $conn);
    $auction_data = mysql_fetch_assoc($result);

    $highest_bid_query = "SELECT MAX(bid_amount) AS highest_bid FROM bids WHERE auction_id = '%s'";
    $highest_bid_result = execute_query(sprintf($highest_bid_query, $auction_id), $conn);
    $highest_bid_data = mysql_fetch_assoc($highest_bid_result);
    $highest_bid = max(floatval($highest_bid_data['highest_bid']), floatval($auction_data['current_bid']));

    if (!$auction_data) {
        die("Auction not found.");
    }

    if ($bid_amount <= $highest_bid) {
        die("Your bid should be higher than the current highest bid!");
    }


// Get the current highest bidder
    $query = "SELECT highest_bidder_id FROM auction_house WHERE auction_id = '%s'";
    $result = execute_query(sprintf($query, $auction_id), $conn);
    $previous_highest_bidder = mysql_fetch_assoc($result)['highest_bidder_id'];


 // Fetch the item details for the event message
    $item_query = "SELECT it.itemname, a.quantity FROM items it JOIN auction_house a ON a.item_id = it.id WHERE a.auction_id = {$auction_id}";
    $item_result = execute_query($item_query, $conn);
    $item_data = mysql_fetch_assoc($item_result);  // This line fetches the item data for the current auction

    // Get the current highest bidder
    $query = "SELECT highest_bidder_id FROM auction_house WHERE auction_id = '%s'";
    $result = execute_query(sprintf($query, $auction_id), $conn);
    $previous_highest_bidder = mysql_fetch_assoc($result)['highest_bidder_id'];

    // Deduct the bid amount from the user's balance
    $deduct_money_query = "UPDATE grpgusers SET money = money - {$bid_amount} WHERE id = {$user_class->id}";
    execute_query($deduct_money_query, $conn);

    // Place the bid
    $query = "INSERT INTO bids (auction_id, bidder_id, bid_amount, bid_time) VALUES ('%s', '%s', '%s', NOW())";
    execute_query(sprintf($query, $auction_id, $user_class->id, $bid_amount), $conn);

    // Update the auction's current bid
    $update_auction_query = "UPDATE auction_house SET current_bid = {$bid_amount}, highest_bidder_id = {$user_class->id} WHERE auction_id = {$auction_id}";
    execute_query($update_auction_query, $conn);

    // If there was a previous bidder and they're not the same as the current user, notify them
    if ($previous_highest_bidder && $previous_highest_bidder != $user_class->id) {
        $notification_msg = "You've been outbid on an auction for " . $item_data['itemname'] . "!";
        Send_Event($previous_highest_bidder, $notification_msg);
    }

    // Send the event to the seller
$bidder_user = new User($user_class->id);

$event_msg = $bidder_user->formattedname . " placed a new bid of $" . $bid_amount . " on your auction for " . $item_data['itemname'] . " (Quantity: " . $item_data['quantity'] . ")!";
    Send_Event($auction_data['seller_id'], $event_msg);

    echo "Bid placed successfully!";
}


echo '<div class="auction-container">'; // Opening the new container div
echo '<h3 class="auction-title">Auction House</h3><hr>';

// Inserting the information box
echo '<div class="info-box">';
echo '<h2>Information</h2>';
echo '<p>Welcome to the Auction House! Here, you can:</p>';
echo '<ul>';
echo '<li>View all available items for auction.</li>';
echo '<li>Place bids on your favorite items.</li>';
echo '<li>See the highest current bid for each item.</li>';
echo '<li>We are still working on this feature so expect some changes as we adjust. </li>';

echo '<li>If you are outbidded on an item, You will <font color=red>NOT get your money back</font>! </li>';
echo '</ul>';
echo '</div>';  // Closing the info box

echo '<div class="auction-content">'; // Opening aucecho '<div class="auction-content">'; // Opening auction content container
echo '';

$query = "
    SELECT 
    a.auction_id, 
    a.item_id, 
    a.seller_id, 
    a.starting_bid AS min_bid, 
    a.current_bid,
    a.highest_bidder_id, 
    a.end_time,
    a.quantity AS auctioned_quantity,
    u.username as seller_name,
    it.itemname,
    it.image,
    it.description  /* This was added */
FROM 
    auction_house a
LEFT JOIN 
    grpgusers u ON a.seller_id = u.id
LEFT JOIN
    items it ON a.item_id = it.id
WHERE 
    a.end_time > UNIX_TIMESTAMP(NOW())
ORDER BY
    a.end_time ASC";

$result = mysql_query($query, $conn);
while ($auction = mysql_fetch_assoc($result)) {



$bidder_query = "
    SELECT u.id as bidder_id, u.username, b.bid_amount
    FROM bids b
    JOIN grpgusers u ON b.bidder_id = u.id
    WHERE b.auction_id = {$auction['auction_id']}
    ORDER BY b.bid_time DESC
    LIMIT 5
";
$bidder_result = mysql_query($bidder_query, $conn);
$bidder_details = [];
while ($bidder_row = mysql_fetch_assoc($bidder_result)) {
    $bidder_user = new User($bidder_row['bidder_id']);
    $formatted_name = $bidder_user->formattedname;
    $bidder_details[] = $formatted_name . " ($" . number_format($bidder_row['bid_amount'], 2) . ")";
}

// Displaying the dropdown with the last 5 bidders and their bid amounts

echo '<details>';
echo '<summary>Last 5 Bidders</summary>';
if (count($bidder_details) > 0) {
    foreach ($bidder_details as $detail) {
        echo '<div>' . $detail . '</div>';
    }
} else {
    echo '<div>No bids yet.</div>';
}
echo '</details>';
echo '<div class="auction-row">';
    echo '<div>';
    echo '<img src="' . $auction['image'] . '" alt="' . $auction['itemname'] . '" title="' . $auction['description'] . '">';
        echo '<div>&nbsp &nbsp &nbsp &nbsp<span class="countdown" data-end-time="' . $auction['end_time'] . '">' . howlongtil($auction['end_time']) . '</span></div>';  // Inserted this line here
    echo '</div>';
    echo '<div>';
        echo '' . $auction['itemname'];
        echo ' x' . $auction['auctioned_quantity'];
    echo '</div>';
$seller_user = new User($auction['seller_id']);
echo '<div>Seller: ' . $seller_user->formattedname . '</div>';  

   $formatted_starting_bid = number_format($auction['min_bid']);
    echo '<div>Starting Bid: $' . $formatted_starting_bid . '</div>';

    $formatted_current_bid = number_format($auction['current_bid']);
    echo '<div>Current Bid: $' . $formatted_current_bid . '</div>';

    echo '<form method="post" action="auction_house.php">';
        echo '<input type="hidden" name="auction_id" value="' . $auction['auction_id'] . '">';
        echo '<input type="text" name="bid_amount" placeholder="Bid" style="width:60px;">';
        echo '<input type="submit" name="submit_bid" value="Place Bid">';
    echo '</form>';
echo '</div>';




}
echo '';

// Fetch the user's items from the inventory table
$query = "SELECT i.id as inventory_id, it.itemname, i.quantity 
          FROM inventory i 
          JOIN items it ON i.itemid = it.id 
          WHERE i.userid = '%s'";
$result = mysql_query(sprintf($query, $user_class->id), $conn);
if (!$result) {
    die('Invalid query: ' . mysql_error());
}

$rows = array();
while ($row = mysql_fetch_assoc($result)) {
    $rows[] = $row;
}


// Fetch finished auctions
$finishedAuctionsQuery = "SELECT * FROM auction_house WHERE status = 'finished'";
$finishedAuctions = mysql_query($finishedAuctionsQuery, $conn);

echo '<details class="previous-auctions-dropdown">';
echo '<summary>Previous Auctions</summary>';
echo '<table>';
echo '<tr><th>Item</th><th>Quantity</th><th>Winning Bidder</th><th>Final Bid</th></tr>';

while ($auction = mysql_fetch_assoc($finishedAuctions)) {
    $itemId = $auction['item_id'];
    $quantity = $auction['quantity'];
    $highestBidderId = $auction['highest_bidder_id'];
    $currentBid = $auction['current_bid'];

    // Fetch item name
    $itemNameQuery = "SELECT itemname FROM items WHERE id = $itemId";
    $itemNameResult = mysql_query($itemNameQuery, $conn);
    $itemName = mysql_fetch_assoc($itemNameResult)['itemname'];

    // Fetch winning bidder's formatted name using the User class
    $bidderUser = new User($highestBidderId);
    $formattedName = $bidderUser->formattedname;

    echo '<tr>';
    echo '<td>' . $itemName . '</td>';
    echo '<td>' . $quantity . '</td>';
    echo '<td>' . $formattedName . '</td>'; // Display the formatted username.
    echo '<td>' . $currentBid . '</td>';
    echo '</tr>';
}

echo '</table>';
echo '</details>';

?>

<div class="add-auction">
    <h3>Add to Auction</h3>
    <form action="auction_house.php" method="post">
        <label for="inventory_id">Item:</label>
        <select name="inventory_id" id="inventory_id">
            <?php
            foreach ($rows as $row) {
                echo '<option value="' . $row['inventory_id'] . '">' . $row['itemname'] . ' [x' . $row['quantity'] . ']</option>';
            }
            ?>
        </select>
        
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required>
        
        <label for="starting_bid">Minimum Bid:</label>
        <input type="number" name="starting_bid" id="starting_bid" required>
        
        <label for="duration">Auction Duration (in hours):</label>
        <input type="number" name="duration" id="duration" required>
        
        <input type="submit" name="submit_auction" value="Add to Auction">
    </form>
</div>
</div>
<script>

function updateCountdown() {
    let countdownElements = document.querySelectorAll('.countdown');

    countdownElements.forEach(function(element) {
        let timeParts = element.innerText.split(' ').map(part => parseInt(part));
        let hours = timeParts[0];
        let minutes = timeParts[1];
        let seconds = timeParts[2];

        if (seconds > 0) {
            seconds--;
        } else if (minutes > 0) {
            minutes--;
            seconds = 59;
        } else if (hours > 0) {
            hours--;
            minutes = 59;
            seconds = 59;
        }

        element.innerText = `${hours}h ${minutes}m ${seconds}s`;
    });
}

setInterval(updateCountdown, 1000);// Suppose this is the end timestamp passed from the server
let endTimeStamp = /* fetched from the server */;

function updateCountdown() {
    console.log('Function called'); // Add this line

    let countdownElements = document.querySelectorAll('.countdown');

    countdownElements.forEach(function(element) {
        let endTime = parseInt(element.getAttribute('data-end-time')) * 1000; // Convert to milliseconds
        let now = new Date().getTime();
        let distance = endTime - now;

        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        element.innerText = `${hours}h ${minutes}m ${seconds}s`;

        if (distance < 0) {
            element.innerText = "Auction Ended!";
        }
    });
}

setInterval(updateCountdown, 1000);



document.addEventListener("DOMContentLoaded", function() {
    var dropdowns = document.querySelectorAll(".bidders-dropdown");

    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener("click", function() {
            var content = this.querySelector(".dropdown-content");
            content.style.display = content.style.display === "block" ? "none" : "block";
        });
    });
});

</script>

<?php
include 'footer.php';
?>
