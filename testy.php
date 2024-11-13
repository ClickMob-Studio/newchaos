<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<div class="container mt-5">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
                    alert(response.message);
                    location.reload(); // Reloads the page to reflect changes; adjust if dynamic updates are desired
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('An error occurred while trying to unequip the item.');
            }
        });
    });
});
</script>


