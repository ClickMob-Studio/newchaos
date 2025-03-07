<?php
include 'header.php';


date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.

$targetDateMilliseconds = strtotime('August 04, 2024 08:00:00') * 1000;

$userCompLeaderboard = getUserCompLeaderboard($user_class->id);

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_raids_complete` DESC LIMIT 15");
$db->execute();
$overallRows = $db->fetch_row();

$milestones = array(
    10 => 2500,
    100 => 5000,
    250 => 7500,
    500 => 10000,
    750 => 20000,
    1000 => 30000,
    1500 => 50000,
    2000 => 70000,
    2500 => 80000,
    3000 => 100000,
    5000 => 125000,
    7500 => 200000,
    10000 => 300000
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
        Welcome to the Raids Contest, complete raids to earn points and push yourself up the leaderboard! <strong>Also earn and collect your milestones at the bottom of the page to earn additional points</strong>.
    </p>

    <p><strong>Your current points:</strong> <?php echo number_format($userCompLeaderboard['overall_raids_complete'], 0); ?></p>

    <p style="color: red">Contest Ends March 10, 2025 08:00:00 Server Time</p>
    <hr />

    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <h2>Leaderboard</h2>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 300,000 points, 1 x Galactic Booster</li>
                    <li>2nd: 100,000 points, 1 x Dark Matter Core</li>
                    <li>3rd: 50,000 points, 1 x Quantum Coil</li>
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

    <br />
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <h2>Milestones</h2>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>Required Raids</th>
                        <th>Points Prize</th>
                    </tr>
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
