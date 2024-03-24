<?php
include 'header.php'; // Make sure this file contains the database connection $conn

if ($user_class->admin != 1) {
    // Display an error message for non-admin users
    echo 'Sorry, the Crafting feature is under development and will be back soon.';
    exit; // Prevent the rest of the script from executing for non-admin users
}

// Function to retrieve item details from the database
function getItemDetails($itemId) {
    $query = "SELECT itemname, image FROM items WHERE id = $itemId";
    $result = mysql_query($query);
    if ($result && mysql_num_rows($result) > 0) {
        return mysql_fetch_assoc($result);
    }
    return null;
}

// Function to get the user's quantity of a specific item
function getUserItemQuantity($userId, $itemId) {
    global $user_class; // Ensure that $user_class is accessible in this scope

    $query = "SELECT quantity FROM inventory WHERE userid = $userId AND itemid = $itemId";
    $result = mysql_query($query);

    if ($result && mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        return $row['quantity'];
    } else {
        // Return 0 if the item is not found in the user's inventory
        return 0;
    }
}

// Function to handle the trade process
function handleTrade($tradeId) {
    global $user_class; // Ensure that $user_class is accessible in this scope
    $userId = $user_class->id; // Fetch the user ID from the user_class object


     // Check for an active cooldown in the crafter_cooldown table
    $currentTimestamp = time(); // Current timestamp in seconds
    $cooldownQuery = "SELECT timestamp FROM crafter_cooldown WHERE user_id = $userId";
    $cooldownResult = mysql_query($cooldownQuery);
    if ($cooldownRow = mysql_fetch_assoc($cooldownResult)) {
        $cooldownTimestamp = strtotime($cooldownRow['timestamp']); // Convert timestamp from database to seconds
        $remainingTime = $cooldownTimestamp - $currentTimestamp;

        if ($remainingTime > 0) {
            // User has an active cooldown, return a message indicating remaining cooldown time
            $minutes = floor($remainingTime / 60);
            $seconds = $remainingTime % 60;
            return "You cannot currently trade! Time remaining: $minutes minutes $seconds seconds";
        }
    }

 // Fetch trade details
    $tradeQuery = "SELECT * FROM trades WHERE id = $tradeId";
    $tradeResult = mysql_query($tradeQuery);
    if (!$tradeResult || mysql_num_rows($tradeResult) == 0) {
        return "Invalid trade.";
    }
    $cooldownQuery = "SELECT * FROM crafter_cooldown WHERE user_id = $userId AND NOW() > timestamp";
    $cooldownResult = mysql_query($cooldownQuery);
    
    // Check if the query was successful
    if (mysql_num_rows($cooldownResult)) {
        // Check if there are no rows returned, meaning there's no cooldown record for the user or the cooldown has expired
        if (mysql_num_rows($cooldownResult) < 1) {
            
            // There is a cooldown record for the user and the current time is before the cooldown expiration
            // You can return the message if you want, or execute other actions
            return "You cannot currently trade!";
        }
    } 
    
    
    $trade = mysql_fetch_assoc($tradeResult);

    // List to hold items that the user lacks
    $lackingItems = [];

    // Check if user has required items
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["item$i"]) && $trade["item{$i}quantity"] > 0) {
            $userQuantity = getUserItemQuantity($userId, $trade["item$i"]);
            if ($userQuantity < $trade["item{$i}quantity"]) {
                $itemName = getItemDetails($trade["item$i"])['itemname'];
                $neededQuantity = $trade["item{$i}quantity"] - $userQuantity;
                $lackingItems[] = "You need $neededQuantity more $itemName";
            }
        }
    }

   // If there are any lacking items, return the list as a string
    if (!empty($lackingItems)) {
        return implode(", ", $lackingItems) . ".";
    }
    // Begin transaction
    mysql_query("START TRANSACTION");
    $userId = mysql_real_escape_string($userId); // Assuming $conn is your MySQL connection
    $timestampOneHourAhead = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Check if the record already exists for the user ID
    $checkQuery = "SELECT * FROM crafter_cooldown WHERE user_id = $userId";
    $checkResult = mysql_query($checkQuery);
    if (mysql_num_rows($checkResult) > 0) {
        // Update the existing record
        $updateQuery = "UPDATE crafter_cooldown SET timestamp = '$timestampOneHourAhead' WHERE user_id = $userId";
        $updateResult = mysql_query($updateQuery);
    } else {
        // Insert a new record
        $insertQuery = "INSERT INTO crafter_cooldown (user_id, timestamp) VALUES ($userId, '$timestampOneHourAhead')";
        $insertResult = mysql_query($insertQuery);
    }
    
     // Deduct required items from user's inventory and remove if quantity becomes 0
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["item$i"])) {
            // Deduct the item
            $deductQuery = "UPDATE inventory SET quantity = quantity - {$trade["item{$i}quantity"]} WHERE userid = $userId AND itemid = {$trade["item$i"]}";
            $result = mysql_query($deductQuery);
            if (!$result) {
                mysql_query("ROLLBACK");
                return "Failed to deduct items.";
            }

            // Check if quantity is now 0 and remove the item if it is
            $checkQuantityQuery = "SELECT quantity FROM inventory WHERE userid = $userId AND itemid = {$trade["item$i"]}";
            $checkResult = mysql_query($checkQuantityQuery);
            if ($checkResult) {
                $row = mysql_fetch_assoc($checkResult);
                if ($row['quantity'] <= 0) {
                    $deleteQuery = "DELETE FROM inventory WHERE userid = $userId AND itemid = {$trade["item$i"]}";
                    $deleteResult = mysql_query($deleteQuery);
                    if (!$deleteResult) {
                        mysql_query("ROLLBACK");
                        return "Failed to remove item with zero quantity.";
                    }
                }
            }
          
        }
    }
    // Add reward items to user's inventory
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["itemreward$i"])) {
            $rewardItemId = $trade["itemreward$i"];

            // Check if the user already has the reward item
            $inventoryQuery = "SELECT quantity FROM inventory WHERE userid = $userId AND itemid = $rewardItemId";
            $inventoryResult = mysql_query($inventoryQuery);

            if ($inventoryResult && mysql_num_rows($inventoryResult) > 0) {
                // User already has the item, update the quantity
                $row = mysql_fetch_assoc($inventoryResult);
                $newQuantity = $row['quantity'] + 1; // Assuming each trade adds one of the reward item
                $updateQuery = "UPDATE inventory SET quantity = $newQuantity WHERE userid = $userId AND itemid = $rewardItemId";
                $updateResult = mysql_query($updateQuery);
                if (!$updateResult) {
                    mysql_query("ROLLBACK");
                    return "Failed to update reward item in inventory.";
                }
            } else {
                // User does not have the item, insert a new row
                $insertQuery = "INSERT INTO inventory (userid, itemid, quantity) VALUES ($userId, $rewardItemId, 1)";
                $insertResult = mysql_query($insertQuery);
                if (!$insertResult) {
                    mysql_query("ROLLBACK");
                    return "Failed to insert reward item into inventory.";
                }
            }
        }
    }
    // Commit transaction
    mysql_query("COMMIT");
    return "Trade successful!";
}

// Check for trade submission
$message = '';
if (isset($_POST['tradeId'])) {
    $message = handleTrade($_POST['tradeId']);
}

// Function to display the trade tile
function displayTradeTile($trade) {
    global $user_class; // Ensure that $user_class is accessible in this scope
    $user_id = $user_class->id; // Fetch the user ID from the user_class object
    ?>
    <form method="post" action="">

<div class="floaty" data-type="<?= htmlspecialchars($trade['type']) ?>">
    <h3 class="trade-title"><?= htmlspecialchars($trade['name']) ?></h3>
    <div class="trade-section">
        <div class="trade-column">
       <table class="crafter-items">
    <thead>
        <tr>
            <th>Item</th>
        </tr>
    </thead>
    <tbody>
        <?php
        for ($i = 1; $i <= 6; $i++) {
            if (!empty($trade["item$i"]) && $trade["item{$i}quantity"] > 0) {
                $itemId = $trade["item$i"];
                $item = getItemDetails($itemId); // Assuming this function returns item details including the name.
                $userQuantity = getUserItemQuantity($user_id, $itemId);
                $insufficientClass = $userQuantity < $trade["item{$i}quantity"] ? 'item-insufficient' : '';

                if ($item) {
                    echo "<tr class='{$insufficientClass}'>";
                    // Item Image and Name
                    echo "<td><img src='{$item['image']}' alt='{$item['itemname']}' class='item-image' /><span class='item-name'>{$item['itemname']}</span></td>";
                    // Quantity
                    echo "<td>Quantity: {$userQuantity}/{$trade["item{$i}quantity"]}</td>";
                    echo "</tr>";
                }
            }
        }
        ?>
    </tbody>
</table>





                </tbody>
            </table>
        </div>
        <div class="trade-column">
          <table class="crafter-items">
                <thead>
                    <tr>
                        <th>Reward</center></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 1; $i <= 6; $i++) {
                        if (!empty($trade["itemreward$i"])) {
                            $item = getItemDetails($trade["itemreward$i"]);
                            if ($item) {
                                echo "<tr class='item'>";
                                echo "<td><img src='{$item['image']}' alt='{$item['itemname']}' class='item-image' /></td>";
                                echo "<td>{$item['itemname']}</td>";
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <button type="submit" class="trade-button" name="tradeId" value="<?= $trade['id'] ?>">Trade</button>
</div>

    <?php
}

// Fetch all the trades from the database
$tradesQuery = "SELECT * FROM trades";
$tradesResult = mysql_query($tradesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trade Items</title>
    <style>
        body {
            background-color: #454545; /* Dark background */
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
       .floaty {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #333; /* Dark background for the floaty container */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    color: #fff; /* Light text for visibility on dark background */
}

.trade-title {
    text-align: center; /* Center the header text */
    margin-bottom: 20px;
    color: #fff; /* Ensure the title is visible on the dark background */
}

.trade-section {
    display: flex;
    width: 100%;
    margin-bottom: 20px;
}

.trade-column {
    flex: 1;
    margin: 0 10px; /* Space between columns */
}

.trade-table, .reward-table {
    width: 100%;
    background-color: #222; /* Very dark background for tables */
    border-collapse: collapse;
    border-radius: 8px; /* Rounded corners for tables */
    overflow: hidden; /* Ensures the border radius clips the content */
        text-align: left; /* Center align header text */

}

.trade-table thead, .reward-table thead {
    background-color: #333; /* Slightly lighter than the table for the header */
    text-align: left; /* Center align header text */
}

.trade-table tbody, .reward-table tbody {
    text-align: left; /* Center align table content */
}

.trade-table th, .trade-table td, .reward-table th, .reward-table td {
    padding: 10px;
    border-bottom: 1px solid #444; /* Single border for separating rows */
}

.trade-table tr:last-child td, .reward-table tr:last-child td {
    border-bottom: none; /* Remove bottom border from the last row */
}


.trade-button {
    background-color: #4CAF50; /* Green color for the trade button */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.trade-button:hover {
    background-color: #43A047; /* Darker green for the hover state */
}

/* Remove the odd blue background from item images if needed */
.item img {
    background-color: transparent; /* or any desired color */
    border: none; /* Remove borders around images if present */
}

        h4 {
            margin: 10px 0;
        }

     .shopkeeper-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .shopkeeper-image {
            width: 200px; /* Adjust size as needed */
            height: auto;
        }

        .shopkeeper-description {
            width: 70%; /* Adjust width as needed */
            text-align: left;
            font-family: Arial, sans-serif;
            color: #fff;
        }

.item-insufficient {
    position: relative;
    display: inline-block;
}

 
   .item {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        margin-top: 10px;

        text-align: center;
    }

    .item img {
        width: 50px; /* Adjust as needed */
        height: 50px; /* Adjust as needed */
        display: block;
        margin: 0 auto;
    }

    .quantity-indicator {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: rgba(0, 0, 0, 0.75);
        color: #fff;
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 0.8em;
        z-index: 3;
    }

    .item-insufficient::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1; /* Ensures the red overlay is beneath the item name */
    }

    .item-name {
        display: block;
        margin-top: 5px;
        color: #fff; /* Ensures the text is white */
        font-size: 0.9em;
        z-index: 4; /* Higher than .item-insufficient::after to be on top of the red overlay */
        position: relative; /* This property is crucial for z-index to take effect */
    }






.trade-title {
    text-align: center;
    /* Additional styling as needed */
}
.contenthead {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

.trade-title {
    text-transform: uppercase;
    font-size: 20px;
    text-align: center;
    width: 100%;
}

.trade-message {
    text-align: center;
    color: #f0f0f0;
    margin-bottom: 20px;
}

.trade-container {
    width: 100%;
    overflow-x: auto;
}
.crafter-items {
    width: 100%;
    background-color: #222; /* Very dark background for tables */
    border-collapse: collapse;
    border-radius: 8px; /* Rounded corners for tables */
    overflow: hidden; /* Ensures the border radius clips the content */
}

.crafter-items thead {
    background-color: #333; /* Slightly lighter than the table for the header */
    text-align: center; /* Center align header text */
}

.crafter-items tbody {
    text-align: center; /* Center align table content */
}

.crafter-items th, .crafter-items td {
    padding: 10px;
    border-bottom: 1px solid #444; /* Single border for separating rows */
}

.crafter-items tr:last-child td {
    border-bottom: none; /* Remove bottom border from the last row */
}

.item-image {
    width: 50px; /* Size of the item images */
    height: auto;
    margin-right: 10px; /* Space between image and item name */
    vertical-align: middle;
}

.item-insufficient {
    color: #ff4747; /* Bright color for insufficient items for visibility */
    background-color: #222; /* You can adjust this if you want a different background for insufficient items */
}

.item-name {
    vertical-align: middle;
    color: #fff; /* Ensures the text is white */
    display: inline; /* Keeps name inline with the image */
}

    </style>
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trade Items</title>
    <link rel="stylesheet" href="css/newgamecss1.css">
</head>
<body>

<div class="contenthead floaty">
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
<h4>Crafter</h4></span>
    <div class="shopkeeper-section">
        <img src="/css/images/NewGameImages/crafter.png" alt="Shopkeeper" class="shopkeeper-image">
        <div class="shopkeeper-description">
            <h2>Welcome to the Crafting Station!</h2>
            <p>Here at the crafting, you can exchange items you've collected on your adventures for rare and powerful goods. Our friendly shopkeeper has a keen eye for value and will offer you the best deals for your treasures. Take a look and see what wonders await!</p>
        </div>
    </div>
</div>

<h4>Available Trades</h4>

<?php 
$currentTimestamp = time(); // Current timestamp in seconds
$cooldownQuery = "SELECT timestamp FROM crafter_cooldown WHERE user_id = $user_class->id";
$cooldownResult = mysql_query($cooldownQuery);
if ($cooldownRow = mysql_fetch_assoc($cooldownResult)) {
    $cooldownTimestamp = strtotime($cooldownRow['timestamp']); // Convert timestamp from database to seconds
    $remainingTime = $cooldownTimestamp - $currentTimestamp;

    if ($remainingTime > 0) {
        echo "<script>
        if (!window.countdownInterval) {
            var remainingSeconds = $remainingTime;
            window.countdownInterval = setInterval(function() {
                var minutes = Math.floor(remainingSeconds / 60);
                var seconds = remainingSeconds % 60;
                document.getElementById('countdowns').innerHTML = 'Time remaining: ' + minutes + ' minutes ' + seconds + ' seconds';
                remainingSeconds--;
                if (remainingSeconds < 0) {
                    clearInterval(window.countdownInterval);
                    document.getElementById('countdowns').innerHTML = 'Cooldown expired';
                }
            }, 1000);
        }
        </script>
        
   <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded. Setting up tab listeners...');

    var tabs = document.querySelectorAll('.tab-link');
    console.log('Found tabs: ', tabs.length); // Debug: Print the number of tabs found

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var selectedType = this.getAttribute('data-type');
            console.log('Clicked tab with data-type: ', selectedType); // Debug: Print the data-type of the clicked tab

            var trades = document.querySelectorAll('.floaty');
            console.log('Found trades: ', trades.length); // Debug: Print the number of trade tiles found

            trades.forEach(function(trade) {
                console.log('Trade data-type: ', trade.getAttribute('data-type')); // Debug: Print each trade's data-type before comparison
                if (trade.getAttribute('data-type') === selectedType) {
                    console.log('Showing trade with matching data-type: ', trade.getAttribute('data-type')); // Debug: Print the data-type of trades being shown
                    trade.style.display = ''; // Show the trade
                } else {
                    console.log('Hiding trade with non-matching data-type: ', trade.getAttribute('data-type')); // Debug: Print the data-type of trades being hidden
                    trade.style.display = 'none'; // Hide the trade
                }
            });
        });
    });
});
</script>
        
        ";
    }
}

if ($message) echo "<p class='trade-message'>$message</p>"; ?>
<div class="trade-container">
  <?php if ($remainingTime > 0) {
    ?>
     <span style="width:100%" id="countdowns"></span> before you can trade
     <?php
  }?>
    <?php
    while ($trade = mysql_fetch_assoc($tradesResult)) {
        displayTradeTile($trade);
    }
    ?>
</div>

</body>
</html>
