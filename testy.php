<?php
include 'header.php';

if (isset($_GET['exchangetoken'])) {
    if ($user_class->donate_token > 0) {
        $db->query("UPDATE grpgusers SET donate_token = donate_token - 1, points = points + 15000 WHERE id = ?");
        $db->execute(
            array(
                $user_class->id,
            )
        );
        $message = "You have exchanged a " . item_popup('Donation Boost Token', 156) . " for 15,000 Points";
        Send_Event($user_class->id, "You have exchanged a Donation Boost Token for 15,000 Points.", $user_class->id);
        diefun($message);
    } else {
        diefun('Sorry you do not have any tokens to exchange');
    }
}
// Define restricted and multi-use item arrays
$restrictedUseItems = array(68, 69, 155, 195, 156, 157, 194, 158, 159, 165, 167, 285);
$restrictedSendItems = array(155, 195, 156, 157, 194, 158, 159, 165, 167, 256);
$restrictedDropItems = array(155, 195, 157, 194, 156, 158, 159, 167, 256);
$multiUseItems = array(252, 253, 42, 10, 163, 256, 283, 251, 288, 289);  // Items allowing multiple uses

if ($user_class->gang > 0) {
    $tempItemUse = getItemTempUse($user_class->id);
    $now = time();
    if ($tempItemUse['gang_double_exp_hours'] > 0 && $tempItemUse['gang_double_exp_time'] < $now) {
        echo '
            <hr />
            <center>
             <a href="trigger_doublexp_hour.php" onclick="return confirm(\'Are you sure you want to trigger double EXP?\');"><font color=red>You have ' . $tempItemUse['gang_double_exp_hours'] . ' hours of double EXP! Click to run 1 hour of double exp.</font></a>
            </center>
            <hr />
        ';
    }
}
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
                echo '<img class="card-img-top" style="max-width: 120px; max-height: 120px; margin: auto;" src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<h6 class="card-title text-white">' . htmlspecialchars($item['name']) . '</h6>';
                echo '<button class="btn btn-sm btn-warning unequip-btn mt-2" data-type="' . $type . '" data-id="' . $item['id'] . '">Unequip</button>';
            } else {
                echo '<img class="card-img-top" style="max-width: 120px; max-height: 120px; margin: auto;" src="/css/images/empty.jpg" alt="Empty Slot">';
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
    <?php if ($user_class->donate_token > 0) {
    echo '<div class="flexcont" border = "thick solid #0000FF"; style="text-align:center;position: relative;flex-flow:row wrap;">';
    echo image_popup('css/newgame/items/donate_boost.png', 156) . '<br/>';
    echo '<span class="text-14">x' . $user_class->donate_token . '</span><br/>';
    echo '<a class="text-14 text-yellow" href="store.php">Boost Donation</a><br/><br/>';
    echo '<a class="text-14 text-yellow" href="inventory.php?exchangetoken">Exchange x1 for 15,000 Points</a>
    </div>';
}
?>
    <?php
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

        if (isset($item['type']) && $item['category'] == 'crafting') {
            $categorizedItems['gem'][] = $item;
        }elseif ($item['type'] == 'Gems') {
                $categorizedItems['consumable'][] = $item;
        } elseif ($itemType === 'rare') {
            $categorizedItems['rare'][] = $item;

        } elseif ($item['type'] == 'booster') {
            $categorizedItems['booster'][] = $item;
        }else {
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
            $sell = ($item['cost'] > 0) 
            ? "<a class='button-sm btn btn-sm btn-secondary mt-2' href='sellitem.php?id=" . $item['id'] . "'>Sell</a>"
            : "";
            list($itemType, $itemSubtype) = getItemType($item);
            $showEquipButton = in_array($itemType, array('weapon', 'armor', 'shoes')) || in_array($itemSubtype, array('weapon', 'armor', 'shoes'));
            $dataType = $itemSubtype ?: $itemType;

            echo '<div class="col-6 col-md-4 col-lg-3 mb-3">';
            echo '<div class="card shadow-sm h-100">';
            echo '<img class="card-img-top" style="max-width: 120px; max-height: 120px; margin: auto;" src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '">';
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
            if (!$loan && !in_array($item['id'], [155, 195, 156, 194, 157, 158, 159, 165, 167, 256])) {
                $buttonHtml .= '<a class="btn btn-sm btn-primary mt-2" href="putonmarket.php?id=' . $item['id'] . '">Market</a> ';
            }
            

          
                if ($itemType == 'consumable' || ($itemType == "rare" && !in_array($item['id'], $restrictedUseItems) && $item['category'] != 'crafting')) {
                    // Multi-use items
                    if (in_array($item['id'], $multiUseItems)) {
                        $buttonHtml .= '<button class="use-btn-multi btn btn-sm btn-primary mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '" data-item-quantity="' . (int)$item['quantity'] . '">Use Multiple</button>';
                    } 
                    // Single-use consumables
                    elseif (!in_array($item['id'], [285, 155, 195, 156, 157, 194, 158, 159, 165, 167])) {
                        $buttonHtml .= '<button class="use-btn btn btn-sm btn-primary mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '" data-item-quantity="' . (int)$item['quantity'] . '">Use</button>';
                    }
                }
            
            


            if (!in_array($item['id'], $restrictedSendItems)) {
                $buttonHtml .= '<button class="btn btn-sm btn-info send-btn mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '" data-item-quantity="' . (int)$item['quantity'] . '">Send</button> ';
            }
            $buttonHtml .= $sell;
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

    <!-- Modal for Sending Items -->
    <div id="sendModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Send Item</h2>
            <form id="sendForm">
                <p>Sending <strong id="item-name"></strong></p>
                <input type="hidden" name="item_id" id="item-id">
                <label for="recipient">Recipient Username/ID:</label>
                <input type="text" id="recipient" name="recipient" required>
                <label for="quantity">Quantity to send:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1">
                <button type="submit" class="send-confirm-btn">Send Item</button>
            </form>
        </div>
    </div>

    <!-- Modal for Using Multiple Items -->
    <div id="useMultiModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Use Multiple Items</h2>
            <form id="useMultiForm">
                <input type="hidden" name="item_id" id="use-item-id">
                <p>Using <strong id="use-item-name"></strong></p>
                <label for="use-quantity">Quantity to use:</label>
                <input type="number" id="use-quantity" name="quantity" min="1" value="1">
                <button type="submit" class="use-confirm-btn">Use Item(s)</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script>
        function showMessage(message, isSuccess) {
            var messageBox = $("#messageBox");
            messageBox
                .text(message)
                .removeClass("alert-success alert-danger")
                .addClass(isSuccess ? "alert-success" : "alert-danger")
                .fadeIn();
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            setTimeout(function() { messageBox.fadeOut(); }, 3000);
        }

        function updateEquippedItem(type, itemId, itemName, itemImage) {
            const equippedItemContainer = document.querySelector(`.equipped-${type}`);
            if (equippedItemContainer) {
                equippedItemContainer.innerHTML = `
                    <img class="card-img-top" style="max-width: 120px; max-height: 120px; margin: auto;"
src="${itemImage}" alt="${itemName}">
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

        // Modal control
        $(document).on('click', '.send-btn', function () {
            var itemId = $(this).data('item-id');
            var itemName = $(this).data('item-name');
            var itemQuantity = $(this).data('item-quantity');
            $("#sendModal").show();
            $("#item-id").val(itemId);
            $("#item-name").text(itemName);
            $("#quantity").attr("max", itemQuantity);
            $("#quantity").val(1);
        });

        $(document).on('click', '.use-btn', function () {
    var itemId = $(this).data('item-id'); // Extract item ID from data attribute

    if (!itemId) {
        showMessage("Invalid item selected.", false); // Prevent unnecessary AJAX call
        return;
    }

    $.ajax({
        url: 'ajax_use_item.php', // Endpoint for item usage
        type: 'GET', // HTTP method
        dataType: 'json', // Expect JSON response
        data: { use: itemId }, // Send item ID
        success: function (response) {
            if (response.success) {
                showMessage(response.message, true); // Show success message
                // Optional: Update UI to reflect the changes, e.g., reduce item quantity, update HP display, etc.
                updateHP(response.newHP); // Example for updating HP display
                updateHospitalTime(response.newHospitalTime); // Example for updating hospital time
            } else {
                showMessage(response.message || "An unknown error occurred.", false); // Show error from response
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown); // Log error for debugging
            showMessage("Error using the item. Please try again later.", false); // Display a generic error
        }
    });
});


        $(document).on('click', '.use-btn-multi', function () {
            var itemId = $(this).data('item-id');
            var itemName = $(this).data('item-name');
            var itemQuantity = $(this).data('item-quantity');
            $("#useMultiModal").show();
            $("#use-item-id").val(itemId);
            $("#use-item-name").text(itemName);
            $("#use-quantity").attr("max", itemQuantity);
            $("#use-quantity").val(1);
        });

        $(".close").on('click', function () {
            $(this).closest(".modal").hide();
        });

        $("#useMultiForm").on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'ajax_use_multi_item.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    showMessage(response.message, response.success);
                    $("#useMultiModal").hide();
                },
                error: function() {
                    showMessage("Error processing the request.", false);
                }
            });
        });

        $("#sendForm").on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'send_item.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    showMessage(response.message, response.success);
                    $("#sendModal").hide();
                },
                error: function() {
                    showMessage("Error processing the request.", false);
                }
            });
        });
    </script>
</div>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        top: 25%;
        left: 25%;
        width: 100%;
        height: 100%;
        overflow: auto;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #000;
        color: #fff;
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
        color: #fff;
    }
</style>
