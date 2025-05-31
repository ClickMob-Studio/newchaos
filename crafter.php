<?php
include 'header.php'; // Make sure this file contains the database connection $conn
?>
<h1>Crafter</h1>
<link rel="stylesheet" href="asset/css/crafter.css">
<?php
// Function to retrieve item details from the database
function getItemDetails($itemId)
{
    global $db;

    $db->query("SELECT itemname, image FROM items WHERE id = ?");
    $db->execute([$itemId]);

    return $db->fetch_row(true);
}

// Function to handle the trade process
function handleTrade($tradeId)
{
    global $db, $user_class; // Ensure that $user_class is accessible in this scope

    // Fetch trade details
    $db->query("SELECT * FROM trades WHERE id = ?");
    $db->execute([$tradeId]);
    $trade = $db->fetch_row(true);
    if (!$trade || empty($trade)) {
        return "Invalid trade.";
    }

    if ($trade['inventory_limit'] > 0 && Check_item($trade['itemreward1'], $user_class->id) >= $trade['inventory_limit']) {
        return 'You can only have a maximum of ' . $trade['inventory_limit'] . ' of this item in your inventory.';
    }

    // List to hold items that the user lacks
    $lackingItems = [];

    // Check if user has required items
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["item$i"]) && $trade["item{$i}quantity"] > 0) {
            $userQuantity = Check_Item($trade["item$i"], $user_class->id);
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

    // Deduct required items from user's inventory and remove if quantity becomes 0
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["item$i"])) {
            Take_Item($trade["item$i"], $user_class->id, $trade["item{$i}quantity"]);
        }
    }

    // Add reward items to user's inventory
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["itemreward$i"])) {
            $rewardItemId = $trade["itemreward$i"];
            Give_Item($rewardItemId, $user_class->id, 1);
        }
    }

    return "Trade successful!";
}

// Check for trade submission
$message = '';
if (isset($_POST['tradeId'])) {
    $message = handleTrade($_POST['tradeId']);
}

// Function to display the trade tile
// Function to display the trade tile in a block format
function displayTradeTile($trade)
{
    global $user_class;
    $user_id = $user_class->id;

    echo "<div class='trade-card " . $trade['trade_group_name'] . "-card'>";

    // Display trade name
    echo "<h3>" . htmlspecialchars($trade['name']) . "</h3>";

    // Start trade-item-container
    echo "<div class='trade-item-container'>"; // Wrap items in a container

    // Loop through items required for the trade and display them
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($trade["item$i"]) && $trade["item{$i}quantity"] > 0) {
            $itemId = $trade["item$i"];
            $item = getItemDetails($itemId);
            $userQuantity = Check_Item($itemId, $user_id);
            if ($item) {
                echo "<div class='trade-item'>";
                echo "<img src='" . htmlspecialchars($item['image']) . "' alt='" . htmlspecialchars($item['itemname']) . "' style='width:50px; height:50px;'>"; // Control image size here
                echo "<div class='item-details'>";
                echo "<span class='item-name'>" . htmlspecialchars($item['itemname']) . "</span>";
                echo "<span class='item-requirement'>You Require:<font color=orange><b> " . $trade["item{$i}quantity"] . "</b></font><br></span>";
                echo "<span class='user-quantity'>You have:<b style='color:red'> " . $userQuantity . "</b></span>";
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
                echo "<span class='reward-name'>Reward:<br /> " . htmlspecialchars($rewardItem['itemname']) . "</span>";
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
if (isset($_GET['filter_results']) && in_array($_GET['filter_results'], haystack: array('Materials', 'Boosters', 'HI', 'Consumables'))) {
    $db->query("SELECT * FROM trades WHERE trade_group_name = ?");
    $db->execute([$_GET['filter_results']]);
} else {
    $db->query("SELECT * FROM trades");
    $db->execute();
}

$tradesResult = $db->fetch_row();

?>



<div class="contenthead floaty">
    <div class="shopkeeper-section">
        <div class="shopkeeper-description">
            <h2>Welcome to the Crafting Station!</h2>
            <p>Here at the crafting, you can exchange items you've collected on your adventures for rare and powerful
                goods. Our friendly shopkeeper has a keen eye for value and will offer you the best deals for your
                treasures. Take a look and see what wonders await!</p>

            <p><strong>Filter:</strong> <a href="crafter.php">All</a> | <a
                    href="crafter.php?filter_results=Materials">Materials</a> | <a
                    href="crafter.php?filter_results=Boosters">Boosters</a> | <a
                    href="crafter.php?filter_results=HI">Home Improvement</a> | <a
                    href="crafter.php?filter_results=Consumables">Consumables</a></p>
        </div>
    </div>
</div>

<?php
$currentTimestamp = time(); // Current timestamp in seconds
$cooldownQuery = "SELECT timestamp FROM crafter_cooldown WHERE user_id = $user_class->id";
$db->query("SELECT timestamp FROM crafter_cooldown WHERE user_id = ?");
$db->execute([$user_class->id]);
$cooldownResult = $db->fetch_single();
if ($cooldownResult) {
    $cooldownTimestamp = strtotime($cooldownResult); // Convert timestamp from database to seconds
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

if ($message)
    echo "<p class='trade-message'>$message</p>"; ?>
<div class="trade-container">
    <?php if ($remainingTime > 0) {
        ?>
        <span style="width:100%" id="countdowns"></span> before you can trade
        <?php
    } ?>
    <?php
    foreach ($tradesResult as $trade) {
        displayTradeTile($trade);
    }
    ?>
</div>

<?php require "footer.php"; ?>