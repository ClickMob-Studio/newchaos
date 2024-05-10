<?php 
include "ajax_header.php";
$user_class = new User($_SESSION['id']);

$gyms = mysql_query("SELECT record_date, strength, defense, speed FROM daily_user_stats WHERE user_id = ".$user_class->id);
$gym = mysql_fetch_array($gyms);

var_dump($gym);
echo json_encode($gym);

?>

