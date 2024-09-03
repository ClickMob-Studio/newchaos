<?php

require '../ajax_header.php'; 
$user_class = new User($_SESSION['id']);
// Ensure the user is logged in and is an admin
if (!isset($user_class) || $user_class->admin <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}


if (!isset($_POST['message_id']) || !is_numeric($_POST['message_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid message ID.']);
    exit;
}

$message_id = (int)$_POST['message_id'];


try {
    // Start a transaction
    $db->startTrans();

    // Prepare the delete query for the globalchat table
    $db->query("DELETE FROM globalchat WHERE id = :message_id");
    $db->bind(':message_id', $message_id, PDO::PARAM_INT);

    // Execute the query
    $db->execute();

    // Check if any rows were affected (i.e., deleted)
    if ($db->affected_rows() > 0) {
        // Commit the transaction
        $db->endTrans();
        echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully.']);
    } else {
        // Rollback the transaction if no rows were affected
        $db->cancelTransaction();
        echo json_encode(['status' => 'error', 'message' => 'Message not found or already deleted.']);
    }
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $db->cancelTransaction();
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while deleting the message.']);
    error_log('Delete Message Error: ' . $e->getMessage());
    exit;
}
?>
