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

    $html = '<div>';
    $html .= '<img width="100" height="100" src="' . htmlspecialchars($itemImg) . '" alt="' . htmlspecialchars($itemName) . '" /><br />';
    $html .= htmlspecialchars($itemName);
    $html .= '<br />';
    $html .= '<a class="button-sm unequip-btn" href="#" data-type="' . htmlspecialchars($itemType) . '">Unequip</a>';
    $html .= '</div>';

    return $html;
}

// Handle requests to load equipped items on page load
if (isset($_GET['action']) && $_GET['action'] == 'load') {
    $equippedItems = array(
        'weapon' => getEquippedItemHtml('weapon', $user_class->eqweapon, $user_class->weaponimg, $user_class->weaponname),
        'armor' => getEquippedItemHtml('armor', $user_class->eqarmor, $user_class->armorimg, $user_class->armorname),
        'shoes' => getEquippedItemHtml('shoes', $user_class->eqshoes, $user_class->shoesimg, $user_class->shoesname),
    );
    $response['success'] = true;
    $response['equippedItems'] = $equippedItems;
    echo json_encode($response);
    exit;
}

// Equip handling
$equipItems = array(68, 69, 229, 230, 231, 250, 252, 255, 264);
if (isset($_GET['id']) && in_array($_GET['id'], $equipItems)) {
    $itemId = $_GET['id'];
    error_log("Attempting to equip item ID: $itemId"); // Log for debugging

    $db->query("SELECT itemname, `image`, quantity FROM inventory inv JOIN items i ON inv.itemid = i.id WHERE inv.userid = ? AND inv.itemid = ? ");
    $db->execute(array($user_class->id, $itemId));
    $items = $db->fetch_row();

    if (!$items) {
        $response['message'] = "Item not found in your inventory.";
        error_log("Equip failed: Item not found for ID $itemId"); // Log error
        echo json_encode($response);
        exit;
    }

    // Equip the item based on available slot or replace shoes if all slots are occupied
    if ($user_class->eqweapon == 0) {
        $db->query("UPDATE grpgusers SET eqweapon = ?, weploaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("weapon", $itemId, $items['image'], $items['itemname']);
        $response['message'] = "Equipped {$items['itemname']} as weapon!";
        $response['slot'] = 'weapon';
    } elseif ($user_class->eqarmor == 0) {
        $db->query("UPDATE grpgusers SET eqarmor = ?, armloaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['success'] = true;
        $response['newItemHtml'] = getEquippedItemHtml("armor", $itemId, $items['image'], $items['itemname']);
        $response['message'] = "Equipped {$items['itemname']} as armor!";
        $response['slot'] = 'armor';
    } elseif ($user_class->eqshoes == 0) {
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['success'] = true;
        $response['newItemHtml'] = getEquippedItemHtml("shoes", $itemId, $items['image'], $items['itemname']);
        $response['message'] = "Equipped {$items['itemname']} as shoes!";
        $response['slot'] = 'shoes';
    } else {
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['success'] = true;
        $response['newItemHtml'] = getEquippedItemHtml("shoes", $itemId, $items['image'], $items['itemname']);
        $response['message'] = "Replaced shoes with {$items['itemname']}!";
        $response['slot'] = 'shoes';
    }

    $response['success'] = true;
    echo json_encode($response);
    exit;
}


// Handle unequipping logic
if (isset($_GET['unequip'])) {
    if ($_GET['unequip'] == "weapon" && $user_class->eqweapon != 0) {
        if ($user_class->weploaned == 1) {
            Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
        } else {
            Give_Item($user_class->eqweapon, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));

        $response['newItemHtml'] = getEquippedItemHtml('weapon', 0, '', '');
        $response['success'] = true;
        $response['message'] = "Weapon unequipped successfully!";
        echo json_encode($response);
        exit;
    }

    if ($_GET['unequip'] == "armor" && $user_class->eqarmor != 0) {
        if ($user_class->armloaned == 1) {
            Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
        } else {
            Give_Item($user_class->eqarmor, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));

        $response['newItemHtml'] = getEquippedItemHtml('armor', 0, '', '');
        $response['success'] = true;
        $response['message'] = "Armor unequipped successfully!";
        echo json_encode($response);
        exit;
    }

    if ($_GET['unequip'] == "shoes" && $user_class->eqshoes != 0) {
        if ($user_class->shoeloaned == 1) {
            Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
        } else {
            Give_Item($user_class->eqshoes, $user_class->id);
        }
        $db->query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));

        $response['newItemHtml'] = getEquippedItemHtml('shoes', 0, '', '');
        $response['success'] = true;
        $response['message'] = "Shoes unequipped successfully!";
        echo json_encode($response);
        exit;
    }
}

// General equip handling
if (isset($_GET['eq']) && isset($_GET['id'])) {
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

        $newItemHtml = getEquippedItemHtml("shoes", $_GET['id'], $row['image'], $row['itemname']);
        $response['success'] = true;
        $response['message'] = "Shoes equipped successfully!";
        $response['newItemHtml'] = $newItemHtml;
        echo json_encode($response);
        exit;
    }
}
?>
