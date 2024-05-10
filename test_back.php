<?php
include 'header.php';

$user_id = $user_class->id;
$gang_id = $user_class->gang;

?>
<div class='box_top'>Gang Vault</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($gang_id != 0) {
            $gang_class = new Gang($gang_id);

            // Handle deposit of money
            if (isset($_POST['deposit'])) {
                $amount = $_POST['damount'];
                if ($amount > $user_class->money) {
                    echo Message("You do not have that much money.");
                } elseif ($amount < 1) {
                    echo Message("Please enter a valid amount.");
                } else {
                    // Use PDO to update user money and gang vault
                    $db->query("UPDATE grpgusers SET money = money - :amount WHERE id = :id");
                    $db->bind(':amount', $amount);
                    $db->bind(':id', $user_id);
                    $db->execute();

                    $db->query("UPDATE gangs SET moneyvault = moneyvault + :amount WHERE id = :gang_id");
                    $db->bind(':amount', $amount);
                    $db->bind(':gang_id', $gang_id);
                    $db->execute();

                    // Log the donation
                    $db->query("INSERT INTO gang_vault_log (gang_id, user_id, type, added, balance) VALUES (:gang_id, :user_id, 'money', :amount, (SELECT moneyvault FROM gangs WHERE id = :gang_id))");
                    $db->bind(':gang_id', $gang_id);
                    $db->bind(':user_id', $user_id);
                    $db->bind(':amount', $amount);
                    $db->execute();

                    echo Message("You have donated $" . prettynum($amount) . " to your gang.");
                }
            }

            // Similar process for points donation
            if (isset($_POST['deposit2'])) {
                $amount = $_POST['damount'];
                if ($amount > $user_class->points) {
                    echo Message("You do not have that many points.");
                } elseif ($amount < 1) {
                    echo Message("Please enter a valid amount.");
                } else {
                    $db->query("UPDATE grpgusers SET points = points - :amount WHERE id = :id");
                    $db->bind(':amount', $amount);
                    $db->bind(':id', $user_id);
                    $db->execute();

                    $db->query("UPDATE gangs SET pointsvault = pointsvault + :amount WHERE id = :gang_id");
                    $db->bind(':amount', $amount);
                    $db->bind(':gang_id', $gang_id);
                    $db->execute();

                    echo Message("You have donated " . prettynum($amount) . " points to your gang.");
                }
            }

            // Handling item donations
            if (isset($_POST['submit'])) {
                if (empty($_POST['armoury'])) {
                    echo "You need to pick an item to donate.<br/><br/><a href='gangvault.php'>Go Back</a>";
                } else {
                    $item_id = $_POST['armoury'];
                    $qty = (int) $_POST['qty'];

                    // Check if user has enough items
                    $db->query("SELECT quantity FROM inventory WHERE userid = :userid AND itemid = :itemid");
                    $db->bind(':userid', $user_id);
                    $db->bind(':itemid', $item_id);
                    $inventory = $db->fetch_row(true);

                    if ($inventory['quantity'] < $qty) {
                        echo "You don't have enough of those.";
                    } else {
                        // Update the user's inventory
                        $db->query("UPDATE inventory SET quantity = quantity - :qty WHERE userid = :userid AND itemid = :itemid");
                        $db->bind(':qty', $qty);
                        $db->bind(':userid', $user_id);
                        $db->bind(':itemid', $item_id);
                        $db->execute();

                        // Add items to gang armoury
                        // Assuming AddToArmory is now implemented using PDO in the Gang class
                        $gang_class->AddToArmory($item_id, $qty);

                        echo Message("You have donated [x$qty] " . $worked['itemname'] . " to your gang.");
                    }
                }
            }

            // Display current vault status
            $db->query("SELECT moneyvault, pointsvault FROM gangs WHERE id = :gang_id");
            $db->bind(':gang_id', $gang_id);
            $vault = $db->fetch_row(true);
            echo "
                Welcome to the gang vault. Here you can store cash, points, and items!<br /><br />
                <table id='newtables' style='width:100%; table-layout:fixed;'>
                    <tr>
                        <th>Money:</th><td>" . prettynum($vault['moneyvault'], 1) . "</td>
                        <th>Points:</th><td>" . prettynum($vault['pointsvault']) . "</td>
                    </tr>
                </table>";

            // Further implementation would continue here...
        } else {
            echo Message("You aren't in a gang.");
        }
        ?>
    </div>
</div>
<?php
include 'footer.php';
?>
