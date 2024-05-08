<?php
require "ajax_header.php";
$user_class = new User($_SESSION['id']);

$carousel_order = json_encode($_POST['order']);
$carousel_order = mysql_real_escape_string($carousel_order); // Sanitize to prevent SQL injection

$user_id = intval($user_class->id); // Ensure the ID is an integer to prevent SQL injection

// Build the SQL query
$query = sprintf("INSERT INTO user_preferences (user_id, carousel_order) VALUES (%d, '%s')
                  ON DUPLICATE KEY UPDATE carousel_order = '%s'",
                  $user_id,
                  $carousel_order,
                  $carousel_order);

// Execute the query
$result = mysql_query($query);

// Check for query success
if (!$result) {
    echo 'MySQL Error: ' . mysql_error();
}
?>
