<?php
require "header.php";

function testing12($field, $value) {// Function to check if the user is in a gang with a current mission and update the active missionfunction updateGangActiveMission($field, $value) {
    global $user_class;
    // Check if the user is in a gang
    if ($user_class->gang != 0) {
        // Check for an active mission
        $checkActiveMission = mysql_query("SELECT agm.kills, agm.busts, agm.crimes, agm.mugs, gm.name, gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.reward, gm.time AS 'mission_time', UNIX_TIMESTAMP() AS 'current_time', agm.end_time FROM active_gang_missions agm JOIN gang_missions gm ON agm.mission_id = gm.id WHERE agm.gangid = {$user_class->gang} AND agm.completed = 0 LIMIT 1");
        if (!$checkActiveMission) {
            die('Invalid query: ' . mysql_error());
        }

        if ($activeMission = mysql_fetch_assoc($checkActiveMission)) {
            // Sanitize the field name to prevent SQL injection
            $allowed_fields = ['kills', 'busts', 'crimes', 'mugs'];
            if (!in_array($field, $allowed_fields)) {
                die('Invalid field specified.');
            }

            // Update the field in the active mission
            $updateQuery = "UPDATE active_gang_missions SET {$field} = {$field} + {$value} WHERE gangid = '{$user_class->gang}' AND completed = 0 LIMIT 1";
            if (!mysql_query($updateQuery)) {
                die('Failed to update the mission: ' . mysql_error());
            }
        }
    }
}
testing12('busts', 1);