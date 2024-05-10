<?php 
include "ajax_header.php";
$user_class = new User($_SESSION['id']);

$db->query("SELECT record_date, strength, defense, speed FROM daily_user_stats WHERE user_id = ?");
$db->execute([$user_class->id]);
$gym = $db->fetch_row();

echo json_encode($gym);

?>

