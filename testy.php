<?php
include 'header.php';

// Define restricted and multi-use item arrays
$restrictedUseItems = array(68,69, 155, 195, 156, 157, 194, 158, 159, 165, 167, 285);
$restrictedSendItems = array(155, 195, 156, 157, 194, 158, 159, 165, 167, 256);
$restrictedDropItems = array(155, 195, 157, 194, 156, 158, 159, 167, 256);
$multiUseItems = array(251, 253, 42, 10, 163, 256);  // Items allowing multiple uses
?>

<div class="container-fluid my-4">
    <div id="messageBox" class="alert" style="display: none;"></div>

    <h1 class="text-center mb-4">Equipped Items</h1>
    <div class="row text-center g-3">
        <?php
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
            echo '<div class="col-6 col-md-4 mb-3 equipped-' . $type . '">';
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
        global $restrictedSendItems, $multiUseItems, $restrictedUseItems, $loan;
    
        if (empty($items)) return;

        echo '<div class="card my-4 category-card">';
        echo '<div class="card-header text-white text-center" style="background-color: #8e8e8e21;">';
        echo "<h2 class='text-white'>$categoryName</h2>";
        echo '</div>';
        echo '<div class="card-body">';
        echo '<div class="row g-3 text-center">';
    
        foreach ($items as $item) {
            $itemName = !empty($item['overridename']) ? $item['overridename'] : $item['itemname'];
            $itemImage = !empty($item['overrideimage']) ? $item['overrideimage'] : $item['image'];
            $buttonHtml = '';
    
            list($itemType, $itemSubtype) = getItemType($item);
            $showEquipButton = in_array($itemType, array('weapon', 'armor', 'shoes')) || in_array($itemSubtype, array('weapon', 'armor', 'shoes'));
            $dataType = $itemSubtype ?: $itemType;
    
            echo '<div class="col-6 col-md-4 col-lg-3 mb-3">';
            echo '<div class="card shadow-sm h-100">';
            echo '<img class="card-img-top" src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '">';
            echo '<div class="card-body d-flex flex-column">';
            echo '<h6 class="card-title text-white">' . htmlspecialchars($itemName) . '</h6>';
            echo 'x ' . $item['quantity'];
    
            if ($showEquipButton) {
                $buttonHtml .= '<button class="btn btn-sm btn-primary equip-btn mt-2" data-type="' . $dataType . '" data-id="' . intval($item['itemid']) . '" data-name="' . htmlspecialchars($itemName) . '" data-img="' . htmlspecialchars($itemImage) . '">Equip</button>';
            }

            // Special buttons based on item ID
            switch ($item['id']) {
                case 155:
                    $buttonHtml .= '<a class="btn btn-sm btn-info mt-2" href="inventory.php?use=' . $item['id'] . '">Share The Love</a> ';
                    break;
                case 194:
                    $buttonHtml .= '<a class="btn btn-sm btn-success mt-2" href="raids.php">Use Speedup</a> ';
                    break;
                case 195:
                    $buttonHtml .= '<a class="btn btn-sm btn-warning mt-2" href="inventory.php?use=' . $item['id'] . '">Trick Or Treat</a> ';
                    break;
                case 156:
                    $buttonHtml .= '<a class="btn btn-sm btn-secondary mt-2" href="inventory.php?use=' . $item['id'] . '">Share</a> ';
                    break;
                case 157:
                    $buttonHtml .= '<a class="btn btn-sm btn-danger mt-2" href="inventory.php?use=' . $item['id'] . '">Send Egg</a> ';
                    break;
                case 158:
                    $buttonHtml .= '<a class="btn btn-sm btn-success mt-2" href="inventory.php?use=' . $item['id'] . '">Independence!</a> ';
                    break;
                case 159:
                    $buttonHtml .= '<a class="btn btn-sm btn-danger mt-2" href="inventory.php?use=' . $item['id'] . '">Send Rayz</a> ';
                    break;
                case 165:
                    $buttonHtml .= '<a class="btn btn-sm btn-dark mt-2" href="inventory.php?use=' . $item['id'] . '">Send Ghosts</a> ';
                    break;
                case 167:
                    $buttonHtml .= '<a class="btn btn-sm btn-info mt-2" href="inventory.php?use=' . $item['id'] . '">Send Christmas Present</a> ';
                    break;
            }
    
            // Market button
            if (!$loan && !in_array($item['id'], $restrictedDropItems)) {
                $buttonHtml .= '<a class="btn btn-sm btn-warning mt-2" href="putonmarket.php?id=' . $item['id'] . '">Market</a> ';
            }
    
            if ($itemType == 'consumable' || ($itemType == "rare" && !in_array($item['id'], $restrictedUseItems))) {
                if (in_array($item['id'], $multiUseItems)) {
                    $buttonHtml .= '<button class="use-btn-multi btn btn-sm btn-primary mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '" data-item-quantity="' . (int)$item['quantity'] . '">Use Multiple</button>';
                } else {
                    $buttonHtml .= '<button class="use-btn btn btn-sm btn-primary mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '">Use</button>';
                }
            }
    
            if (!in_array($item['id'], $restrictedSendItems)) {
                $buttonHtml .= '<button class="btn btn-sm btn-info send-btn mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '" data-item-quantity="' . (int)$item['quantity'] . '">Send</button> ';
            }

            echo $buttonHtml;
            echo '</div></div></div>';
        }
    
        echo '</div></div></div>';
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

        function updateEquippedItem(type, itemId, itemName, itemImage) {
            const equippedItemContainer = document.querySelector(`.equipped-${type}`);
            if (equippedItemContainer) {
                equippedItemContainer.innerHTML = `
                    <img class="card-img-top" src="${itemImage}" alt="${itemName}">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-white">${itemName}</h6>
                        <button class="btn btn-sm btn-warning unequip-btn mt-2" data-type="${type}" data-id="${itemId}">Unequip</button>
                    </div>
                `;
            }
        }

        $(document).on('click', '.equip-btn', function () {
            var type = $(this).data('type');
            var itemId = $(this).data('id');
            var itemName = $(this).data('name');
            var itemImage = $(this).data('img');

            $.ajax({
                url: 'equip_action.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'equip', type: type, item_id: itemId },
                success: function (response) {
                    if (response.status === 'success') {
                        showMessage(response.message, true);
                        updateEquippedItem(type, itemId, itemName, itemImage);
                    } else {
                        showMessage(response.message, false);
                    }
                },
                error: function () {
                    showMessage("Error processing the request.", false);
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
                        updateEquippedItem(type, 0, "Empty Slot", "/css/images/empty.jpg");
                    } else {
                        showMessage(response.message, false);
                    }
                },
                error: function () {
                    showMessage("Error processing the request.", false);
                }
            });
        });

        $(document).on('click', '.use-btn', function () {
            var itemId = $(this).data('item-id');

            $.ajax({
                url: 'ajax_use_item.php',
                type: 'GET',
                dataType: 'json',
                data: { use: itemId },
                success: function (response) {
                    showMessage(response.message, response.success);
                },
                error: function () {
                    showMessage("Error using the item.", false);
                }
            });
        });
    </script>
</div>

<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #000; /* Black background */
        color: #fff; /* White text for contrast */
        padding: 20px;
        border-radius: 5px;
        width: 50%;
        max-width: 500px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .close {
        float: right;
        font-size: 1.5rem;
        cursor: pointer;
        color: #fff; /* Close button in white for visibility */
    }
    .category-card {
        display: block;
    }
</style>