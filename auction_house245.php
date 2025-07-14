<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$user_class) {
    die("You must be logged in to access the Auction House.");
}

// Handle adding an item to the auction house
if (isset($_POST['submit_auction'])) {
    $inventory_id = intval($_POST['inventory_id']);
    $quantity_to_auction = intval($_POST['quantity']);
    $starting_bid = floatval($_POST['starting_bid']);
    $duration_hours = intval($_POST['duration']);
    $end_time = strtotime('+' . $duration_hours . ' hours');

    $db->query("SELECT * FROM inventory WHERE id = ? AND userid = ? LIMIT 1");
    $db->execute([$inventory_id, $user_class->id]);
    $item_data = $db->fetch_row(true);
    if (!$item_data || $item_data['quantity'] < $quantity_to_auction) {
        die("Item not found in your inventory or insufficient quantity.");
    }

    perform_query("INSERT INTO auction_house (item_id, seller_id, starting_bid, current_bid, end_time, quantity) VALUES(?, ?, ?, ?, ?, ?)", [
        $item_data['itemid'],
        $user_class->id,
        $starting_bid,
        $starting_bid,
        $end_time,
        $quantity_to_auction
    ]);

    // Update the user's inventory
    if ($item_data['quantity'] == $quantity_to_auction) {
        perform_query("DELETE FROM inventory WHERE id = ?", [$inventory_id]);
    } else {
        perform_query("UPDATE inventory SET quantity = quantity - ? WHERE id = ?", [$quantity_to_auction, $inventory_id]);
    }

    echo "Item added to the auction house successfully!";
}

// Handle bid submissions
if (isset($_POST['submit_bid'])) {
    $auction_id = intval($_POST['auction_id']);
    $bid_amount = floatval($_POST['bid_amount']);

    // Checking if the user has enough money
    $db->query("SELECT money FROM grpgusers WHERE id = ? LIMIT 1");
    $db->execute([$user_class->id]);
    $user_money = $db->fetch_single();
    if ($user_money < $bid_amount) {
        die("You do not have enough money to place this bid.");
    }

    $db->query("SELECT current_bid, seller_id, item_id, quantity FROM auction_house WHERE auction_id = ? LIMIT 1");
    $db->execute([$auction_id]);
    $auction_data = $db->fetch_row(true);
    if (!$auction_data) {
        die("Auction not found.");
    }

    if ($bid_amount <= $highest_bid) {
        die("Your bid should be higher than the current highest bid!");
    }

    // Deduct the bid amount from the user's balance
    perform_query("UPDATE grpgusers SET money = money - ? WHERE id = ?", [$bid_amount, $user_class->id]);

    // Place the bid
    perform_query("INSERT INTO bids (auction_id, bidder_id, bid_amount, bid_time) VALUES(?, ?, ?, NOW())", [
        $auction_id,
        $user_class->id,
        $bid_amount
    ]);

    // Update the auction's current bid
    perform_query("UPDATE auction_house SET current_bid = ?, highest_bidder_id = ? WHERE auction_id = ?", [
        $bid_amount,
        $user_class->id,
        $auction_id
    ]);

    // Send the event
    $event_msg = $user_class->username . " placed a new bid of $" . $bid_amount . " on your auction for " . $item_data['itemname'] . " (Quantity: " . $item_data['quantity'] . ")!";
    Send_Event($auction_data['seller_id'], $event_msg);

    echo "Bid placed successfully!";
}

echo '<h3>Auction House</h3>';
echo '<hr>';
echo '<table width="100%">';

$db->query("SELECT a.auction_id, a.item_id, a.seller_id, a.starting_bid AS min_bid, a.current_bid, a.highest_bidder_id, a.end_time, a.quantity AS auctioned_quantity, u.username as seller_name, it.itemname, it.image FROM auction_house a LEFT JOIN grpgusers u ON a.seller_id = u.id LEFT JOIN items it ON a.item_id = it.id WHERE a.end_time > UNIX_TIMESTAMP(NOW()) ORDER BY a.end_time ASC");
$db->execute();
$result = $db->fetch_row();
foreach ($result as $auction) {
    echo '<tr>';
    echo '<td><img src="' . $auction['image'] . '" width="100" height="100" alt="' . $auction['itemname'] . '"></td>';
    echo '<td>Item: ' . $auction['itemname'] . ' (Quantity: ' . $auction['auctioned_quantity'] . ')</td>';
    echo '<td>Seller: ' . $auction['seller_name'] . '</td>';
    echo '<td>Starting Bid: ' . $auction['min_bid'] . '</td>';
    echo '<td>Current Bid: ' . $auction['current_bid'] . '</td>';
    echo '<td>End Time: ' . date('Y-m-d H:i:s', $auction['end_time']) . '</td>';
    echo '<td>
            <form method="post" action="auction_house.php">
                <input type="hidden" name="auction_id" value="' . $auction['auction_id'] . '">
                <input type="text" name="bid_amount" placeholder="Enter bid">
                <input type="submit" name="submit_bid" value="Place Bid">
            </form>
          </td>';
    echo '</tr>';
}
echo '</table>';

$db->query("SELECT i.id AS inventory_id, it.itemname, i.quantity FROM inventory i JOIN items it ON i.itemid = it.id WHERE i.userid = ?");
$db->execute([$user_class->id]);
$rows = $db->fetch_row();
if (empty($rows)) {
    die('You have no items in your inventory.');
}

?>

<h3>Add to Auction</h3>
<form action="auction_house.php" method="post">
    <select name="inventory_id"> <!-- Use inventory_id -->
        <?php
        foreach ($rows as $row) {
            echo '<option value="' . $row['inventory_id'] . '">' . $row['itemname'] . ' [x' . $row['quantity'] . ']</option>';
        }
        ?>
    </select>
    <label>Quantity:</label>
    <input type="number" name="quantity" required>
    <label>Minimum Bid:</label>
    <input type="number" name="starting_bid" required>
    <label>Auction Duration (in hours):</label>
    <input type="number" name="duration" required>
    <input type="submit" name="submit_auction" value="Add to Auction">
</form>

<?php
include 'footer.php';
?>