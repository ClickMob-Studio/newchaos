<?php
include 'header.php';?>
<div class='box_top'>Send Item</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
if (!isset($_GET['id']))
    error("You haven't picked an item.");

if ($_GET['id'] == 155)
    header('location: inventory.php?use=155');
$howmany = Check_Item($_GET['id'], $user_class->id);
$result3 = mysql_query("SELECT * FROM grpgusers WHERE id = {$_POST['theirid']}");
$userexist = mysql_num_rows($result3);
$result2 = mysql_query("SELECT * FROM items WHERE id = {$_GET['id']}");
$worked = mysql_fetch_array($result2);
if ($worked['itemname'] == "")
    error("That isn't a real item.");
if (isset($_POST['submit'])) {
    security($_POST['amnt'], 'num');
    security($_POST['theirid'], 'num');
    $error = ($howmany == 0) ? "You don't have any of those." : $error;
    $error = ($howmany < $_POST['amnt']) ? "You don't have enough of those." : $error;
    $error = ($_POST['amnt'] == 0) ? "You have to send at least 1." : $error;
    $error = ($userexist == 0) ? "That User ID is invalid." : $error;

    if ($_GET['id'] == 271 || $_GET['id'] == 272 || $_GET['id'] == 278 || $_GET['id'] == 320 || $_GET['id'] == 321) {
        if (Check_Item($_GET['id'], $_POST['theirid']) > 5) {
            $error = 'The player your sending this item too already has the maximum amount of 5 in their inventory.';
        }
    }

    if ($_GET['id'] == 287 || $_GET['id'] == 293) {
        if (Check_Item($_GET['id'], $_POST['theirid']) > 10) {
            $error = 'The player your sending this item too already has the maximum amount of 10 in their inventory.';
        }
    }

    if (isset($error))
        error($error);
    Give_Item($_GET['id'], $_POST['theirid'], $_POST['amnt']);
    Take_Item($_GET['id'], $user_class->id, $_POST['amnt']);
    $person = new User($_POST['theirid']);
    mysql_query("INSERT INTO transferlog (toip, fromip, timestamp, `to`, `from`, item)" . "VALUES ($person->ip, $user_class->ip, unix_timestamp(), $person->id, $user_class->id, {$_GET['id']})");
    echo Message("You have sent [x{$_POST['amnt']}] {$worked['itemname']} to $person->formattedname.");
    Send_Event($person->id, "[-_USERID_-] has sent you [x{$_POST['amnt']}] {$worked['itemname']}.", $user_class->id);
}

print '
<p>Here you can send items to other users, you can only send items once you reach level 5</p>
<tr><td class="contentspacer"></td></tr>
<tr><td class="contentcontent">';
if ($user_class->level <= 4)
    print '<center><font size="3px"><font color=lime>You must be at least level 5 to send items</font><br></center>';
if ($user_class->level > 4) {
    print "
    <b>You are sending a {$worked['itemname']}.</b><br /><br />
    <form method='post' action='senditem.php?id={$_GET['id']}'>
        <table>
            <tr>
                <td width='35%'>Send To:</td>
                <td width='35%'><input name='theirid' type='text' size='18' value='{$_GET['person']}'> [ID]</td>
            </tr>
            <tr>
                <td width='35%'># of items:</td>
                <td width='35%'><input name='amnt' type='text' size='18' value='1'> [MAX: $howmany]</td>
            </tr>
            <tr>
                <td colspan='2'><input type='submit' name='submit' value='Send Item'></td>
            </tr>
        </table>
    </form>
    ";
}
print "</td></tr>"; // Close the table

require 'footer.php';
function error($msg) {
    echo Message($msg);
    include 'footer.php';
    die();
}
?>
