<?php
include 'header.php';
$attack_person = new User($_GET['seduce']);
echo Message("You Seducing " . $attack_person->formattedname . " and getting jiggy with them! I bet they enjoyed that.");
$result = mysql_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id`='" . $user_class->id . "'");
Send_Event($attack_person->id, "" . $user_class->formattedname . " Has just touched your ***** and is passionately having there way with you.");
?>