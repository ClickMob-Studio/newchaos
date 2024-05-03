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
        // Round down any decimal values to the nearest integer
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
        Vault_Event($gang_class->id, "[-_USERID_-] donated a " . $worked['itemname'] . " to the gang.", $user_class->id);
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
        <center><b>Gang Armoury</b></center>
        <table id='newtables' style='table-layout:fixed;'>
            <tr>
                <th>Item Name</th>
                <th>Amount</th>
            </tr>";
    $result = mysql_query("SELECT * FROM gangarmory WHERE gangid = $user_class->gang ORDER BY quantity DESC");
    while ($line = mysql_fetch_array($result)) {
        $worked = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE id = {$line['itemid']}"));
        echo "
        <tr>
            <td width='50%'>" . item_popup($worked['itemname'], $worked['id']) . "</td>
            <td width='20%'>" . prettynum($line['quantity']) . "</td>
        </tr>
        ";
    }
    print "</table>";
    $result = mysql_query("SELECT * FROM inventory WHERE userid = $user_class->id ORDER BY quantity DESC");
    echo " 
    <form method='post'>
        <select name='armoury'>
            <option value=''></option>
        ";
    while ($rank = mysql_fetch_array($result)) {
        $result2 = mysql_query("SELECT * FROM items WHERE id='" . $rank['itemid'] . "'");
        $worked = mysql_fetch_array($result2);
        echo "
            <option value='{$rank['itemid']}'>{$worked['itemname']} [x{$rank['quantity']}]</option>
    	";
    }
    echo "
        </select> x<input type='text' size='4' placeholder='QTY' name='qty' pattern='[0-9]*' title='Please enter whole numbers only' required /> <input type='submit' name='submit' value='Donate Item' />
    </form>
        ";
    ?>
    </td></tr>
    <?php
    echo "<td><tr>";
} else {
    echo Message("You aren't in a gang.");
}
include("gangheaders.php");
include 'footer.php';
?>
