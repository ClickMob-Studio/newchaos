<?php
include 'header.php';

function purchaseItem($rpoints, $user_class, $db){
    if ($user_class->raidpoints >= $rpoints) {
        // Deduct points from the user's raid points
        $user_class->raidpoints -= $rpoints;

        // Update the user's raid points in the database
        $db->query("UPDATE grpgusers SET raidpoints = raidpoints - ? WHERE id = ?");
        $db->execute(array($rpoints, $user_class->id));

        return true; // Indic ate successful purchase
    } else {
        return false; // Indicate insufficient points
    }
}

// Define the available items in the store
$items = array(
    array("JBO", "100 Jail Bot Credits", 100),
    array("RT", "10 Raid Tokens", 250),
    array("RSU", "1 Raid Speed Up Token", 250),
    array("RPA", "1 Raid Pass", 500),
    array("RB", "1 Raid Booster", 750),
    array("PB", "Police Badge", 750),
    array("EB", "Energy Booster", 750),
);

if(isset($_GET['buy'])){
    foreach($items as $item) {
        if($_GET['buy'] == $item[0]) {
            //Send_Event(2, $user_class->formattedname . ' RAID STORE: ' . $item[1] . ' - ' . $item[2] . ' - ' . $user_class->raidpoints, 2);
            if(purchaseItem($item[2], $user_class, $db)) {
                // Handle the purchase based on item code
                switch ($item[0]) {
                    case 'JBO':
                        $db->query("UPDATE grpgusers SET jail_bot_credits = jail_bot_credits + 100 WHERE id = ?");
                        $db->execute(array($user_class->id));
                        $message = "100 Jail Bot Credits Points";
                        break;
                    case 'RT':
                        $db->query("UPDATE grpgusers SET raidtokens = raidtokens + 10 WHERE id = ?");
                        $db->execute(array($user_class->id));
                        $message = "10 Raid Tokens";
                        break;
                    case 'RSU':
                        Give_Item(194, $user_class->id, 1);
                        $message = "1 Raid Speed Up Token";
                        break;
                    case 'RPA':
                        Give_Item(251, $user_class->id, 1);
                        $message = "1 Raid Pass";
                        break;
                    case 'RB':
                        Give_Item(252, $user_class->id, 1);
                        $message = "1 Raid Booster";
                        break;
                    case 'PB':
                        Give_Item(163, $user_class->id, 1);
                        $message = "1 Police Badge";
                        break;
                    case 'EB':
                        Give_Item(69, $user_class->id, 1);
                        $message = "1 Energy Booster";
                        break;
                }

                // Confirm the purchase to the user
                echo "
                    <div class='alert alert-success'>
                      <p>You have successfully purchased {$message} for {$item[2]} Raid Points.</p>
                    </div>                   
                ";
            } else {
                echo "
                <div class='alert alert-danger'>
                    <p>You do not have enough Raid Points for this purchase.</p>
                </div>
                ";
            }
            break; // Exit the loop once the item is found and processed
        }
    }
}

?>

<div class="box_top"><h1>Raid Points Store</h1></div>
<div class="box_middle">
    <div class="pad">
        <p>
            Welcome to the Raid Points Store. Here you can spend the points you've earned completing raids. You currently have
            <strong><?php echo number_format($user_class->raidpoints, 0) ?> raid points</strong> to spend.
        </p>


        <table id="newtables" style="width:100%;">
            <tr>
                <th>Item</th>
                <th>Cost (Raid Points)</th>
                <th width="10%">Qty</th>
                <th>Purchase</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <form method="POST" action="?buy=<?php echo $item[0] ?>">
                    <tr>
                        <td><?php echo $item[1] ?></td>
                        <td><?php echo prettynum($item[2]) ?> Raid Points</td>
                        <td><input type="number" name="qty" style="width: 100px;" /></td>
                        <td><input type="submit" class="btn btn-primary" value="BUY NOW" /></td>
                    </tr>
                </form>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php
include 'footer.php';
?>
