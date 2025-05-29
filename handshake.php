<?php
include 'header.php';
$attack_person = new User($_GET['shake']);
echo Message("You have just shook " . $attack_person->formattedname . " 's hand.");
perform_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id`= ?", [$user_class->id]);
Send_Event($attack_person->id, "" . $user_class->formattedname . " Has just shaken your hand. Ha!.");
?>