<?php

include 'header.php';

date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.

$targetDateMilliseconds = strtotime('May 06, 2024 22:00:00') * 1000;

$userCompLeaderboard = getUserCompLeaderboard($user_class->id);

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `daily_crimes_complete` DESC LIMIT 15");
$db->execute();
$dailyCrimeRows = $db->fetch_row();

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_crimes_complete` DESC LIMIT 15");
$db->execute();
$overallCrimeRows = $db->fetch_row();

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `daily_attacks_complete` DESC LIMIT 15");
$db->execute();
$dailyAttackRows = $db->fetch_row();

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_attacks_complete` DESC LIMIT 15");
$db->execute();
$overallAttackRows = $db->fetch_row();

// CRIME MILESTONES
if (isset($_GET['action']) && $_GET['action'] === 'milestone' && isset($_GET['type']) && $_GET['type'] === 'crimes') {
    $milestones = array(
        100 => 1000,
        5000 => 5000,
        50000 => 15000,
        250000 => 300000,
        500000 => 750000,
        750000 => 1250000
    );

    $mileCollected = 0;
    foreach ($milestones as $mile => $prize) {
        if ($userCompLeaderboard['crimes_milestone_collected'] < $mile && $userCompLeaderboard['overall_crimes_complete'] >= $mile) {
            $mileCollected = $mile;

            Send_Event($user_class->id, 'You collected your ' . number_format($mile, 0) . ' crime milestone and claimed ' . number_format($prize, 0) . ' points.');

            $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $user_class->id);
            $db->execute();

            $db->query("UPDATE user_comp_leaderboard SET crimes_milestone_collected = " . $mile . " WHERE user_id = " . $user_class->id);
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
if (isset($_GET['action']) && $_GET['action'] === 'contest_token' && isset($_GET['type'])) {
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

    <h1>Crime & Attack Contest</h1>
    <p>
        Welcome to the Crime & Attack Contest, complete crimes and attacks to work your way up the daily and overall leaderboards and earn
        some great prizes. Daily Prizes & Contest Tokens will be paid at rollover. Milestone payments can be collected manually.
    </p>

    <p style="color: red">Contest Ends May 06, 2024 22:00:00</p>
    <hr />
    <h2>Crimes</h2>
    <p>Each crime you complete that uses at least 50% of your max nerve will count as 1 point. Any other crime will not count.</p>

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h3>Daily Leaderboard</h3>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 50,000 points</li>
                    <li>2nd: 25,000 points</li>
                    <li>3rd: 10,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($dailyCrimeRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($dailyCrimeRows as $dailyCrimeRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($dailyCrimeRow['user_id']) ?></td>
                                <td><?php echo number_format($dailyCrimeRow['daily_crimes_complete'], 0) ?></td>
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
                <h3>Overall Leaderboard</h3>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 500,000 points</li>
                    <li>2nd: 200,000 points</li>
                    <li>3rd: 50,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($overallCrimeRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($overallCrimeRows as $overallCrimeRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($overallCrimeRow['user_id']) ?></td>
                                <td><?php echo number_format($overallCrimeRow['overall_crimes_complete'], 0) ?></td>
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
    <h2>Attacks</h2>
    <p>Each attack you complete will count as 1 point.</p>

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h3>Daily Leaderboard</h3>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 50,000 points</li>
                    <li>2nd: 25,000 points</li>
                    <li>3rd: 10,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($dailyAttackRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($dailyAttackRows as $dailyAttackRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($dailyAttackRow['user_id']) ?></td>
                                <td><?php echo number_format($dailyAttackRow['daily_attacks_complete'], 0) ?></td>
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
                <h3>Overall Leaderboard</h3>
                <p><strong>Prizes:</strong></p>
                <ul>
                    <li>1st: 500,000 points</li>
                    <li>2nd: 200,000 points</li>
                    <li>3rd: 50,000 points</li>
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
            <h3>Overall Crime Milestones</h3>
            <ul>
                <li>100 crimes: 1,000 points</li>
                <li>5,000 crimes: 5,000 points</li>
                <li>50,000 crimes: 15,000 points</li>
                <li>250,000 crimes: 300,000 points</li>
                <li>500,000 crimes: 750,000 points</li>
                <li>750,000 crimes: 1,250,000 points</li>
            </ul>

            <a href="crime_contest.php?action=milestone&type=crimes"><button>Collect Milestones</button></a>
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

    <hr />
    <h2>Contest Tokens</h2>
    <div class="row">
        <div class="col-md-12">
            <p>The top 5 players on each daily leaderboard will earn 1 contest token. Contest tokens can be spent below.</p>
            <p>You currently have <?php echo $userCompLeaderboard['contest_token'] ?> contest tokens</p>

            <!-- 6 Tokens Up For Grabs -->

            <div class="table-container">
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th> </th>
                    </tr>

                    <!-- Police Badge -->
                    <tr>
                        <td>1 x Police Badge</td>
                        <td>1 Contest Token</td>
                        <td><a href="crime_contest.php?action=contest_token&type=1">Buy</a></td>
                    </tr>

                    <!-- Credits -->
                    <tr>
                        <td>200 Gold</td>
                        <td>2 Contest Tokens</td>
                        <td><a href="#">Buy</a></td>
                    </tr>

                    <!-- Nerve Vial -->
                    <tr>
                        <td>1 x Nerve Vial</td>
                        <td>3 Contest Tokens</td>
                        <td><a href="#">Buy</a></td>
                    </tr>

                    <!-- Advanced Booster -->
                    <tr>
                        <td>1 x Advanced Booster</td>
                        <td>4 Contest Tokens</td>
                        <td><a href="#">Buy</a></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>




<?php

require "footer.php";