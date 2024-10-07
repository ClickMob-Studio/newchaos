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
    $html .= '<a class="button-sm unequip-btn" href="#" data-type="' . $itemType . '">Unequip</a>';
    return $html;
}

// Function to prepare item data for the inventory
function getInventoryItemData($itemId, $itemImg, $itemName, $quantity) {
    return array(
        'id' => $itemId,
        'image' => $itemImg,
        'name' => $itemName,
        'quantity' => $quantity
    );
}

// Function to get the equipped items for page load or reloading
function getEquippedItems($user_class) {
    return array(
        'weapon' => getEquippedItemHtml('weapon', $user_class->eqweapon, $user_class->weaponimg, $user_class->weaponname),
        'armor' => getEquippedItemHtml('armor', $user_class->eqarmor, $user_class->armorimg, $user_class->armorname),
        'shoes' => getEquippedItemHtml('shoes', $user_class->eqshoes, $user_class->shoesimg, $user_class->shoesname),
    );
}

// Handle requests to load equipped items on page load
if (isset($_GET['action']) && $_GET['action'] == 'load') {
    $equippedItems = getEquippedItems($user_class);
    $response['success'] = true;
    $response['equippedItems'] = $equippedItems;
    echo json_encode($response);
    exit;
}

// Handle equipping an item and manage inventory updates
if (isset($_GET['eq']) && isset($_GET['id'])) {
    $itemId = (int)$_GET['id'];

    // Check if user has the item in their inventory
    $db->query("SELECT itemname, `image`, quantity FROM inventory inv JOIN items i ON inv.itemid = i.id WHERE inv.userid = ? AND inv.itemid = ?");
    $db->execute(array($user_class->id, $itemId));
    $items = $db->fetch_row();

    if (!$items) {
        $response['message'] = "Item not found in your inventory.";
        echo json_encode($response);
        exit;
    }

    $itemName = $items['itemname'];
    $itemImg = $items['image'];
    $itemQuantity = (int)$items['quantity'];

    // Equip the item based on the type
    if ($_GET['eq'] == "weapon" && $user_class->eqweapon == 0) {
        // Equip weapon
        $db->query("UPDATE grpgusers SET eqweapon = ?, weploaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("weapon", $itemId, $itemImg, $itemName);
        $response['slot'] = 'weapon';
    } elseif ($_GET['eq'] == "armor" && $user_class->eqarmor == 0) {
        // Equip armor
        $db->query("UPDATE grpgusers SET eqarmor = ?, armloaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("armor", $itemId, $itemImg, $itemName);
        $response['slot'] = 'armor';
    } elseif ($_GET['eq'] == "shoes" && $user_class->eqshoes == 0) {
        // Equip shoes
        $db->query("UPDATE grpgusers SET eqshoes = ?, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($itemId, $user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml("shoes", $itemId, $itemImg, $itemName);
        $response['slot'] = 'shoes';
    } else {
        $response['message'] = "Item could not be equipped.";
        echo json_encode($response);
        exit;
    }

    // Update inventory (decrement or remove item)
    if ($itemQuantity == 1) {
        $db->query("DELETE FROM inventory WHERE userid = ? AND itemid = ?");
        $db->execute(array($user_class->id, $itemId));
    } else {
        $newQuantity = $itemQuantity - 1;
        $db->query("UPDATE inventory SET quantity = ? WHERE userid = ? AND itemid = ?");
        $db->execute(array($newQuantity, $user_class->id, $itemId));
    }

    $response['success'] = true;
    $response['message'] = "$itemName equipped successfully!";
    echo json_encode($response);
    exit;
}

// Handle unequipping logic
if (isset($_GET['unequip'])) {
    $itemType = $_GET['unequip'];
    $unequipped = false;

    // Unequip weapon
    if ($itemType == "weapon" && $user_class->eqweapon != 0) {
        Give_Item($user_class->eqweapon, $user_class->id);
        $db->query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml('weapon', 0, '', '');
        $unequipped = true;
    }

    // Unequip armor
    if ($itemType == "armor" && $user_class->eqarmor != 0) {
        Give_Item($user_class->eqarmor, $user_class->id);
        $db->query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml('armor', 0, '', '');
        $unequipped = true;
    }

    // Unequip shoes
    if ($itemType == "shoes" && $user_class->eqshoes != 0) {
        Give_Item($user_class->eqshoes, $user_class->id);
        $db->query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?");
        $db->execute(array($user_class->id));
        $response['newItemHtml'] = getEquippedItemHtml('shoes', 0, '', '');
        $unequipped = true;
    }

    if ($unequipped) {
        // Fetch item data to send back to inventory
        $db->query("SELECT itemname, `image`, quantity FROM items WHERE id = ?");
        $db->execute(array($user_class->eqweapon));  // Adjust for different items
        $item = $db->fetch_row();

        $response['success'] = true;
        $response['message'] = ucfirst($itemType) . " unequipped successfully!";
        $response['itemData'] = getInventoryItemData($user_class->eqweapon, $item['image'], $item['itemname'], $item['quantity']);
    } else {
        $response['message'] = "Error unequipping item.";
    }

    echo json_encode($response);
    exit;
}
?>
