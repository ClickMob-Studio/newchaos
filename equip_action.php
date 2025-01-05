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

    // Before equipping the new item, handle any current equipped loaned items
    switch ($type) {
        case 'weapon':
            if ($user_class->eqweapon != 0 && $user_class->weploaned == 1) {
                Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id); // Return the loaned weapon to gang_loans
            }
            break;
        case 'armor':
            if ($user_class->eqarmor != 0 && $user_class->armloaned == 1) {
                Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id); // Return the loaned armor to gang_loans
            }
            break;
        case 'shoes':
            if ($user_class->eqshoes != 0 && $user_class->shoeloaned == 1) {
                Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id); // Return the loaned shoes to gang_loans
            }
            break;
        case 'gloves':
            if ($user_class->eqgloves != 0 && $user_class->glovesloaned == 1) {
                Loan_Item($user_class->gang, $user_class->eqgloves, $user_class->id); // Return the loaned gloves to gang_loans
            }
            break;
    }

    // Fetch the new item (loaned or not)
    if ($loaned == 1) {
        $db->query("SELECT * FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE gl.idto = ? AND i.id = ?");
        $db->execute(array($user_class->id, $item_id));
        if ($db->num_rows() == 0) return array("status" => "error", "message" => "Loaned item not found");
        $item = $db->fetch_row(true);

        $db->query("UPDATE gang_loans SET quantity = quantity - 1 WHERE item = ? AND idto = ?");
        $db->execute(array($item_id, $user_class->id));

        // If quantity is 0, delete the record from gang_loans
        $db->query("DELETE FROM gang_loans WHERE item = ? AND idto = ? AND quantity <= 0");
        $db->execute(array($item_id, $user_class->id));
    } else {
        $db->query("SELECT * FROM items WHERE id = ?");
        $db->execute(array($item_id));
        if ($db->num_rows() == 0) return array("status" => "error", "message" => "Item not found");
        $item = $db->fetch_row(true);
    }

    // Check if user level is enough to equip the item
    if ($item['level'] > $user_class->level) {
        return array("status" => "error", "message" => "You aren't high enough level to use this.");
    }

    // Equip the item based on its type
    switch ($type) {
        case 'weapon':
            if ($item['offense'] <= 0) return array("status" => "error", "message" => "This item is not a weapon");
            return equipSpecificItem($user_id, 'weapon', $item_id, $loaned, 'weploaned');
        case 'armor':
            if ($item['defense'] <= 0) return array("status" => "error", "message" => "This item is not armor");
            return equipSpecificItem($user_id, 'armor', $item_id, $loaned, 'armloaned');
        case 'shoes':
            if ($item['speed'] <= 0) return array("status" => "error", "message" => "This item is not shoes");
            return equipSpecificItem($user_id, 'shoes', $item_id, $loaned, 'shoeloaned');
        case 'gloves':
            if ($item['agility'] <= 0) return array("status" => "error", "message" => "This item is not gloves");
            return equipSpecificItem($user_id, 'gloves', $item_id, $loaned, 'glovesloaned');
        default:
            return array("status" => "error", "message" => "Invalid equipment type");
    }
}

// Unequip item function
function unequipItem($user_id, $type) {
    global $db, $user_class;

    switch ($type) {
        case 'weapon':
            if ($user_class->eqweapon != 0) {
                handleReturnOrLoan('weapon', $user_class->eqweapon, $user_class->weploaned);
                // If loaned, add it back to gang_loans using Loan_Item function
                if ($user_class->weploaned == 1) {
                    Loan_Item($user_class->gang, $user_class->eqweapon, $user_class->id);
                }
                $db->query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?");
                $db->execute(array($user_id));

                return array("status" => "success", "message" => "Weapon unequipped");
            }
            break;
        case 'armor':
            if ($user_class->eqarmor != 0) {
                handleReturnOrLoan('armor', $user_class->eqarmor, $user_class->armloaned);
                if ($user_class->armloaned == 1) {
                    Loan_Item($user_class->gang, $user_class->eqarmor, $user_class->id);
                }
                $db->query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?");
                $db->execute(array($user_id));

                return array("status" => "success", "message" => "Armor unequipped");
            }
            break;
        case 'shoes':
            if ($user_class->eqshoes != 0) {
                handleReturnOrLoan('shoes', $user_class->eqshoes, $user_class->shoeloaned);
                // If loaned, add it back to gang_loans using Loan_Item function
                if ($user_class->shoeloaned == 1) {
                    Loan_Item($user_class->gang, $user_class->eqshoes, $user_class->id);
                }
                $db->query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?");
                $db->execute(array($user_id));

                return array("status" => "success", "message" => "Shoes unequipped");
            }
            break;
        case 'gloves':
            if ($user_class->eqgloves != 0) {
                handleReturnOrLoan('gloves', $user_class->eqgloves, $user_class->glovesloaned);
                // If loaned, add it back to gang_loans using Loan_Item function
                if ($user_class->glovesloaned == 1) {
                    Loan_Item($user_class->gang, $user_class->eqgloves, $user_class->id);
                }
                $db->query("UPDATE grpgusers SET eqgloves = 0, glovesloaned = 0 WHERE id = ?");
                $db->execute(array($user_id));

                return array("status" => "success", "message" => "Gloves unequipped");
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
    $db->query("UPDATE grpgusers SET $column = ?, $loaned_column = ? WHERE id = ?");
    $db->execute(array($item_id, $loaned, $user_id));

    // If the item is not loaned, remove it from the user's inventory
    if ($loaned == 0) {
        Take_Item($item_id, $user_class->id);
    }

    return array("status" => "success", "message" => ucfirst($type) . " equipped");
}

// Helper function to handle return/loan
function handleReturnOrLoan($type, $item_id, $loaned) {
    global $user_class, $db;
    // If the item is loaned, return it to gang_loans
    if ($loaned == 1) {
        // Do nothing, the item is already returned to the gang_loans table in the equip process
    } else {
        // If not loaned, return it to inventory
        Give_Item($item_id, $user_class->id);
    }
}
