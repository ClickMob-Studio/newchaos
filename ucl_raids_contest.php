<?php
include 'header.php';

if ($user_class->admin < 1) {
    exit;
}

date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.

$targetDateMilliseconds = strtotime('August 04, 2024 08:00:00') * 1000;

$userCompLeaderboard = getUserCompLeaderboard($user_class->id);

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_raids_complete` DESC LIMIT 15");
$db->execute();
$overallRows = $db->fetch_row();

$milestones = array(
    10 => 2500,
    50 => 5000,
    100 => 7500,
    125 => 10000,
    150 => 20000,
    300 => 30000,
    750 => 50000,
    1000 => 70000,
    1250 => 80000,
    1500 => 100000,
    2000 => 125000,
    3000 => 200000
);

// MILESTONES
if (isset($_GET['action']) && $_GET['action'] === 'milestone' && isset($_GET['type']) && $_GET['type'] === 'raids') {

    $mileCollected = 0;
    foreach ($milestones as $mile => $prize) {
        if ($userCompLeaderboard['raids_milestone_collected'] < $mile && $userCompLeaderboard['overall_raids_complete'] >= $mile) {
            $mileCollected = $mile;

            Send_Event($user_class->id, 'You collected your ' . number_format($mile, 0) . ' Raid milestone and claimed ' . number_format($prize, 0) . ' points.');

            $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $user_class->id);
            $db->execute();

            $db->query("UPDATE user_comp_leaderboard SET raids_milestone_collected = " . $mile . " WHERE user_id = " . $user_class->id);
            $db->execute();
        }
    }

    if ($mileCollected > 0) {
        echo Message('You have successfully collected your milestone prizes, check your events to see which ones you claimed. <a href="ucl_raids_contest.php">Go Back</a>');
    } else {
        echo Message('You had no milestone prizes to collect. <a href="ucl_raids_contest.php">Go Back</a>');
    }

}

?>

    <h1>Raids Contest</h1>
    <p>
        Welcome to the Raids Contest, complete raids to earn points and push yourself up the leaderboard! Also earn and collect your milestones to earn additional points.
    </p>

    <p><strong>Your current points:</strong></p>
    <ul>
        <li><strong>Overall:</strong> <?php echo number_format($userCompLeaderboard['overall_raids_complete'], 0); ?></li>
    </ul>

    <p style="color: red">Contest Ends August 04, 2024 19:00:00 Server Time</p>
    <hr />

    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <h2>Leaderboard</h2>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 200,000 points, 1 x Space Infinity Stone (Ultra Rare), 1 x Mission Pass</li>
                    <li>2nd: 100,000 points, 1 x Voidglass (Rare)</li>
                    <li>3rd: 50,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($overallRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($overallRows as $overallRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($overallRow['user_id']) ?></td>
                                <td><?php echo number_format($overallRow['overall_raids_complete'], 0) ?></td>
                            </tr>

                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                No Results
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <h2>Milestones</h2>
                <table class="new_table" id="newtables" style="width:100%;">
                    <?php foreach ($milestones as $number => $points): ?>
                        <tr>
                            <td><?php echo number_format($number, 0) ?></td>
                            <td><?php echo number_format($points, 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2">
                            <a href="ucl_raids_contest.php?action=milestone&type=raids"><button>Collect Milestones</button></a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php

require "footer.php";