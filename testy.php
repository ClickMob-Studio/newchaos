<?php
include 'header.php';
?>

<div class="container-fluid my-4">
    <!-- Message Box for Success/Error -->
    <div id="messageBox" class="alert" style="display: none;"></div>

    <h1 class="text-center mb-4">Inventory</h1>

    <?php
    // Query to get all items with custom overrides for this user
    $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage 
                FROM inventory inv 
                JOIN items it ON inv.itemid = it.id 
                LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid 
                WHERE inv.userid = ?");
    $db->execute(array($user_class->id));
    $items = $db->fetch_row(); // Fetch all items associated with the user

  // Function to determine item type based on item properties
  function getItemType($row) {
    $type = '';
    $subtype = '';

    if ($row['offense'] > 0 && ($row['defense'] > 0 || $row['speed'] > 0)) {
        if ($row['offense'] > $row['defense']) {
            $type = ($row['offense'] > $row['speed']) ? 'weapon' : 'shoes';
        } else if ($row['defense'] > $row['speed']) {
            $type = 'armor';
        } else {
            $type = 'shoes';
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
            if ($row['offense'] > 0) {
                $subtype = 'weapon';
            } elseif ($row['defense'] > 0) {
                $subtype = 'armor';
            } elseif ($row['speed'] > 0) {
                $subtype = 'shoes';
            }
        } elseif ($row['awake_boost'] > 0) {
            $type = 'house';
        } else {
            $type = 'consumable';
        }
    }

    return array($type, $subtype);
}

// Adjusted categorization logic for "Equip" button display
foreach ($items as $item) {
    list($itemType, $itemSubtype) = getItemType($item);

    // Add items to categorized array
    if ($itemType === 'rare') {
        $categorizedItems['rare'][] = $item;
    } elseif ($itemType === 'booster' && $itemSubtype) {
        $categorizedItems[$itemSubtype][] = $item; 
    } else {
        $categorizedItems[$itemType][] = $item;
    }
}


    // Arrays to hold items by category
    $categorizedItems = [
        'weapon' => [],
        'armor' => [],
        'shoes' => [],
        'rare' => [],
        'house' => [],
        'consumable' => []
    ];

    // Categorize each item based on its type and subtype
    foreach ($items as $item) {
        list($itemType, $itemSubtype) = getItemType($item);

        // If it's a booster with a specific subtype, categorize separately
        if ($itemType === 'rare') {
            $categorizedItems['rare'][] = $item;
        }else if ($itemType === 'booster' && $itemSubtype) {
            $categorizedItems[$itemSubtype][] = $item; // Place in specific subtype (weapon, armor, or shoes)
        } else {
            $categorizedItems[$itemType][] = $item;
        }
    }


  // Render category with Equip button check
  function renderCategory($categoryName, $items) {
    if (empty($items)) return;

    echo '<div class="card my-4">';
    echo '<div class="card-header text-white text-center" style="background-color: #8e8e8e21;">';
    echo "<h2 class='text-white'>$categoryName</h2>";
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="row g-3 text-center">';

    foreach ($items as $item) {
        // Check if override name or image exists; fallback to default name and image
        $itemName = !empty($item['overridename']) ? $item['overridename'] : $item['itemname'];
        $itemImage = !empty($item['overrideimage']) ? $item['overrideimage'] : $item['image'];

        // Determine item type and subtype
        list($itemType, $itemSubtype) = getItemType($item);

        // Show "Equip" button for weapons, armor, shoes, or rare subtypes
        $showEquipButton = in_array($itemType, ['weapon', 'armor', 'shoes']) || in_array($itemSubtype, ['weapon', 'armor', 'shoes']);

        // Use subtype as the data-type if available, otherwise use itemType
        $dataType = $itemSubtype ?: $itemType;

        echo '<div class="col-6 col-md-4 col-lg-3 mb-3">';
        echo '<div class="card shadow-sm h-100">';
        echo '<img class="card-img-top" src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '">';
        echo '<div class="card-body d-flex flex-column">';
        echo '<h6 class="card-title">' . htmlspecialchars($itemName) . '</h6>';
        
        // Show the "Equip" button if the item is eligible
        if ($showEquipButton) {
            echo '<button class="btn btn-sm btn-primary equip-btn mt-2" data-type="' . $dataType . '" data-id="' . intval($item['itemid']) . '">Equip</button>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';
}


    // Render each category section within a card
    renderCategory("Weapons", $categorizedItems['weapon']);
    renderCategory("Armor", $categorizedItems['armor']);
    renderCategory("Shoes", $categorizedItems['shoes']);
    renderCategory("Boosters", $categorizedItems['booster']); // Display all boosters without specific subtype
    renderCategory("Home Improvements", $categorizedItems['house']);
    renderCategory("Consumables", $categorizedItems['consumable']);
    renderCategory("Rare Items", $categorizedItems['rare']); // Render Rare Items category
    ?>

    <h1 class="text-center mt-5">Equipped Items</h1>
    <div class="row text-center g-3">
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
            echo '<div class="col-6 col-md-4 mb-3">';
            echo '<div class="card shadow-sm h-100">';
            if ($item['id'] != 0) {
                echo '<img class="card-img-top" src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<h6 class="card-title">' . htmlspecialchars($item['name']) . '</h6>';
                echo '<button class="btn btn-sm btn-warning unequip-btn mt-2" data-type="' . $type . '" data-id="' . $item['id'] . '">Unequip</button>';
            } else {
                echo '<img class="card-img-top" src="/css/images/empty.jpg" alt="Empty Slot">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<p class="card-text">' . $item['placeholder'] . '</p>';
            }
            echo '</div>';
            echo '</div>';
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
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    location.reload(); // Reload items to reflect equipped status
                } else {
                    showMessage(response.message, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
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
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    location.reload(); // Reload items to reflect unequipped status
                } else {
                    showMessage(response.message, false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showMessage('Error processing the request: ' + textStatus, false);
            }
        });
    });
</script>
