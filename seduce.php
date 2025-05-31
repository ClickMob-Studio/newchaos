<?php
include 'header.php';


$attack_person = new User($_GET['seduce']);

echo Message("You seduced " . $attack_person->formattedname . " and got jiggy with them! I bet they enjoyed that.");

perform_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id`= ?", [$user_class->id]);
Send_Event($attack_person->id, "" . $user_class->formattedname . " has just touched your ***** and is passionately having their way with you.");
?>