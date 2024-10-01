<?php
include "ajax_header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $quantity_to_drop = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($item_id > 0 && $quantity_to_drop > 0) {
        $user_id = $_SESSION['id'];
        if (!$user_id) {
            echo "Error: You must be logged in to drop items.";
            exit;
        }

        // Check if the user has the item and enough quantity
        $db->query("SELECT quantity FROM inventory WHERE userid = :user_id AND itemid = :item_id");
        $db->bind(':user_id', $user_id);
        $db->bind(':item_id', $item_id);
        $item_quantity = $db->fetch_single();

        if ($item_quantity && $item_quantity >= $quantity_to_drop) {
            try {
                // Start transaction
                $db->startTrans();

                // Deduct the quantity from the user's inventory
                $db->query("UPDATE inventory SET quantity = quantity - :quantity WHERE userid = :user_id AND itemid = :item_id");
                $db->bind(':user_id', $user_id);
                $db->bind(':item_id', $item_id);
                $db->bind(':quantity', $quantity_to_drop);
                $db->execute();

                // If quantity becomes 0, delete the row from the inventory
                if ($item_quantity - $quantity_to_drop <= 0) {
                    $db->query("DELETE FROM inventory WHERE userid = :user_id AND itemid = :item_id");
                    $db->bind(':user_id', $user_id);
                    $db->bind(':item_id', $item_id);
                    $db->execute();
                }

                $db->endTrans();

                echo "Item(s) dropped successfully!";
            } catch (Exception $e) {
                $db->cancelTransaction();
                echo "Error: Failed to drop item(s). Please try again.";
            }
        } else {
            echo "Error: You do not have enough quantity of this item.";
        }
    } else {
        echo "Error: Invalid item or quantity.";
    }
} else {
    echo "Error: Invalid request method.";
}
