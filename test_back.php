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
    if($db->num_rows() < 1) {
        echo Message("This user does not have any items or does not exist");
        require "footer.php";
        die();
    }
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
        ?>
<div class="container mt-5">

<?php 
        $db->query("SELECT * FROM items");
        $db->execute();
        $allItems = $db->fetch_row();
        ?>
        <h3>Add Items to User</h3>
        <form method="post">
            <input type="hidden" name="user_id" value="<?php echo $userid; ?>">
            <input type="hidden" name="giveitem" value="1">
            <select name="item_id">
                <?php foreach ($allItems as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['itemname']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity" min="1">
            <button type="submit">Add Item</button>
        </form>

</div>

        <?php
    } else {
        echo '<p>No inventory found for this user.</p>';
    }
}

echo '</div>';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['giveitem'], $_POST['item_id'])) {
    $user_id = $_POST['user_id'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    var_dump($_POST);
        echo Give_Item( $item_id, $user_id, $quantity);
      echo "item added to the user";

}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('.save-form').on('submit', function(e) {
    console.log("Form submitted");  // Check if this logs when you submit the form
    e.preventDefault();
    

        var form = $(this);

        console.log("Serialized Data: ", form.serialize());
        $.ajax({
    url: form.attr('action'),
    type: 'POST',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8', // This is the default, but setting it explicitly can help
    data: form.serialize(),
    success: function(response) {
        form.find('.form-result').html('<p>' + response + '</p>');
    },
    error: function(xhr, status, error) {
        form.find('.form-result').html('<p>Error updating item.</p>');
    }
});

    });
});
</script>
