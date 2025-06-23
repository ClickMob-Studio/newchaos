<?php
require "header.php";
if (isset($_GET['ret'])) {
    $userid = $user_class->id;
    if (in_array($_GET['ret'], array('eqweapon', 'eqarmor', 'eqshoes'))) {
        switch ($_GET['ret']) {
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
        $db->query("SELECT {$_GET['ret']} FROM grpgusers WHERE {$_GET['ret']} <> 0 AND $which <> 0 AND id = ? AND gang = ?");
        $db->execute(array(
            $userid,
            $user_class->gang
        ));
        $toret = $db->fetch_single();
        if (empty($toret))
            diefun("Error, item not found.");
        $db->query("UPDATE grpgusers SET {$_GET['ret']} = 0, $which = 0 WHERE id = ? AND gang = ?");
        $db->execute(array(
            $userid,
            $user_class->gang
        ));

        $item = Get_Item($toret);
        $itemname = $item['itemname'];
        Send_Event($user_class->id, "You sent your gang their $itemname back.");
        Vault_Event($user_class->gang, "$itemname was given back from [-_USERID_-].", $user_class->id);
        AddToArmory($toret, $user_class->gang);
    } else {
        security($_GET['ret']);
        $db->query("SELECT *, i.id AS itemid FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE gl.id = ? AND idto = ?");
        $db->execute(array(
            $_GET['ret'],
            $user_class->id
        ));
        if (!$db->num_rows())
            diefun("This loan does not exist.");
        $row = $db->fetch_row(true);
        if ($row['gang'] != $user_class->gang)
            diefun("This item does not belong to your gang.");
        if ($row['quantity'] <= 0)
            diefun("This loan does not exist.");
        $itemname = $row['itemname'];
        Send_Event($user_class->id, "You sent your gang their $itemname back.");
        Vault_Event($user_class->gang, "$itemname was given back from [-_USERID_-].", $user_class->id);
        Take_Loan($_GET['ret'], $user_class->id);

        echo Message("$itemname was given back to your gang.");

        AddToArmory($row['itemid'], $user_class->gang);

    }
}

require "footer.php";