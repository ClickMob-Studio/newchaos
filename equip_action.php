<?php
include "ajax_header.php"; // Ensures session data and headers are set up for AJAX responses
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_class = new User($_SESSION['id']); // Creates a User object for the logged-in user
$response = array("status" => "error", "message" => "Invalid request"); // Default response

if (isset($_POST['action']) && isset($_POST['item_id'])) { // Checks if the required parameters are passed in the POST request
    $user_id = $user_class->id;
    $item_id = intval($_POST['item_id']); // Sanitizes item_id
    $loaned = isset($_POST['loaned']) ? intval($_POST['loaned']) : 0; // Checks if the item is on loan (optional)

    switch ($_POST['action']) {
        case 'equip': // Handles item equip action
            $response = equipItem($user_id, $item_id, $_POST['type'], $loaned);
            break;
        case 'unequip': // Handles item unequip action
            $response = unequipItem($user_id, $_POST['type']);
            break;
        default:
            $response['message'] = 'Invalid action'; // Updates message for invalid action
    }
}

header('Content-Type: application/json'); // Sets the header for JSON response
echo json_encode($response); // Outputs the response as JSON

// Equip item function
function equipItem($user_id, $item_id, $type, $loaned) {
    global $db, $user_class;

    // If the item is loaned, fetch it from the gang_loans table
    if ($loaned == 1) {
        $db->query("SELECT * FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE gl.idto = ? AND i.id = 264");
        $db->execute(array($user_class->id)); // Use loan_id to fetch the loaned item
        if ($db->num_rows() == 0) return array("status" => "error", "message" => "Loaned item not found");
        $item = $db->fetch_row(true);
    } else {
        // Non-loaned item from the items table
        $db->query("SELECT * FROM items WHERE id = ?");
        $db->execute(array($item_id));
        if ($db->num_rows() == 0) return array("status" => "error", "message" => "Item not found");
        $item = $db->fetch_row(true);
    }

    if ($item['level'] > $user_class->level) return array("status" => "error", "message" => "You aren't high enough level to use this.");

    // Equip the item depending on the type
    switch ($type) { // Checks if the item matches the requested equipment type
        case 'weapon':
            if ($item['offense'] <= 0) return array("status" => "error", "message" => "This item is not a weapon");
            return equipSpecificItem($user_id, 'weapon', $item_id, $loaned, 'weploaned');
        case 'armor':
            if ($item['defense'] <= 0) return array("status" => "error", "message" => "This item is not armor");
            return equipSpecificItem($user_id, 'armor', $item_id, $loaned, 'armloaned');
        case 'shoes':
            if ($item['speed'] <= 0) return array("status" => "error", "message" => "This item is not a shoe");
            return equipSpecificItem($user_id, 'shoes', $item_id, $loaned, 'shoeloaned');
        default:
            return array("status" => "error", "message" => "Invalid equipment type");
    }
}

// Unequip item function
function unequipItem($user_id, $type) {
    global $db, $user_class;

    // Checks if the specified type of item is equipped and unequips it if so
    switch ($type) {
        case 'weapon':
            if ($user_class->eqweapon != 0) {
                handleReturnOrLoan('weapon', $user_class->eqweapon, $user_class->weploaned); // Handles item return or loan
                $db->query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?"); // Updates DB
                $db->execute(array($user_id));
                return array("status" => "success", "message" => "Weapon unequipped");
            }
            break;
        case 'armor':
            if ($user_class->eqarmor != 0) {
                handleReturnOrLoan('armor', $user_class->eqarmor, $user_class->armloaned);
                $db->query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?"); 
                $db->execute(array($user_id));
                return array("status" => "success", "message" => "Armor unequipped");
            }
            break;
        case 'shoes':
            if ($user_class->eqshoes != 0) {
                handleReturnOrLoan('shoes', $user_class->eqshoes, $user_class->shoeloaned);
                $db->query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?"); 
                $db->execute(array($user_id));
                return array("status" => "success", "message" => "Shoes unequipped");
            }
            break;
    }

    return array("status" => "error", "message" => "No item to unequip");
}

// Helper function to equip specific item
function equipSpecificItem($user_id, $type, $item_id, $loaned, $loaned_column) {
    global $db, $user_class;

    $column = "eq" . $type;

    // Handles currently equipped item before equipping the new one
    if ($user_class->$column != 0) {
        handleReturnOrLoan($type, $user_class->$column, $user_class->$loaned_column);
    }

    // Update DB with the new equipment
    $db->query("UPDATE grpgusers SET $column = ?, $loaned_column = ? WHERE id = ?"); // Updates DB with the new equipment
    $db->execute(array($item_id, $loaned, $user_id));

    return array("status" => "success", "message" => ucfirst($type) . " equipped");
}

// Helper function to handle return/loan
function handleReturnOrLoan($type, $item_id, $loaned) {
    global $user_class;
    if ($loaned == 1) {
        Loan_Item($user_class->gang, $item_id, $user_class->id); // Returns item to the gang if it was loaned
    } else {
        Give_Item($item_id, $user_class->id); // Returns item to inventory if not on loan
    }
}
?>
