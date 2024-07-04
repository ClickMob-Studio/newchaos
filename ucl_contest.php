<?php
include 'header.php';

date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.

$targetDateMilliseconds = strtotime('June 10, 2024 21:00:00') * 1000;

$userCompLeaderboard = getUserCompLeaderboard($user_class->id);

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `daily_activity_complete` DESC LIMIT 15");
$db->execute();
$dailyRows = $db->fetch_row();

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_activity_complete` DESC LIMIT 15");
$db->execute();
$overallRows = $db->fetch_row();

// CRIME MILESTONES
if (isset($_GET['action']) && $_GET['action'] === 'milestone' && isset($_GET['type']) && $_GET['type'] === 'ba') {
    exit;
    $milestones = array(
        500 => 5000,
        1000 => 15000,
        1500 => 25000,
        2000 => 35000,
        2500 => 50000,
        5000 => 100000,
        7500 => 200000,

    );

    $mileCollected = 0;
    foreach ($milestones as $mile => $prize) {
        if ($userCompLeaderboard['ba_milestone_collected'] < $mile && $userCompLeaderboard['overall_ba_complete'] >= $mile) {
            $mileCollected = $mile;

            Send_Event($user_class->id, 'You collected your ' . number_format($mile, 0) . ' BA milestone and claimed ' . number_format($prize, 0) . ' points.');

            $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $user_class->id);
            $db->execute();

            $db->query("UPDATE user_comp_leaderboard SET ba_milestone_collected = " . $mile . " WHERE user_id = " . $user_class->id);
            $db->execute();
        }
    }

    if ($mileCollected > 0) {
        echo Message('You have successfully collected your milestone prizes, check your events to see which ones you claimed.');
    } else {
        echo Message('You had no milestone prizes to collect.');
    }

}

// ATTACK MILESTONES
if (isset($_GET['action']) && $_GET['action'] === 'milestone' && isset($_GET['type']) && $_GET['type'] === 'attacks') {
    exit;
    $milestones = array(
        100 => 1500,
        1000 => 20000,
        2500 => 40000,
        5000 => 80000,
        15000 => 150000,
        25000 => 350000
    );

    $mileCollected = 0;
    foreach ($milestones as $mile => $prize) {
        if ($userCompLeaderboard['attacks_milestone_collected'] < $mile && $userCompLeaderboard['overall_attacks_complete'] >= $mile) {
            $mileCollected = $mile;

            Send_Event($user_class->id, 'You collected your ' . number_format($mile, 0) . ' attack milestone and claimed ' . number_format($prize, 0) . ' points.');

            $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $user_class->id);
            $db->execute();

            $db->query("UPDATE user_comp_leaderboard SET attacks_milestone_collected = " . $mile . " WHERE user_id = " . $user_class->id);
            $db->execute();
        }
    }

    if ($mileCollected > 0) {
        echo Message('You have successfully collected your milestone prizes, check your events to see which ones you claimed.');
    } else {
        echo Message('You had no milestone prizes to colleheact.');
    }

}

// CONTEST TOKEN SHOP
if (false && isset($_GET['action']) && $_GET['action'] === 'contest_token' && isset($_GET['type'])) {
    exit;
    // 1 Police Badge = 1
    // 200 Gold = 2
    // 1 Nerve Vial = 3
    // Advanced Booster = 4

    if ($_GET['type'] == 1) {
        if ($userCompLeaderboard['contest_token'] < 1) {
            diefun('You do not have enough contest tokens.');
        }

        Give_Item(163, $user_class->id, 1);

        $db->query('UPDATE user_comp_leaderboard SET contest_token = contest_token - 1 WHERE user_id = ' . $user_class->id);
        $db->execute();

        echo Message('You have purchased 1 x Police Badge');
    }

    if ($_GET['type'] == 2) {
        if ($userCompLeaderboard['contest_token'] < 2) {
            diefun('You do not have enough contest tokens.');
        }

        $db->query('UPDATE grpgusers SET credits = credits + 200 WHERE id = ' . $user_class->id);
        $db->execute();

        $db->query('UPDATE user_comp_leaderboard SET contest_token = contest_token - 2 WHERE user_id = ' . $user_class->id);
        $db->execute();

        echo Message('You have purchased 200 Credits');
    }

    if ($_GET['type'] == 3) {
        if ($userCompLeaderboard['contest_token'] < 3) {
            diefun('You do not have enough contest tokens.');
        }

        Give_Item(256, $user_class->id, 1);

        $db->query('UPDATE user_comp_leaderboard SET contest_token = contest_token - 3 WHERE user_id = ' . $user_class->id);
        $db->execute();

        echo Message('You have purchased 1 x Nerve Vial');
    }

    if ($_GET['type'] == 4) {
        if ($userCompLeaderboard['contest_token'] < 4) {
            diefun('You do not have enough contest tokens.');
        }

        Give_Item(250, $user_class->id, 1);

        $db->query('UPDATE user_comp_leaderboard SET contest_token = contest_token - 4 WHERE user_id = ' . $user_class->id);
        $db->execute();

        echo Message('You have purchased 1 x Advanced Booster');
    }
}
?>

    <h1>Activity Contest</h1>
    <p>
        Welcome to the Activity Contest, complete actions to earn points and push yourself up the leaderboard, but there's a twist! Each hour the required action to earn
        activity points will change so keep an eye out and grind your way way to some great prizes!
    </p>

    <p>
        We have a daily leaderboard which will reset daily as well as an overall leaderboard! Daily leaderboard will be paid out and reset at Rollover.
    </p>

    <p><strong>Your current points:</strong></p>
    <ul>
        <li><strong>Daily:</strong> <?php echo number_format($userCompLeaderboard['daily_activity_complete'], 0); ?></li>
        <li><strong>Overall:</strong> <?php echo number_format($userCompLeaderboard['overall_activity_complete'], 0); ?></li>
    </ul>

    <?php
    $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
    $db->execute();
    $activityContest = $db->fetch_row(true);
    ?>

    <p><strong>Current Requirement: Complete <?php echo ucfirst($activityContest['type']) ?> to earn activity points</strong></p>

    <p style="color: red">Contest Ends July 06, 2024 19:00:00 Server Time</p>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h2>Daily</h2>
                <p><strong>Prize:</strong></p>
                <ul>
                    <li>1st: 150,000 points, 2 x Stone (Rare) & 1 x Hourglass Gem (Rare)</li>
                    <li>2nd: 25,000 points & 1 x Stone (Rare)</li>
                    <li>3rd: 1 x Stone (Rare)</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($dailyRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($dailyRows as $dailyRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($dailyRow['user_id']) ?></td>
                                <td><?php echo number_format($dailyRow['daily_activity_complete'], 0) ?></td>
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

        <div class="col-md-6">
            <div class="table-container">
                <h2>Overall</h2>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 1,000,000 points, 1 x Space Infinity Stone (Ultra Rare), 1 x Research Token &  1 x Fireplace</li>
                    <li>2nd: 500,000 points, 1 x Voidglass (Rare) & 1 x Sofa</li>
                    <li>3rd: 250,000 points, 5 x Stone (Rare)</li>
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
                                <td><?php echo number_format($overallRow['overall_activity_complete'], 0) ?></td>
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
<?php

require "footer.php";