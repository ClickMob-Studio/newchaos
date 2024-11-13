<?php
include 'header.php';

// Query to retrieve items with custom overrides for the user
$db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage 
            FROM inventory inv 
            JOIN items it ON inv.itemid = it.id 
            LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid 
            WHERE inv.userid = ?");
$db->execute(array($user_class->id));
$items = $db->fetch_row();

// Define categories for sorted items
$categories = [
    'weapon' => [],
    'armor' => [],
    'shoes' => [],
    'house' => [],
    'consumable' => [],
    'boosters' => []
];

// Function to determine item type and subtype
function getItemType($row) {
    $type = '';
    $subtype = '';

    if ($row['offense'] > 0 && ($row['defense'] > 0 || $row['speed'] > 0)) {
        if ($row['offense'] > $row['defense']) {
            if ($row['offense'] > $row['speed']) {
                $type = 'weapon';
            } else {
                $type = 'shoes';
            }
        } elseif ($row['defense'] > $row['speed']) {
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
            $type = 'boosters';
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

// Organize items into categories based on their types
foreach ($items as $item) {
    list($type, $subtype) = getItemType($item);

    $item['subtype'] = $subtype; // Set subtype for use in button conditions

    if ($type === 'boosters' && !empty($subtype)) {
        $categories['boosters'][$subtype][] = $item;
    } else {
        $categories[$type][] = $item;
    }
}

// Display items as Bootstrap cards
echo '<div class="container my-4">';

foreach ($categories as $categoryName => $categoryItems) {
    // Only display category if it has items
    if (!empty($categoryItems)) {
        echo '<div class="mb-4">';
        echo '<h1 class="text-center text-white p-3" style="background-color: #8e8e8e21;">' . ucfirst($categoryName) . '</h1>';
        echo '<div class="row">';

        foreach ($categoryItems as $subtype => $items) {
            if (is_array($items)) { // Handle subcategories for boosters
                foreach ($items as $item) {
                    displayItemCard($item, $categoryName, $subtype);
                }
            } else {
                displayItemCard($items, $categoryName, null);
            }
        }

        echo '</div></div>';
    }
}

echo '</div>';

// Function to display item card
function displayItemCard($item, $type, $subtype = null) {
    $itemName = !empty($item['overridename']) ? $item['overridename'] : $item['itemname'];
    $itemImage = !empty($item['overrideimage']) ? $item['overrideimage'] : $item['image'];

    echo '<div class="col-md-3 col-sm-6 mb-4">';
    echo '<div class="card">';
    echo '<img src="' . htmlspecialchars($itemImage) . '" class="card-img-top" alt="' . htmlspecialchars($itemName) . '">';
    echo '<div class="card-body">';
    echo '<h5 class="card-title text-white text-center" style="background-color: #8e8e8e21;">' . htmlspecialchars($itemName) . '</h5>';
    
    // Equip button if applicable
    if (in_array($type, array('weapon', 'armor', 'shoes')) || in_array($subtype, array('weapon', 'armor', 'shoes'))) {
        echo '<a href="equip.php?eq=' . (!empty($subtype) ? $subtype : $type) . '&id=' . $item['id'];
        if ($item['loaned']) {
            echo '&loaned=1';
        }
        echo '" class="btn btn-primary btn-sm d-block mt-2">Equip</a>';
    }

    echo '</div></div></div>';
}
?>

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
