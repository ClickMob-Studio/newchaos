<?php
include 'header.php';

if ($_POST['buy']) {
    $id = security($_POST['id']);
    $qty = security($_POST['qty']);
    if($qty <= 0)
        diefun("Invalid quantity entered.");
    $db->query("SELECT im.*, itemname FROM itemmarket im JOIN items i on i.id = im.itemid WHERE im.id = ?");
    $db->execute(array(
        $id
    ));
    $row = $db->fetch_row(true);

    if($qty > $row['qty'])
        diefun("Not enough items on the market.");
    $price = $row['cost'] * $qty;
    if ($row['userid'] == $user_class->id) {
        Give_Item($row['itemid'], $user_class->id, $qty);
        $db->query("UPDATE itemmarket SET qty = qty - ? WHERE id = ?");
        $db->execute(array(
            $qty,
            $id
        ));
        $db->query("DELETE FROM itemmarket WHERE qty <= 0");
        $db->execute();
        diefun("You have taken <span style='color:red;'>[x$qty]</span> " . $row['itemname'] . " off the market.");
    }
    if ($row['itemid'] == 271 || $row['itemid'] == 272 || $row['itemid'] == 278) {
        if (Check_Item($row['itemid'], $user_class->id) > 5) {
            diefun('You already have the maximum amount for this item in your inventory.');
        }
    }
    if ($price > $user_class->{$row['currency']}) {
        diefun("You don't have enough {$row['currency']}.");
    } else {
        if($row['currency'] == 'money'){
            $var1 = 'money';
            $var2 = 'bank';
        } else {
            $var1 = $var2 = 'points';
            $db->query("UPDATE grpgusers SET $var1 = $var1 - ? WHERE id = ?");
            $db->execute(array(
                $price,
                $user_class->id
            ));
            $db->query("UPDATE grpgusers SET $var2 = $var2 + ? WHERE id = ?");
            $db->execute(array(
                $price,
                $row['userid']
            ));
            $user_class->{$row['currency']} -= $price;
            $db->query("UPDATE itemmarket SET qty = qty - $qty WHERE id = $id");
            $db->execute();
            $db->query("DELETE FROM itemmarket WHERE qty <= 0");
            $db->execute();
            Give_Item($row['itemid'], $user_class->id, $qty);
            $p = ($row['currency'] == 'money') ? prettynum($price, 1) : prettynum($price) . ' points';
            Send_Event($row['userid'], "[-_USERID_-] has bought your " . $row['itemname'] . " for $p.", $user_class->id);
            diefun("You have bought a " . $row['itemname'] . "for $p.");

        }
    }
}

$db->query("SELECT im.*, itemname FROM itemmarket im JOIN items i on i.id = im.itemid WHERE im.userid = " . $user_class->id . " ORDER BY cost ASC");
$db->execute();
$yourRows = $db->fetch_row();

if (isset($_GET['itemid']) && (int)$_GET['itemid']) {
    security($_GET['itemid']);
    $db->query("SELECT im.*, itemname FROM itemmarket im JOIN items i on i.id = im.itemid WHERE im.userid <> " . $user_class->id . " AND im.itemid = " . (int)$_GET['itemid'] . " ORDER BY cost ASC");
    $db->execute();
    $rows = $db->fetch_row();
} else {
    $db->query("SELECT im.*, itemname FROM itemmarket im JOIN items i on i.id = im.itemid WHERE im.userid <> " . $user_class->id . " ORDER BY cost ASC");
    $db->execute();
    $rows = $db->fetch_row();
}

$indexedRows = array();
if (isset($_GET['itemid']) && (int)$_GET['itemid']) {
    $indexedRows = $rows;
} else {
    foreach ($rows as $row) {
        if (!isset($indexedRows[$row['itemid']])) {
            $indexedRows[$row['itemid']] = $row;
            $indexedRows[$row['itemid']]['count'] = 1;
        } else {
            $indexedRows[$row['itemid']]['count']++;
        }
    }

}

$db->query('SELECT * FROM items ORDER BY itemname');
$db->execute();
$items = $db->fetch_row();
?>


<div class='box_top'>Item Market</div>
<div class='box_middle'>
    <div class='pad'>
        <div class="floaty">
            <hr />
            <p>
                Filter:
                <form method="GET">
                    <select name="itemid">
                        <option value="">--- Item --></option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?php echo $item['id'] ?>"><?php echo $item['itemname'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </p>
            <hr />


            <?php if (isset($_GET['itemid']) && (int)$_GET['itemid']): ?>
                <p><a href="itemmarketv2.php">Back to all listings</a></p>
            <?php endif; ?>
            <table id="newtables" style="width:100%;">
                <tr>
                    <th>Seller</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Buy</th>
                </tr>
                <?php foreach ($indexedRows as $row): ?>
                    <?php
                    $submittext = ($row['userid'] == $user_class->id) ? "Remove" : "Buy";
                    if ($row['currency'] == 'money') {
                        $currency = prettynum($row['cost'], 1);
                    } else {
                        $currency = prettynum($row['cost']) . ' points';
                    }
                    ?>

                    <tr>
                        <td><?php echo formatName($row['userid']) ?></td>
                        <td>
                            <?php echo $row['itemname'] ?> <span style="color:red;">[x<?php echo $row['qty'] ?>] <br />
                            <?php if ($row['count'] > 1): ?>
                                <a style="color: red;" href="itemmarketv2.php?itemid=<?php echo $row['itemid'] ?>">See Other Listings</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $currency ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                <input type="text" size="5" name="qty" value="<?php echo min(floor(($row['currency'] == 'money' ? $user_class->money : $user_class->points) / $row['cost']), $row['qty']) ?>">
                                <input type="submit" name="buy" value="<?php echo $submittext ?>">
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<div class='box_top'>Your Listings</div>
<div class='box_middle'>
    <div class='pad'>
        <div class="floaty">
            <table id="newtables" style="width:100%;">
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>&nbsp;``</th>
                </tr>
                <?php foreach ($yourRows as $yourRow): ?>
                    <?php
                    $submittext = ($yourRow['userid'] == $user_class->id) ? "Remove" : "Buy";
                    if ($yourRow['currency'] == 'money') {
                        $currency = prettynum($yourRow['cost'], 1);
                    } else {
                        $currency = prettynum($yourRow['cost']) . ' points';
                    }
                    ?>

                    <tr>
                        <td><?php echo $yourRow['itemname'] ?> <span style="color:red;">[x<?php echo $yourRow['qty'] ?>] </td>
                        <td><?php echo $currency ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id" value="<?php echo $yourRow['id'] ?>">
                                <input type="text" size="5" name="qty" value="<?php echo min(floor(($yourRow['currency'] == 'money' ? $user_class->money : $user_class->points) / $yourRow['cost']), $yourRow['qty']) ?>">
                                <input type="submit" name="buy" value="<?php echo $submittext ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>