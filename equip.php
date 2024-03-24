<?php
include 'header.php';

$error = "";
if ($_GET['loaned'] == 1) {
    if ($_GET['id'] == "")
        diefun("No item picked.");
    $howmany = Check_Loan($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array(
        $_GET['id']
    ));
    if(!$db->num_rows())
        diefun("Item not found.");
    $row = $db->fetch_row(true);
    $error = ($howmany <= 0) ? "You don't have any of those." : $error;
    $error = ($row['level'] > $user_class->level) ? "You aren't high enough level to use this." : $error;
    if (!empty($error))
        diefun($error);
    if ($_GET['eq'] == "weapon") {
        if($row['offense'] <= 0)
            diefun("This item is not a weapon.");
        if ($user_class->eqweapon != 0){
        print "1";
            if($user_class->weploaned == 1)
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
            else
                Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = ?, `weploaned` = 1 WHERE id = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $takeid = $db->fetch_single();
        Take_Loan($takeid, $user_class->id);
        header("Location: inventory.php");
    }
    if ($_GET['eq'] == "armor") {
        if($row['defense'] <= 0)
            diefun("This item is not an armor.");
        if ($user_class->eqarmor != 0){
            if($user_class->armloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
            else
                Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = ?, `armloaned` = 1 WHERE id = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $takeid = $db->fetch_single();
        Take_Loan($takeid, $user_class->id);
        header("Location: inventory.php");
    }
    if ($_GET['eq'] == "shoes") {
        if($row['speed'] <= 0)
            diefun("This item is not a shoe.");
        if ($user_class->eqshoes != 0){
            if($user_class->shoeloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
            else
                Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = ?, `shoeloaned` = 1 WHERE id = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $takeid = $db->fetch_single();
        Take_Loan($takeid, $user_class->id);
        header("Location: inventory.php");
    }
} else {
    if ($_GET['unequip'] == "weapon" && $user_class->eqweapon != 0) {
        if ($user_class->weploaned == 1)
            Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
        else
            Give_Item($user_class->eqweapon, $user_class->id);
        $db->query("UPDATE grpgusers SET eqweapon = 0, `weploaned` = 0 WHERE id = ?");
        $db->execute(array(
           $user_class->id 
        ));
        header("Location: inventory.php");
    }
    if ($_GET['unequip'] == "armor" && $user_class->eqarmor != 0) {
        if ($user_class->armloaned == 1)
            Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
        else
            Give_Item($user_class->eqarmor, $user_class->id);
        $db->query("UPDATE grpgusers SET eqarmor = 0, `armloaned` = 0 WHERE id = ?");
        $db->execute(array(
           $user_class->id 
        ));
        header("Location: inventory.php");
    }
    if ($_GET['unequip'] == "shoes" && $user_class->eqshoes != 0) {
        if ($user_class->shoeloaned == 1)
            Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
        else
            Give_Item($user_class->eqshoes, $user_class->id);
        $db->query("UPDATE grpgusers SET eqshoes = 0, `shoeloaned` = 0 WHERE id = ?");
        $db->execute(array(
           $user_class->id 
        ));
        header("Location: inventory.php");
    }
    if (empty($_GET['id']))
        diefun("No item picked.");
    $howmany = Check_Item($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array(
        $_GET['id']
    ));
    $row = $db->fetch_row(true);
    $error = ($howmany == 0) ? "You don't have any of those." : $error;
    $error = ($row['level'] > $user_class->level) ? "You aren't high enough level to use this." : $error;
    if (!empty($error))
        diefun($error);
    if ($_GET['eq'] == "weapon") {
        if($row['offense'] <= 0)
            diefun("This item is not a weapon.");
        if ($user_class->eqweapon != 0){
            if($user_class->weploaned == 1)
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
            else
                Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = ?, `weploaned` = 0 WHERE id = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        $takeid = $db->fetch_single();
        Take_Item($_GET['id'], $user_class->id);
        header("Location: inventory.php");
    }
    if ($_GET['eq'] == "armor") {
        if($row['defense'] <= 0)
            diefun("This item is not an armor.");
        if ($user_class->eqarmor != 0){
            if($user_class->armloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
            else
                Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = ?, `armloaned` = 0 WHERE id = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        Take_Item($_GET['id'], $user_class->id);
        header("Location: inventory.php");
    }
    if ($_GET['eq'] == "shoes") {
        if($row['speed'] <= 0)
            diefun("This item is not a shoe.");
        if ($user_class->eqshoes != 0){
            if($user_class->shoeloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
            else
                Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = ?, `shoeloaned` = 0 WHERE id = ?");
        $db->execute(array(
            $_GET['id'],
            $user_class->id
        ));
        Take_Item($_GET['id'], $user_class->id);
        header("Location: inventory.php");
    }
}
include 'footer.php';
?>