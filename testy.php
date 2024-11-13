<?php
include 'header.php';
?>

<div class="container my-4">
    <!-- Message Box for Success/Error -->
    <div id="messageBox" class="alert" style="display: none;"></div>

    <h1>All Items</h1>

    <?php
    // Query to get all items with custom overrides for this user
    $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage 
                FROM inventory inv 
                JOIN items it ON inv.itemid = it.id 
                LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid 
                WHERE inv.userid = ?");
    $db->execute(array($user_class->id));
    $items = $db->fetch_row(); // Fetch all items associated with the user

    // Function to determine item type and subtype based on item properties
    function getItemType($row) {
        $type = '';
        $subtype = '';

        if ($row['offense'] > 0 && ($row['defense'] > 0 || $row['speed'] > 0)) {
            if ($row['offense'] > $row['defense']) {
                $type = ($row['offense'] > $row['speed']) ? 'weapon' : 'shoes';
            } else {
                $type = ($row['defense'] > $row['speed']) ? 'armor' : 'shoes';
            }
        } else {
            if ($row['offense'] > 0 && $row['rare'] == 0) {
                $type = 'weapon';
            } elseif ($row['defense'] > 0 && $row['rare'] == 0) {
                $type = 'armor';
            } elseif ($row['speed'] > 0 && $row['rare'] == 0) {
                $type = 'shoes';
            } elseif ($row['rare'] == 1) {
                $type = 'rare';
                if ($row['offense']) $subtype = 'weapon';
                if ($row['defense']) $subtype = 'armor';
                if ($row['speed']) $subtype = 'shoes';
            } elseif ($row['awake_boost'] > 0) {
                $type = 'house';
            } else {
                $type = 'consumable';
            }
        }
        
        return [$type, $subtype];
    }

    // Arrays to hold items by category and subtype
    $categorizedItems = [
        'weapon' => [],
        'armor' => [],
        'shoes' => [],
        'rare' => [
            'weapon' => [],
            'armor' => [],
            'shoes' => []
        ],
        'house' => [],
        'consumable' => []
    ];

    // Categorize each item based on its type and subtype
    foreach ($items as $item) {
        list($itemType, $subType) = getItemType($item);
        if ($itemType == 'rare' && $subType) {
            $categorizedItems['rare'][$subType][] = $item;
        } else {
            $categorizedItems[$itemType][] = $item;
        }
    }

    // Function to render a category of items
    function renderCategory($categoryName, $items) {
        if (empty($items)) return;

        echo "<h1>$categoryName</h1>";
        echo '<div class="row text-center">';

        foreach ($items as $item) {
            // Check if override name or image exists; fallback to default name and image
            $itemName = !empty($item['overridename']) ? $item['overridename'] : $item['itemname'];
            $itemImage = !empty($item['overrideimage']) ? $item['overrideimage'] : $item['image'];
            list($itemType, $subType) = getItemType($item);

            echo '<div class="col-md-3 mb-3">';
            echo '<img width="100" height="100" src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '"><br />';
            echo '<strong>' . htmlspecialchars($itemName) . '</strong><br />';
            echo '<button class="btn btn-sm btn-primary mt-2 equip-btn" data-type="' . $itemType . '" data-id="' . intval($item['itemid']) . '">Equip</button>';
            echo '</div>';
        }

        echo '</div>';
    }

    // Render each category section
    renderCategory("Weapons", $categorizedItems['weapon']);
    renderCategory("Armor", $categorizedItems['armor']);
    renderCategory("Shoes", $categorizedItems['shoes']);
    renderCategory("Rare Weapons", $categorizedItems['rare']['weapon']);
    renderCategory("Rare Armor", $categorizedItems['rare']['armor']);
    renderCategory("Rare Shoes", $categorizedItems['rare']['shoes']);
    renderCategory("Home Improvements", $categorizedItems['house']);
    renderCategory("Consumables", $categorizedItems['consumable']);
    ?>

    <h1 class="mt-5">Equipped Items</h1>
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
