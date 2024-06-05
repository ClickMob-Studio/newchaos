<?php

include 'header.php';

date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.

$targetDateMilliseconds = strtotime('June 09, 2024 22:00:00') * 1000;

$userCompLeaderboard = getUserCompLeaderboard($user_class->id);

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_ba_complete` DESC LIMIT 15");
$db->execute();
$overallBaRows = $db->fetch_row();

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_attacks_complete` DESC LIMIT 15");
$db->execute();
$overallAttackRows = $db->fetch_row();

// CRIME MILESTONES
if (isset($_GET['action']) && $_GET['action'] === 'milestone' && isset($_GET['type']) && $_GET['type'] === 'ba') {
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
        if ($userCompLeaderboard['ba_milestone_collected'] < $mile && $userCompLeaderboard['ba_milestone_collected'] >= $mile) {
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

    <h1>BA & Attack Contest</h1>
    <p>
        Welcome to the BA & Attack Contest, complete Backalley searches and attacks to work your way up the overall leaderboards and earn
        some great prizes. Milestone payments can be collected manually.
    </p>

    <p style="color: red">Contest Ends May 09, 2024 22:00:00</p>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h2>BA Searches</h2>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 300,000 points</li>
                    <li>2nd: 100,000 points</li>
                    <li>3rd: 25,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($overallBaRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($overallBaRows as $overallBaRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($overallBaRow['user_id']) ?></td>
                                <td><?php echo number_format($overallBaRow['overall_ba_complete'], 0) ?></td>
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
                <h2>Attacks</h2>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 300,000 points</li>
                    <li>2nd: 100,000 points</li>
                    <li>3rd: 25,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($overallAttackRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($overallAttackRows as $overallAttackRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($overallAttackRow['user_id']) ?></td>
                                <td><?php echo number_format($overallAttackRow['overall_attacks_complete'], 0) ?></td>
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

    <hr />
    <h2>Milestone Payments</h2>
    <div class="row">
        <div class="col-md-6">
            <h3>Overall BA Milestones</h3>
            <ul>
                <li>500 Searches: 5,000 points</li>
                <li>1,000 Searches: 15,000 points</li>
                <li>1,500 Searches: 25,000 points</li>
                <li>2,000 Searches: 35,000 points</li>
                <li>2,500 Searches: 50,000 points</li>
                <li>5,000 Searches: 100,000 points</li>
                <li>7,500 Searches: 200,000 points</li>
            </ul>

            <a href="crime_contest.php?action=milestone&type=ba"><button>Collect Milestones</button></a>
        </div>
        <div class="col-md-6">
            <h3>Overall Attack Milestones</h3>
            <ul>
                <li>100 attacks: 1,500 points</li>
                <li>1,000 attacks: 20,000 points</li>
                <li>2,500 attacks: 40,000 points</li>
                <li>5,000 attacks: 80,000 points</li>
                <li>15,000 attacks: 150,000 points</li>
                <li>35,000 attacks: 350,000 points</li>
            </ul>

            <a href="crime_contest.php?action=milestone&type=attacks"><button>Collect Milestones</button></a>
        </div>
    </div>





<?php

require "footer.php";