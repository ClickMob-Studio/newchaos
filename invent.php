<?php
require_once "header.php";

function getInventoryItems() {
    global $db, $user_class;
    
    $db->query("
        SELECT 
            i.itemname AS name, 
            IF(i.type = '' OR i.type IS NULL, 'Misc', i.type) AS type, 
            inv.quantity, 
            i.image 
        FROM inventory inv 
        JOIN items i ON inv.itemid = i.id 
        WHERE inv.userid = :user_id
        ORDER BY type, i.itemname
    ");
  
    $db->bind(':user_id', $user_class->id);  
    return $db->fetch_row();
}

$items = getInventoryItems(); 
?>

<div class="inventory-container">
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php 
                $currentType = null;  
                foreach ($items as $item): 
                    
                    if ($item['type'] != $currentType): 
                        $currentType = $item['type'];
                ?>
                    <tr>
                        <td colspan="4" class="item-type-header"><?= htmlspecialchars($currentType); ?></td>
                    </tr>
                <?php endif; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td><img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="item-image"></td>
                        <td><?= (int)$item['quantity']; ?></td>
                        <td>
                            <button class="use-btn">Use</button>
                            <button class="drop-btn">Drop</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No items found.</td>
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
