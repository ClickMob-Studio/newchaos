<?php
include 'header.php';

$db->query("SELECT * FROM inventory WHERE userid = 5");
$db->execute();
$rows = $db->fetch_row();

$itemIds = array();
foreach ($rows as $row) {
    if (!isset($itemIds[$row['itemid']])) {
        $itemIds[$row['itemid']] = $row['id'];
    } else {
        $rowId = $itemIds[$row['itemid']];

        $db->query("UPDATE inventory SET quantity = quantity + " . $row['quantity'] . "  WHERE id = " . $rowId . " ");
        $db->execute();

        $db->query("DELETE FROM inventory  WHERE id = " . $row['quantity'] . " ");
        $db->execute();
    }
}

include 'footer.php';
?>