<?php

include 'header.php';


$now = new \DateTime();

$db->query("INSERT INTO halloween_user_list (user_id, month_year) VALUES (" . $user_class->id . ", '" . $now->format('d-m-Y-h') . "')");
$db->execute();