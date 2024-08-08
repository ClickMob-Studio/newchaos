<?php
include 'header.php';

function purchaseItem($rpoints, $user_class, $db){
    if ($user_class->raidpoints >= $rpoints) {
        // Deduct points from the user's raid points
        $user_class->raidpoints -= $rpoints;

        // Update the user's raid points in the database
        $db->query("UPDATE grpgusers SET raidpoints = raidpoints - ? WHERE id = ?");
        $db->execute(array($rpoints, $user_class->id));

        return true; // Indicate successful purchase
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

if(isset($_POST['buy'])){
    $total_cost = 0;
    $purchases = [];
    foreach($items as $item) {
        $code = $item[0];
        if(isset($_POST[$code]) && $_POST[$code] > 0) {
            $qty = (int)$_POST[$code];
            $cost = $item[2] * $qty;
            $total_cost += $cost;
            $purchases[] = array($code, $qty, $cost, $item[1]);
        }
    }

    if($total_cost == 0) {
        diefun('Please ensure you enter a valid quantity. <a href="raidpointstore.php">Go Back</a>');
    }

    if(purchaseItem($total_cost, $user_class, $db)) {
        foreach($purchases as $purchase) {
            $code = $purchase[0];
            $qty = $purchase[1];
            $cost = $purchase[2];
            $name = $purchase[3];

            switch ($code) {
                case 'JBO':
                    $reward = 100 * $qty;

                    $db->query("UPDATE grpgusers SET jail_bot_credits = jail_bot_credits + ? WHERE id = ?");
                    $db->execute(array($reward, $user_class->id));
                    $message = $reward . " Jail Bot Credits";
                    break;
                case 'RT':
                    $reward = 10 * $qty;

                    $db->query("UPDATE grpgusers SET raidtokens = raidtokens + ? WHERE id = ?");
                    $db->execute(array($reward, $user_class->id));
                    $message = $reward . " Raid Tokens";
                    break;
                case 'RSU':
                    Give_Item(194, $user_class->id, $qty);
                    $message = $qty . " x Raid Speed Up Token";
                    break;
                case 'RPA':
                    Give_Item(251, $user_class->id, $qty);
                    $message = $qty . " x Raid Pass";
                    break;
                case 'RB':
                    Give_Item(252, $user_class->id, $qty);
                    $message = $qty . " x Raid Booster";
                    break;
                case 'PB':
                    Give_Item(163, $user_class->id, $qty);
                    $message = $qty . " x Police Badge";
                    break;
                case 'EB':
                    Give_Item(69, $user_class->id, $qty);
                    $message = $qty . " x Energy Booster";
                    break;
            }

            // Confirm the purchase to the user
            echo "
                <div class='alert alert-success'>
                    <p>You have successfully purchased {$message} for {$cost} Raid Points.</p>
                </div>                   
            ";
        }
    } else {
        echo "
        <div class='alert alert-danger'>
            <p>You do not have enough Raid Points for this purchase.</p>
        </div>
        ";
    }
}
?>

<div class="box_top"><h1>Raid Points Store</h1></div>
<div class="box_middle">
    <div class="pad">
        <p>
            Welcome to the Raid Points Store. Here you can spend the points you've earned completing raids. You currently have
            <strong id="userPoints"><?php echo number_format($user_class->raidpoints, 0) ?> raid points</strong> to spend.
        </p>

        <form method="POST" action="" id="purchaseForm">
            <table id="newtables" style="width:100%;">
                <tr>
                    <th>Item</th>
                    <th>Cost (Raid Points)</th>
                    <th width="10%">Qty</th>
                </tr>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item[1] ?></td>
                        <td><?php echo prettynum($item[2]) ?> Raid Points</td>
                        <td><input type="number" name="<?php echo $item[0] ?>" style="width: 100px;" min="0" data-cost="<?php echo $item[2] ?>" class="item-qty" /></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" name="buy" class="btn btn-primary" value="BUY NOW" />
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('purchaseForm');
        const userPoints = parseInt(document.getElementById('userPoints').innerText.replace(/,/g, ''));
        const itemQtyInputs = document.querySelectorAll('.item-qty');

        itemQtyInputs.forEach(input => {
            input.addEventListener('input', function() {
                let totalCost = 0;
                itemQtyInputs.forEach(item => {
                    const qty = parseInt(item.value) || 0;
                    const cost = parseInt(item.dataset.cost);
                    totalCost += qty * cost;
                });

                if (totalCost > userPoints) {
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });
        });

        form.addEventListener('submit', function(event) {
            let totalCost = 0;
            itemQtyInputs.forEach(item => {
                const qty = parseInt(item.value) || 0;
                const cost = parseInt(item.dataset.cost);
                totalCost += qty * cost;
            });

            if (totalCost > userPoints) {
                event.preventDefault();
                alert('You do not have enough Raid Points to make this purchase.');
            }
        });
    });
</script>

<?php
include 'footer.php';
?>
