<?php
include 'header.php';
exit;
$relCompLeaderboard = geRelCompLeaderboard($user_class->id);

$db->query("SELECT * FROM `rel_comp_leaderboard` ORDER BY `daily_activity_complete` DESC LIMIT 15");
$db->execute();
$dailyRows = $db->fetch_row();

$db->query("SELECT * FROM `rel_comp_leaderboard` ORDER BY `overall_activity_complete` DESC LIMIT 15");
$db->execute();
$overallRows = $db->fetch_row();

?>

    <h1>Valentines Activity Contest</h1>
    <p>
        Welcome to the Valentines Activity Contest, you and your partner can complete actions to earn points and push yourselves up the leaderboard, but there's a twist! Each hour the required action to earn
        activity points will change so keep an eye out and grind your way to some great prizes!
    </p>

    <p>
        We have a daily leaderboard which will reset daily as well as an overall leaderboard! Daily leaderboard will be paid out and reset at Rollover.
    </p>

    <p><strong>Your current points:</strong></p>
    <ul>
        <li><strong>Daily:</strong> <?php echo number_format($relCompLeaderboard['daily_activity_complete'], 0); ?></li>
        <li><strong>Overall:</strong> <?php echo number_format($relCompLeaderboard['overall_activity_complete'], 0); ?></li>
    </ul>

    <?php
    $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
    $db->execute();
    $activityContest = $db->fetch_row(true);
    ?>

    <p><strong>Current Requirement: Complete <?php echo ucfirst($activityContest['type']) ?> to earn activity points</strong></p>

    <p style="color: red">Contest Ends February 15, 2025 09:00:00 Server Time</p>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h2>Daily</h2>
                <p><strong>Prize:</strong></p>
                <ul>
                    <li>1st: 150,000 points</li>
                    <li>2nd: 25,000 points</li>
                    <li>3rd: 10,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>Users</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($dailyRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($dailyRows as $dailyRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($dailyRow['user_id']) ?> & <?php echo formatName($dailyRow['two_user_id']) ?></td>
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
                    <li>1st: 1,000,000 points</li>
                    <li>2nd: 500,000 points</li>
                    <li>3rd: 250,000 points</li>
                </ul>
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th>&nbsp;</th>
                        <th>Users</th>
                        <th>Points</th>
                    </tr>
                    <?php if (count($overallRows) > 0): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($overallRows as $overallRow): ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo formatName($overallRow['user_id']) ?> & <?php echo formatName($overallRow['two_user_id']) ?></td>
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
