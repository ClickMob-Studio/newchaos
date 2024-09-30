<?php
include "ajax_header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $recipient = isset($_POST['recipient']) ? trim($_POST['recipient']) : '';

    if ($item_id > 0 && !empty($recipient)) {
        // Ensure the sender is logged in
        $sender_id = $_SESSION['id']; 
        if (!$sender_id) {
            echo "Error: You must be logged in to send items.";
            exit;
        }

        // Check if the recipient exists
        $db->query("SELECT id FROM grpgusers WHERE username = :recipient OR id = :recipient LIMIT 1");
        $db->bind(':recipient', $recipient);
        $recipient_id = $db->fetch_single();

        if (!$recipient_id) {
            echo "Error: Recipient not found.";
            exit;
        }

        // Check if the sender has the item in their inventory
        $db->query("SELECT quantity FROM inventory WHERE userid = :sender_id AND itemid = :item_id");
        $db->bind(':sender_id', $sender_id);
        $db->bind(':item_id', $item_id);
        $item_quantity = $db->fetch_single();

        if ($item_quantity && $item_quantity > 0) {
            $db->startTrans();
            try {
                // Deduct the item from the sender
                $db->query("UPDATE inventory SET quantity = quantity - 1 WHERE userid = :sender_id AND itemid = :item_id AND quantity > 0");
                $db->bind(':sender_id', $sender_id);
                $db->bind(':item_id', $item_id);
                $db->execute();

                // Check if the recipient already has this item
                $db->query("SELECT quantity FROM inventory WHERE userid = :recipient_id AND itemid = :item_id");
                $db->bind(':recipient_id', $recipient_id);
                $db->bind(':item_id', $item_id);
                $recipient_item_quantity = $db->fetch_single();

                if ($recipient_item_quantity) {
                    // Update the quantity if the recipient already has the item
                    $db->query("UPDATE inventory SET quantity = quantity + 1 WHERE userid = :recipient_id AND itemid = :item_id");
                } else {
                    // Insert a new row if the recipient does not have the item
                    $db->query("INSERT INTO inventory (userid, itemid, quantity) VALUES (:recipient_id, :item_id, 1)");
                }

                $db->bind(':recipient_id', $recipient_id);
                $db->bind(':item_id', $item_id);
                $db->execute();

                $db->endTrans();
                echo "Item sent successfully!";
            } catch (Exception $e) {
                $db->cancelTransaction();
                echo "Error: Failed to send item. Please try again.";
            }
        } else {
            echo "Error: You do not have this item in your inventory.";
        }
    } else {
        echo "Error: Invalid item or recipient.";
    }
} else {
    echo "Error: Invalid request method.";
}
?>
