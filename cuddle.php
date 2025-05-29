<?php
include 'header.php';

$attack_person = new User($_GET['cuddle']);

echo Message("You have just given " . $attack_person->formattedname . " a big hug!.");

perform_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id`=?", [$user_class->id]);

Send_Event($attack_person->id, "" . $user_class->formattedname . " Has just given you a big hug!.");
?>