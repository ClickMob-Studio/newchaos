<?php
include 'header.php';
?>

<div class="container mt-5">
<div id="messageBox" class="alert" style="display: none;"></div>

    <h2>Your Inventory</h2>
    <div id="inventoryItems" class="row">
        <?php
        $user_id = $user_class->id;
        // Fetching equipped items with AJAX unequip buttons
        $equipped_items = [
            "weapon" => ["item_id" => $user_class->eqweapon, "loaned" => $user_class->weploaned, "type" => "weapon"],
            "armor" => ["item_id" => $user_class->eqarmor, "loaned" => $user_class->armloaned, "type" => "armor"],
            "shoes" => ["item_id" => $user_class->eqshoes, "loaned" => $user_class->shoeloaned, "type" => "shoes"]
        ];

        foreach ($equipped_items as $type => $item) {
            if ($item["item_id"] > 0) {
                echo "<div class='col-md-4 mb-4 text-center'>";
                echo "<div class='card'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . ucfirst($type) . "</h5>";
                echo "<img src='path_to_item_image/{$item['item_id']}.png' alt='{$type} image' class='img-fluid mb-3'>";
                echo "<p class='card-text'>Equipped: $type</p>";
                echo "<button class='btn btn-danger btn-sm unequip-button' data-item-id='{$item['item_id']}' data-type='{$item['type']}'>Unequip</button>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>
</div>

<script>
function showMessage(message, isSuccess) {
    const messageBox = document.getElementById("messageBox");
    messageBox.innerText = message;
    messageBox.className = "alert"; // Reset classes
    messageBox.classList.add(isSuccess ? "alert-success" : "alert-error"); // Add success or error styling
    messageBox.style.display = "block"; // Show the message box
}
$(document).ready(function () {
    $('.unequip-button').click(function () {
        const itemType = $(this).data('type');
        
        $.ajax({
            url: 'equip_action.php', // Your script for equip/unequip actions
            type: 'POST',
            data: {
                action: 'unequip',
                item_id: 0, // We don't need an item ID for unequip
                type: itemType
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    // Update the inventory on success
                    showMessage(response.message, true);
                    location.reload(); // Reloads the page to reflect changes; adjust if dynamic updates are desired
                } else {
                    showMessage(response.message, false);
                }
            },
            error: function () {
                alert('An error occurred while trying to unequip the item.');
            }
        });
    });
});
</script>


<style>
    .alert {
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 4px;
    font-size: 16px;
    display: none; /* Hidden by default */
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

</style>