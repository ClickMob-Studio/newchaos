<?php
require_once "header.php";

function getInventoryItems() {
    global $db, $user_class;
    
    // Query to join the inventory and items tables
    $db->query("
        SELECT 
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

// Function to categorize items based on their attributes
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

// Fetch inventory items
$items = getInventoryItems(); 
?>

<div class="inventory-container">
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php 
                $currentType = null;  // To track the current item type
                foreach ($items as $item): 
                    // Categorize the item based on its attributes
                    $itemType = categorizeItem($item);
                    
                    // If the type has changed, output a new row with the item type as a header
                    if ($itemType !== $currentType): 
                        $currentType = $itemType;
                ?>
                    <tr>
                        <td colspan="5" class="item-type-header"><?= htmlspecialchars($currentType); ?></td>
                    </tr>
                <?php endif; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td><img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="item-image"></td>
                        <td><?= (int)$item['quantity']; ?></td>
                        <td><?= htmlspecialchars($itemType); ?></td>
                        <td>
                            <button class="use-btn">Use</button>
                            <button class="drop-btn">Drop</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>

<style>
.inventory-container {
    width: 80%;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.item-type-header {
    background-color: #f0f0f0;
    font-weight: bold;
    text-align: center;
    padding: 10px;
    font-size: 1.2em;
    border-bottom: 2px solid #ddd;
}

.item-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
}

.use-btn, .drop-btn {
    padding: 5px 10px;
    margin-right: 5px;
    cursor: pointer;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
}

.drop-btn {
    background-color: #f44336;
}
</style>
