<?php 
require "header.php";
if (isset($_GET['ret'])) {
    $userid = $user_class->id;
    if (in_array($_GET['ret'], array('eqweapon', 'eqarmor', 'eqshoes'))) {
        switch($_GET['ret']){
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
        if(empty($toret))
            diefun("Error, item not found.");
        $db->query("UPDATE grpgusers SET {$_GET['ret']} = 0, $which = 0 WHERE id = ? AND gang = ?");
        $db->execute(array(
            $userid,
            $user_class->gang
        ));
        $it = mysql_query("SELECT * FROM items WHERE `id` = ".$toret);
        $tiem = mysql_fetch_assoc($it);
        $itemname = $tiem['itemname'];
        Send_Event($_GET['user'], "Your gang took their $itemname back from you.");
        Vault_Event($gang_class->id, "$itemname was taken from [-_USERID_-].", $_GET['user']);
        AddToArmory($toret, $user_class->gang);
    } else {
        security($_GET['ret']);
        $db->query("SELECT *, i.id AS itemid FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE gl.id = ? AND idto = ?");
        $db->execute(array(
            $_GET['ret'],
            $_GET['user']
        ));
        if (!$db->num_rows())
            diefun("This loan does not exist.");
        $row = $db->fetch_row(true);
        if ($row['gang'] != $user_class->gang)
            diefun("This item does not belong to your gang.");
        if ($row['quantity'] <= 0)
            diefun("This loan does not exist.");
        $itemname = $row['itemname'];
        Send_Event($_GET['user'], "Your gang took their $itemname back from you.");
        Vault_Event($gang_class->id, "$itemname was taken from [-_USERID_-].", $_GET['user']);
        Take_Loan($_GET['ret'], $_GET['user']);
    
        echo Message("$itemname was taken from " . formatName($_GET['user']) . ".");
        
        AddToArmory($row['itemid'], $user_class->gang);

    }
}