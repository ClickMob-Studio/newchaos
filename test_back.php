<?php
include 'header.php';  
error_reporting(E_ALL);

if (!isset($user_class) || $user_class->admin < 1) {
    die("Unauthorized access.");
}

echo '<div class="container mt-5">';
echo '<form method="post" class="mb-3">';
echo '<div class="mb-3">';
echo '<label for="userid" class="form-label">User ID:</label>';
echo '<input type="text" class="form-control" id="userid" name="userid" required>';
echo '</div>';
echo '<button type="submit" class="btn btn-primary">Load Inventory</button>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userid'])) {
    $userid = $_POST['userid'];
    $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
    $db->execute(array($userid));
    $inventory = $db->fetch_row();

    if ($inventory) {
        echo '<form method="post" class="mb-3">';
        echo '<input type="hidden" name="userid" value="'.$userid.'">';
        foreach ($inventory as $item) {
            echo '<div class="row mb-3">';
            echo '<div class="col-md-6">';
            $item_name = isset($item['overridename']) ? $item['overridename'] : $item['itemname'];
            echo '<label class="form-label">' . htmlspecialchars($item_name) . '</label>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<input type="text" class="form-control" name="quantity['. $item['itemid'] .']" value="'. htmlspecialchars($item['quantity']) .'">';
            echo '<input type="hidden" name="itemid[]" value="'. $item['itemid'] .'">';
            echo '</div>';
            echo '</div>';
        }
        echo '<button type="submit" class="btn btn-success">Save Changes</button>';
        echo '</form>';
    } else {
        echo '<p>No inventory found for this user.</p>';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    $userid = $_POST['userid']; 
    foreach ($_POST['itemid'] as $index => $itemid) {
        $quantity = $_POST['quantity'][$itemid];
        $db->query("UPDATE inventory SET quantity = ? WHERE itemid = ? AND userid = ?");
        $db->execute(array($quantity, $itemid, $userid));
        echo '<p>Inventory updated successfully.</p>';
    }
} else {
    echo '<p>Invalid access or no UserID provided.</p>';
}
echo '</div>';
?>
