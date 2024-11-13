<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
$response = ["status" => "error", "message" => "Invalid request"];

if (isset($_POST['action']) && isset($_POST['item_id'])) {
    $user_id = $user_class->id;
    $item_id = intval($_POST['item_id']);
    $loaned = intval($_POST['loaned'] ?? 0);

    switch ($_POST['action']) {
        case 'equip':
            $response = equipItem($user_id, $item_id, $_POST['type'], $loaned);
            break;
        case 'unequip':
            $response = unequipItem($user_id, $_POST['type']);
            break;
        default:
            $response['message'] = 'Invalid action';
    }
}

header('Content-Type: application/json');
echo json_encode($response);

// Equip item function
function equipItem($user_id, $item_id, $type, $loaned) {
    global $db, $user_class;

    // Fetch item details
    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute([$item_id]);
    if (!$db->num_rows()) return ["status" => "error", "message" => "Item not found"];

    $item = $db->fetch_row(true);
    if ($item['level'] > $user_class->level) return ["status" => "error", "message" => "You aren't high enough level to use this."];

    switch ($type) {
        case 'weapon':
            // Ensure the item has offense
            if ($item['offense'] <= 0) return ["status" => "error", "message" => "This item is not a weapon"];
            return equipSpecificItem($user_id, 'weapon', $item_id, $loaned);
        case 'armor':
            if ($item['defense'] <= 0) return ["status" => "error", "message" => "This item is not armor"];
            return equipSpecificItem($user_id, 'armor', $item_id, $loaned);
        case 'shoes':
            if ($item['speed'] <= 0) return ["status" => "error", "message" => "This item is not a shoe"];
            return equipSpecificItem($user_id, 'shoes', $item_id, $loaned);
        default:
            return ["status" => "error", "message" => "Invalid equipment type"];
    }
}

// Unequip item function
function unequipItem($user_id, $type) {
    global $db, $user_class;

    switch ($type) {
        case 'weapon':
            if ($user_class->eqweapon != 0) {
                handleReturnOrLoan('weapon', $user_class->eqweapon, $user_class->weploaned);
                $db->query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?");
                $db->execute([$user_id]);
                return ["status" => "success", "message" => "Weapon unequipped"];
            }
            break;
        case 'armor':
            if ($user_class->eqarmor != 0) {
                handleReturnOrLoan('armor', $user_class->eqarmor, $user_class->armloaned);
                $db->query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?");
                $db->execute([$user_id]);
                return ["status" => "success", "message" => "Armor unequipped"];
            }
            break;
        case 'shoes':
            if ($user_class->eqshoes != 0) {
                handleReturnOrLoan('shoes', $user_class->eqshoes, $user_class->shoeloaned);
                $db->query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?");
                $db->execute([$user_id]);
                return ["status" => "success", "message" => "Shoes unequipped"];
            }
            break;
    }

    return ["status" => "error", "message" => "No item to unequip"];
}

// Helper function to equip item
function equipSpecificItem($user_id, $type, $item_id, $loaned) {
    global $db;

    $column = "eq" . $type;
    $loaned_column = $type . "loaned";

    // Handle currently equipped item
    if ($user_class->$column != 0) {
        handleReturnOrLoan($type, $user_class->$column, $user_class->$loaned_column);
    }

    $db->query("UPDATE grpgusers SET $column = ?, $loaned_column = ? WHERE id = ?");
    $db->execute([$item_id, $loaned, $user_id]);

    return ["status" => "success", "message" => ucfirst($type) . " equipped"];
}

// Helper function to handle return/loan
function handleReturnOrLoan($type, $item_id, $loaned) {
    global $user_class;
    if ($loaned == 1) {
        Loan_Item($user_class->gang, $item_id, $user_class->id);
    } else {
        Give_Item($item_id, $user_class->id);
    }
}
?>
