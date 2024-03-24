<?php
include("header.php");
security($_GET['id']);
$db->query("SELECT * FROM items WHERE id = ?");
$db->execute(array(
    $_GET['id']
));
if (!$db->num_rows())
    diefun("Item not found.");
$row = $db->fetch_row(true);
$howmany = Check_Loan($_GET['id'], $user_class->id);
if ($howmany == 0)
    diefun("You don't have any of those to return.");
if ($_GET['confirm'] == "true") {
    $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
    $db->execute(array(
        $_GET['id'],
        $user_class->id
    ));
    $takeid = $db->fetch_single();
    Take_Loan($takeid, $user_class->id);
    AddToArmory($_GET['id'], $user_class->gang);
    Vault_Event($user_class->gang, "[-_USERID_-] returned the {$row['itemname']} that was loaned to him.", $user_class->id);
    diefun("You have returned your {$row['itemname']}.");
}
?>
<tr>
        <td class="contentspacer"></td>
</tr>
<tr>
        <td class="contenthead">Return Item To Gang</td>
</tr>
<tr>
    <td class="contentcontent">
        Are you sure you want to return your <?php echo $row['itemname']; ?>?<br />
        <br />
        <a href="return.php?id=<?php echo $_GET['id']; ?>&confirm=true">Yes</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="inventory.php">No</a>
    </td>
</tr>
<?php
include("footer.php");
?>