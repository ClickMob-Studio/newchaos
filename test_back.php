<?php
include 'header.php';
error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);

if($user_class->admin < 1 ){
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userid'])) {
    $userid = $_POST['userid'];

    $db->query("SELECT inv.*, it.*, c.name AS overridename, c.image AS overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
    $db->execute(array($userid));
    $inventory = $db->fetch();

    if ($inventory) {
        echo "<form method='post'>";
        foreach ($inventory as $item) {
            echo "<div>";
            echo "<label>{$item['name']}</label>";
            echo "<input type='text' name='quantity[{$item['itemid']}]' value='{$item['quantity']}' />";
            echo "<input type='hidden' name='itemid[]' value='{$item['itemid']}' />";
            echo "</div>";
        }
        echo "<button type='submit'>Save Changes</button>";
        echo "</form>";
    } else {
        echo "<p>No inventory found for this user.</p>";
    }
} else {

    echo "<p>Invalid access or no UserID provided.</p>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    foreach ($_POST['itemid'] as $index => $itemid) {
        $quantity = $_POST['quantity'][$itemid];  
        $db->query("UPDATE inventory SET quantity = ? WHERE itemid = ? AND userid = ?");
        $db->execute(array($quantity, $itemid, $_SESSION['userid']));  
    }
    echo "<p>Inventory updated successfully.</p>";
}

?>

    <form method="post">
        <label for="userid">User ID:</label>
        <input type="text" id="userid" name="userid" required>
        <button type="submit">Load Inventory</button>
    </form>

