<?php
include 'header.php';
$attack_person = new User($_GET['kiss']);
echo Message("You have just passionately kissed " . $attack_person->formattedname . " on the lips! Gonna take this further!?.");
perform_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id`= ?", [$user_class->id]);
Send_Event($attack_person->id, "" . $user_class->formattedname . " Has just kissed you!. What you gonna do about it!.");
?>