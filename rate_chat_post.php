<?php
include "ajax_header.php";

$action = security($_POST['action']);
$post_id = security($_POST['post_id']);

// Check if the user has already liked or disliked this post
$db->query("SELECT post_id FROM chat_rating WHERE user_id = ? AND post_id = ?");
$db->execute(array($user_class->id, $post_id));
$existingRating = $db->fetch_single();

if ($existingRating) {
    // Update or delete existing rating
    if ($_POST['action'] == 'unlike' || $_POST['action'] == 'undislike') {
        // Remove the existing rating
        $db->query("DELETE FROM chat_rating WHERE post_id = ?");
        $db->execute(array($existingRating));
    } else {
        // Update the existing rating
        $new_action = ($action === 'like') ? 'like' : 'dislike';
        $db->query("UPDATE chat_rating SET rating_action = ? WHERE post_id = ?");
        $db->execute(array($new_action, $existingRating));
    }
} else {
    // Insert new rating
    $db->query("INSERT INTO chat_rating (user_id, post_id, rating_action) VALUES (?, ?, ?)");
    $db->execute(array($user_class->id, $post_id, $action));
}

// Query the updated like and dislike counts
$db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = ? AND rating_action = 'like'");
$db->execute(array($post_id));
$likes = $db->fetch_single();

$db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = ? AND rating_action = 'dislike'");
$db->execute(array($post_id));
$dislikes = $db->fetch_single();

// Return the updated counts as a JSON object
echo json_encode(array("likes" => $likes, "dislikes" => $dislikes));
?>
