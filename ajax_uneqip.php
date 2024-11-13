<?php
include 'ajax_header.php';
$user_class = new User($_SESSION['id']); // User class instance

$response = ["status" => "error", "message" => "Invalid request"]; // Default response

// Check POST data for unequip action
if (isset($_POST['action']) && $_POST['action'] === 'unequip' && isset($_POST['type'])) {
    $type = $_POST['type'];

    switch ($type) {
        case 'weapon':
            if ($user_class->eqweapon != 0) {
                unequipItem($user_class->id, 'weapon');
                $response = ["status" => "success", "message" => "Weapon unequipped successfully."];
            }
            break;
        case 'armor':
            if ($user_class->eqarmor != 0) {
                unequipItem($user_class->id, 'armor');
                $response = ["status" => "success", "message" => "Armor unequipped successfully."];
            }
            break;
        case 'shoes':
            if ($user_class->eqshoes != 0) {
                unequipItem($user_class->id, 'shoes');
                $response = ["status" => "success", "message" => "Shoes unequipped successfully."];
            }
            break;
        default:
            $response["message"] = "Invalid equipment type";
    }
}

header('Content-Type: application/json');
echo json_encode($response);

// Function to unequip the item
function unequipItem($user_id, $type) {
    global $db;
    $column = "eq" . $type;
    $loaned_column = $type . "loaned";

    $db->query("UPDATE grpgusers SET $column = 0, $loaned_column = 0 WHERE id = ?");
    $db->execute([$user_id]);
}
?>
