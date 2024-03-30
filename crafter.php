<?php
include 'header.php'; // Make sure this file contains the database connection $conn
?>
	
	<div class='box_top'>Crafter</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php


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
  //  $currentTimestamp = time(); // Current timestamp in seconds
  //  $cooldownQuery = "SELECT timestamp FROM crafter_cooldown WHERE user_id = //$userId";
  //  $cooldownResult = mysql_query($cooldownQuery);
  //  if ($cooldownRow = mysql_fetch_assoc($cooldownResult)) {
   //     $cooldownTimestamp = strtotime($cooldownRow['timestamp']); // Convert //timestamp from database to seconds
     //   $remainingTime = $cooldownTimestamp - $currentTimestamp;

      //  if ($remainingTime > 0) {
            // User has an active cooldown, return a message indicating //remaining cooldown time
       //     $minutes = floor($remainingTime / 60);
     //       $seconds = $remainingTime % 60;
   //         return "You cannot currently trade! Time remaining: $minutes minutes //$seconds seconds";
  //      }
//    }

 // Fetch trade details
    $tradeQuery = "SELECT * FROM trades WHERE id = $tradeId";
    $tradeResult = mysql_query($tradeQuery);
    if (!$tradeResult || mysql_num_rows($tradeResult) == 0) {
        return "Invalid trade.";
    }
  //  $cooldownQuery = "SELECT * FROM crafter_cooldown WHERE user_id = $userId //AND NOW() > timestamp";
 //   $cooldownResult = mysql_query($cooldownQuery);
    
    // Check if the query was successful
 //   if (mysql_num_rows($cooldownResult)) {
        // Check if there are no rows returned, meaning there's no cooldown record for the user or the cooldown has expired
 //       if (mysql_num_rows($cooldownResult) < 1) {
            
            // There is a cooldown record for the user and the current time is before the cooldown expiration
            // You can return the message if you want, or execute other actions
  //          return "You cannot currently trade!";
   //     }
 //   } 
    
    
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
//    $checkQuery = "SELECT * FROM crafter_cooldown WHERE user_id = $userId";
//    $checkResult = mysql_query($checkQuery);
//    if (mysql_num_rows($checkResult) > 0) {
//        // Update the existing record
//        $updateQuery = "UPDATE crafter_cooldown SET timestamp = //'$timestampOneHourAhead' WHERE user_id = $userId";
 //       $updateResult = mysql_query($updateQuery);
 //   } else {
//        // Insert a new record
//        $insertQuery = "INSERT INTO crafter_cooldown (user_id, timestamp) //VALUES ($userId, '$timestampOneHourAhead')";
//        $insertResult = mysql_query($insertQuery);
//    }
    
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
// Function to display the trade tile in a block format
function displayTradeTile($trade) {
    global $user_class;
    $user_id = $user_class->id;

    echo "<div class='trade-card'>";
    
    // Display trade name
    echo "<h3>" . htmlspecialchars($trade['name']) . "</h3>";

    // Start trade-item-container
    echo "<div class='trade-item-container'>"; // Wrap items in a container

    // Loop through items required for the trade and display them
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["item$i"]) && $trade["item{$i}quantity"] > 0) {
            $itemId = $trade["item$i"];
            $item = getItemDetails($itemId);
            $userQuantity = getUserItemQuantity($user_id, $itemId);
            if ($item) {
                echo "<div class='trade-item'>";
                echo "<img src='" . htmlspecialchars($item['image']) . "' alt='" . htmlspecialchars($item['itemname']) . "' style='width:50px; height:50px;'>"; // Control image size here
                echo "<div class='item-details'>";
                echo "<span class='item-name'>" . htmlspecialchars($item['itemname']) . "</span>";
                echo "<span class='item-requirement'>You Require:<font color=orange><b> " . $trade["item{$i}quantity"] . "</b></font><br></span>";
                echo "<span class='user-quantity'>You have:<font color=black><b> " . $userQuantity . "</b></font></span>";
                echo "</div>"; // Close item-details
                echo "</div>"; // Close trade-item
            }
        }
    }

    // Close trade-item-container
    echo "</div>"; // This div wraps all items

    // Display reward items
    echo "<div class='trade-rewards'>";
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["itemreward$i"])) {
            $rewardItem = getItemDetails($trade["itemreward$i"]);
            if ($rewardItem) {
                echo "<div class='reward-item'>";
                echo "<img src='" . htmlspecialchars($rewardItem['image']) . "' alt='" . htmlspecialchars($rewardItem['itemname']) . "' style='width:50px; height:50px;'>"; // Control image size here
                echo "<span class='reward-name'>Reward: " . htmlspecialchars($rewardItem['itemname']) . "</span>";
                echo "</div>"; // Close reward-item
            }
        }
    }
    echo "</div>"; // Close trade-rewards

    // Display the trade button
    echo "<form method='post' action=''>";
    echo "<button type='submit' class='trade-button' name='tradeId' value='" . $trade['id'] . "'>Trade</button>";
    echo "</form>";

    echo "</div>"; // Close trade-card
}


// Fetch all the trades from the database
$tradesQuery = "SELECT * FROM trades";
$tradesResult = mysql_query($tradesQuery);
?>
<style>

.trade-item-image, .reward-item-image {
    width: 50px; /* Adjust width */
    height: 50px; /* Adjust height */
    display: block; /* Images are block level to sit above the text */
    margin: 0 auto 10px; /* Center image and add space below */
}

.reward-name {
    display: block; /* Ensure the reward name is on a new line */

    text-align: center; /* Center the text */
    margin-top: 5px; /* Space above the reward name */
}

.trade-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between; /* Adjust as needed for spacing */
    margin-bottom: 30px;
}

.trade-card {
    flex-basis: calc(33.33% - 20px); /* Adjust based on your desired spacing */
      padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.5); /* Subtle shadow */
    text-align: center;
}

.trade-item-container, .trade-rewards {
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.5); /* Subtle shadow */
    border-radius: 10px; /* Rounded corners */
    padding: 10px; /* Padding inside the containers */
    margin-bottom: 20px; /* Space between containers */
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* Center items */
    gap: 10px; /* Space between items */
}

.trade-item, .reward-item {
    background-color: transparent; /* Transparent background for items to match the card */
    box-shadow: none; /* No additional shadow on individual items */
    margin-bottom: 10px; /* Space between items */
    border-radius: 5px; /* Slightly rounded corners for items */
    flex: 1 0 auto; /* Grow to fill space, don't shrink smaller than content */
}

.item-name, .item-quantity, .reward-name {
    display: block; /* Ensure these are on their own line */
    text-align: center;
    color: #fff;
}

@media (max-width: 768px) {
    .trade-card {
        flex-basis: calc(50% - 20px); /* Show 2 per row on smaller screens */
    }
}

.shopkeeper-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.shopkeeper-image {
    width: 200px;
    height: auto;
}

.shopkeeper-description {
    width: 70%;
    text-align: left;
    font-family: Arial, sans-serif;
    color: #fff ;
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
    width: 40px;
    height: 40px;
    display: block;
    margin: 0 auto;
}

.quantity-indicator {
    position: absolute;
    top: -10px;
    right: -10px;
    background-color: rgba(0, 0, 0, 0.75);
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
    font-size: 0.9em;
    z-index: 4;
    position: relative;
}

.trade-title {
    text-align: center;
}

.contenthead {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}



    </style>


<div class="contenthead floaty">
    <div class="shopkeeper-section">
        <div class="shopkeeper-description">
            <h2>Welcome to the Crafting Station!</h2>
            <p>Here at the crafting, you can exchange items you've collected on your adventures for rare and powerful goods. Our friendly shopkeeper has a keen eye for value and will offer you the best deals for your treasures. Take a look and see what wonders await!</p>
        </div>
    </div>
</div>


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
