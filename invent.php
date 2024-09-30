<?php
require_once "header.php";


function getInventoryItems() {
    global $db, $user_class;
    
    $db->query("
        SELECT i.itemname AS name, i.type, inv.quantity 
        FROM inventory inv 
        JOIN items i ON inv.itemid = i.id 
        WHERE inv.userid = :user_id
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
                <th>Quantity</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td><?= (int)$item['quantity']; ?></td>
                        <td><?= htmlspecialchars($item['type']); ?></td>
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
