<?php
require_once "header.php";

function getInventoryItems() {
    global $db, $user_class;
    
    $db->query("
        SELECT 
            i.id, 
            i.itemname AS name, 
            i.offense, i.defense, i.speed, i.rare, i.awake_boost,
            inv.quantity, 
            i.image 
        FROM inventory inv 
        JOIN items i ON inv.itemid = i.id 
        WHERE inv.userid = :user_id
        ORDER BY i.itemname
    ");
  
    $db->bind(':user_id', $user_class->id);  
    return $db->fetch_row();  
}

function categorizeItem($row) {
    if ($row['offense'] > 0 && $row['rare'] == 0) {
        $type = 'Weapon';
    } elseif ($row['defense'] > 0 && $row['rare'] == 0) {
        $type = 'Armor';
    } elseif ($row['speed'] > 0 && $row['rare'] == 0) {
        $type = 'Shoes';
    } elseif ($row['rare'] == 1) {
        $type = 'Rare';
        if ($row['offense'] > 0) {
            $subtype = 'Weapon';
        } elseif ($row['defense'] > 0) {
            $subtype = 'Armor';
        } elseif ($row['speed'] > 0) {
            $subtype = 'Shoes';
        }
    } elseif ($row['awake_boost'] > 0) {
        $type = 'House';
    } else {
        $type = 'Consumable';
    }

    return isset($subtype) ? $type . ' (' . $subtype . ')' : $type;
}

$items = getInventoryItems();

// List of items that can't be sent
$restrictedItems = [155, 195, 157, 194, 156, 158, 159, 167, 256];

$groupedItems = [];
foreach ($items as $item) {
    $itemType = categorizeItem($item);
    $groupedItems[$itemType][] = $item;
}
?>

<div class="inventory-container">
    <?php if (!empty($groupedItems)): ?>
        <?php foreach ($groupedItems as $type => $items): ?>
            <div class="inventory-group">
                <h2 class="item-type-header"><?= htmlspecialchars(ucfirst($type)); ?></h2>
                <div class="inventory-items">
                    <?php foreach ($items as $item): ?>
                        <div class="inventory-item">
                            <div class="item-image-container">
                                <img src="<?= htmlspecialchars($item['image']) ?: 'path/to/default-image.png'; ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="item-image">
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['name']); ?></h3>
                                <p>Quantity: <?= (int)$item['quantity']; ?></p>
                                <button class="use-btn">Use</button>
                                <button class="drop-btn">Drop</button>
                                <!-- Conditionally display the "Send" button if item is not restricted -->
                                <?php if (!in_array($item['id'], $restrictedItems)): ?>
                                    <button class="send-btn" data-item-id="<?= $item['id']; ?>" data-item-name="<?= htmlspecialchars($item['name']); ?>">Send</button>
                                <?php endif; ?>
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

<!-- Modal for Sending Items -->
<div id="sendModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Send Item</h2>
        <form id="sendForm" action="send_item.php" method="POST">
            <p>Sending <strong id="item-name"></strong></p>
            <input type="hidden" name="item_id" id="item-id">
            <label for="recipient">Recipient Username/ID:</label>
            <input type="text" id="recipient" name="recipient" required>
            <button type="submit" class="send-confirm-btn">Send Item</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<style>
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

/* Modal Content */
.modal-content {
    background-color: #21201c;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 40%;
    border-radius: 8px;
}

/* Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus {
    color: #fff;
    text-decoration: none;
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
// Modal functionality
var modal = document.getElementById("sendModal");
var span = document.getElementsByClassName("close")[0];

// Open modal when "Send" button is clicked
document.querySelectorAll('.send-btn').forEach(button => {
    button.addEventListener('click', function() {
        var itemId = this.getAttribute('data-item-id');
        var itemName = this.getAttribute('data-item-name');
        document.getElementById('item-id').value = itemId;
        document.getElementById('item-name').textContent = itemName;
        modal.style.display = "block";
    });
});

// Close modal when clicking the close button
span.onclick = function() {
    modal.style.display = "none";
}

// Close modal when clicking outside of the modal
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
