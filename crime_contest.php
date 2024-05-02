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
?>

    <h1>Crime & Attack Contest</h1>
    <p>
        Welcome to the Crime & Attack Contest, complete crimes and attacks to work your way up the daily and overall leaderboards and earn
        some great prizes.
    </p>

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
                    <?php foreach ($dailyCrimeRows as $dailyCrimeRow): ?>
                    <?php endforeach; ?>
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
                </table>
            </div>
        </div>
    </div>


<?php

require "footer.php";