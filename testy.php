<?php
include 'header.php';
?>

<div class="container my-4">
    <!-- Message Box for Success/Error -->
    <div id="messageBox" class="alert" style="display: none;"></div>

    <h2>All Items</h2>
    <div class="row text-center">
        <?php
        // Query to get all items with custom overrides for this user
        $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage 
                    FROM inventory inv 
                    JOIN items it ON inv.itemid = it.id 
                    LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid 
                    WHERE inv.userid = ?");
        $db->execute(array($user_class->id));
        $items = $db->fetch_row(true); // Fetch all items associated with the user
        var_dump($items);
        foreach ($items as $item) {
            $itemName = !empty($item['itemname']) ? $item['itemname'] : $item['itemname'];
            $itemImage = !empty($item['overrideimage']) ? $item['overrideimage'] : $item['image'];
            $itemType = htmlspecialchars($item['type']); // type: weapon, armor, shoes, etc.
            
            echo '<div class="col-md-3 mb-3">';
            echo '<img width="100" height="100" src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '"><br />';
            echo '<strong>' . $item['itemname']. '</strong><br />';
            echo '<button class="btn btn-sm btn-primary mt-2 equip-btn" data-type="' . $itemType . '" data-id="' . intval($item['itemid']) . '">Equip</button>';
            echo '</div>';
        }
        ?>
    </div>

    <h2 class="mt-5">Equipped Items</h2>
    <div class="row text-center">
        <?php
        // Array to manage equipped items with respective properties
        $equippedItems = array(
            'weapon' => array(
                'id' => $user_class->eqweapon,
                'img' => $user_class->weaponimg,
                'name' => $user_class->weaponname,
                'placeholder' => 'You are not holding a weapon.'
            ),
            'armor' => array(
                'id' => $user_class->eqarmor,
                'img' => $user_class->armorimg,
                'name' => $user_class->armorname,
                'placeholder' => 'You are not wearing armor.'
            ),
            'shoes' => array(
                'id' => $user_class->eqshoes,
                'img' => $user_class->shoesimg,
                'name' => $user_class->shoesname,
                'placeholder' => 'You are not wearing boots.'
            )
        );

        // Display equipped items with placeholders if not equipped
        foreach ($equippedItems as $type => $item) {
            echo '<div class="col-md-4 mb-3">';
            if ($item['id'] != 0) {
                echo image_popup($item['img'], $item['id']);
                echo '<br />';
                echo item_popup($item['name'], $item['id']);
                echo '<br />';
                echo '<button class="btn btn-sm btn-warning mt-2 unequip-btn" data-type="' . $type . '" data-id="' . $item['id'] . '">Unequip</button>';
            } else {
                echo '<img width="100" height="100" src="/css/images/empty.jpg" alt="Empty Slot"><br />';
                echo $item['placeholder'];
            }
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script>
    function showMessage(message, isSuccess) {
        var messageBox = $("#messageBox");
        messageBox
            .text(message)
            .removeClass("alert-success alert-danger")
            .addClass(isSuccess ? "alert-success" : "alert-danger")
            .fadeIn();

        // Hide message after 3 seconds
        setTimeout(function() { messageBox.fadeOut(); }, 3000);
    }

    $(document).on('click', '.equip-btn', function () {
        var type = $(this).data('type');
        var itemId = $(this).data('id'); // Get the item ID for equipping

        $.ajax({
            url: 'equip_action.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'equip', type: type, item_id: itemId }, // Send item ID and type in request
            success: function (response) {
                console.log(response); // Log the response for debugging
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    location.reload(); // Reload items to reflect equipped status
                } else {
                    showMessage(response.message, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error: " + textStatus + ": " + errorThrown); // Log detailed error
                showMessage('Error processing the request: ' + textStatus, false);
            }
        });
    });

    $(document).on('click', '.unequip-btn', function () {
        var type = $(this).data('type');
        var itemId = $(this).data('id'); // Get the item ID for unequipping

        $.ajax({
            url: 'equip_action.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'unequip', type: type, item_id: itemId }, // Send item ID in request
            success: function (response) {
                console.log(response); // Log the response for debugging
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    location.reload(); // Reload items to reflect unequipped status
                } else {
                    showMessage(response.message, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error: " + textStatus + ": " + errorThrown); // Log detailed error
                showMessage('Error processing the request: ' + textStatus, false);
            }
        });
    });
</script>
