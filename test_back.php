<?php
include 'header.php';  
error_reporting(E_ALL);

// Check user permissions
if (!isset($user_class) or $user_class->admin < 1) {
    die("Unauthorized access.");
}

echo '<div class="container mt-5">';
echo '<div class="result" style="margin-bottom: 20px;"></div>'; // Global result container
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
            echo '<form method="post" action="ajax_admin_inventory.php" class="save-form mb-3">';
            echo '<input type="hidden" name="userid" value="' . $userid . '">';
            echo '<input type="hidden" name="itemid" value="' . $item['itemid'] . '">';
            echo '<div class="row mb-3">';
            echo '<div class="col-md-4">';
            $item_name = isset($item['overridename']) ? $item['overridename'] : $item['itemname'];
            echo '<label class="form-label">' . htmlspecialchars($item_name) . '</label>';
            echo '</div>';
            echo '<div class="col-md-5">';
            echo '<input type="text" class="form-control" name="quantity" value="' . htmlspecialchars($item['quantity']) . '">';
            echo '</div>';
            echo '<div class="col-md-3 mt-2">';
            echo '<button type="submit" name="action" value="update_item" class="btn btn-success">Update Quantity</button>';
            echo '</div>';
            echo '</div>';
            echo '<div class="form-result"></div>'; // Result container for this form
            echo '</form>';
        }
    } else {
        echo '<p>No inventory found for this user.</p>';
    }
}

echo '</div>';
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('.save-form').on('submit', function(e) {
    console.log("Form submitted");  // Check if this logs when you submit the form
    e.preventDefault();

        var form = $(this);
        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                console.log(response);
                form.find('.form-result').html('<p>' + response + '</p>'); // Update the form-result div in this form
            },
            error: function(xhr, status, error) {
                form.find('.form-result').html('<p>Error updating item.</p>'); // Error handling
            }
        });
    });
});
</script>
