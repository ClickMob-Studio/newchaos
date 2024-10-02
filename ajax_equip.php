<?php
include 'ajax_header.php';  // Include your AJAX-compatible header
$user_class = new User($_SESSION['id']);
$response = array("success" => false, "message" => "");
$error = '';

// Function to generate the HTML for the equipped item
function getEquippedItemHtml($itemType, $itemId, $itemImg, $itemName) {
    if ($itemId == 0) {
        return '<img width="100" height="100" src="/css/images/empty.jpg" /><br /> You are not using a ' . $itemType . '.';
    }

    $html = '';
    $html .= image_popup($itemImg, $itemId);
    $html .= '<br />';
    $html .= item_popup($itemName, $itemId);
    $html .= '<br />';
    $html .= '<a class="button-sm" href="equip.php?unequip=' . $itemType . '">Unequip</a>';
    return $html;
}

// Function to get the equipped items for page load or reloading
function getEquippedItems($user_class) {
    return array(
        'weapon' => getEquippedItemHtml('weapon', $user_class->eqweapon, $user_class->weaponimg, $user_class->weaponname),
        'armor' => getEquippedItemHtml('armor', $user_class->eqarmor, $user_class->armorimg, $user_class->armorname),
        'shoes' => getEquippedItemHtml('shoes', $user_class->eqshoes, $user_class->shoesimg, $user_class->shoesname),
    );
}

// If a request to get equipped items on page load
if (isset($_GET['action']) && $_GET['action'] == 'load') {
    $equippedItems = getEquippedItems($user_class);
    $response['success'] = true;
    $response['equippedItems'] = $equippedItems;
    echo json_encode($response);
    exit;
}

if (isset($_GET['id']) && $_GET['id'] == 69) {
    $a = $db->query("SELECT itemname, `image` FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    $items = $db->fetch_row();
    $itemName = $items['itemname'];
    $itemImg = $items['image'];
    // First, check which slot is empty
    if ($user_class->eqweapon == 0) {
        // Equip in the weapon slot if it's empty
        $db->query("UPDATE grpgusers SET eqweapon = ?, weploaned = 0 WHERE id = ?");
        $db->execute(array(69, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("weapon", 69, $itemImg, $itemName);
        $response['message'] = "Equipped $itemName as weapon!";
        $response['slot'] = 'weapon';
    } elseif ($user_class->eqarmor == 0) {
        // Equip in the armor slot if it's empty
        $db->query("UPDATE grpgusers SET eqarmor = ?, armloaned = 0 WHERE id = ?");
        $db->execute(array(69, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("armor", 69, $itemImg, $itemName);
        $response['message'] = "Equipped $itemName as armor!";
        $response['slot'] = 'armor';
    } elseif ($user_class->eqshoes == 0) {
        // Equip in the shoes slot if it's empty
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 0 WHERE id = ?");
        $db->execute(array(69, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("shoes", 69, $itemImg, $itemName);
        $response['message'] = "Equipped $itemName as shoes!";
        $response['slot'] = 'shoes';
    } else {
        // Overwrite the shoes slot if all slots are filled
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 0 WHERE id = ?");
        $db->execute(array(69, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("shoes", 69, $itemImg, $itemName);
        $response['message'] = "All slots were full. Replaced shoes with $itemName!";
        $response['slot'] = 'shoes';
    }

    $response['success'] = true;
    echo json_encode($response);
    exit;
}


// If a request to get equipped items on page load
if (isset($_GET['action']) && $_GET['action'] == 'load') {
    $equippedItems = getEquippedItems($user_class);
    $response['success'] = true;
    $response['equippedItems'] = $equippedItems;
    echo json_encode($response);
    exit;
}
if (isset($_GET['loaned']) && $_GET['loaned'] == 1) {
    if (empty($_GET['id'])) {
        $response['message'] = "No item picked.";
        echo json_encode($response);
        exit;
    }

    $howmany = Check_Loan($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    if (!$db->num_rows()) {
        $response['message'] = "Item not found.";
        echo json_encode($response);
        exit;
    }

    $row = $db->fetch_row(true);
    $error = ($howmany <= 0) ? "You don't have any of those." : $error;
    $error = ($row['level'] > $user_class->level) ? "You aren't high enough level to use this." : $error;
    
    if (!empty($error)) {
        $response['message'] = $error;
        echo json_encode($response);
        exit;
    }

    if ($_GET['eq'] == "weapon") {
        if ($row['offense'] <= 0) {
            $response['message'] = "This item is not a weapon.";
            echo json_encode($response);
            exit;
        }
        if ($user_class->eqweapon != 0) {
            if ($user_class->weploaned == 1)
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
            else
                Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = ?, weploaned = 1 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        
        Take_Loan($_GET['id'], $user_class->id);

        // Generate the new HTML for the equipped weapon
        $newItemHtml = getEquippedItemHtml("weapon", $_GET['id'], $row['image'], $row['itemname']);
        
        $response['success'] = true;
        $response['message'] = "Weapon equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;  // Add the new HTML to the response
        echo json_encode($response);
        exit;
    }

    if ($_GET['eq'] == "armor") {
        if ($row['defense'] <= 0) {
            $response['message'] = "This item is not an armor.";
            echo json_encode($response);
            exit;
        }
        if ($user_class->eqarmor != 0) {
            if ($user_class->armloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
            else
                Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = ?, armloaned = 1 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        
        Take_Loan($_GET['id'], $user_class->id);

        // Generate the new HTML for the equipped armor
        $newItemHtml = getEquippedItemHtml("armor", $_GET['id'], $row['image'], $row['itemname']);

        $response['success'] = true;
        $response['message'] = "Armor equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;  // Add the new HTML to the response
        echo json_encode($response);
        exit;
    }

    if ($_GET['eq'] == "shoes") {
        if ($row['speed'] <= 0) {
            $response['message'] = "This item is not shoes.";
            echo json_encode($response);
            exit;
        }
        if ($user_class->eqshoes != 0) {
            if ($user_class->shoeloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
            else
                Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 1 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        
        Take_Loan($_GET['id'], $user_class->id);

        // Generate the new HTML for the equipped shoes
        $newItemHtml = getEquippedItemHtml("shoes", $_GET['id'], $row['image'], $row['itemname']);

        $response['success'] = true;
        $response['message'] = "Shoes equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;  // Add the new HTML to the response
        echo json_encode($response);
        exit;
    }
} else {
    // Handle unequip
    if ($_GET['unequip'] == "weapon" && $user_class->eqweapon != 0) {
        if ($user_class->weploaned == 1)
            Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
        else
            Give_Item($user_class->eqweapon, $user_class->id);
        $db->query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        $response['success'] = true;
        $response['message'] = "Weapon unequipped successfully!";
        echo json_encode($response);
        exit;
    }

    if ($_GET['unequip'] == "armor" && $user_class->eqarmor != 0) {
        if ($user_class->armloaned == 1)
            Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
        else
            Give_Item($user_class->eqarmor, $user_class->id);
        $db->query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        $response['success'] = true;
        $response['message'] = "Armor unequipped successfully!";
        echo json_encode($response);
        exit;
    }

    if ($_GET['unequip'] == "shoes" && $user_class->eqshoes != 0) {
        if ($user_class->shoeloaned == 1)
            Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
        else
            Give_Item($user_class->eqshoes, $user_class->id);
        $db->query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        $response['success'] = true;
        $response['message'] = "Shoes unequipped successfully!";
        echo json_encode($response);
        exit;
    }
    
    // General equip handling
    if (empty($_GET['id'])) {
        $response['message'] = "No item picked.";
        echo json_encode($response);
        exit;
    }
    
    // Check if user has the item
    $howmany = Check_Item($_GET['id'], $user_class->id);
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute(array($_GET['id']));
    $row = $db->fetch_row(true);
    $error = ($howmany == 0) ? "You don't have any of those." : $error;
    $error = ($row['level'] > $user_class->level) ? "You aren't high enough level to use this." : $error;
    
    if (!empty($error)) {
        $response['message'] = $error;
        echo json_encode($response);
        exit;
    }

    // Equip weapon
    if ($_GET['eq'] == "weapon") {
        if ($row['offense'] <= 0) {
            $response['message'] = "This item is not a weapon.";
            echo json_encode($response);
            exit;
        }
        if ($user_class->eqweapon != 0) {
            if ($user_class->weploaned == 1)
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
            else
                Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = ?, weploaned = 0 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        Take_Item($_GET['id'], $user_class->id);

        // Generate the new HTML for the equipped weapon
        $newItemHtml = getEquippedItemHtml("weapon", $_GET['id'], $row['image'], $row['itemname']);

        $response['success'] = true;
        $response['message'] = "Weapon equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;
        echo json_encode($response);
        exit;
    }

    // Equip armor
    if ($_GET['eq'] == "armor") {
        if ($row['defense'] <= 0) {
            $response['message'] = "This item is not an armor.";
            echo json_encode($response);
            exit;
        }
        if ($user_class->eqarmor != 0) {
            if ($user_class->armloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
            else
                Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = ?, armloaned = 0 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        Take_Item($_GET['id'], $user_class->id);

        // Generate the new HTML for the equipped armor
        $newItemHtml = getEquippedItemHtml("armor", $_GET['id'], $row['image'], $row['itemname']);

        $response['success'] = true;
        $response['message'] = "Armor equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;
        echo json_encode($response);
        exit;
    }

    // Equip shoes
    if ($_GET['eq'] == "shoes") {
        if ($row['speed'] <= 0) {
            $response['message'] = "This item is not shoes.";
            echo json_encode($response);
            exit;
        }
        if ($user_class->eqshoes != 0) {
            if ($user_class->shoeloaned == 1)
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
            else
                Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($_GET['id'], $user_class->id));
        Take_Item($_GET['id'], $user_class->id);

        // Generate the new HTML for the equipped shoes
        $newItemHtml = getEquippedItemHtml("shoes", $_GET['id'], $row['image'], $row['itemname']);

        $response['success'] = true;
        $response['message'] = "Shoes equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;
        echo json_encode($response);
        exit;
    }
}
?>
