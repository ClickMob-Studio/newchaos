<?php
include 'header.php';
?>
<div class='box_top'>Contest</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        date_default_timezone_set('Europe/London'); // This will automatically account for BST as well.
        
        // Target date and time for 31st March at 5 PM UK time
        $targetDateMilliseconds = strtotime('April 10, 2024 17:00:00') * 1000;
        ?>
        <style>
            /* Resets for consistent styling */
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            /* General styling for all containers */
            .prize-container,
            .milestone-container,
            .table-container {

                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
                margin-bottom: 20px;
                /* Spacing between containers */
                width: 745px;
                /* Adjusted width for two containers side by side */
                margin-right: 20px;
                /* Right margin for spacing */
            }

            /* Clears the right margin for the second container */
            .prize-container:last-child,
            .milestone-container:last-child {
                margin-right: 0;
            }

            /* Ensures containers don't wrap */
            .flex-container {
                display: flex;
                justify-content: space-between;
                /* Spacing out children */
                flex-wrap: nowrap;
                /* Prevent wrapping */
            }

            /* Specific container adjustments */
            .prize-container {
                min-height: 300px;
                /* Adjust this value as needed to match your content */
            }

            .milestone-container {
                min-height: 300px;
                /* Adjust this value as needed to match your content */
            }

            /* Data Table Styling */
            .table-container {
                width: calc(50% - 30px);
                /* Adjust for margins */
                vertical-align: top;
                /* Aligns tables to the top */
            }

            /* Flex container for bottom tables to ensure alignment and even spacing */
            .bottom-tables {
                display: flex;
                justify-content: space-between;
                /* Evenly spaces the child tables */
            }

            /* Full-width containers on smaller screens */
            @media screen and (max-width: 768px) {

                .prize-container,
                .milestone-container,
                .table-container {
                    width: 90%;
                    margin: 0 auto;
                    display: block;
                    /* Stacks containers */
                }

                .flex-container,
                .bottom-tables {
                    flex-wrap: wrap;
                    /* Allows wrapping on small screens */
                    justify-content: center;
                    /* Centers the tables */
                }
            }

            /* Table element styling */
            table.myTable {
                width: 100%;
                /* Full width of their container */
                margin: 0 auto;
                border-collapse: collapse;
                /* Collapses border spacing */
            }

            .flex-container {
                display: flex;
                width: 90%;
                margin: 0 auto;
                justify-content: space-between;
                flex-wrap: nowrap;
            }

            table.myTable th,
            table.myTable td {
                text-align: left;
                padding: 8px;
                border: 1px solid #444;
                /* Borders for cells */
            }

            .reward-box {

                padding: 10px;
                margin: 0 10px;
                /* Horizontal spacing between reward boxes */
                display: inline-block;
                /* Allows side-by-side layout */
                width: calc(33.333% - 20px);
                /* Adjust width for three boxes in a row */
                vertical-align: top;
                /* Align the tops of the boxes if they have unequal heights */
                box-sizing: border-box;
                /* Include padding and border in the element's width and height */
            }

            .rewards-container {
                text-align: center;
                /* Center the text within the container */
                margin-bottom: 20px;
                /* Spacing below the rewards section */
            }
        </style>

        <?php
        date_default_timezone_set('Europe/London'); // This will automatically account for BST as well
        
        // Target date and time for 31st July at 5 PM UK time
        $targetDate = strtotime('July 31, 2024 17:00:00');

        // Pass the target date to JavaScript
        echo "<script>var targetDate = $targetDate * 1000; // Convert to milliseconds for JavaScript</script>";
        // Your MySQL connection code goes here (assuming $conn is your connection variable)
        
        echo '<div class="contenthead floaty">';
        echo '    <h4>Welcome to ChaosCity Raid and Attack contest</h4>';
        echo '    <p>This is your chance to win some amazing prizes! All you need to do';
        echo 'is win more Raids and Attacks than your fellow players.</p>';
        echo '    <p><span style="color: white;">Note: Obtain Bonus points to your counter when Summoning Bosses.</span></p>';
        echo '<h4><font color=orange>Both Competitions will end in</font> <div id="countdownTimer" style="color: #FF0000; font-size: 20px;"></div></h4>';

        echo '        <!-- Timer will be displayed here -->';
        echo '    </div>';
        echo '</div>';

        echo '<script>';
        echo 'document.addEventListener(\'DOMContentLoaded\', function() {';
        echo '    var countDownDate = ' . $targetDateMilliseconds . ';'; // Concatenate the PHP variable
        
        echo '    function updateCountdown() {';
        echo '        var now = new Date().getTime();';
        echo '        var distance = countDownDate - now;';

        echo '        var days = Math.floor(distance / (1000 * 60 * 60 * 24));';
        echo '        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));';
        echo '        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));';
        echo '        var seconds = Math.floor((distance % (1000 * 60)) / 1000);';

        echo '        document.getElementById("countdownTimer").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";';

        echo '        if (distance < 0) {';
        echo '            clearInterval(x);';
        echo '            document.getElementById("countdownTimer").innerHTML = "This Competition has ended";';
        echo '        }';
        echo '    };';

        echo '    var x = setInterval(updateCountdown, 1000);';
        echo '});';
        echo '</script>';

        // Duplicate rewards section
        echo '<div class="flex-container">'; // Wrapper for prize and milestone sections
        
        // Prize Container
        echo '<div class="prize-container">';

        // Rewards Header
        echo '<h3>Raid Comp Rewards</h3>';
        // 1st, 2nd, and 3rd Place
        echo '<div class="reward-box" style="color: gold;">';
        echo '<strong>1ST PLACE</strong><br>';
        echo '150,000 Points<br>';
        echo '$50,000,000<br>';
        echo '100x Raid tokens';
        echo '</div>';

        // 2ND Place Reward Box
        echo '<div class="reward-box" style="color: silver;">';
        echo '<strong>2ND PLACE</strong><br>';
        echo '100,000 Points<br>';
        echo '$25,000,000<br>';
        echo '50x Raid tokens';
        echo '</div>';

        // 3RD Place Reward Box
        echo '<div class="reward-box" style="color: bronze;">';
        echo '<strong>3RD PLACE</strong><br>';
        echo '50,000 Points<br>';
        echo '$12,500,000<br>';
        echo '25x Raid tokens';
        echo '</div>';

        echo '<h3>Milestone Rewards(Only Highest Reward Paid)</h3>';
        echo '- <font color=bronze>50 Raid event points</font>: 25,000 Points<br>';
        echo '- <font color=silver>100 Raid event points</font>: 50,000 Points<br>';
        echo '- <font color=gold>250 Raid event points</font>: 100,000 Points<br>';
        echo '- <font color=orange>500 Raid event points</font>: 125,000 Points<br>';
        echo '- <font color=green>750 Raid event points</font>: 150,000 Points<br>';
        echo '- <font color=red>1,000 Raid event points</font>: 200,000 Points<br>';
        echo '- <font color=gold>1,250 Raid event points</font>: 250,000 Points<br>';
        echo 'This Competition Will end on the 31st of March 2024 at 5pm (Game Time)';
        echo '</div>'; // Close prize-container
        
        // Duplicate Prize Container (for demonstration as per your request)
        echo '<div class="prize-container">';

        // Rewards Header again for the duplicated container
        echo '<h3>Kill Comp Rewards</h3>';
        // 1st, 2nd, and 3rd Place duplicated
        echo '<div class="reward-box" style="color: gold;">';
        echo '<strong>1ST PLACE</strong><br>';
        echo '150,000 Points<br>';
        echo '$50,000,000<br>';
        echo '100x Raid tokens';
        echo '</div>';

        // 2ND Place Reward Box
        echo '<div class="reward-box" style="color: silver;">';
        echo '<strong>2ND PLACE</strong><br>';
        echo '100,000 Points<br>';
        echo '$25,000,000<br>';
        echo '50x Raid tokens';
        echo '</div>';

        // 3RD Place Reward Box
        echo '<div class="reward-box" style="color: bronze;">';
        echo '<strong>3RD PLACE</strong><br>';
        echo '50,000 Points<br>';
        echo '$12,500,000<br>';
        echo '25x Raid tokens';
        echo '</div>';


        // Milestone Rewards within the second container
        echo '<h3>Milestone Rewards(Only Highest Reward Paid)</h3>';
        echo '<div class="reward-tier"><font color=bronze>1,000 Kills</font>: 2,500 Points<br></div>';
        echo '<div class="reward-tier"><font color=silver>2,500 Kills</font>: 6,500 Points<br></div>';
        echo '<div class="reward-tier"><font color=gold>5,000 Kills</font>: 13,000 Points<br></div>';
        echo '<div class="reward-tier"><font color=orange>15,000 Kills</font>: 37,500 Points<br></div>';
        echo '<div class="reward-tier"><font color=green>30,000 Kills</font>: 75,000 Points<br></div>';
        echo '<div class="reward-tier"><font color=red>50,000 Kills</font>: 150,000 Points<br></div>';

        echo 'This Competition Will end on the 31st of March 2024 at 5pm (Game Time)';

        echo '</div>'; // Close the second prize-container (now with milestones)
        
        echo '</div>';

        // Right side content - Leaderboard tables
        echo '<div class="right-side" style="display: flex;">'; // Add flex display for side-by-side layout
        
        // First leaderboard table
        echo '<div class="table-container" style="margin:0 auto;">'; // Set width to 100% to stretch across the right side
        echo '<table width="100%" cellpadding="4" cellspacing="0" class="myTable">';
        echo '<tr><th>Rank</th><th>Username</th><th>RaidEvent Points</th></tr>';

        $db->query("SELECT id, username, raidcomp FROM grpgusers ORDER BY raidcomp DESC LIMIT 50");
        $db->execute();
        $result = $db->fetch_row();

        if (empty($result)) {
            die('No ongoing raid contest found.');
        }

        $rank = 1;
        foreach ($result as $row) {
            $formattedName = htmlspecialchars($row['username']);
            echo '<tr>';
            echo '<td>' . $rank++ . '</td>';
            echo '<td>' . $formattedName . '</td>';
            echo '<td>' . $row['raidcomp'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>'; // Closing the container for the first table
        
        // Second leaderboard table
        echo '<div class="table-container" style="margin:0 auto;">'; // Set width to 100% to stretch across the right side
        echo '<table width="100%" cellpadding="4" cellspacing="0" class="myTable">';
        echo '<tr><th>Rank</th><th>Username</th><th>Kills</th></tr>';

        $db->query("SELECT id, username, killcomp1 FROM grpgusers ORDER BY killcomp1 DESC LIMIT 50");
        $db->execute();
        $result = $db->fetch_row();

        $rank = 1; // Reset rank for the second table
        foreach ($result as $row) {
            $formattedName = htmlspecialchars($row['username']);
            echo '<tr>';
            echo '<td>' . $rank++ . '</td>';
            echo '<td>' . $formattedName . '</td>';
            echo '<td>' . $row['killcomp1'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>';

        require "footer.php";