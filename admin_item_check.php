<?php
include 'header.php';

$db->query("SELECT itemid, SUM(quantity) AS total FROM inventory GROUP BY itemid ORDER BY total DESC;");
$db->execute();
$rows = $db->fetch_row();

?>

<h1>Item Count</h1>
<div class="table-container">
    <table class="new_table" id="newtables" style="width:100%;">
        <thead>
            <tr>
                <th></th>
                <th>Item</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo $row['itemid'] ?></td>
                    <td><?php echo Item_Name($row['itemid']) ?></td>
                    <td><?php echo number_format($row['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

