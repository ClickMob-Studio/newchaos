<?php
include 'header.php';
include 'database.php'; // Ensure this file contains your database class as provided earlier

$db = database::getInstance(); // Get the singleton instance of the database
$gang_id = $user_class->gang; // Assuming $user_class is already populated

?>
<div class='box_top'>Gang Vault</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($gang_id != 0) {
            $gang_class = new Gang($gang_id);

            // Process a money deposit
            if (isset($_POST['deposit'])) {
                $amount = $_POST['damount'];
                if ($amount > $user_class->money) {
                    echo Message("You do not have that much money.");
                } elseif ($amount < 1) {
                    echo Message("Please enter a valid amount.");
                } else {
                    // Update user's money
                    $db->query("UPDATE grpgusers SET money = money - :amount WHERE id = :id");
                    $db->bind(':amount', $amount);
                    $db->bind(':id', $user_class->id);
                    $db->execute();

                    // Update gang's vault
                    $db->query("UPDATE gangs SET moneyvault = moneyvault + :amount WHERE id = :gang_id");
                    $db->bind(':amount', $amount);
                    $db->bind(':gang_id', $gang_id);
                    $db->execute();

                    // Insert log entry
                    $db->query("INSERT INTO gang_vault_log (gang_id, user_id, type, added, balance) VALUES (:gang_id, :user_id, 'money', :amount, (SELECT moneyvault FROM gangs WHERE id = :gang_id))");
                    $db->bind(':gang_id', $gang_id);
                    $db->bind(':user_id', $user_class->id);
                    $db->bind(':amount', $amount);
                    $db->execute();

                    echo Message("You have donated $" . prettynum($amount) . " to your gang.");
                }
            }

            // Process a points deposit
            if (isset($_POST['deposit2'])) {
                $amount = $_POST['damount'];
                if ($amount > $user_class->points) {
                    echo Message("You do not have that many points.");
                } elseif ($amount < 1) {
                    echo Message("Please enter a valid amount.");
                } else {
                    // Update user's points
                    $db->query("UPDATE grpgusers SET points = points - :amount WHERE id = :id");
                    $db->bind(':amount', $amount);
                    $db->bind(':id', $user_class->id);
                    $db->execute();

                    // Update gang's points vault
                    $db->query("UPDATE gangs SET pointsvault = pointsvault + :amount WHERE id = :gang_id");
                    $db->bind(':amount', $amount);
                    $db->bind(':gang_id', $gang_id);
                    $db->execute();

                    echo Message("You have donated " . prettynum($amount) . " points to your gang.");
                }
            }

            // Handle item donations
            if (isset($_POST['submit'])) {
                if (empty($_POST['armoury'])) {
                    echo "You need to pick an item to donate.<br/><br/><a href='gangvault.php'>Go Back</a>";
                } else {
                    $item_id = $_POST['armoury'];
                    $qty = (int) $_POST['qty'];
                    $qty = max(0, floor($qty)); // Ensure quantity is non-negative and an integer

                    // Check user's item quantity
                    $db->query("SELECT quantity FROM inventory WHERE userid = :userid AND itemid = :itemid");
                    $db->bind(':userid', $user_class->id);
                    $db->bind(':itemid', $item_id);
                    if ($item = $db->fetch_row(true)) {
                        if ($item['quantity'] < $qty) {
                            echo Message("You don't have enough of those.");
                        } else {
                            // Deduct items from user inventory
                            $db->query("UPDATE inventory SET quantity = quantity - :qty WHERE userid = :userid AND itemid = :itemid");
                            $db->bind(':qty', $qty);
                            $db->bind(':userid', $user_class->id);
                            $db->bind(':itemid', $item_id);
                            $db->execute();

                            // Add items to gang armoury
                            $db->query("INSERT INTO gangarmory (gangid, itemid, quantity) VALUES (:gang_id, :itemid, :qty) ON DUPLICATE KEY UPDATE quantity = quantity + :qty");
                            $db->bind(':gang_id', $gang_id);
                            $db->bind(':itemid', $item_id);
                            $db->bind(':qty', $qty);
                            $db->execute();

                            echo Message("You have donated [x$qty] " . $item['itemname'] . " to your gang.");
                        }
                    } else {
                        echo Message("Error retrieving item details.");
                    }
                }
            }

            // Display current vault status
            echo "<br/><center><b>Gang Armoury</b></center><table id='newtables' style='table-layout:fixed;'>";
            $db->query("SELECT ga.itemid, ga.quantity, i.itemname FROM gangarmory ga JOIN items i ON ga.itemid = i.id WHERE ga.gangid = :gang_id ORDER BY ga.quantity DESC");
            $db->bind(':gang_id', $gang_id);
            $items = $db->fetch_row();
            foreach ($items as $item) {
                echo "<tr><td width='50%'>" . item_popup($item['itemname'], $item['itemid']) . "</td><td width='20%'>" . prettynum($item['quantity']) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo Message("You aren't in a gang.");
        }
        ?>
    </div>
</div>
<?php
include 'footer.php';
?>
