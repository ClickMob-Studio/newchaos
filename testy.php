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
    <!-- <h2 class="text-center mt-5">Inventory Filters</h2>
    <div class="text-center my-4">
        <button class="btn btn-outline-secondary filter-btn" data-category="all">All</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="weapon">Weapons</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="armor">Armor</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="shoes">Shoes</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="booster">Boosters</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="house">Home Improvements</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="consumable">Consumables</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="rare">Rare Items</button>
        <button class="btn btn-outline-secondary filter-btn" data-category="gem">Gems</button>
    </div> -->
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

        $categoryClass = strtolower(str_replace(' ', '-', $categoryName)); // e.g., "weapon", "armor"
    
        echo '<div class="card my-4 category-card category-' . $categoryClass . '">';
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
    
            // Equip button
            if ($showEquipButton) {
                $buttonHtml .= '<button class="btn btn-sm btn-primary equip-btn mt-2" data-type="' . $dataType . '" data-id="' . intval($item['itemid']) . '">Equip</button>';
            }
    
            // Custom Buttons based on Item ID
            if (in_array($item['id'], [155, 195, 156, 157, 194, 158, 159, 165, 167])) {
                switch ($item['id']) {
                    case 155:
                        $buttonHtml .= ' <a class="btn btn-sm btn-info mt-2" href="inventory.php?use=' . $item['id'] . '">Share The Love</a> ';
                        break;
                    case 194:
                        $buttonHtml .= ' <a class="btn btn-sm btn-success mt-2" href="raids.php">Use Speedup</a> ';
                        break;
                    case 195:
                        $buttonHtml .= ' <a class="btn btn-sm btn-warning mt-2" href="inventory.php?use=' . $item['id'] . '">Trick Or Treat</a> ';
                        break;
                    case 156:
                        $buttonHtml .= ' <a class="btn btn-sm btn-secondary mt-2" href="inventory.php?use=' . $item['id'] . '">Share</a> ';
                        break;
                    case 157:
                        $buttonHtml .= ' <a class="btn btn-sm btn-danger mt-2" href="inventory.php?use=' . $item['id'] . '">Send Egg</a> ';
                        break;
                    case 158:
                        $buttonHtml .= ' <a class="btn btn-sm btn-success mt-2" href="inventory.php?use=' . $item['id'] . '">Independence!</a> ';
                        break;
                    case 159:
                        $buttonHtml .= ' <a class="btn btn-sm btn-danger mt-2" href="inventory.php?use=' . $item['id'] . '">Send Rayz</a> ';
                        break;
                    case 165:
                        $buttonHtml .= ' <a class="btn btn-sm btn-dark mt-2" href="inventory.php?use=' . $item['id'] . '">Send Ghosts</a> ';
                        break;
                    case 167:
                        $buttonHtml .= ' <a class="btn btn-sm btn-info mt-2" href="inventory.php?use=' . $item['id'] . '">Send Christmas Present</a> ';
                        break;
                }
            }
    
            // Market button if no loan and item is not in restricted list
            if (!$loan && !in_array($item['id'], [155, 195, 156, 194, 157, 158, 159, 165, 167, 256])) {
                $buttonHtml .= ' <a class="btn btn-sm btn-warning mt-2" href="putonmarket.php?id=' . $item['id'] . '">Market</a> ';
            }
    
            // Use or Use Multiple buttons for consumables or eligible rare items
            if ($itemType == 'consumable' || ($itemType == "rare" && !in_array($item['id'], $restrictedUseItems))) {
                if (in_array($item['id'], $multiUseItems)) {
                    $buttonHtml .= '<button class="use-btn-multi btn btn-sm btn-primary mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '" data-item-quantity="' . (int)$item['quantity'] . '">Use Multiple</button>';
                } else {
                    $buttonHtml .= '<button class="use-btn btn btn-sm btn-primary mt-2" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($itemName) . '">Use</button>';
                }
            }
    
            // Send button for items not in the restricted send list
            if (!in_array($item['id'], $restrictedSendItems)) {
                $buttonHtml .= ' <button class="btn btn-sm btn-info send-btn mt-2" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '" data-item-name="' . htmlspecialchars($itemName) . '">Send</button> ';
            }
    
            echo $buttonHtml;
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
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const categoryCards = document.querySelectorAll('.category-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const category = button.getAttribute('data-category');

                categoryCards.forEach(card => {
                    card.style.display = (category === 'all' || card.classList.contains('category-' + category)) ? 'block' : 'none';
                });

                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    });

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

    // Send Item Modal Functionality
    document.querySelectorAll('.send-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var itemId = this.getAttribute('data-item-id');
            var itemName = this.getAttribute('data-item-name');
            var itemQuantity = this.getAttribute('data-item-quantity');
            document.getElementById('item-id').value = itemId;
            document.getElementById('item-name').textContent = itemName;
            document.getElementById('quantity').max = itemQuantity;
            document.getElementById('quantity').value = 1;
            document.getElementById('sendModal').style.display = "flex"; // Set to flex to show the modal
        });
    });

    // Close modal when 'X' is clicked
    document.querySelectorAll(".close").forEach(function (closeButton) {
        closeButton.addEventListener('click', function () {
            closeButton.closest(".modal").style.display = "none";
        });
    });

    $(document).on('click', '.use-btn', function () {
    var itemId = $(this).data('item-id');

    $.ajax({
        url: 'ajax_use_item.php',  // Make sure this points to the correct path of `ajax_use_item.php`
        type: 'GET',  // Use GET since you are using $_GET['use'] in PHP script
        dataType: 'json',
        data: { use: itemId },
        success: function (response) {
            showMessage(response.message, response.success);
            if (response.success) {
                // Optionally, refresh or update inventory UI
            }
        },
        error: function () {
            showMessage("Error using the item.", false);
        }
    });
});


    // Use Multiple Items Modal Functionality
    document.querySelectorAll('.use-btn-multi').forEach(function(button) {
        button.addEventListener('click', function() {
            var itemId = this.getAttribute('data-item-id');
            var itemName = this.getAttribute('data-item-name');
            var itemQuantity = this.getAttribute('data-item-quantity');
            document.getElementById('use-item-id').value = itemId;
            document.getElementById('use-item-name').textContent = itemName;
            document.getElementById('use-quantity').max = itemQuantity;
            document.getElementById('use-quantity').value = 1;
            document.getElementById('useMultiModal').style.display = "flex";
        });
    });  
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Submit Use Multiple Form via AJAX
    document.getElementById("useMultiForm").addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax_use_multi_item.php", true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    showMessage(response.message, response.success);
                    closeModal("useMultiModal");
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            }
        };
        xhr.send(formData);
    });
</script>
