<?php
require "ajax_header.php";
$user_class = new User($_SESSION['id']);
$carousel_order = json_encode($_POST['order']); 
$sql = mysql_query("INSERT INTO user_preferences (user_id, carousel_order) VALUES (".$user_class->id.", '".$carousel_order."')
 ON DUPLICATE KEY UPDATE carousel_order = '".$carousel_order."'");
