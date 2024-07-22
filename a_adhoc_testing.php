<?php

include 'dbcon.php';
include 'classes.php';
include 'database/pdo_class.php';
print "working";

$activeMissionsQuery = "SELECT agm.id AS mission_id, agm.gangid, agm.time, agm.end_time, agm.kills, agm.busts, agm.crimes, agm.mugs 
        FROM active_gang_missions agm 
        JOIN gang_missions gm ON agm.mission_id = gm.id 
        WHERE agm.completed = 0";
$activeMissionsResult = mysql_query($activeMissionsQuery);

if ($activeMissionsResult) {
    echo 'misson found - ';
    $currentTime = time();

    while ($mission = mysql_fetch_assoc($activeMissionsResult)) {
        $missionId = $mission['mission_id'];
        $gangId = $mission['gangid'];
        $startTime = strtotime($mission['time']);


        // Fetch the target criteria from the gang_missions table
        $missionDetailsQuery = "SELECT gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.reward 
            FROM gang_missions gm 
            WHERE gm.id = (SELECT mission_id FROM active_gang_missions WHERE id = $missionId) LIMIT 1";
        $missionDetailsResult = mysql_query($missionDetailsQuery);
        if (!$missionDetailsResult) {
            continue;  // Skip if details can't be fetched
        }
        $missionDetails = mysql_fetch_assoc($missionDetailsResult);

        $allTargetsMet = true;

        // Check each mission type separately
        foreach (['kills', 'busts', 'crimes', 'mugs'] as $type) {
            if ($missionDetails['target_' . $type] > 0) {
                if ($mission[$type] < $missionDetails['target_' . $type]) {
                    $allTargetsMet = false;
                    break;  // No need to check further if any target is not met
                }
            }
        }

        if ($allTargetsMet) {
            echo 'targets met - ';
        } else {
            echo 'targets not met - ';
        }

        if ($allTargetsMet) {
            // Update gang points and notify members about successful mission completion
            $rewardQuery = "UPDATE gangs SET pointsvault = pointsvault + {$missionDetails['reward']} WHERE id = $gangId";
            mysql_query($rewardQuery);

            $successMessage = "Your gang has successfully completed the mission and earned a reward of {$missionDetails['reward']} points.";

            $gangMembersQuery = "SELECT id FROM grpgusers WHERE gang = $gangId";
            $gangMembersResult = mysql_query($gangMembersQuery);
            while ($member = mysql_fetch_assoc($gangMembersResult)) {
                $userId = $member['id'];
                Send_event($userId, "Congratulations! " . $successMessage);
            }
            $endTime = $mission['end_time'];
            // Mark the mission as completed
            $markCompletedQuery = "UPDATE active_gang_missions SET completed = 1 WHERE id = $missionId";
            mysql_query($markCompletedQuery);
        } elseif ($currentTime < $endTime) {
            // Notify gang members about mission failure due to time running out
            $failureMessage = "The mission was not completed in time.";

            $gangMembersQuery = "SELECT id FROM grpgusers WHERE gang = $gangId";
            $gangMembersResult = mysql_query($gangMembersQuery);
            while ($member = mysql_fetch_assoc($gangMembersResult)) {
                $userId = $member['id'];
                Send_event($userId, $failureMessage);
            }

            // Mark the mission as completed
            $markCompletedQuery = "UPDATE active_gang_missions SET completed = 1 WHERE id = $missionId";
            mysql_query($markCompletedQuery);
        }
    }
}