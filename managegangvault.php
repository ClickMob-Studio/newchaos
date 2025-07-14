<?php
include 'header.php';
?>
<div class='box_top'>Manage Gang Vault</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if (!$user_class->gang) {
            diefun("You are not in a gang.");
        }

        if ($user_class->id == 1) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
        $gang_class = new Gang($user_class->gang);
        $members = '';
        $db->query("SELECT * FROM grpgusers WHERE gang = ? ORDER BY exp DESC");
        $db->execute(array(
            $user_class->gang
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $row)
            $members .= "<option value='{$row['id']}'>{$row['username']}</option>";
        $user_rank = new GangRank($user_class->grank);
        if ($user_rank->vault != 1 && $user_class->admin != 1)
            diefun("You don't have permission to be here!");
        if (isset($_POST['withdraw1'])) {
            security($_POST['amount']);
            security($_POST['type']);
            $amount = $_POST['amount'];
            $type = $_POST['type'];
            if ($amount < 1)
                echo Message("Please enter a valid amount.");
            elseif (empty($_POST['withdrawid']))
                echo Message("You can't send money to no-one!");
            elseif ($amount > $gang_class->moneyvault)
                echo Message("You don't have that much money in the vault.");
            elseif ($type != 1 && $type != 2)
                echo Message("Invalid transfer type");
            else {
                $send_class = new User($_POST['withdrawid']);

                $gang_class->moneyvault -= $amount;

                // Hand
                if ($type == 1) {
                    // $send_class->money += $amount;
                    // $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                    // $db->execute(array(
                    //     $send_class->money,
                    //     $send_class->id
                    // ));
        
                    $db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
                    $db->execute(array(
                        $amount,
                        $send_class->id
                    ));

                    Vault_Event($gang_class->id, prettynum($amount, 1) . " was sent to [-_USERID_-].", $send_class->id);
                    echo Message(prettynum($amount, 1) . " was sent to $send_class->formattedname.");
                    Send_Event($send_class->id, "Your gang sent you " . prettynum($amount, 1), $send_class->id);

                } else if ($type == 2) {
                    // To Bank 2% Fee
                    $fee = $amount * 0.02;
                    $amount = $amount * 0.98;
                    $send_class->bank += $amount;
                    // $db->query("UPDATE grpgusers SET bank = ? WHERE id = ?");
                    // $db->execute(array(
                    //     $send_class->bank,
                    //     $send_class->id
                    // ));
        
                    $db->query("UPDATE grpgusers SET bank = bank + ? WHERE id = ?");
                    $db->execute(array(
                        $amount,
                        $send_class->id
                    ));

                    Vault_Event($gang_class->id, prettynum($amount, 1) . " was sent to [-_USERID_-] bank. Fee: " . prettynum($fee, 1), $send_class->id);
                    echo Message(prettynum($amount, 1) . " was sent to $send_class->formattedname's bank. Fee: " . prettynum($fee, 1));
                    Send_Event($send_class->id, "Your gang sent you " . prettynum($amount, 1) . ' to your bank', $send_class->id);
                }

                $db->query("UPDATE gangs SET moneyvault = moneyvault - ? WHERE id = ?");
                $db->execute(array(
                    $amount,
                    $gang_class->id
                ));
            }
        }
        if (isset($_POST['withdraw2'])) {
            security($_POST['amount']);
            $amount = $_POST['amount'];
            if ($amount < 1)
                echo Message("Please enter a valid amount.");
            elseif (empty($_POST['withdrawid']))
                echo Message("You can't send Points to no-one!");
            elseif ($amount > $gang_class->pointsvault)
                echo Message("You don't have that many Points in the vault.");
            else {
                $send_class = new User($_POST['withdrawid']);
                $gang_class->pointsvault -= $amount;
                $send_class->points += $amount;
                // $db->query("UPDATE grpgusers SET points = ? WHERE id = ?");
                // $db->execute(array(
                //     $send_class->points,
                //     $send_class->id
                // ));
                // $db->query("UPDATE gangs SET pointsvault = ? WHERE id = ?");
                // $db->execute(array(
                //     $gang_class->pointsvault,
                //     $gang_class->id
                // ));
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $amount,
                    $send_class->id
                ));
                $db->query("UPDATE gangs SET pointsvault = pointsvault - ? WHERE id = ?");
                $db->execute(array(
                    $amount,
                    $gang_class->id
                ));
                Vault_Event($gang_class->id, prettynum($amount) . " points were sent to [-_USERID_-].", $send_class->id);
                echo Message(prettynum($amount) . " points were sent to $send_class->formattedname.");
                Send_Event($send_class->id, "Your gang sent you " . prettynum($amount) . " points.", $send_class->id);
            }
        }
        if (isset($_POST['withdraw3'])) {
            security($_POST['amount']);
            security($_POST['itemid']);
            $amount = $_POST['amount'];
            $item = $_POST['itemid'];
            $db->query("SELECT itemname FROM items WHERE id = ?");
            $db->execute(array(
                $item
            ));
            $itemname = $db->fetch_single();
            $db->query("SELECT quantity FROM gangarmory WHERE gangid = ? AND itemid = ?");
            $db->execute(array(
                $user_class->gang,
                $item
            ));
            $howmany = $db->fetch_single();
            if ($amount < 1)
                echo Message("Please enter a valid amount.");
            elseif ($howmany < $amount)
                echo Message("You don't have that many $itemname's in the vault.");
            elseif (empty($_POST['withdrawid']))
                echo Message("You can't send items to no-one!");
            else {
                $send_class = new User($_POST['withdrawid']);
                TakeFromArmory($item, $user_class->gang, $amount);
                Give_Item($item, $send_class->id, $amount);
                Vault_Event($gang_class->id, "$amount $itemname were sent to [-_USERID_-].", $send_class->id);
                echo Message("$amount $itemname's were sent to $send_class->formattedname.");
                Send_Event($send_class->id, "Your gang sent you $amount $itemname.", $send_class->id);
            }
        }
        if (isset($_POST['withdraw4'])) {
            security($_POST['itemid']); // Assuming `security` sanitizes input.
            $item = $_POST['itemid'];
            // Fetch the item name, ensuring you're using prepared statements correctly.
            $db->query("SELECT itemname FROM items WHERE id = ?");
            $db->execute(array($item));
            $itemname = $db->fetch_single();

            // Fetch the item quantity from the gang armory.
            $db->query("SELECT quantity FROM gangarmory WHERE gangid = ? AND itemid = ?");
            $db->execute(array($user_class->gang, $item));
            $quantity = $db->fetch_single();

            if ($quantity < 1) {
                echo Message("You don't have any of this item in the gang Armory.");
            } elseif (empty($_POST['withdrawid'])) {
                echo Message("You can't send items to no-one!");
            } else {
                $newQuantity = $quantity - 1; // Decrement the quantity by one.
        
                // If newQuantity is 0, consider deleting the row to avoid zero quantity rows.
                if ($newQuantity <= 0) {
                    $db->query("DELETE FROM gangarmory WHERE gangid = ? AND itemid = ?");
                    $db->execute(array($user_class->gang, $item));
                } else {
                    // Update the quantity in the gang armory.
                    $db->query("UPDATE gangarmory SET quantity = ? WHERE gangid = ? AND itemid = ?");
                    $db->execute(array($newQuantity, $user_class->gang, $item));
                }

                $send_class = new User($_POST['withdrawid']);

                // Proceed with loaning the item.
                Loan_Item($user_class->gang, $item, $send_class->id);

                // Log the event.
                Vault_Event($gang_class->id, "$itemname was loaned to [-_USERID_-].", $send_class->id);
                echo Message("$itemname was loaned to $send_class->formattedname.");
                Send_Event($send_class->id, "Your gang loaned you a $itemname.", $send_class->id);
            }
        }


        if (isset($_POST['ret'])) {
            $userid = security($_POST['user']);
            if (in_array($_POST['ret'], array('eqweapon', 'eqarmor', 'eqshoes'))) {
                switch ($_POST['ret']) {
                    case 'eqweapon':
                        $which = 'weploaned';
                        break;
                    case 'eqarmor':
                        $which = 'armloaned';
                        break;
                    case 'eqshoes':
                        $which = 'shoeloaned';
                        break;
                }
                $db->query("SELECT {$_POST['ret']} FROM grpgusers WHERE {$_POST['ret']} <> 0 AND $which <> 0 AND id = ? AND gang = ?");
                $db->execute(array(
                    $userid,
                    $user_class->gang
                ));
                $toret = $db->fetch_single();
                if (empty($toret))
                    diefun("Error, item not found.");
                $db->query("UPDATE grpgusers SET {$_POST['ret']} = 0, $which = 0 WHERE id = ? AND gang = ?");
                $db->execute(array(
                    $userid,
                    $user_class->gang
                ));

                $item = Get_Item($toret);
                $itemname = $item['itemname'];
                Send_Event($_POST['user'], "Your gang took their $itemname back from you.");
                Vault_Event($gang_class->id, "$itemname was taken from [-_USERID_-].", $_POST['user']);
                AddToArmory($toret, $user_class->gang);
            } else {
                security($_POST['ret']);
                $db->query("SELECT *, i.id AS itemid FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE gl.id = ? AND idto = ?");
                $db->execute(array(
                    $_POST['ret'],
                    $_POST['user']
                ));
                if (!$db->num_rows())
                    diefun("This loan does not exist.");
                $row = $db->fetch_row(true);
                if ($row['gang'] != $user_class->gang)
                    diefun("This item does not belong to your gang.");
                if ($row['quantity'] <= 0)
                    diefun("This loan does not exist.");
                $itemname = $row['itemname'];
                Send_Event($_POST['user'], "Your gang took their $itemname back from you.");
                Vault_Event($gang_class->id, "$itemname was taken from [-_USERID_-].", $_POST['user']);
                Take_Loan($_POST['ret'], $_POST['user']);

                echo Message("$itemname was taken from " . formatName($_POST['user']) . ".");

                AddToArmory($row['itemid'], $user_class->gang);

            }
        }
        $db->query("SELECT * FROM gangarmory g JOIN items i ON g.itemid = i.id WHERE gangid = ? ORDER BY itemid DESC");
        $db->execute(array(
            $user_class->gang
        ));

        $items = $loans = "";
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            $items .= "<option value='{$row['itemid']}'>{$row['itemname']} [x{$row['quantity']}]</option>";
            if ($row['offense'] > 0 || $row['defense'] > 0 || $row['speed'] > 0)
                $loans .= "<option value='{$row['itemid']}'>{$row['itemname']} [x{$row['quantity']}]</option>";
        }
        print "
        <tr><td class='contentspacer'></td></tr><tr><td class='contentcontent'>
            <table width='100%'>
                <tr>
                    <td>
                        <form method='post'>
                            <table id='newtables' style='width:100%;'>
                                <tr>
                                    <th colspan='2'>Send Money</th>
                                </tr>
                                <tr>
                                    <th>Person:</th>
                                    <td>
                                        <select name='withdrawid'>
                                            <option value=''></option>
                                            $members
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td><input type='text' name='amount' size='20' value='$gang_class->moneyvault' /></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td><select name='type'><option value=1>Hand</option><option value=2>Bank</option></select></td>
                                </tr>
                                <tr>
                                    <td colspan='2'>Bank transfer has a 2% fee</td>
                                </tr>
                                <tr>
                                    <td colspan='2'><input type='submit' name='withdraw1' value='Send Money' /></td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td>
                        <form method='post'>
                            <table id='newtables' style='width:100%;'>
                                <tr>
                                    <th colspan='2'>Send Points</th>
                                </tr>
                                <tr>
                                    <th>Person:</th>
                                    <td>
                                        <select name='withdrawid'>
                                            <option value=''></option>
                                            $members
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td><input type='text' name='amount' size='20' value='$gang_class->pointsvault' /></td>
                                </tr>
                                <tr>
                                    <td colspan='2'><input type='submit' name='withdraw2' value='Send Points' /></td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form method='post'>
                            <table id='newtables' style='width:100%;'>
                                <tr>
                                    <th colspan='2'>Send Items</th>
                                </tr>
                                <tr>
                                    <th>Person:</th>
                                    <td>
                                        <select name='withdrawid'>
                                            <option value=''></option>
                                            $members
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Item:</th>
                                    <td>
                                        <select name='itemid'>
                                            <option value=''></option>
                                            $items
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><b>Amount:</b></th>
                                    <td><input type='text' name='amount' size='20' value='1' /></td>
                                </tr>
                                <tr>
                                    <td colspan='2'><input type='submit' name='withdraw3' value='Send Item(s)' /></td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td>
                        <form method='post'>
                            <table id='newtables' style='width:100%;'>
                                <tr>
                                    <th colspan='2'>Loan Items</th>
                                </tr>
                                <tr>
                                    <th>Person:</th>
                                    <td>
                                        <select name='withdrawid'>
                                            <option value=''></option>
                                            $members
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Item:</td>
                                    <td>
                                        <select name='itemid'>
                                            <option value=''></option>
                                            $loans
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><input type='submit' name='withdraw4' value='Loan Item(s)' /></td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
        </td>
    </tr>";
        $db->query("SELECT gl.id, itemname, idto FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE gang = ?");
        $db->execute(array(
            $user_class->gang
        ));
        $unloans = $db->num_rows();
        $unitems = $db->fetch_row();
        $db->query("SELECT id, eqweapon, eqarmor, eqshoes, weploaned, armloaned, shoeloaned FROM grpgusers WHERE (weploaned > 0 OR armloaned > 0 OR shoeloaned > 0) AND gang = ?");
        $db->execute(array(
            $user_class->gang
        ));
        $eqloans = $db->num_rows();
        $eqitems = $db->fetch_row();
        if ($unloans || $eqloans) {
            echo '
    <tr><td class="contentspacer"></td></tr>
        <tr><td class="contentcontent">
            <table id="newtables" style="width:100%;">
                <tr>
                    <th colspan="3">Loaned Items</th>
                </tr>
                <tr>
                    <th>Item Name</td>
                    <th>Loaned To</td>
                    <th>Action</td>
                </tr>';
            foreach ($unitems as $item)
                echo "
                <tr>
                    <td>{$item['itemname']}</td>
                    <td>" . formatName($item['idto']) . "</td>
                    <td>
                        <form method='post'>
                            <input type='hidden' name='ret' value='{$item['id']}' />
                            <input type='hidden' name='user' value='{$item['idto']}' />
                            <input type='submit' value='Retrieve' />
                        </form>
                    </td>
                </tr>";
            foreach ($eqitems as $item) {
                $items = array();
                if ($item['weploaned']) {
                    $db->query("SELECT itemname FROM items WHERE id = ?");
                    $db->execute(array(
                        $item['eqweapon']
                    ));
                    $items[] = array('eqweapon', $db->fetch_single());
                }
                if ($item['armloaned']) {
                    $db->query("SELECT itemname FROM items WHERE id = ?");
                    $db->execute(array(
                        $item['eqarmor']
                    ));
                    $items[] = array('eqarmor', $db->fetch_single());
                }
                if ($item['shoeloaned']) {
                    $db->query("SELECT itemname FROM items WHERE id = ?");
                    $db->execute(array(
                        $item['eqshoes']
                    ));
                    $items[] = array('eqshoes', $db->fetch_single());
                }
                foreach ($items as $show) {
                    echo "
                    <tr>
                        <td>{$show[1]} - <span style='color:green;text-decoration:italic;'>Equipped</span></td>
                        <td>" . formatName($item['id']) . "</td>
                        <td>
                            <form method='post'>
                                <input type='hidden' name='ret' value='{$show[0]}' />
                                <input type='hidden' name='user' value='{$item['id']}' />
                                <input type='submit' value='Retrieve' />
                            </form>
                        </td>
                    </tr>";
                }
            }
            echo '
            </table>
        </td>
    </tr>';
        }
        include("gangheaders.php");
        include 'footer.php';
        ?>