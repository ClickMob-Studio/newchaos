<?php
include 'ajax_header.php';

$user_class = new User($_SESSION['id']); // User class instance

$response = array("status" => "error", "message" => "Invalid request"); // Default response

// Check POST data for unequip action
if (isset($_POST['action']) && $_POST['action'] === 'unequip' && isset($_POST['type'])) {
    $type = $_POST['type'];

    switch ($type) {
        case 'weapon':
            if ($user_class->eqweapon != 0) {
                unequipItem($user_class->id, 'weapon');
                $response = array("status" => "success", "message" => "Weapon unequipped successfully.");
            } else {
                $response["message"] = "No weapon to unequip.";
            }
            break;
        case 'armor':
            if ($user_class->eqarmor != 0) {
                unequipItem($user_class->id, 'armor');
                $response = array("status" => "success", "message" => "Armor unequipped successfully.");
            } else {
                $response["message"] = "No armor to unequip.";
            }
            break;
        case 'shoes':
            if ($user_class->eqshoes != 0) {
                unequipItem($user_class->id, 'shoes');
                $response = array("status" => "success", "message" => "Shoes unequipped successfully.");
            } else {
                $response["message"] = "No shoes to unequip.";
            }
            break;
        default:
            $response["message"] = "Invalid equipment type.";
    }
} else {
    $response["message"] = "Invalid request parameters.";
}

header('Content-Type: application/json');
echo json_encode($response);

// Function to unequip the item
function unequipItem($user_id, $type) {
    global $db;
    $column = "eq" . $type;
    $loaned_column = $type . "loaned";

    try {
        $db->query("UPDATE grpgusers SET $column = 0, $loaned_column = 0 WHERE id = ?");
        $db->execute(array($user_id));
    } catch (Exception $e) {
        error_log("Database error during unequip: " . $e->getMessage()); // Logs errors for troubleshooting
    }
}
