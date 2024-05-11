<?php
include 'header.php';  
error_reporting(E_ALL);

// Check user permissions
if (!isset($user_class) || $user_class->admin < 1) {
    die("Unauthorized access.");
}
?>
<div class="result"></div>
<?php
echo '<div class="container mt-5">';

echo '<form method="post" class="mb-3">';
echo '<div class="mb-3">';
echo '<label for="userid" class="form-label">User ID:</label>';
echo '<input type="text" class="form-control" id="userid" name="userid" required>';
echo '</div>';
echo '<button type="submit" name="action" value="load_inventory" class="btn btn-primary">Load Inventory</button>';
echo '</form>';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'load_inventory') {
    $userid = $_POST['userid'];
    $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
    $db->execute(array($userid));
    $inventory = $db->fetch_row();


    if ($inventory) {
        foreach ($inventory as $item) {
            echo '<form method="post" action="ajax_admin_inventory.php" id="save" class="mb-3">';
            echo '<input type="hidden" name="userid" value="' . $userid . '">';
            echo '<input type="hidden" name="itemid" value="' . $item['itemid'] . '">';
            echo '<div class="row mb-3">';
            echo '<div class="col-md-5">';
            $item_name = isset($item['overridename']) ? $item['overridename'] : $item['itemname'];
            echo '<label class="form-label">' . htmlspecialchars($item_name) . '</label>';
            echo '</div>';
            echo '<div class="col-md-5">';
            echo '<input type="text" class="form-control" name="quantity" value="' . htmlspecialchars($item['quantity']) . '">';
            echo '</div>';
            echo '<div class="col-md-2 mt-2">';
            echo '<button type="submit" name="action" value="update_item" class="btn btn-success">Update Quantity</button>';
            echo '</div>';
            echo '</div>';
            echo '</form>';
        }
    } else {
        echo '<p>No inventory found for this user.</p>';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_item') {
    $userid = $_POST['userid'];
    $itemid = $_POST['itemid'];
    $quantity = $_POST['quantity'];

    $db->query("UPDATE inventory SET quantity = ? WHERE itemid = ? AND userid = ?");
    if ($db->execute(array($quantity, $itemid, $userid))) {
        echo '<p>Item updated successfully.</p>';
    } else {
        echo '<p>Error updating item.</p>';
    }
}

echo '</div>';
?>
<script>
$(document).ready(function() {
    $('#save').on('submit', function(e) {
        e.preventDefault(); 

        var form = $(this);
        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                form.find('.result').html('<p>' + response + '</p>');
            },
            error: function(xhr, status, error) {
                form.find('.result').html('<p>Error updating item.</p>');
            }
        });
    });
});
</script>