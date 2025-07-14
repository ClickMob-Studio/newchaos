<?php
include 'header.php';
$result = mysql_query("SELECT * FROM `bans` WHERE `type`='mail' AND `id` = '" . $user_class->id . "'");
$check = mysql_num_rows($result);
$worked = mysql_fetch_array($result);
if ($check > 0) {
    echo Message('&nbsp;You have been mail banned for ' . prettynum($worked['days']) . ' days.');
    include 'footer.php';
    die();
}
$attack_person = new User($_GET['slap']);
if (isset($error)) {
    echo Message($error);
} else {

    if ($attack_person->id == 174) {
        echo Message("You tried to slap Terminal but he evaded and sucker puched you straight in the face!.  Ouch I bet that hurt");
    } else {
        echo Message("You have slapped " . $attack_person->formattedname . " on the head! I bet that hurt.");
        perform_query("UPDATE `grpgusers` SET `points` = points - 0 WHERE `id` = ?", [$user_class->id]);
        Send_Event($attack_person->id, "" . $user_class->formattedname . " Has just bitch slapped you on the back of the head!");
    }
}
include 'footer.php';
?>