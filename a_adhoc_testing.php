<?php
include 'header.php';

$db->query("UPDATE user_research_type SET duration_in_days = duration_in_days - 1 WHERE duration_in_days > 0");
$db->execute()
?>