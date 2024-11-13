<?php
require "header.php";
// Fetch all items in user's inventory
$db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage 
            FROM inventory inv 
            JOIN items it ON inv.itemid = it.id 
            LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid 
            WHERE inv.userid = ?");
$db->execute([$user_class->id]);

$inventoryItems = $db->fetch_row();  // Fetch all results
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div id="inventory">
    <?php foreach ($inventoryItems as $item): ?>
        <div class="item" id="item-<?= $item['id'] ?>">
            <span><?= $item['name'] ?> (x<?= $item['quantity'] ?>)</span>
            <button class="equip-item" data-id="<?= $item['id'] ?>" data-type="weapon" data-loaned="0">Equip Weapon</button>
            <button class="unequip-item" data-type="weapon">Unequip Weapon</button>
            <button class="equip-item" data-id="<?= $item['id'] ?>" data-type="armor" data-loaned="1">Equip Armor (Loaned)</button>
            <button class="unequip-item" data-type="armor">Unequip Armor</button>
            <button class="equip-item" data-id="<?= $item['id'] ?>" data-type="shoes" data-loaned="0">Equip Shoes</button>
            <button class="unequip-item" data-type="shoes">Unequip Shoes</button>
        </div>
    <?php endforeach; ?>
</div>

<script>
$(document).ready(function() {
    $('.equip-item').click(function() {
        const itemId = $(this).data('id');
        const type = $(this).data('type');
        const loaned = $(this).data('loaned');
        
        $.post('equip_actions.php', { action: 'equip', item_id: itemId, type: type, loaned: loaned }, function(response) {
            alert(response.message);
            if (response.status === 'success') {
                // Optional: Update UI to reflect equipment status
            }
        }, 'json');
    });

    $('.unequip-item').click(function() {
        const type = $(this).data('type');
        
        $.post('equip_actions.php', { action: 'unequip', type: type }, function(response) {
            alert(response.message);
            if (response.status === 'success') {
                // Optional: Update UI to reflect unequipment status
            }
        }, 'json');
    });
});
</script>
