<?php
include 'header.php';

$user_id = $user_class->id;  // Assuming $user_class is already populated
$gang_id = $user_class->gang;

?>
<div class='box_top'>Gang Vault</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($gang_id != 0) {
            $gang_class = new Gang($gang_id);  // Assuming Gang is correctly implemented

            // Deposit money to gang vault
            if (isset($_POST['deposit'])) {
                $amount = $_POST['damount'];
                if ($amount > $user_class->money) {
                    echo Message("You do not have that much money.");
                } elseif ($amount < 1) {
                    echo Message("Please enter a valid amount.");
                } else {
                    $db->query("UPDATE grpgusers SET money = money - :amount WHERE id = :id");
                    $db->bind(':amount', $amount);
                    $db->bind(':id', $user_id);
                    $db->execute();

                    $db->query("UPDATE gangs SET moneyvault = moneyvault + :amount WHERE id = :gang_id");
                    $db->bind(':amount', $amount);
                    $db->bind(':gang_id', $gang_id);
                    $db->execute();

                    echo Message("You have donated $" . prettynum($amount) . " to your gang.");
                }
            }

            // Deposit points to gang vault
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

            // Donation of items to gang armoury (example)
            if (isset($_POST['submit'])) {
                $item_id = $_POST['armoury'];
                $qty = (int)$_POST['qty'];

                $db->query("SELECT quantity FROM inventory WHERE userid = :userid AND itemid = :itemid");
                $db->bind(':userid', $user_id);
                $db->bind(':itemid', $item_id);
                $inventory = $db->fetch_row(true);

                if ($inventory && $inventory['quantity'] >= $qty) {
                    $db->query("UPDATE inventory SET quantity = quantity - :qty WHERE userid = :userid AND itemid = :itemid");
                    $db->bind(':qty', $qty);
                    $db->bind(':userid', $user_id);
                    $db->bind(':itemid', $item_id);
                    $db->execute();

                    // Add items to gang armoury (assuming gangarmory table and method AddToArmory exists)
                    AddToArmory($item_id, $gang_id, $qty);  // Update this method to use PDO
                } else {
                    echo Message("You don't have enough of those.");
                }
            }

            // Display gang vault status
            $db->query("SELECT moneyvault, pointsvault FROM gangs WHERE id = :gang_id");
            $db->bind(':gang_id', $gang_id);
            $vault = $db->fetch_row(true);

            if ($vault) {
                echo "Money in Vault: " . prettynum($vault['moneyvault']) . "<br/>";
                echo "Points in Vault: " . prettynum($vault['pointsvault']) . "<br/>";
            }
        } else {
            echo Message("You aren't in a gang.");
        }
        ?>
    </div>
</div>
<?php
include("footer.php");
?>
