<?php
require_once "header.php";

function getInventoryItems() {
    global $db, $user_class;

    // Fetching inventory items with relevant fields
    $db->query("
        SELECT 
            i.id, 
            i.itemname AS name, i.cost as cost,
            i.offense, i.defense, i.speed, i.rare, i.awake_boost,
            inv.quantity, 
            i.image 
        FROM inventory inv 
        JOIN items i ON inv.itemid = i.id 
        WHERE inv.userid = :user_id
        ORDER BY i.itemname
    ");
  
    $db->bind(':user_id', $user_class->id);  
    return $db->fetch_row();  // Ensure this returns valid rows or empty array
}

function categorizeItem($row) {
    if ($row['rare'] == 1) {
        $type = 'rare';
    } elseif ($row['offense'] > 0) {
        $type = 'weapon';
    } elseif ($row['defense'] > 0) {
        $type = 'armor';
    } elseif ($row['speed'] > 0) {
        $type = 'shoes';
    } elseif ($row['awake_boost'] > 0) {
        $type = 'house';
    } else {
        $type = 'consumable';
    }

    return $type;
}

$items = getInventoryItems();

$restrictedSendItems = array(155, 195, 156, 157, 194, 158, 159, 165, 167, 256);
$restrictedDropItems = array(155, 195, 157, 194, 156, 158, 159, 167, 256);
$restrictedUseItems = array(69, 155, 195, 156, 157, 194, 158, 159, 165, 167);
$multiUseItems = array(251, 10, 163, 256);  // Items allowing multiple uses
$groupedItems = array();
foreach ($items as $item) {
    $itemType = categorizeItem($item);
    $groupedItems[$itemType][] = $item;
}
?>

<div id="message" style="display: none; padding: 10px; background-color: #4CAF50; color: white; margin-bottom: 20px;"></div>
<div class="equipped-items-container">
    <h2>Equipped Items</h2>
    <div class="equipped-items">
        <div class="equipped-item" data-type="weapon">
            <h3>Weapon</h3>
        </div>
        <div class="equipped-item" data-type="armor">
            <h3>Armor</h3>
        </div>
        <div class="equipped-item" data-type="shoes">
            <h3>Shoes</h3>
        </div>
    </div>
</div>

<div class="inventory-container">
    <?php if (!empty($groupedItems)): ?>
        <?php foreach ($groupedItems as $type => $items): ?>
            <div class="inventory-group">
                <h2 class="item-type-header"><?= htmlspecialchars(ucfirst($type)); ?></h2>
                <div class="inventory-items">
                    <?php foreach ($items as $item): ?>
                        <div class="inventory-item">
                            <div class="item-image-container">
                                <img src="<?= isset($item['image']) && $item['image'] != '' ? htmlspecialchars($item['image']) : 'path/to/default-image.png'; ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="item-image">
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['name']); ?></h3>
                                <p>Quantity: <span class="item-quantity"><?= (int)$item['quantity']; ?></span></p>

                                <?php
                                $equipItems = array(68,69,229, 230,231,250,252, 255, 264);
                                // Equip button logic for items like weapons, armor, or shoes
                                if (in_array($type, ['weapon', 'armor', 'shoes']) || in_array($item['id'], $equipItems)) {
                                    $loanStatus = isset($item['loanid']) && $item['loanid'] > 0 ? 1 : 0;
                                    echo '<button class="equip-btn" data-item-id="' . $item['id'] . '" data-type="' . $type . '" data-loaned="' . $loanStatus . '">Equip</button>';
                                } elseif ($type == 'consumable' || $type == "rare" && !in_array($item['id'], $restrictedUseItems)) {
                                    if (in_array($item['id'], $multiUseItems)) {
                                        echo '<button class="use-btn-multi" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($item['name']) . '" data-item-quantity="' . (int)$item['quantity'] . '">Use Multiple</button>';
                                    } else {
                                        echo '<button class="use-btn" data-item-id="' . $item['id'] . '" data-item-name="' . htmlspecialchars($item['name']) . '">Use</button>';
                                    }
                                }
                                if ($item['id'] == 194) {
                                    echo ' <a class="button-sm" href="raids.php">Use Speedup</a> ';
                                }
                                if (!in_array($item['id'], $restrictedDropItems)) {
                                    echo ' <button class="drop-btn" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '">Drop</button> ';
                                }
                                if (!in_array($item['id'], $restrictedSendItems)) {
                                    echo ' <button class="send-btn" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '" data-item-name="' . htmlspecialchars($item['name']) . '">Send</button> ';
                                }
                                if ($type == 'house') {
                                    echo ' <a class="button-sm" href="market.php?item=' . $item['id'] . '">Market</a> ';
                                }
                                if ($item['cost'] > 0) {
                                    echo ' <a class="button-sm" href="sellitem.php?id=' . $item['id'] . '">Sell</a> ';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No items found.</p>
    <?php endif; ?>
</div>

<!-- Modal for Dropping Items -->
<div id="dropModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Drop Item</h2>
        <form id="dropForm">
            <p>Dropping <strong id="drop-item-name"></strong></p>
            <input type="hidden" name="item_id" id="drop-item-id">
            
            <label for="drop-quantity">Quantity to drop:</label>
            <input type="number" id="drop-quantity" name="quantity" min="1" value="1">
            
            <button type="submit" class="drop-confirm-btn">Drop Item</button>
        </form>
    </div>
</div>

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


<?php include 'footer.php'; ?>

<!-- JavaScript for handling item usage, equip, drop, and send functionality -->
<script>
// Equip and Unequip functions
document.addEventListener('DOMContentLoaded', function () {
    loadEquippedItems();

    // Attach event listeners to equip buttons
    attachEquipListeners();

    // Attach event listeners for opening modals
    attachModalListeners();

    // Attach event listeners to use buttons
    attachUseListeners();
});

// Function to load equipped items on page load
function loadEquippedItems() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_equip.php?action=load', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                updateEquippedItem('weapon', response.equippedItems.weapon);
                updateEquippedItem('armor', response.equippedItems.armor);
                updateEquippedItem('shoes', response.equippedItems.shoes);

                // After loading equipped items, attach event listeners to the unequip buttons
                attachUnequipListeners();
            }
        }
    };
    xhr.send();
}

// Attach event listeners to equip buttons
function attachEquipListeners() {
    var equipButtons = document.querySelectorAll('.equip-btn');
    equipButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var itemId = this.getAttribute('data-item-id');
            var type = this.getAttribute('data-type');
            var loaned = this.getAttribute('data-loaned');
            equipItem(itemId, type, loaned);
        });
    });
}

// Attach event listeners to unequip buttons
function attachUnequipListeners() {
    document.querySelectorAll('.unequip-btn').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            var itemType = this.getAttribute('data-type');
            unequipItem(itemType);
        });
    });
}

// Attach event listeners to use buttons
// Function to attach event listeners to use buttons
function attachUseListeners() {
    var useButtons = document.querySelectorAll('.use-btn');
    useButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var itemId = this.getAttribute('data-item-id');
            var itemName = this.getAttribute('data-item-name');
            var itemQuantity = this.closest('.inventory-item').getAttribute('data-quantity');
            openUseMultiModal(itemId, itemName, itemQuantity);
        });
    });
}
;
}

// Function to handle equipping an item
function equipItem(itemId, type, loaned = 0) {
    var url = 'ajax_equip.php?eq=' + type + '&id=' + itemId + '&loaned=' + loaned;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var messageDiv = document.getElementById('message');
            if (response.success) {
                // Show success message
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#4CAF50';
                messageDiv.textContent = response.message;

                // Update the equipped item slot with the new item
                if (response.newItemHtml && response.slot) {
                    updateEquippedItem(response.slot, response.newItemHtml);
                }

                // Remove the item from the inventory (if necessary)
                removeFromInventory(itemId);

                // Re-attach event listeners after equipping an item
                attachUnequipListeners();
            } else {
                // Show error message
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#f44336';
                messageDiv.textContent = 'Error: ' + response.message;
            }

            setTimeout(function () {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    };
    xhr.send();
}

// Function to handle unequipping an item
function unequipItem(itemType) {
    var url = 'ajax_equip.php?unequip=' + itemType;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var messageDiv = document.getElementById('message');

            if (response.success) {
                // Show success message
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#4CAF50';
                messageDiv.textContent = response.message;

                // Update the equipped item slot with the new (empty) HTML
                if (response.newItemHtml) {
                    updateEquippedItem(itemType, response.newItemHtml);
                }

                // Add the unequipped item back to the inventory
                addToInventory(response.itemData);
            } else {
                // Show error message
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#f44336';
                messageDiv.textContent = 'Error: ' + response.message;
            }

            setTimeout(function () {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    };
    xhr.send();
}

// Function to handle using an item
function useItem(itemId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_use_item.php?use=' + itemId, true); // Make sure the URL points to your use item script
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText); // Parse the JSON response
            var messageDiv = document.getElementById('message'); // Assuming you have a message div

            if (response.success) {
                // Show success message
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#4CAF50';
                messageDiv.innerHTML = response.message; // Display the message
            } else {
                // Show error message
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#f44336';
                messageDiv.innerHTML = response.message; // Display the error message
            }

            // Scroll to the message div
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Hide the message after 5 seconds
            setTimeout(function () {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    };
    xhr.send();
}

function openUseMultiModal(itemId, itemName, itemQuantity) {
    var useModal = document.getElementById("useMultiModal");
    document.getElementById('use-item-id').value = itemId;
    document.getElementById('use-item-name').textContent = itemName;
    document.getElementById('use-quantity').max = itemQuantity;
    useModal.style.display = "block";
}


// Function to handle using multiple items
document.getElementById("useMultiForm").addEventListener('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax_use_multi_item.php", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var messageDiv = document.getElementById('message');
            messageDiv.textContent = response.message;
            messageDiv.style.display = 'block';
            document.getElementById("useMultiModal").style.display = "none";

            if (response.success) {
                var itemElement = document.querySelector('.inventory-item[data-item-id="' + formData.get('item_id') + '"]');
                var newQuantity = itemElement.getAttribute('data-quantity') - formData.get('quantity');
                itemElement.setAttribute('data-quantity', newQuantity);
                itemElement.querySelector('.item-quantity').textContent = newQuantity;
            }

            setTimeout(function () {
                messageDiv.style.display = 'none';
            }, 5000);

            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };
    xhr.send(formData);
});



// Function to update the equipped item slot with new HTML
function updateEquippedItem(type, newItemHtml) {
    var equippedItemContainer = document.querySelector('.equipped-item[data-type="' + type + '"]');
    if (equippedItemContainer) {
        equippedItemContainer.innerHTML = newItemHtml;
    }
}

// Function to remove the item from the inventory after equipping
function removeFromInventory(itemId) {
    var inventoryItem = document.querySelector('.inventory-item[data-item-id="' + itemId + '"]');
    if (inventoryItem) {
        var quantityElement = inventoryItem.querySelector('.item-quantity');
        var currentQuantity = parseInt(quantityElement.textContent);

        if (currentQuantity > 1) {
            quantityElement.textContent = currentQuantity - 1;
        } else {
            inventoryItem.remove();
        }
    }
}

// Function to add the item back to the inventory after unequipping
function addToInventory(itemData) {
    var inventoryContainer = document.querySelector('.inventory-items');
    var newItem = document.createElement('div');
    newItem.classList.add('inventory-item');
    newItem.setAttribute('data-item-id', itemData.id);

    // Example HTML for the new item (you may need to adjust this based on your design)
    newItem.innerHTML = `
        <div class="item-image-container">
            <img src="${itemData.image}" alt="${itemData.name}" class="item-image">
        </div>
        <div class="item-details">
            <h3>${itemData.name}</h3>
            <p>Quantity: <span class="item-quantity">${itemData.quantity}</span></p>
            <button class="equip-btn" data-item-id="${itemData.id}" data-type="${itemData.type}">Equip</button>
        </div>
    `;

    // Append the item back to the inventory
    inventoryContainer.appendChild(newItem);

    // Re-attach event listener for the new equip button
    attachEquipListeners();
}

// Attach modal event listeners for Drop and Send modals
function attachModalListeners() {
    var dropModal = document.getElementById("dropModal");
    var sendModal = document.getElementById("sendModal");

    // Event listeners for opening modals
    document.querySelectorAll('.drop-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var itemId = this.getAttribute('data-item-id');
            var itemName = this.getAttribute('data-item-name');
            var itemQuantity = this.getAttribute('data-item-quantity');
            document.getElementById('drop-item-id').value = itemId;
            document.getElementById('drop-item-name').textContent = itemName;
            document.getElementById('drop-quantity').max = itemQuantity;
            document.getElementById('drop-quantity').value = 1;
            dropModal.style.display = "block";
        });
    });

    document.querySelectorAll('.send-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var itemId = this.getAttribute('data-item-id');
            var itemName = this.getAttribute('data-item-name');
            var itemQuantity = this.getAttribute('data-item-quantity');
            document.getElementById('item-id').value = itemId;
            document.getElementById('item-name').textContent = itemName;
            document.getElementById('quantity').max = itemQuantity;
            document.getElementById('quantity').value = 1;
            sendModal.style.display = "block";
        });
    });

    // Close modal when clicking on the close button
    document.querySelectorAll('.close').forEach(function (span) {
        span.onclick = function () {
            dropModal.style.display = "none";
            sendModal.style.display = "none";
        };
    });
}

// Form submission logic for Drop and Send modals
document.getElementById("dropForm").addEventListener('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "drop_item.php", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var messageDiv = document.getElementById('message');
            messageDiv.textContent = xhr.responseText;
            messageDiv.style.display = 'block';
            document.getElementById("dropModal").style.display = "none";
            setTimeout(function () {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    };
    xhr.send(formData);
});

document.getElementById("sendForm").addEventListener('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "send_item.php", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var messageDiv = document.getElementById('message');
            messageDiv.textContent = xhr.responseText;
            messageDiv.style.display = 'block';
            document.getElementById("sendModal").style.display = "none";
            setTimeout(function () {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    };
    xhr.send(formData);
});
</script>

<style>
/* Global container for the entire inventory */
.inventory-container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
}

/* Group of items per category (Weapons, Armor, etc.) */
.inventory-group {
    background-color: #21201c;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Header for each item type (Weapons, Armor, etc.) */
.item-type-header {
    background-color: #33312e;
    color: white;
    padding: 10px;
    font-size: 1.5em;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}

/* Flex container for items inside each type group */
.inventory-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Individual item container */
.inventory-item {
    background-color: #2d2c28;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: calc(33.333% - 20px); /* 3 items per row on desktop */
}

/* Container for item image */
.item-image-container {
    width: 100%;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

/* Item image style */
.item-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

/* Details for each item (name, quantity, etc.) */
.item-details h3 {
    color: white;
    font-size: 1.2em;
    margin-bottom: 10px;
}

.item-details p {
    color: #aaa;
    margin-bottom: 10px;
}

/* Button container to prevent overlapping */
.item-button-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
}

/* Button styles for use and drop actions */
.use-btn, .drop-btn, .send-btn {
    padding: 5px 10px;
    cursor: pointer;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    margin-top: 10px;  /* Add margin to avoid overlap */
}

.drop-btn {
    background-color: #f44336;
}

.send-btn {
    background-color: #ffa500;
}

.use-btn:hover {
    background-color: #45a049;
}

.drop-btn:hover {
    background-color: #d32f2f;
}

.send-btn:hover {
    background-color: #ff8c00;
}

/* Responsive design for tablets */
@media screen and (max-width: 768px) {
    .inventory-item {
        width: calc(50% - 20px); /* 2 items per row on tablets */
    }
}

/* Responsive design for mobile */
@media screen and (max-width: 480px) {
    .inventory-item {
        width: calc(50% - 10px); /* 2 items per row on mobile */
        margin-bottom: 10px;
    }

    .inventory-items {
        gap: 10px; /* Adjust gap for better spacing on smaller screens */
    }

    /* Buttons inside each item are centered and have enough space */
    .item-button-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
}

/* Full width if there's only one item in the row */
@media screen and (max-width: 480px) {
    .inventory-item:nth-child(odd):last-child {
        width: 100%;
    }
}

/* The Modal (background) */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #21201c;
    margin: auto;
    padding: 20px;
    border-radius: 8px;
    width: 40%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus {
    color: #fff;
    cursor: pointer;
}

/* Send Confirmation Button */
.send-confirm-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 20px;
}

.send-confirm-btn:hover {
    background-color: #45a049;
}

.equipped-items-container {
    background-color: #21201c;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}

.equipped-items {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.equipped-item {
    background-color: #2d2c28;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: calc(33.333% - 20px); /* 3 items per row */
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.equipped-item h3 {
    color: white;
    margin-bottom: 10px;
}

.equipped-item img {
    border-radius: 8px;
    max-width: 100px;
    height: 100px;
    object-fit: cover;
}

.button-sm {
    padding: 5px 10px;
    background-color: #FFA500;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
}

.button-sm:hover {
    background-color: #FF8C00;
}
</style>
