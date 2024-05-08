<?php
require "ajax_header.php"; 
$user_class = new User($_SESSION['id']);  

$carousel_order = json_decode($_POST['order']); 
$carousel_order = json_encode($carousel_order);
echo $carousel_order;
exit;
$carousel_order = stripslashes($carousel_order);

$query = "INSERT INTO user_preferences (user_id, carousel_order) VALUES (:user_id, :carousel_order)
          ON DUPLICATE KEY UPDATE carousel_order = :carousel_order";


$db->query($query);

$db->bind(':user_id', $user_class->id, PDO::PARAM_INT);
$db->bind(':carousel_order', $carousel_order, PDO::PARAM_STR);

if ($db->execute()) {
    echo "User preferences updated successfully.";
} else {
    echo "Failed to update user preferences.";
}
?>