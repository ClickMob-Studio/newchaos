<?php
include 'header.php';
?>

<div class='box_top'>Points Market</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->level < 10 && $user_class->prestige < 1)
            diefun("You must be level 10 or higher to use the points market.");


        if (isset($_POST['buy'])) {
            $id = security($_POST['buy']);
            $qty = security($_POST['qty']);
            if ($qty <= 0)
                diefun("You entered an invalid quantity.");

            $db->query("SELECT * FROM pointsmarket WHERE id = ?");
            $db->execute(array(
                $id
            ));

            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Market listing does not exist.");

            $db->query("SELECT rmdays FROM grpgusers WHERE id = ?");
            $db->execute(array(
                $row['owner']
            ));
            $rmdays = $db->fetch_single();
            $whichpayment = ($rmdays) ? 'bank' : 'money';

            if ($_POST['type'] == "buy" && $row['type'] == 2) {
                // Buy
                if ($qty > $row['amount'])
                    diefun("You are trying to buy more points than there are listed.");

                $cost = $row['price'] * $qty;
                if ($cost > $user_class->money)
                    diefun("You do not have enough money to buy these points. You need $" . number_format($cost, 0));

                if ($user_class->id == $row['owner'])
                    diefun("You cannot buy your own points.");

                $db->query("UPDATE grpgusers SET points = points + ?, money = money - ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $cost,
                    $user_class->id
                ));
                $db->query("UPDATE grpgusers SET $whichpayment = $whichpayment + ? WHERE id = ?");
                $db->execute(array(
                    $cost,
                    $row['owner']
                ));

                $db->query("INSERT INTO buyptmarketlog (owner, buyer, amount, price, timestamp, `type`) VALUES (?, ?, ?, ?, ?, ?)");
                $db->execute(array(
                    $row['owner'],
                    $user_class->id,
                    $qty,
                    $cost,
                    time(),
                    $row['type']
                ));

                if ($qty >= $row['amount']) {
                    $db->query("DELETE FROM pointsmarket WHERE id = ?");
                    $db->execute(array(
                        $id
                    ));
                } else {
                    $db->query("UPDATE pointsmarket SET amount = amount - ? WHERE id = ?");
                    $db->execute(array(
                        $qty,
                        $id
                    ));
                }
                Send_Event($row['owner'], "[-_USERID_-] bought " . prettynum($qty) . " points from you for " . prettynum($cost, 1) . ".", $user_class->id);
                echo Message("You have purchased " . prettynum($qty) . " points for " . prettynum($cost, 1) . ".");
                $user_class->points += $qty;
                $user_class->money -= $cost;

            } else if ($_POST['type'] == "sell" && $row['type'] == 1) {
                // Sell
                if ($qty > $row['amount'])
                    diefun("You are trying to sell more points than the buyer wants.");

                $cost = $row['price'] * $qty;
                if ($qty > $user_class->points)
                    diefun("You do not have that many points to sell.");

                if ($user_class->id == $row['owner'])
                    diefun("You cannot sell yourself points.");

                $db->query("UPDATE grpgusers SET points = points - ?, money = money + ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $cost,
                    $user_class->id
                ));

                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $row['owner']
                ));

                $db->query("INSERT INTO buyptmarketlog (owner, buyer, amount, price, timestamp, `type`) VALUES (?, ?, ?, ?, ?, ?)");
                $db->execute(array(
                    $row['owner'],
                    $user_class->id,
                    $qty,
                    $cost,
                    time(),
                    $row['type']
                ));

                if ($qty >= $row['amount']) {
                    $db->query("DELETE FROM pointsmarket WHERE id = ?");
                    $db->execute(array(
                        $id
                    ));
                } else {
                    $db->query("UPDATE pointsmarket SET amount = amount - ? WHERE id = ?");
                    $db->execute(array(
                        $qty,
                        $id
                    ));
                }
                Send_Event($user_class->id, "You have sold " . prettynum($qty) . " points to [-_USERID_-] for " . prettynum($cost, 1) . ".", $row['owner']);
                Send_Event($row['owner'], "[-_USERID_-] sold " . prettynum($qty) . " points to you for " . prettynum($cost, 1) . ".", $user_class->id);
                echo Message("You have sold " . prettynum($qty) . " points for " . prettynum($cost, 1) . ".");
                $user_class->points -= $qty;
                $user_class->money += $cost;

            } else {
                print_r($_POST);
                diefun('Something went wrong - please try again');
            }



        } elseif ($_POST['remove']) {
            $id = security($_POST['remove']);
            $qty = security($_POST['qty']);
            $db->query("SELECT * FROM pointsmarket WHERE id = ?");
            $db->execute(array(
                $id
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Market listing does not exist.");
            if ($row['owner'] != $user_class->id)
                diefun("You are not the owner of this order.");
            if ($qty > $row['amount'])
                diefun("You are trying to remove too many points.");



            if ($qty >= $row['amount']) {
                $db->query("DELETE FROM pointsmarket WHERE id = ?");
                $db->execute(array(
                    $id
                ));

                $db->query("INSERT INTO removeptmarketlog (owner, amount, price, timestamp, `type`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $row['price'],
                    time(),
                    $row['type']
                ));

            } else {
                $db->query("UPDATE pointsmarket SET amount = amount - ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $id
                ));

                $db->query("INSERT INTO removeptmarketlog (owner, amount, price, timestamp, `type`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $row['price'],
                    time(),
                    $row['type']
                ));
            }

            if ($row['type'] == 1) {
                //Buy
                $cash = $qty * $row['price'];
                $db->query("UPDATE grpgusers SET `money` = `money` + ? WHERE id = ?");
                $db->execute(array(
                    $cash,
                    $user_class->id
                ));
                $user_class->money += $cash;

            } else if ($row['type'] == 2) {
                //Sell
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $user_class->id
                ));
                $user_class->points += $qty;
            }
        } elseif ($_POST['add']) {
            // Security checks for inputs
            $qty = security($_POST['qty']);
            $price = security($_POST['price']);
            $type = security($_POST['type']);

            // Check if the user already has a listing
            $db->query("SELECT COUNT(*) AS count FROM pointsmarket WHERE owner = ?");
            $db->execute(array($user_class->id));
            $existingListing = $db->fetch_single();

            // if($existingListing > 0) {
            //     // User already has a listing, so prevent adding a new one
            //     diefun("Error :: You are only allowed to have 1 listing at a time.");
            // }
        
            // Check for invalid inputs
            if ($qty <= 0)
                diefun("Error :: Invalid quantity.");
            if ($price > 50000 || $price <= 0)
                diefun("Error :: Invalid price.");
            if ($type != 1 && $type != 2)
                diefun("Error :: Invalid type");

            // Additional checks for type 1 (Buy) or type 2 (Sell)
            $cost = $qty * $price;
            if ($type == 1 && $cost > $user_class->money)
                diefun("Error :: You do not have enough cash on hand to place this order. You need $" . number_format($cost, 0));
            if ($type == 2 && $qty > $user_class->points)
                diefun("Error :: You're trying to add more points than you own.");
            if ($cost > $user_class->money && $type == 1)
                diefun("Error :: You do not have enough cash on hand to place this order. You need $" . number_format($cost, 0));
            if ($type != 1 && $type != 2) {
                diefun("Error :: Invalid type");
            }

            if ($type == 1) {
                // Buy
        
                $cost = $qty * $price;
                $db->query("UPDATE grpgusers SET `money` = `money` - ? WHERE id = ?");
                $db->execute(array(
                    $cost,
                    $user_class->id
                ));
                $db->query("INSERT INTO pointsmarket (owner, amount, price, `type`) VALUES (?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $price,
                    $type
                ));
                $db->query("INSERT INTO addptmarketlog (owner, amount, price, timestamp, `type`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $price,
                    time(),
                    $type
                ));
                echo Message("You have added a buy order of " . prettynum($qty) . " points to the market for " . prettynum($price, 1) . " each.");
                Send_Event2($user_class->id, "Has Added <font color=red><b>" . prettynum($qty) . "</b></font> Points To The Market for " . prettynum($price, 1) . " Per Point.", $user_class->id);
                $user_class->money -= $cost;

            } else if ($type == 2) {
                // Sell
        
                $db->query("UPDATE grpgusers SET points = points - ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $user_class->id
                ));
                $db->query("INSERT INTO pointsmarket (owner, amount, price, `type`) VALUES (?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $price,
                    $type
                ));
                $db->query("INSERT INTO addptmarketlog (owner, amount, price, timestamp, `type`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $price,
                    time(),
                    $type
                ));
                echo Message("You have added " . prettynum($qty) . " points to the market for " . prettynum($price, 1) . " each.");
                Send_Event2($user_class->id, "Has Added <font color=red><b>" . prettynum($qty) . "</b></font> Points To The Market for " . prettynum($price, 1) . " Per Point.", $user_class->id);
                $user_class->points -= $qty;

            }
        }

        if ($user_class->rmdays == 0) {
            echo '<div class="floaty" style="width:99%; text-align:center">
        <h3>Caution<br>As a Non-Respected player any sales money will go into hand - Be mindful of those muggers! <br> <a href="rmstore.php">Donate</a> to become a Respected Member</h3>
        </div>';
        }


        echo '<h3>Create Order</h3>';
        echo '<div class="floaty" >';
        echo '<form method="post">';
        echo '<table style="margin:auto;text-align:center;">';
        echo '<tr>';
        echo '<td>Points</td>';
        echo '<td><input type="text" name="qty" size="5" value="0" /></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Price (Max $50K): </td>';
        echo '<td><input type="text" size="5" name="price" value="0" /></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Type</td>';
        echo '<td><select name="type"><option value="1">Buy</option><option value="2">Sell</option></select></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="2"><input type="submit" name="add" value="Add Points to Market" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '</form>';
        echo '</div>';

        echo '<h3 style="margin-bottom:0px">Sell Orders</h3><span>Players who are selling points</span>';
        echo '<div class="floaty" >';
        echo '<table id="newtables" style="margin:auto;width:100%;">';
        echo '<tr>';
        echo '<th>Seller</th>';
        echo '<th>Points</th>';
        echo '<th>Price   Each</th>';
        echo '<th>Price   Total</th>';
        echo '<th>Buy</th>';
        echo '</tr>';
        $db->query("SELECT * FROM pointsmarket WHERE `type` = 2 ORDER BY price ASC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . formatName($row['owner']) . '</td>';
            echo '<td>' . prettynum($row['amount']) . '</td>';
            echo '<td>' . prettynum($row['price'], 1) . '</td>';
            echo '<td>' . prettynum($row['price'] * $row['amount'], 1) . '</td>';
            echo '<td>';
            echo '<form method="post">';
            if ($row['owner'] == $user_class->id) {
                echo '<input type="text" size="5" name="qty" value="' . $row['amount'] . '" />';
                echo '<input type="hidden" name="remove" value="' . $row['id'] . '" />';
                echo '<input type="hidden" name="type" value="buy" />';
                echo '<input type="submit" value="Remove" />';
            } else {
                echo '<input type="text" size="5" name="qty" value="' . min(floor($user_class->money / $row['price']), $row['amount']) . '" />';
                echo '<input type="hidden" name="buy" value="' . $row['id'] . '" />';
                echo '<input type="hidden" name="type" value="buy" />';
                echo '<input type="submit" value="Buy" />';
            }
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        echo '<hr>';

        echo '<h3 style="margin-bottom:0px">Buy Orders</h3><span>Players who wish to buy points</span>';
        echo '<div class="floaty" >';
        echo '<table id="newtables" style="margin:auto;width:100%;">';
        echo '<tr>';
        echo '<th>Buyer</th>';
        echo '<th>Points</th>';
        echo '<th>Price Each</th>';
        echo '<th>Price Total</th>';
        echo '<th>Sell</th>';
        echo '</tr>';
        $db->query("SELECT * FROM pointsmarket WHERE `type` = 1 ORDER BY price ASC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . formatName($row['owner']) . '</td>';
            echo '<td>' . prettynum($row['amount']) . '</td>';
            echo '<td>' . prettynum($row['price'], 1) . '</td>';
            echo '<td>' . prettynum($row['price'] * $row['amount'], 1) . '</td>';
            echo '<td>';
            echo '<form method="post">';
            if ($row['owner'] == $user_class->id) {
                echo '<input type="text" size="5" name="qty" value="' . $row['amount'] . '" />';
                echo '<input type="hidden" name="remove" value="' . $row['id'] . '" />';
                echo '<input type="hidden" name="type" value="sell" />';
                echo '<input type="submit" value="Remove" />';
            } else {
                echo '<input type="text" size="5" name="qty" value="0" />';
                echo '<input type="hidden" name="buy" value="' . $row['id'] . '" />';
                echo '<input type="hidden" name="type" value="sell" />';
                echo '<input type="submit" value="Sell" />';
            }
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        include 'footer.php';
        ?>