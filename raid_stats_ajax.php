<?php
include 'ajax_header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$response['post_data'] = $_POST;

$response = ['success' => false, 'message' => ''];

// Assuming you have your database connection and $user_class initiation here...

if (isset($_POST['stat_to_upgrade'])) {
    $stat_to_upgrade = $_POST['stat_to_upgrade'];

    if (isset($_POST['tier_upgrade'])) {
        // Upgrade the tier
        if ($user_class->raidtokens >= 10) {
            perform_query("UPDATE grpgusers SET raidtokens = raidtokens - 10, {$stat_to_upgrade}_tier = {$stat_to_upgrade}_tier + 1, $stat_to_upgrade = 0 WHERE id = ?", [$user_class->id]);
            $response['success'] = true;
            $response['message'] = 'Tier upgraded successfully!';
        } else {
            $response['message'] = 'You do not have enough raid tokens to upgrade the tier!';
        }
    } else {
        // Upgrade the stat
        $targetLevel = intval($_POST['target_level']);
        $currentLevel = $user_class->$stat_to_upgrade;
        $availablePoints = $user_class->raidpoints;
        $upgradeCost = 100 * ($currentLevel + 1);
        $totalCost = 0;

        while ($currentLevel < $targetLevel) {
            $totalCost += $upgradeCost;
            $currentLevel++;
            $upgradeCost = 100 * ($currentLevel + 1);
        }

        if ($availablePoints >= $totalCost) {
            perform_query("UPDATE grpgusers SET raidpoints = raidpoints - ?, $stat_to_upgrade = ? WHERE id = ?", [$totalCost, $currentLevel, $user_class->id]);
            $response['success'] = true;
            $response['message'] = 'Stat upgraded successfully!';
        } else {
            $response['message'] = 'You do not have enough raid points to upgrade this stat!';
        }
    }
}

// Return the JSON response
echo json_encode($response);
?>