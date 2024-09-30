<?php
require_once "header.php";

function getInventoryItems() {
    global $db, $user_class;
    
   
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
                                <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="item-image">
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['name']); ?></h3>
                                <p>Quantity: <?= (int)$item['quantity']; ?></p>
                                <button class="use-btn">Use</button>
                                <button class="drop-btn">Drop</button>
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

.inventory-group {
    margin-bottom: 20px;
}

.item-type-header {
    background-color: #f0f0f0;
    padding: 10px;
    font-size: 1.5em;
    border-radius: 5px;
    margin-bottom: 10px;
}

.inventory-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.inventory-item {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: calc(33.333% - 20px); /* 3 items per row */
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.item-image-container {
    width: 100%;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

.item-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.item-details h3 {
    font-size: 1.2em;
    margin-bottom: 10px;
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

/* Responsive design */
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
</style>
