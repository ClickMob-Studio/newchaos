<?php 
include "ajax_header.php";
$user_class = new User($_SESSION['id']);

$db->query("SELECT record_date, strength, defense, speed, agility FROM daily_user_stats WHERE user_id = ? ORDER BY record_date ASC LIMIT 20");
$db->execute([$user_class->id]);
$gym = $db->fetch_row();

echo json_encode($gym);

?>

