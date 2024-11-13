<?php
include 'ajax_header.php';
$user_class= new User($_SESSION['id']);
$response = array("success" => false, "message" => "");
$error = "";
// Function to generate the HTML for the equipped item
function getEquippedItemHtml($itemType, $itemId, $itemImg, $itemName) {
    if ($itemId == 0) {
        return '<img width="100" height="100" src="/css/images/empty.jpg" /><br /> You are not using a ' . $itemType . '.';
    }

    $html = '<div>';
    $html .= '<img width="100" height="100" src="' . htmlspecialchars($itemImg) . '" alt="' . htmlspecialchars($itemName) . '" /><br />';
    $html .= htmlspecialchars($itemName);
    $html .= '<br />';
    $html .= '<a class="button-sm unequip-btn" href="#" data-type="' . htmlspecialchars($itemType) . '">Unequip</a>';
    $html .= '</div>';

    return $html;
}
// Utility function to respond with JSON
function jsonResponse($message, $success = false, $newItemHtml = null, $slot = null) {
    echo json_encode(array("success" => $success, "message" => $message, "newItemHtml" => $newItemHtml, "slot" => $slot));
    exit;
}

if ($_GET['loaned'] == 1) {
    if (empty($_GET['id'])) {
        jsonResponse("No item picked.");
    }

    $howmany = Check_Loan($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    if (!$db->num_rows()) {
        jsonResponse("Item not found.");
    }
    $row = $db->fetch_row(true);

    $error = ($howmany <= 0) ? "You don't have any of those." : $error;
    $error = ($row['level'] > $user_class->level) ? "You aren't high enough level to use this." : $error;
    if (!empty($error)) {
        jsonResponse($error);
    }

    // Equip weapon
    if ($_GET['eq'] == "weapon") {
        if ($row['offense'] <= 0) {
            jsonResponse("This item is not a weapon.");
        }
        if ($user_class->eqweapon != 0) {
            if ($user_class->weploaned == 1)
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
            else
                Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = ?, `weploaned` = 1 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        $db->query("SELECT id FROM gang_loans WHERE item = ? AND idto = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        $takeid = $db->fetch_single();
        Take_Loan($takeid, $user_class->id);

        $newItemHtml = getEquippedItemHtml("weapon", $_GET['id'], $row['image'], $row['itemname']);
        jsonResponse("Weapon equipped successfully!", true, $newItemHtml, 'weapon');
    }

    // Equip armor
    if ($_GET['eq'] == "armor") {
        if ($row['defense'] <= 0) {
            jsonResponse("This item is not an armor.");
        }
        if ($user_class->eqarmor != 0) {
            if ($user_class->armloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
            else
                Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = ?, `armloaned` = 1 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        $takeid = $db->fetch_single();
        Take_Loan($takeid, $user_class->id);

        $newItemHtml = getEquippedItemHtml("armor", $_GET['id'], $row['image'], $row['itemname']);
        jsonResponse("Armor equipped successfully!", true, $newItemHtml, 'armor');
    }

    // Equip shoes
    if ($_GET['eq'] == "shoes") {
        if ($row['speed'] <= 0) {
            jsonResponse("This item is not shoes.");
        }
        if ($user_class->eqshoes != 0) {
            if ($user_class->shoeloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
            else
                Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = ?, `shoeloaned` = 1 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        $takeid = $db->fetch_single();
        Take_Loan($takeid, $user_class->id);

        $newItemHtml = getEquippedItemHtml("shoes", $_GET['id'], $row['image'], $row['itemname']);
        jsonResponse("Shoes equipped successfully!", true, $newItemHtml, 'shoes');
    }
} else {
    // Unequip weapon
    if ($_GET['unequip'] == "weapon" && $user_class->eqweapon != 0) {
        if ($user_class->weploaned == 1)
            Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
        else
            Give_Item($user_class->eqweapon, $user_class->id);

        $db->query("UPDATE grpgusers SET eqweapon = 0, `weploaned` = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        jsonResponse("Weapon unequipped successfully!", true, getEquippedItemHtml('weapon', 0, '', ''), 'weapon');
    }

    // Unequip armor
    if ($_GET['unequip'] == "armor" && $user_class->eqarmor != 0) {
        if ($user_class->armloaned == 1)
            Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
        else
            Give_Item($user_class->eqarmor, $user_class->id);

        $db->query("UPDATE grpgusers SET eqarmor = 0, `armloaned` = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        jsonResponse("Armor unequipped successfully!", true, getEquippedItemHtml('armor', 0, '', ''), 'armor');
    }

    // Unequip shoes
    if ($_GET['unequip'] == "shoes" && $user_class->eqshoes != 0) {
        if ($user_class->shoeloaned == 1)
            Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
        else
            Give_Item($user_class->eqshoes, $user_class->id);

        $db->query("UPDATE grpgusers SET eqshoes = 0, `shoeloaned` = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        jsonResponse("Shoes unequipped successfully!", true, getEquippedItemHtml('shoes', 0, '', ''), 'shoes');
    }

    if (empty($_GET['id'])) {
        jsonResponse("No item picked.");
    }

    $howmany = Check_Item($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    $row = $db->fetch_row(true);
    $error = ($howmany == 0) ? "You don't have any of those." : $error;
    $error = ($row['level'] > $user_class->level) ? "You aren't high enough level to use this." : $error;
    if (!empty($error)) {
        jsonResponse($error);
    }

    // Equip weapon without loan
    if ($_GET['eq'] == "weapon") {
        if ($row['offense'] <= 0) {
            jsonResponse("This item is not a weapon.");
        }
        if ($user_class->eqweapon != 0) {
            if ($user_class->weploaned == 1)
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
            else
                Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = ?, `weploaned` = 0 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        Take_Item($_GET['id'], $user_class->id);

        $newItemHtml = getEquippedItemHtml("weapon", $_GET['id'], $row['image'], $row['itemname']);
        jsonResponse("Weapon equipped successfully!", true, $newItemHtml, 'weapon');
    }

    // Equip armor without loan
    if ($_GET['eq'] == "armor") {
        if ($row['defense'] <= 0) {
            jsonResponse("This item is not an armor.");
        }
        if ($user_class->eqarmor != 0) {
            if ($user_class->armloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
            else
                Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = ?, `armloaned` = 0 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        Take_Item($_GET['id'], $user_class->id);

        $newItemHtml = getEquippedItemHtml("armor", $_GET['id'], $row['image'], $row['itemname']);
        jsonResponse("Armor equipped successfully!", true, $newItemHtml, 'armor');
    }

    // Equip shoes without loan
    if ($_GET['eq'] == "shoes") {
        if ($row['speed'] <= 0) {
            jsonResponse("This item is not shoes.");
        }
        if ($user_class->eqshoes != 0) {
            if ($user_class->shoeloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
            else
                Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = ?, `shoeloaned` = 0 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        Take_Item($_GET['id'], $user_class->id);

        $newItemHtml = getEquippedItemHtml("shoes", $_GET['id'], $row['image'], $row['itemname']);
        jsonResponse("Shoes equipped successfully!", true, $newItemHtml, 'shoes');
    }
}
?>
