<?php
include 'header.php';

// Define restricted and multi-use item arrays
$restrictedSendItems = array(155, 195, 156, 157, 194, 158, 159, 165, 167, 256);
$restrictedDropItems = array(155, 195, 157, 194, 156, 158, 159, 167, 256);
$restrictedUseItems = array(69, 155, 195, 156, 157, 194, 158, 159, 165, 167, 285);
$multiUseItems = array(251, 253, 42, 10, 163, 256);

?>

<div class="container-fluid my-4">
    <div id="messageBox" class="alert" style="display: none;"></div>

    <h1 class="text-center mb-4">Equipped Items</h1>
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

        foreach ($equippedItems as $type => $item) {
            echo '<div class="col-6 col-md-4 mb-3">';
            echo '<div class="card shadow-sm h-100">';
            if ($item['id'] != 0) {
                echo '<img class="card-img-top" src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<h6 class="card-title text-white">' . htmlspecialchars($item['name']) . '</h6>';
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

    <h1 class="text-center mt-5">Inventory</h1>

    <?php
    // Query to get all items with custom overrides for this user
    $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage 
                FROM inventory inv 
                JOIN items it ON inv.itemid = it.id 
                LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid 
                WHERE inv.userid = ?");
    $db->execute(array($user_class->id));
    $items = $db->fetch_row();

    function getItemType($row) {
        $type = '';
        $subtype = '';

        if ($row['offense'] > 0 && ($row['defense'] > 0 || $row['speed'] > 0)) {
            if ($row['offense'] > $row['defense']) {
                $type = ($row['offense'] > $row['speed']) ? 'weapon' : 'shoes';
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

    $categorizedItems = array(
        'weapon' => array(),
        'armor' => array(),
        'shoes' => array(),
        'rare' => array(),
        'house' => array(),
        'consumable' => array(),
        'gem' => array()
    );

    foreach ($items as $item) {
        list($itemType, $itemSubtype) = getItemType($item);

        if (isset($item['type']) && $item['type'] == 'Gems') {
            $categorizedItems['gem'][] = $item;
        } elseif ($itemType === 'rare') {
            $categorizedItems['rare'][] = $item;
        } elseif ($item['type'] == 'booster') {
            $categorizedItems['booster'][] = $item;
        } else {
            $categorizedItems[$itemType][] = $item;
        }
    }

    function renderCategory($categoryName, $items) {
        global $restrictedSendItems, $restrictedDropItems;

        if (empty($items)) return;

        echo '<div class="card my-4">';
        echo '<div class="card-header text-white text-center" style="background-color: #8e8e8e21;">';
        echo "<h2 class='text-white'>$categoryName</h2>";
        echo '</div>';
        echo '<div class="card-body">';
        echo '<div class="row g-3 text-center">';

        foreach ($items as $item) {
            $itemName = !empty($item['overridename']) ? $item['overridename'] : $item['itemname'];
            $itemImage = !empty($item['overrideimage']) ? $item['overrideimage'] : $item['image'];

            list($itemType, $itemSubtype) = getItemType($item);
            $showEquipButton = in_array($itemType, array('weapon', 'armor', 'shoes')) || in_array($itemSubtype, array('weapon', 'armor', 'shoes'));
            $dataType = $itemSubtype ?: $itemType;

            echo '<div class="col-6 col-md-4 col-lg-3 mb-3">';
            echo '<div class="card shadow-sm h-100">';
            echo '<img class="card-img-top" src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '">';
            echo '<div class="card-body d-flex flex-column">';
            echo '<h6 class="card-title text-white">' . htmlspecialchars($itemName) . '</h6>';
            echo 'x ' . $item['quantity'];
            
            // Equip button
            if ($showEquipButton) {
                echo '<button class="btn btn-sm btn-primary equip-btn mt-2" data-type="' . $dataType . '" data-id="' . intval($item['itemid']) . '">Equip</button>';
            }

            // Specific Use Speedup button for item id 194
            if ($item['id'] == 194) {
                echo ' <a class="btn btn-sm btn-secondary mt-2" href="raids.php">Use Speedup</a> ';
            }

            // Drop button for items not in the restricted drop list
            if (!in_array($item['id'], $restrictedDropItems)) {
                echo ' <button class="btn btn-sm btn-danger drop-btn mt-2" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '">Drop</button> ';
            }

            // Send button for items not in the restricted send list
            if (!in_array($item['id'], $restrictedSendItems)) {
                echo ' <button class="btn btn-sm btn-info send-btn mt-2" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '" data-item-name="' . htmlspecialchars($itemName) . '">Send</button> ';
            }

            // Market link for items of type house
            if ($itemType == 'house') {
                echo ' <a class="btn btn-sm btn-secondary mt-2" href="market.php?item=' . $item['id'] . '">Market</a> ';
            }

            // Sell link for items with a cost
            if ($item['cost'] > 0) {
                echo ' <a class="btn btn-sm btn-warning mt-2" href="sellitem.php?id=' . $item['id'] . '">Sell</a> ';
            }

            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    renderCategory("Weapons", $categorizedItems['weapon']);
    renderCategory("Armor", $categorizedItems['armor']);
    renderCategory("Shoes", $categorizedItems['shoes']);
    renderCategory("Boosters", $categorizedItems['booster']);
    renderCategory("Home Improvements", $categorizedItems['house']);
    renderCategory("Consumables", $categorizedItems['consumable']);
    renderCategory("Rare Items", $categorizedItems['rare']);
    renderCategory("Gems", $categorizedItems['gem']); 
   ?>

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

        setTimeout(function() { messageBox.fadeOut(); }, 3000);
    }

    $(document).on('click', '.equip-btn', function () {
        var type = $(this).data('type');
        var itemId = $(this).data('id');

        $.ajax({
            url: 'equip_action.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'equip', type: type, item_id: itemId },
            success: function (response) {
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    location.reload();
                    window.scrollTo(0, 0);
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
        var itemId = $(this).data('id');

        $.ajax({
            url: 'equip_action.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'unequip', type: type, item_id: itemId },
            success: function (response) {
                if (response.status === 'success') {
                    showMessage(response.message, true);
                    location.reload();
                    window.scrollTo(0, 0);
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
