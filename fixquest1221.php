<?php

require "header.php";

if ($user_class->admin != 1) {
    die("Unauthorized");
}

$db->query("SELECT * FROM `quest_season_mission` WHERE `id` = 17");
$mission = $db->fetch_row(true);
if (empty($mission)) {
    die("Could not find mission with ID 17");
}

$db->query("SELECT * FROM `quest_season_mission_user` WHERE `quest_season_mission_id` = 17 AND `is_paid_out` = 1");
$missionUsers = $db->fetch_row();
if (empty($missionUsers)) {
    die("No users that haven't been paid out");
}

foreach ($missionUsers as $u) {
    $payouts = json_decode($mission['payouts'], true);
    $payoutsToDisplay = 'You have received the following payouts:<br />';
    $payoutsToDisplay .= '<ul>';
    foreach ($payouts as $field => $value) {
        $user_c = new User($u['user_id']);
        if ($field === 'items') {
        } else {
            if ($field === 'exp') {
                $value = $user_c->maxexp / 100 * $value;
            }
            $db->query('UPDATE grpgusers SET ' . $field . ' = ' . $field . ' + ? WHERE id = ?');
            $db->execute(array($value, $user_c->id));
        }
    }
}

