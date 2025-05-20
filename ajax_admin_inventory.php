<?php
require "ajax_header.php";

$user_class = new User($_SESSION['id']);
if ($user_class->admin < 1) {
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are present
    if (!isset($_POST['userid'], $_POST['itemid'], $_POST['quantity'])) {
        echo 'Error: Missing data';
        exit;
    }

    $userid = filter_var($_POST['userid'], FILTER_VALIDATE_INT);
    $itemid = filter_var($_POST['itemid'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

    if (!$userid || !$itemid || !$quantity) {
        echo 'Error: Invalid input data';
        exit;
    }

    // Assuming $db is your database connection
    $db->query("UPDATE inventory SET quantity = ? WHERE itemid = ? AND userid = ?");
    $result = $db->execute(array($quantity, $itemid, $userid));

    if ($result) {
        echo 'Item updated successfully.';
    } else {
        echo 'Error updating item.';
    }
    exit;
}
?>