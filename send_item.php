<?php
include "ajax_header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $recipient = isset($_POST['recipient']) ? trim($_POST['recipient']) : '';
    $quantity_to_send = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($item_id > 0 && !empty($recipient) && $quantity_to_send > 0) {
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

        // Check if the sender has the item and enough quantity
        $db->query("SELECT quantity FROM inventory WHERE userid = :sender_id AND itemid = :item_id");
        $db->bind(':sender_id', $sender_id);
        $db->bind(':item_id', $item_id);
        $item_quantity = $db->fetch_single();

        // Fetch the item name for the event message
        $db->query("SELECT itemname FROM items WHERE id = :item_id");
        $db->bind(':item_id', $item_id);
        $item_name = $db->fetch_single();  // Get the item name

        if ($item_quantity && $item_quantity >= $quantity_to_send) {
            $db->startTrans();
            try {
                // Deduct the specified quantity from the sender
                $db->query("UPDATE inventory SET quantity = quantity - :quantity WHERE userid = :sender_id AND itemid = :item_id");
                $db->bind(':sender_id', $sender_id);
                $db->bind(':item_id', $item_id);
                $db->bind(':quantity', $quantity_to_send);
                $db->execute();

                // If the sender's quantity is now 0, delete the item from their inventory
                if ($item_quantity - $quantity_to_send <= 0) {
                    $db->query("DELETE FROM inventory WHERE userid = :sender_id AND itemid = :item_id");
                    $db->bind(':sender_id', $sender_id);
                    $db->bind(':item_id', $item_id);
                    $db->execute();
                }

                // Check if the recipient already has this item
                $db->query("SELECT quantity FROM inventory WHERE userid = :recipient_id AND itemid = :item_id");
                $db->bind(':recipient_id', $recipient_id);
                $db->bind(':item_id', $item_id);
                $recipient_item_quantity = $db->fetch_single();

                if ($recipient_item_quantity) {
                    // Update the quantity if the recipient already has the item
                    $db->query("UPDATE inventory SET quantity = quantity + :quantity WHERE userid = :recipient_id AND itemid = :item_id");
                } else {
                    // Insert a new row if the recipient does not have the item
                    $db->query("INSERT INTO inventory (userid, itemid, quantity) VALUES (:recipient_id, :item_id, :quantity)");
                }

                $db->bind(':recipient_id', $recipient_id);
                $db->bind(':item_id', $item_id);
                $db->bind(':quantity', $quantity_to_send);
                $db->execute();

                $db->endTrans();

                // Send the event notification
                $u = new User($_SESSION['id']);
                Send_Event($recipient_id, $u->formattedname . ' sent you ' . $quantity_to_send . ' x ' . htmlspecialchars($item_name));

                echo "Item(s) sent successfully!";
            } catch (Exception $e) {
                $db->cancelTransaction();
                echo "Error: Failed to send item(s). Please try again.";
            }
        } else {
            echo "Error: You do not have enough quantity of this item.";
        }
    } else {
        echo "Error: Invalid item, recipient, or quantity.";
    }
} else {
    echo "Error: Invalid request method.";
}
