<?php
include 'header.php';
?>

<div class='box_top'>Gold Market</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->level < 10 && $user_class->prestige < 1)
            diefun("You must be level 10 or higher to use the GOLD market.");


        if (isset($_POST['buy'])) {
            $id = security($_POST['buy']);
            $qty = security($_POST['qty']);
            if ($qty <= 0)
                diefun("You entered an invalid quantity.");

            $db->query("SELECT * FROM creditsmarket WHERE id = ?");
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
            $whichpayment = ($rmdays) ? 'bank' : 'points';

            if ($_POST['type'] == "buy" && $row['type'] == 2) {
                // Buy
                if ($qty > $row['amount'])
                    diefun("You are trying to buy more GOLD than there are listed.");

                $cost = $row['price'] * $qty;
                if ($cost > $user_class->points)
                    diefun("You do not have enough points to buy this GOLD. You need " . number_format($cost) . " points");

                if ($user_class->id == $row['owner'])
                    diefun("You cannot buy your own GOLD.");

                $db->query("UPDATE grpgusers SET credits = credits + ?, points = points - ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $cost,
                    $user_class->id
                ));
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $cost,
                    $row['owner']
                ));

                if ($qty >= $row['amount']) {
                    $db->query("DELETE FROM creditsmarket WHERE id = ?");
                    $db->execute(array(
                        $id
                    ));
                } else {
                    $db->query("UPDATE creditsmarket SET amount = amount - ? WHERE id = ?");
                    $db->execute(array(
                        $qty,
                        $id
                    ));
                }
                Send_Event($row['owner'], "[-_USERID_-] bought " . prettynum($qty) . " GOLD from you for " . prettynum($cost) . ".", $user_class->id);
                echo Message("You have purchased " . prettynum($qty) . " GOLD for " . prettynum($cost) . ".");
                $user_class->credits += $qty;
                $user_class->points -= $cost;

            } else if ($_POST['type'] == "sell" && $row['type'] == 1) {
                // Sell
                if ($qty > $row['amount'])
                    diefun("You are trying to sell more GOLD than the buyer wants.");

                $cost = $row['price'] * $qty;
                if ($qty > $user_class->credits)
                    diefun("You do not have that many GOLD to sell.");

                if ($user_class->id == $row['owner'])
                    diefun("You cannot sell yourself GOLD.");

                $db->query("UPDATE grpgusers SET credits = credits - ?, points = points + ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $cost,
                    $user_class->id
                ));

                $db->query("UPDATE grpgusers SET credits = credits + ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $row['owner']
                ));

                if ($qty >= $row['amount']) {
                    $db->query("DELETE FROM creditsmarket WHERE id = ?");
                    $db->execute(array(
                        $id
                    ));
                } else {
                    $db->query("UPDATE creditsmarket SET amount = amount - ? WHERE id = ?");
                    $db->execute(array(
                        $qty,
                        $id
                    ));
                }
                Send_Event($user_class->id, "You have sold " . prettynum($qty) . " GOLD to [-_USERID_-] for " . prettynum($cost) . " points.", $row['owner']);
                Send_Event($row['owner'], "[-_USERID_-] sold " . prettynum($qty) . " GOLD to you for " . prettynum($cost) . " points.", $user_class->id);
                echo Message("You have sold " . prettynum($qty) . " GOLD for " . prettynum($cost, 1) . ".");
                $user_class->credits -= $qty;
                $user_class->points += $cost;

            } else {
                print_r($_POST);
                diefun('Something went wrong - please try again');
            }



        } elseif (isset($_POST['remove'])) {
            $id = security($_POST['remove']);
            $qty = security($_POST['qty']);
            $db->query("SELECT * FROM creditsmarket WHERE id = ?");
            $db->execute(array(
                $id
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Market listing does not exist.");
            if ($row['owner'] != $user_class->id)
                diefun("You are not the owner of this order.");
            if ($qty > $row['amount'])
                diefun("You are trying to remove too much GOLD.");



            if ($qty >= $row['amount']) {
                $db->query("DELETE FROM creditsmarket WHERE id = ?");
                $db->execute(array(
                    $id
                ));

            } else {
                $db->query("UPDATE creditsmarket SET amount = amount - ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $id
                ));
            }

            if ($row['type'] == 1) {
                //Buy
                $cash = $qty * $row['price'];
                $db->query("UPDATE grpgusers SET `points` = `points` + ? WHERE id = ?");
                $db->execute(array(
                    $cash,
                    $user_class->id
                ));
                $user_class->points += $cash;

            } else if ($row['type'] == 2) {
                //Sell
                $db->query("UPDATE grpgusers SET credits = credits + ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $user_class->id
                ));
                $user_class->credits += $qty;
            }
        } elseif (isset($_POST['add'])) {
            $qty = security($_POST['qty']);
            $price = security($_POST['price']);
            $type = security($_POST['type']);
            $cost = $qty * $price;

            if ($qty <= 0)
                diefun("Error :: Invalid quantity.");
            if ($price > 50000 || $price <= 0)
                diefun("Error :: Invalid price.");
            if ($qty > $user_class->credits && $type == 2)
                diefun("Error :: You're tring to add more GOLD than you own.");
            if ($cost > $user_class->points && $type == 1)
                diefun("Error :: You do not have enough points. You need " . number_format($cost));
            if ($type != 1 && $type != 2) {
                diefun("Error :: Invalid type");
            }

            if ($type == 1) {
                // Buy
        
                $cost = $qty * $price;
                $db->query("UPDATE grpgusers SET `points` = `points` - ? WHERE id = ?");
                $db->execute(array(
                    $cost,
                    $user_class->id
                ));
                $db->query("INSERT INTO creditsmarket (owner, amount, price, `type`) VALUES (?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $price,
                    $type
                ));

                echo Message("You have added a buy order of " . prettynum($qty) . " credits to the market for " . prettynum($price) . " points each.");
                Send_Event2($user_class->id, "Has Added <font color=red><b>" . prettynum($qty) . "</b></font> GOLD To The Market for " . prettynum($price) . " Per Credit.", $user_class->id);
                $user_class->points -= $cost;

            } else if ($type == 2) {
                // Sell
        
                $db->query("UPDATE grpgusers SET credits = credits - ? WHERE id = ?");
                $db->execute(array(
                    $qty,
                    $user_class->id
                ));
                $db->query("INSERT INTO creditsmarket (owner, amount, price, `type`) VALUES (?, ?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $qty,
                    $price,
                    $type
                ));

                echo Message("You have added " . prettynum($qty) . " credits to the market for " . prettynum($price) . " each.");
                Send_Event2($user_class->id, "Has Added <font color=red><b>" . prettynum($qty) . "</b></font> GOLD To The Market for " . prettynum($price) . " Per Point.", $user_class->id);
                $user_class->credits -= $qty;

            }
        }
        echo '<div class="contenthead floaty">';
        echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>GOLD MARKET</h4></span>';
        echo '<h4>Create Order</h4>';
        echo '<div class="floaty" style="width:100%;">';
        echo '<form method="post">';
        echo '<table style="margin:auto;text-align:center;">';
        echo '<tr>';
        echo '<td>GOLD</td>';
        echo '<td><input type="text" name="qty" size="5" value="0" /></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Price (Max 50K): </td>';
        echo '<td><input type="text" size="5" name="price" value="0" /></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Type</td>';
        echo '<td><select name="type"><option value="1">Buy</option><option value="2">Sell</option></select></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="2"><input type="submit" name="add" value="Add GOLD to Market" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '<br>';


        echo '<div class="contenthead floaty">';
        echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Sell Orders</h4></span>';
        echo '<div class="floaty" style="width:85%;">';
        echo '<table id="newtables" style="margin:auto;width:100%;">';
        echo '<tr>';
        echo '<th>Seller</th>';
        echo '<th>GOLD</th>';
        echo '<th>Price Each (Points)</th>';
        echo '<th>Price Total (Points)</th>';
        echo '<th>Buy</th>';
        echo '</tr>';
        $db->query("SELECT * FROM creditsmarket WHERE `type` = 2 ORDER BY price ASC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . formatName($row['owner']) . '</td>';
            echo '<td>' . prettynum($row['amount']) . '</td>';
            echo '<td>' . prettynum($row['price']) . '</td>';
            echo '<td>' . prettynum($row['price'] * $row['amount']) . '</td>';
            echo '<td>';
            echo '<form method="post">';
            if ($row['owner'] == $user_class->id) {
                echo '<input type="text" size="5" name="qty" value="' . $row['amount'] . '" />';
                echo '<input type="hidden" name="remove" value="' . $row['id'] . '" />';
                echo '<input type="hidden" name="type" value="buy" />';
                echo '<input type="submit" value="Remove" />';
            } else {
                echo '<input type="text" size="5" name="qty" value="' . min(floor($user_class->points / $row['price']), $row['amount']) . '" />';
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
        echo '<br>';

        echo '<div class="contenthead floaty">';
        echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Buy Orders</h4></span>';

        echo '<div class="floaty" style="width:85%;">';
        echo '<table id="newtables" style="margin:auto;width:100%;">';
        echo '<tr>';
        echo '<th>Buyer</th>';
        echo '<th>GOLD</th>';
        echo '<th>Price Each (Points)</th>';
        echo '<th>Price Total (Points)</th>';
        echo '<th>Sell</th>';
        echo '</tr>';
        $db->query("SELECT * FROM creditsmarket WHERE `type` = 1 ORDER BY price ASC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . formatName($row['owner']) . '</td>';
            echo '<td>' . prettynum($row['amount']) . '</td>';
            echo '<td>' . prettynum($row['price']) . '</td>';
            echo '<td>' . prettynum($row['price'] * $row['amount']) . '</td>';
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