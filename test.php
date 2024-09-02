<?php

require "header.php";

if ($user_class->id > 2) {
    exit;
}



function mission1($update, $howmany = 1)
{
    global $user_class, $db;

    $prestigeUserSkills = getUserPrestigeSkills($user_class);
    $pointsPayoutBoost = ($prestigeUserSkills['mission_point_boost_level'] > 0) 
        ? 2 * $prestigeUserSkills['mission_point_boost_level'] 
        : 0;

    // Start a transaction to ensure data consistency
    $db->startTrans();

    $userMission = getCurrentMission($user_class->id, $db);
    if (!$userMission) {
        return 1; // No active missions
    }

    $missionDetails = getMissionDetails($userMission['mid'], $db);
    if (!$missionDetails) {
        $db->cancelTransaction();
        return 1; // Mission details not found
    }

    try {
        switch ($update) {
            case 'k':
                processMissionUpdate('kills', $userMission, $missionDetails, $pointsPayoutBoost, $db, $user_class);
                break;
            case 'b':
                processMissionUpdate('busts', $userMission, $missionDetails, $pointsPayoutBoost, $db, $user_class);
                break;
            case 'c':
                processCrimeUpdate($howmany, $userMission, $missionDetails, $db, $user_class);
                break;
            case 'm':
                processMissionUpdate('mugs', $userMission, $missionDetails, $pointsPayoutBoost, $db, $user_class);
                break;
            case 'ba':
                processMissionUpdate('backalleys', $userMission, $missionDetails, $pointsPayoutBoost, $db, $user_class);
                break;
            case 'ra':
                processMissionUpdate('raids', $userMission, $missionDetails, $pointsPayoutBoost, $db, $user_class);
                break;
            default:
                $db->cancelTransaction();
                return 1; // Invalid update type
        }

        $db->endTrans(); // Commit the transaction if everything is successful
    } catch (Exception $e) {
        $db->cancelTransaction(); // Rollback in case of any failure
        error_log($e->getMessage());
        return 1;
    }

    return 1;
}

function getCurrentMission($userId, $db)
{
    $db->query("SELECT * FROM missions WHERE userid = ? AND completed = 'no'");
    $db->execute(array($userId));
    return $db->fetch_row(true);
}

function getMissionDetails($missionId, $db)
{
    $db->query("SELECT * FROM mission WHERE id = ?");
    $db->execute(array($missionId));
    return $db->fetch_row(true);
}

function processMissionUpdate($type, &$userMission, $missionDetails, $pointsPayoutBoost, $db, $user_class)
{
    $db->query("UPDATE missions SET $type = $type + 1 WHERE userid = ? AND completed = 'no'");
    $db->execute(array($user_class->id));
    $userMission[$type]++;

    $requiredCount = $missionDetails[$type];

    // Debugging: Check what value is being fetched from mission details
    $payKey = "pay" . ucfirst($type);
    if (!isset($missionDetails[$payKey])) {
        echo "Error: Mission payout key '$payKey' not found in mission details.";
        return;
    }

    $basePayout = $missionDetails[$payKey];

    // Debugging: Check the base payout before calculation
    echo "Base Payout for $type: $basePayout<br>";

    $payout = calculatePayout($basePayout, $pointsPayoutBoost);

    // Debugging: Check the calculated payout
    echo "Calculated Payout: $payout<br>";

    if ($userMission[$type] == $requiredCount) {
        rewardUser($payout, $user_class->id, $db);
        logMissionCompletion($missionDetails['name'], $type, $requiredCount, $user_class->id, $db);
        sendEvent($user_class->id, "You have completed {$missionDetails['name']} objective to get {$requiredCount} $type.");
    }
}

function calculatePayout($basePayout, $boost)
{
    // Ensure both values are numbers
    if (!is_numeric($basePayout) || !is_numeric($boost)) {
        echo "Invalid payout or boost values: Base - $basePayout, Boost - $boost<br>";
        return $basePayout; // Return base if the boost is not valid
    }

    $calculatedPayout = $basePayout + ($basePayout * $boost / 100);

    // Debugging: Print the calculation
    echo "Calculated Payout with Boost: $calculatedPayout<br>";

    return $calculatedPayout;
}


function processCrimeUpdate($howmany, &$userMission, $missionDetails, $db, $user_class)
{
    $db->query("UPDATE missions SET crimes = crimes + ? WHERE userid = ? AND completed = 'no'");
    $db->execute(array($howmany, $user_class->id));
    $userMission['crimes'] += $howmany;

    // Add further checks and logic as necessary
}



function rewardUser($points, $userId, $db)
{
    $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
    $db->execute(array($points, $userId));
}

function logMissionCompletion($missionName, $type, $count, $userId, $db)
{
    $db->query("INSERT INTO missionlog VALUES(NULL, '[x] successfully completed {$missionName} objective to get {$count} {$type}, $userId', unix_timestamp())");
    $db->execute();
}

function sendEvent($userId, $message)
{
    Send_event($userId, $message);
}

// Call the mission function to update kills as an example
mission1('c');
