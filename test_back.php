<?php
include 'header.php';
?>
<div class='box_top'>Gang Vault</div>
						<div class='box_middle'>
							<div class='pad'>
<?php
$gang_class = new Gang($user_class->gang);
if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
    if (isset($_POST['deposit'])) {
        security($_POST['damount']);
        $amount = $_POST['damount'];
        if ($amount > $user_class->money)
            echo Message("You do not have that much money.");
        elseif ($amount < 1)
            echo Message("Please enter a valid amount.");
        else {
            echo Message("You have donated $" . prettynum($amount) . " to your gang.");
            $gang_class->moneyvault += $amount;
            $user_class->money -= $amount;
            mysql_query("UPDATE grpgusers SET money = $user_class->money WHERE id = $user_class->id");
            mysql_query("UPDATE gangs SET moneyvault = $gang_class->moneyvault WHERE id = $gang_class->id");

            mysql_query("INSERT INTO gang_vault_log (gang_id, user_id, type, added, balance) VALUES (" . $gang_class->id . ", " . $user_class->id . ", 'money', " . $amount . ", " . $gang_class->moneyvault . ")");

            Vault_Event($gang_class->id, "[-_USERID_-] donated " . prettynum($amount, 1) . " to the gang.", $user_class->id);
        }
    }
    if (isset($_POST['deposit2'])) {
        security($_POST['damount']);
        $amount = $_POST['damount'];
        if ($amount > $user_class->points)
            echo Message("You do not have that many points.");
        elseif ($amount < 1)
            echo Message("Please enter a valid amount.");
        else {
            echo Message("You have donated " . prettynum($amount) . " points to your gang.");
            $gang_class->pointsvault += $amount;
            $user_class->points -= $amount;
            mysql_query("UPDATE grpgusers SET points = $user_class->points WHERE id = $user_class->id");
            mysql_query("UPDATE gangs SET pointsvault = $gang_class->pointsvault WHERE id = $gang_class->id");

            mysql_query("INSERT INTO gang_vault_log (gang_id, user_id, type, added, balance) VALUES (" . $gang_class->id . ", " . $user_class->id . ", 'points', " . $amount . ", " . $gang_class->pointsvault . ")");

            Vault_Event($gang_class->id, "[-_USERID_-] donated " . prettynum($amount) . " points to the gang.", $user_class->id);
        }
    }
    if (isset($_POST['submit'])) {
        if (empty($_POST['armoury']))
            diefun("You need to pick an item to donate.<br/><br/><a href='gangvault.php'>Go Back</a>");
        $qty = (int) $_POST['qty'];

        $qty = floor($qty);
        security($qty);
        $howmany = Check_Item($_POST['armoury'], $user_class->id);
        $result2 = mysql_query("SELECT * FROM items WHERE id = {$_POST['armoury']}");
        $worked = mysql_fetch_array($result2);
        if ($howmany < $qty)
            diefun("You don't have enough of those.");
        AddToArmory($_POST['armoury'], $user_class->gang, $qty);
        Take_Item($_POST['armoury'], $user_class->id, $qty);
        echo Message("You have donated [x$qty] " . $worked['itemname'] . " to your gang.");
        Vault_Event($gang_class->id, "[-_USERID_-] donated ".$qty." x " . $worked['itemname'] . " to the gang.", $user_class->id);
    }
    print "
        Welcome to the gang vault. Here you can store cash, points and items!<br /><br />
        <table id='newtables' style='width:100%;table-layout:fixed;'>
            <tr>
                <th>Money:</th><td>" . prettynum($gang_class->moneyvault, 1) . "</td>
                <th>Points:</th><td>" . prettynum($gang_class->pointsvault) . "</td>
            </tr>
            <tr>
                <td colspan='2'>
                    <form method='post'>
                        <input type='text' name='damount' value='$user_class->money' size='10' maxlength='20'>
                        <input type='submit' name='deposit' value='Donate Money'>
                    </form>
                </td>
                <td colspan='2'>
                    <form method='post'>
                        <input type='text' name='damount' value='$user_class->points' size='10' maxlength='20'>
                        <input type='submit' name='deposit2' value='Donate Points'>
                    </form>
                </td>
            </tr>
        </table>
        <br />
        <br />
        <center><h1>Gang Armoury</h1></center>
";
$result = mysql_query("
SELECT a.quantity, i.id, i.itemname, i.offense, i.defense, i.speed, i.rare, i.type
FROM gangarmory a
JOIN items i ON a.itemid = i.id
WHERE a.gangid = {$user_class->gang}
ORDER BY a.quantity DESC
");
        $$items_by_category = ['weapon' => [], 'armor' => [], 'shoes' => [], 'rare' => [], 'booster' => [], 'gems' => [], 'consumable' => []];

        while ($row = mysql_fetch_array($result)) {
            $type = 'consumable'; 
            $subtype = '';
        
      
            if ($row['type'] == 'booster') {
                $type = 'booster';
            } elseif ($row['type'] == 'Gems') {
                $type = 'gems';
            } else {
            
                if ($row['offense'] > 0 && ($row['defense'] > 0 || $row['speed'] > 0)) {
                    if ($row['offense'] > $row['defense']) {
                        if ($row['offense'] > $row['speed']) {
                            $type = 'weapon';
                        } else {
                            $type = 'shoes';
                        }
                    } else if ($row['defense'] > $row['speed']) {
                        $type = 'armor';
                    } else {
                        $type = 'shoes';
                    }
                } else {
                    if ($row['offense'] > 0 && $row['rare'] == 0)
                        $type = 'weapon';
                    elseif ($row['defense'] > 0 && $row['rare'] == 0)
                        $type = 'armor';
                    elseif ($row['speed'] > 0 && $row['rare'] == 0)
                        $type = 'shoes';
                    elseif ($row['rare'] == 1) {
                        $type = 'rare';
                        if ($row['offense']) $subtype = 'weapon';
                        if ($row['defense']) $subtype = 'armor';
                        if ($row['speed']) $subtype = 'shoes';
                    }
                }
            }
        
            
            $items_by_category[$type][] = [
                'name' => $row['itemname'],
                'id' => $row['id'],
                'quantity' => $row['quantity'],
                'subtype' => $subtype
            ];
        }
        
        
        echo '<div class="container mt-4">';
        echo '<div class="row row-cols-2 row-cols-md-2 row-cols-lg-3 g-4">'; 
        
        foreach ($items_by_category as $category => $items) {
            echo '<div class="col">';
            echo '<div class="card h-100 text-white" style="background-color: #292929;">'; 
            echo '<div class="card-header" style="background:#000;"><h5 class="card-title">' . ucfirst($category) . '</h5></div>';
            echo '<div class="card-body">';
        
            foreach ($items as $item) {
                echo "<p>{$item['name']} x {$item['quantity']}</p>";
            }
            echo '</div>'; 
            echo '</div>'; 
            echo '</div>'; 
        }
        echo '<div class="col">';
echo '<div class="card h-100 text-white" style="background-color: #292929;">'; 
echo '<div class="card-header" text-white" style="background-color: #000;"><h5 class="card-title">Donate an Item</h5></div>';
echo '<div class="card-body">';
echo '<form method="post">';
echo '<select class="form-select" name="armoury">';
echo '<option value=""></option>';
$result = mysql_query("SELECT * FROM inventory WHERE userid = {$user_class->id} ORDER BY quantity DESC");
while ($rank = mysql_fetch_array($result)) {
    $result2 = mysql_query("SELECT * FROM items WHERE id='" . $rank['itemid'] . "'");
    $worked = mysql_fetch_array($result2);
    echo "<option value='{$rank['itemid']}'>{$worked['itemname']} [x{$rank['quantity']}]</option>";
}
echo '</select>';
echo '<div class="input-group mb-3">';
echo '<input type="text" class="form-control" placeholder="QTY" aria-label="Quantity" name="qty" pattern="[0-9]*" title="Please enter whole numbers only" required>';
echo '<button class="btn btn-primary" type="submit" name="submit">Donate Item</button>';
echo '</div>';
echo '</form>';
echo '</div>'; 
echo '</div>'; 
echo '</div>'; 

echo '</div>'; 
echo '</div>'; 
} else {
    echo Message("You aren't in a gang.");
}
include("gangheaders.php");
include 'footer.php';
?>
