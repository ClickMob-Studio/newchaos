<?php
include 'header.php';

function purchaseItem($rpoints, $user_class, $db)
{
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
    array("PTS1K", "1000 Points", 200),
    array("RT", "10 Raid Tokens", 250),
    array("RSU", "1 Raid Speed Up Token", 250),
    array("GRT", "5 Gold Rush Tokens", 500),
    array("RPA", "1 Raid Pass", 500),
    array("RB", "1 Raid Booster", 750),
    array("NV", "1 Nerve Vial", 750),
    array("PB", "Police Badge", 750),
    array("EB", "Energy Booster", 750),
    array("MB", "Mystery Box", 1000),
    array("RST", "Raid Statue", 10000),
    array("LEET", 'The "Legendary Looter" Achievement', 100000),
);

if (isset($_POST['buy'])) {
    $total_cost = 0;
    $purchases = [];
    foreach ($items as $item) {
        $code = $item[0];

        if ($code == 'LEET') {
            $db->query("SELECT COUNT(*) FROM user_badges WHERE user_id = ? AND badge_id = 3");
            $db->execute(array($user_class->id));
            $has_leet = $db->fetch_single();
            if ($has_leet) {
                diefun('You already have the "Legendary Looter" Achievement. <a href="raidpointstore.php">Go Back</a>');
            }
        }


        if (isset($_POST[$code]) && $_POST[$code] > 0) {
            $qty = (int) $_POST[$code];
            $cost = $item[2] * $qty;
            $total_cost += $cost;
            $purchases[] = array($code, $qty, $cost, $item[1]);

            if ($code == "LEET" && $qty > 1) {
                diefun('You can only purchase the "Legendary Looter" Achievement once. <a href="raidpointstore.php">Go Back</a>');
            }

            if ($code == "RST" && $qty > 5) {
                diefun('You can only purchase a maximum of 5 Raid Statues at a time. <a href="raidpointstore.php">Go Back</a>');
            } else if ($code == "RST") {
                $amount = Check_Item(357, $user_class->id);
                if ($amount + $qty > 5) {
                    diefun('You can only have a maximum of 5 Raid Statues at a time, you already have ' . $amount . '. <a href="raidpointstore.php">Go Back</a>');
                }
            }
        }
    }

    if ($total_cost == 0) {
        diefun('Please ensure you enter a valid quantity. <a href="raidpointstore.php">Go Back</a>');
    }

    if (purchaseItem($total_cost, $user_class, $db)) {
        foreach ($purchases as $purchase) {
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
                case 'PTS1K':
                    $reward = 1000 * $qty;

                    $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                    $db->execute(array($reward, $user_class->id));
                    $message = $reward . " Points";
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
                case 'GRT':
                    Give_Item(253, $user_class->id, $qty);
                    $message = $qty . " x Gold Rush Token";
                    break;
                case 'RPA':
                    Give_Item(251, $user_class->id, $qty);
                    $message = $qty . " x Raid Pass";
                    break;
                case 'RB':
                    Give_Item(252, $user_class->id, $qty);
                    $message = $qty . " x Raid Booster";
                    break;
                case 'NV':
                    Give_Item(256, $user_class->id, $qty);
                    $message = $qty . " x Nerve Vial";
                    break;
                case 'PB':
                    Give_Item(163, $user_class->id, $qty);
                    $message = $qty . " x Police Badge";
                    break;
                case 'EB':
                    Give_Item(69, $user_class->id, $qty);
                    $message = $qty . " x Energy Booster";
                    break;
                case 'MB':
                    Give_Item(42, $user_class->id, $qty);
                    $message = $qty . " x Mystery Box";
                    break;
                case 'RST':
                    Give_Item(357, $user_class->id, $qty);
                    $message = $qty . " x Raid Statue";
                    break;
                case 'LEET':
                    $db->query("INSERT INTO user_badges (user_id, badge_id, timestamp) VALUES (?, 3, ?)");
                    $db->execute([$user_class->id, time()]);
                    $message = 'The "Legendary Looter" Achievement';
                    break;
            }

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

<div class="box_top">
    <h1>Raid Points Store</h1>
</div>
<div class="box_middle">
    <div class="pad">
        <p>
            Welcome to the Raid Points Store. Here you can spend the points you've earned completing raids. You
            currently have
            <strong id="userPoints"><?php echo number_format($user_class->raidpoints, 0) ?> raid points</strong> to
            spend.
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
                        <td><input type="number" name="<?php echo $item[0] ?>" style="width: 100px;" min="0"
                                data-cost="<?php echo $item[2] ?>" class="item-qty" /></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" name="buy" class="btn btn-primary" value="BUY NOW" />
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('purchaseForm');
        const userPoints = parseInt(document.getElementById('userPoints').innerText.replace(/,/g, ''));
        const itemQtyInputs = document.querySelectorAll('.item-qty');

        itemQtyInputs.forEach(input => {
            input.addEventListener('input', function () {
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

        form.addEventListener('submit', function (event) {
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