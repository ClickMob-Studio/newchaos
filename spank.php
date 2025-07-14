<?php
include 'header.php';
$attack_person = new User($_GET['spank']);
echo Message("You have just spanked " . $attack_person->formattedname . " right on their ass!.");
perform_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id` = ?", [$user_class->id]);
Send_Event($attack_person->id, "" . $user_class->formattedname . " Has just spanked you hard. Better go and get them back!.");
?>