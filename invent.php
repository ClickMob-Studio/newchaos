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

    return isset($subtype) ? $type . ' (' . $subtype . ')' : $type;
}

$items = getInventoryItems();

$restrictedSendItems = array(155, 195, 156, 157, 194, 158, 159, 165, 167, 256);
$restrictedDropItems = array(155, 195, 157, 194, 156, 158, 159, 167, 256);

$groupedItems = array();
foreach ($items as $item) {
    $itemType = categorizeItem($item);
    $groupedItems[$itemType][] = $item;
}
?>

<div id="message" style="display: none; padding: 10px; background-color: #4CAF50; color: white; margin-bottom: 20px;"></div>

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
                                // Equip button logic for items like weapons, armor, or shoes
								if (in_array($type, ['weapon', 'armor', 'shoes'])) {
									$loanStatus = isset($item['loanid']) && $item['loanid'] > 0 ? 1 : 0;
									echo '<button class="equip-btn" data-item-id="' . $item['id'] . '" data-type="' . $type . '" data-loaned="' . $loanStatus . '">Equip</button>';
								} elseif ($type == 'consumable') {
                                    // Use button for consumable items
                                    echo ' <a class="button-sm" href="inventory.php?use=' . $item['id'] . '">Use</a> ';
                                }

                                // Drop button
                                if (!in_array($item['id'], $restrictedDropItems)) {
                                    echo ' <button class="drop-btn" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '">Drop</button> ';
                                }

                                // Send button (not restricted)
                                if (!in_array($item['id'], $restrictedSendItems)) {
                                    echo ' <button class="send-btn" data-item-id="' . $item['id'] . '" data-item-quantity="' . (int)$item['quantity'] . '" data-item-name="' . htmlspecialchars($item['name']) . '">Send</button> ';
                                }

                                // Sell button if item has a cost
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

            <!-- Quantity input is now always visible -->
            <label for="quantity">Quantity to send:</label>
            <input type="number" id="quantity" name="quantity" min="1" value="1">
            
            <button type="submit" class="send-confirm-btn">Send Item</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

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
    width: calc(33.333% - 20px); /* 3 items per row */
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
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

/* Button styles for use and drop actions */
.use-btn, .drop-btn, .send-btn {
    padding: 5px 10px;
    margin-right: 5px;
    cursor: pointer;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
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

/* Responsive design for smaller screens */
@media screen and (max-width: 768px) {
    .inventory-item {
        width: calc(50% - 20px); /* 2 items per row on tablets */
    }
}

@media screen and (max-width: 480px) {
    .inventory-item {
        width: 100%; /* 1 item per row on mobile */
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
</style>

<script>
// Drop Modal functionality
var dropModal = document.getElementById("dropModal");
var spanDrop = document.getElementsByClassName("close")[0];
var messageDiv = document.getElementById('message');
var currentDropItemQuantityElement = null;

// Open modal when "Drop" button is clicked
document.querySelectorAll('.drop-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        var itemId = this.getAttribute('data-item-id');
        var itemName = this.getAttribute('data-item-name');
        var itemQuantity = this.getAttribute('data-item-quantity'); // Get current quantity from the button

        currentDropItemQuantityElement = this.closest('.inventory-item').querySelector('.item-quantity'); // Store reference to quantity element
        
        document.getElementById('drop-item-id').value = itemId;
        document.getElementById('drop-item-name').textContent = itemName;

        // Set the max value of the quantity input based on the user's item quantity
        document.getElementById('drop-quantity').max = itemQuantity;
        document.getElementById('drop-quantity').value = 1; // Default quantity to 1

        dropModal.style.display = "block";
    });
});

// Close modal
spanDrop.onclick = function() {
    dropModal.style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == dropModal) {
        dropModal.style.display = "none";
    }
}

// Handle the form submission with AJAX
document.getElementById("dropForm").addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "drop_item.php", true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText;
            messageDiv.textContent = response;
            messageDiv.style.display = 'block';

            var quantityDropped = parseInt(document.getElementById('drop-quantity').value, 10);
            var currentQuantity = parseInt(currentDropItemQuantityElement.textContent, 10);

            // Update the quantity on the page
            var newQuantity = currentQuantity - quantityDropped;
            if (newQuantity <= 0) {
                currentDropItemQuantityElement.textContent = '0';
                document.querySelector('.drop-btn[data-item-id="'+ document.getElementById('drop-item-id').value +'"]').disabled = true;
            } else {
                currentDropItemQuantityElement.textContent = newQuantity;
            }

            dropModal.style.display = "none";

            setTimeout(function() {
                messageDiv.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        } else {
            messageDiv.textContent = "Error dropping item.";
            messageDiv.style.backgroundColor = '#f44336'; // Change background color to red for error
            messageDiv.style.display = 'block';
        }
    };
    
    xhr.send(formData);
});

// Send Modal functionality
var sendModal = document.getElementById("sendModal");
var spanSend = document.getElementsByClassName("close")[1];
var currentItemId = null;
var currentItemQuantityElement = null;

// Open modal when "Send" button is clicked
document.querySelectorAll('.send-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        var itemId = this.getAttribute('data-item-id');
        var itemName = this.getAttribute('data-item-name');
        var itemQuantity = this.getAttribute('data-item-quantity'); // Get current quantity from the button

        currentItemId = itemId; // Set current item ID for later use
        currentItemQuantityElement = this.closest('.inventory-item').querySelector('.item-quantity'); // Store reference to quantity element
        
        document.getElementById('item-id').value = itemId;
        document.getElementById('item-name').textContent = itemName;

        // Set the max value of the quantity input based on the user's item quantity
        document.getElementById('quantity').max = itemQuantity;
        document.getElementById('quantity').value = 1; // Default quantity to 1

        sendModal.style.display = "block";
    });
});

// Close modal
spanSend.onclick = function() {
    sendModal.style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == sendModal) {
        sendModal.style.display = "none";
    }
}

// Handle the form submission with AJAX
document.getElementById("sendForm").addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "send_item.php", true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText;
            messageDiv.textContent = response;
            messageDiv.style.display = 'block';

            var quantitySent = parseInt(document.getElementById('quantity').value, 10);
            var currentQuantity = parseInt(currentItemQuantityElement.textContent, 10);

            // Update the quantity on the page
            var newQuantity = currentQuantity - quantitySent;
            if (newQuantity <= 0) {
                currentItemQuantityElement.textContent = '0';
                document.querySelector('.send-btn[data-item-id="'+ currentItemId +'"]').disabled = true;
            } else {
                currentItemQuantityElement.textContent = newQuantity;
            }

            sendModal.style.display = "none";

            setTimeout(function() {
                messageDiv.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        } else {
            messageDiv.textContent = "Error sending item.";
            messageDiv.style.backgroundColor = '#f44336'; // Change background color to red for error
            messageDiv.style.display = 'block';
        }
    };
    
    xhr.send(formData);
});
document.addEventListener('DOMContentLoaded', function () {
    // Select all equip buttons
    var equipButtons = document.querySelectorAll('.equip-btn');

    // Add click event listener to each equip button
    equipButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var itemId = this.getAttribute('data-item-id');
            var type = this.getAttribute('data-type');
            var loaned = this.getAttribute('data-loaned');

            // Call the equipItem function with the item ID, type, and loaned status
            equipItem(itemId, type, loaned);
        });
    });
});

function equipItem(itemId, type, loaned = 0) {
    var url = 'ajax_equip.php?eq=' + type + '&id=' + itemId + '&loaned=' + loaned;

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(response.message);  // Show success message

                // Update the button to reflect that the item is equipped
                var button = document.querySelector('.equip-btn[data-item-id="' + itemId + '"]');
                if (button) {
                    button.textContent = 'Equipped';
                    button.disabled = true;  // Disable the button after equipping
                }
            } else {
                alert('Error: ' + response.message);  // Show error message
            }
        }
    };
    xhr.send();
}


</script>
