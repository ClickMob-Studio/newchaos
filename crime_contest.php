<?php

include 'header.php';

date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.

$targetDateMilliseconds = strtotime('May 06, 2024 22:00:00') * 1000;

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
?>

    <h1>Crime & Attack Contest</h1>
    <p>
        Welcome to the Crime & Attack Contest, complete crimes and attacks to work your way up the daily and overall leaderboards and earn
        some great prizes.
    </p>

    <hr />
    <h2>Crimes</h2>
    <p>Each crime you complete that uses at least 75% of your max nerve will count as 1 point. Any other crime will not count.</p>

    <div class="row">
        <div class="col-md-6">
            <div class="table-container">
                <h3>Daily Leaderboard</h3>
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
                                <td><?php echo number_format($overallCrimeRow['daily_crimes_complete'], 0) ?></td>
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
                                <td><?php echo number_format($dailyAttackRow['daily_crimes_complete'], 0) ?></td>
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
                                <td><?php echo number_format($overallAttackRow['daily_crimes_complete'], 0) ?></td>
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