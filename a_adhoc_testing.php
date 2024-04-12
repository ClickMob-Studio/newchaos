<?php
include 'header.php';

$db->query("SELECT * FROM inventory WHERE quantity <= 0 AND userid = " . $user_class->id);
$db->execute();
$rows = $db->fetch_row();

$itemIds = array();
foreach ($rows as $row) {
    if (!isset($itemIds[$row['itemid']])) {
        $itemIds[$row['itemid']] = $row['quantity'];
    } else {
        echo 'DUPE FOUND: ' . $row['itemid'] . '<br />';
    }
}

include 'footer.php';
?>