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
        if ($field === 'items') {
            foreach ($value as $key => $item) {
                // Give_Item($item['id'], $user_class->id, $item['quantity']);

                $payoutsToDisplay .= '<li>' . number_format($item['quantity'], 0) . ' x ' . Item_Name($item['id']) . '</li>';
            }
        } else {
            if ($field === 'exp') {
                $value = $user_class->maxexp / 100 * $value;
            }
            $payoutsToDisplay .= '<li>' . number_format($value, 0) . ' ' . ucwords($field) . '</li>';
            // $db->query('UPDATE grpgusers SET ' . $field . ' = ' . $field . ' + ? WHERE id = ?');
            // $db->execute(array($value, $user_class->id));
        }
    }
    $payoutsToDisplay .= '</ul>';

    echo "For User({$u['user_id']})<br />" . $payoutsToDisplay;
    // $db->query('UPDATE quest_season_mission_user SET is_paid_out = 1 WHERE id = ?');
    // $db->execute(array($questSeasonMissionUser['id']));
}

