<?php
include 'ajax_header.php';

$user_class = new User($_SESSION['id']);
$response = array("success" => false, "equippedItems" => array());

// Ensure user is logged in
if (!isset($user_class->id)) {
    $response['message'] = "User is not logged in.";
    echo json_encode($response);
    exit;
}

// Define equipped item slots to retrieve
$equippedSlots = array('eqweapon', 'eqarmor', 'eqshoes');

// Fetch equipped items from the database
global $db;
$equippedItems = array();

foreach ($equippedSlots as $slot) {
    $db->query("SELECT i.id, i.itemname AS name, i.image, i.offense, i.defense, i.speed
                FROM grpgusers u
                JOIN items i ON u.$slot = i.id
                WHERE u.id = :user_id");
    $db->bind(':user_id', $user_class->id);
    $item = $db->fetch_row(true);

    if ($item) {
        $itemType = substr($slot, 2); // Remove 'eq' prefix, so 'eqweapon' -> 'weapon'
        $equippedItems[$itemType] = $item;
    }
}

// Populate response with equipped items data
$response['success'] = true;
$response['equippedItems'] = $equippedItems;

echo json_encode($response);
exit;
