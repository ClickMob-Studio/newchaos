<?php
include 'ajax_header.php'; 

$response = array("success" => false, "message" => "");
$user_class = new User($_SESSION['id']); 


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $itemId = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);

    // Check if the user has enough items to use
    $howmany = check_items($itemId);

    if ($howmany >= $quantity && $itemId == 251) { // Only handle item 251 for now
       
        for ($i = 0; $i < $quantity; $i++) {  
            addItemTempUse($user_class, 'raid_pass');
        }

        // Remove the items from inventory after use
        Take_Item($itemId, $user_class->id, $quantity); // Reduce the quantity of the item

        $response['success'] = true;
        $response['message'] = "You have used $quantity raid passes. The next $quantity raids you host will be successful.";
    } else {
        $response['message'] = "You don't have enough raid passes to use.";
    }
} else {
    $response['message'] = "Invalid request.";
}

// Return the response as JSON
echo json_encode($response);
exit;
