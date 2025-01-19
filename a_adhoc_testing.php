<?php

include 'header.php';


$db->query("SELECT * FROM gang_territory_zone_battle WHERE is_complete > 0");
$rows = $db->fetch_row();

foreach ($rows as $row) {
    if ($row['winning_gang_id'] == $row['attacking_gang_id']) {
        if ($row['strength_attacking_user_id'] > 0) {
            $db->query("UPDATE grpgusers SET gtzb_count = gtzb_count + 1 WHERE id = " . $row['strength_attacking_user_id']);
            $db->execute();
        }
        if ($row['speed_attacking_user_id'] > 0) {
            $db->query("UPDATE grpgusers SET gtzb_count = gtzb_count + 1 WHERE id = " . $row['speed_attacking_user_id']);
            $db->execute();
        }
        if ($row['defense_attacking_user_id'] > 0) {
            $db->query("UPDATE grpgusers SET gtzb_count = gtzb_count + 1 WHERE id = " . $row['defense_attacking_user_id']);
            $db->execute();
        }
    } else {
        if ($row['strength_defending_user_id'] > 0) {
            $db->query("UPDATE grpgusers SET gtzb_count = gtzb_count + 1 WHERE id = " . $row['strength_defending_user_id']);
            $db->execute();
        }
        if ($row['speed_defending_user_id'] > 0) {
            $db->query("UPDATE grpgusers SET gtzb_count = gtzb_count + 1 WHERE id = " . $row['speed_defending_user_id']);
            $db->execute();
        }
        if ($row['defense_defending_user_id'] > 0) {
            $db->query("UPDATE grpgusers SET gtzb_count = gtzb_count + 1 WHERE id = " . $row['defense_defending_user_id']);
            $db->execute();
        }
    }

}
