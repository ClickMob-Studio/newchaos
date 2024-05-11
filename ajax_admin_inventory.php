<?php
require "ajax_header.php";
$user_class = new User($_SESSION['id']);
if($User_class->admin < 1) {
    die();
}
echo "HELLLOOOOO";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_item') {
    $userid = $_POST['userid'];
    $itemid = $_POST['itemid'];
    $quantity = $_POST['quantity'];

    $db->query("UPDATE inventory SET quantity = ? WHERE itemid = ? AND userid = ?");
    if ($db->execute(array($quantity, $itemid, $userid))) {
        echo 'Item updated successfully.';
    } else {
        echo 'Error updating item.';
    }
    exit;
}
?>
