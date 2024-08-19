<?php
include 'header.php';

// Function to handle item purchase and update user's points in the database
function purchaseItem($apoints, $user_class, $db){
    if ($user_class->apoints >= $apoints) {
        // Deduct points from the user's activity points
        $user_class->apoints -= $apoints;

        // Update the user's activity points in the database
        $db->query("UPDATE grpgusers SET apoints = apoints - ? WHERE id = ?");
        $db->execute(array($apoints, $user_class->id));

        return true; // Indicate successful purchase
    } else {
        return false; // Indicate insufficient points
    }
}

// Define the available items in the store
$items = array(
    array("DE", "1 Double EXP Pill", 250),
    array("MS", "15 Maze Searches", 250),
    array("JBO", "100 Jail Bot Credits", 150),
    array("RT", "10 Raid Tokens", 250),
    array("MNY", "$5,000,000 Money", 100),
    array("RSU", "1 Raid Speed Up Token", 100),
    array("PT", "15,000 Points", 500), // New item: 10,000 Points for 500 Activity Points
    array("GRT", "1 Gold Rush Token", 200),
    array("CMB", "1 Crime Booster", 250),
);

// Check if the 'buy' GET parameter is set
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
        diefun('Please ensure you enter a valid quantity. <a href="spendactivity.php">Go Back</a>');
    }

    if(purchaseItem($total_cost, $user_class, $db)) {
        foreach($purchases as $purchase) {
            $code = $purchase[0];
            $qty = $purchase[1];
            $cost = $purchase[2];
            $name = $purchase[3];

            switch ($code) {
                case 'DE':
                    Give_Item(10, $user_class->id, $qty);
                    $message = $qty . " x Hour Double EXP pack";
                    break;
                case 'MS':
                    $reward = 15 * $qty;
                    $db->query("UPDATE grpgusers SET cityturns = cityturns + " . $qty . " WHERE id = ?");
                    $db->execute(array($user_class->id));
                    $message = $reward . " Maze Searches";
                    break;
                case 'JBO':
                    $reward = 100 * $qty;

                    $db->query("UPDATE grpgusers SET jail_bot_credits = jail_bot_credits + " . $reward . " WHERE id = ?");
                    $db->execute(array($user_class->id));
                    $message = $reward . " Jail Bot Credits Points";
                    break;
                case 'RT':
                    $reward = 10 * $qty;
                    $db->query("UPDATE grpgusers SET raidtokens = raidtokens + " . $reward . " WHERE id = ?");
                    $db->execute(array($user_class->id));
                    $message = $reward . " Raid Tokens";
                    break;
                case 'MNY':
                    $reward = 5000000 * $qty;
                    $db->query("UPDATE grpgusers SET money = money + " . $reward . " WHERE id = ?");
                    $db->execute(array($user_class->id));
                    $message = "$" . number_format($reward, 0) . " Money";
                    break;
                case 'RSU':
                    Give_Item(194, $user_class->id, $qty);
                    $message = $qty . " x Raid Speed Up Token(s)";
                    break;
                case 'PT':
                    $reward = 15000 * $qty;
                    $db->query("UPDATE grpgusers SET points = points + " . $reward . " WHERE id = ?");
                    $db->execute(array($user_class->id));
                    $message = number_format($reward) . " Points";
                    break;
                case 'GRT':
                    Give_Item(253, $user_class->id, $qty);
                    $message = $qty . " x Gold Rush Token(s)";
                    break;
                case 'CMB':
                    Give_Item(255, $user_class->id, $qty);
                    $message = $qty . " x Crime Booster(s)";
                    break;
            }

            // Confirm the purchase to the user
            echo "
                <div class='alert alert-success'>
                    <p>You have successfully purchased {$message} for {$cost} Activity Points.</p>
                </div>                   
            ";
        }
    } else {
        echo "
        <div class='alert alert-danger'>
            <p>You do not have enough Activity Points for this purchase.</p>
        </div>
        ";
    }
}

?>

<div class="box_top"><h1>Activity Points Store</h1></div>
<div class="box_middle">
    <div class="pad">
        <p>
            Welcome to the Activity Points Store. Here you can spend the points you've earned for playing the game. You currently have
            <strong id="userPoints"><?php echo number_format($user_class->apoints, 0) ?> activity points</strong> to spend.
        </p>

        <form method="POST" action="" id="purchaseForm">
            <table id="newtables" style="width:100%;">
                <tr>
                    <th>Item</th>
                    <th>Cost (Activity Points)</th>
                    <th width="10%">Qty</th>
                </tr>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item[1] ?></td>
                        <td><?php echo prettynum($item[2]) ?> Activity Points</td>
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
                alert('You do not have enough activity to make this purchase.');
            }
        });
    });
</script>
<?php
include 'footer.php';
?>
