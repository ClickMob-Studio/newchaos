<?php 
include "ajax_header.php";


$db->query("SELECT record_date, strength, defense, speed FROM daily_user_stats WHERE user_id = ?");
$db->bind(1, $$user_class->id, PDO::PARAM_INT);
$db->execute();
$results = $db->fetch_row();


echo json_encode($results);

?>

