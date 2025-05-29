<?php
include 'header.php';
?>
<div class='box_top'>Raid Stats</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        // Fetch user's current raid stats, tiers, raidpoints, and raidtokens using $user_class
        $user_data = [
            'raidstrength' => $user_class->raidstrength,
            'raidstrength_tier' => $user_class->raidstrength_tier,
            'raiddefense' => $user_class->raiddefense,
            'raiddefense_tier' => $user_class->raiddefense_tier,
            'raidagility' => $user_class->raidagility,
            'raidagility_tier' => $user_class->raidagility_tier,
            'raidluck' => $user_class->raidluck,
            'raidluck_tier' => $user_class->raidluck_tier,
            'raidpoints' => $user_class->raidpoints,
            'raidtokens' => $user_class->raidtokens
        ];

        // Handle stat upgrade if requested
        if (isset($_POST['stat_to_upgrade'])) {
            $stat_to_upgrade = $_POST['stat_to_upgrade'];

            if (isset($_POST['tier_upgrade'])) {
                // Upgrade the tier
                if ($user_data['raidtokens'] >= 10) {
                    perform_query("UPDATE grpgusers SET raidtokens = raidtokens - 10, {$stat_to_upgrade}_tier = {$stat_to_upgrade}_tier + 1, $stat_to_upgrade = 0 WHERE id = ?", [$user_class->id]);
                    header('Location: raid_stats_page.php');
                    exit;
                } else {
                    $error_msg = "You do not have enough raid tokens to upgrade the tier!";
                }
            } else {
                // Upgrade the stat
                $targetLevel = intval($_POST['target_level']);
                $currentLevel = $user_data[$stat_to_upgrade];
                $availablePoints = $user_data['raidpoints'];
                $upgradeCost = 100 * ($currentLevel + 1);
                $totalCost = 0;

                while ($currentLevel < $targetLevel) {
                    $totalCost += $upgradeCost;
                    $currentLevel++;
                    $upgradeCost = 100 * ($currentLevel + 1);
                }

                if ($availablePoints >= $totalCost) {
                    perform_query("UPDATE grpgusers SET raidpoints = raidpoints - $totalCost, $stat_to_upgrade = $currentLevel WHERE id = ?", [$user_class->id]);
                    header('Location: raid_stats_page.php');
                    exit;
                } else {
                    $error_msg = "You do not have enough raid points to upgrade this stat!";
                }
            }
        }
        ?>
        <!-- Styling for the card, bars, and descriptions -->
        <style>
            /* Add the existing styles from the raid competition page */
            .prize-container,
            .contenthead.floaty {
                color: black;
                /* Light text */
                padding: 20px;
                border-radius: 10px;
                /* Rounded corners */
                box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
                margin-bottom: 20px;
            }

            .contenthead {
                margin-bottom: 20px;
                /* Space below the content head */
            }

            .floaty {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                /* Subtle shadow */
                margin-bottom: 20px;
                /* Space below each floaty section */
            }

            /* Add additional styles for the stats page as needed */

            .raid-stats-card {
                background-color: #282c36;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                max-width: 800px;
                margin: 50px auto;
                color: #FFF;
                transition: all 0.3s ease-in-out;
                /* Smooth transition for any changes */
            }

            .stat-bar-container {
                display: flex;
                gap: 2px;
                margin-bottom: 15px;
                padding: 5px;
                /* Adjust padding for alignment */
                background-color: #191919;
                /* White background for the container */
                border-radius: 5px;
                /* Slightly rounded corners for the container */
                box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
                /* Inner shadow for depth */
            }

            .stat-bar {
                flex-grow: 1;
                height: 10px;
                /* Smaller height for the bars */
                background-color: #eee;
                /* Light grey as default background for bars */
                border-radius: 3px;
                /* Rounded corners for bars */
                transition: background-color 0.3s;
                /* Transition for the background color */
            }

            .stat-bar.active {
                background-color: #4CAF50;
                /* Active bars show in green */
            }

            .stat-bar:hover {
                cursor: pointer;
                background-color: #8BC34A;
                /* Lighter green on hover for potential upgrade */
            }

            .stat-bar.not-possible {
                background-color: #F44336;
                /* Red color when upgrade is not possible */
                cursor: not-allowed;
            }

            /* Style for a single full tab to show progress */
            .stat-progress {
                width: 100%;
                /* Full width to represent total progress */
                height: 10px;
                /* Same height as individual bars */
                background-color: #4CAF50;
                /* Green background for completed progress */
                border-radius: 3px;
                /* Rounded corners for progress bar */
                box-shadow: 0 0 3px rgba(0, 0, 0, 0.4);
                /* Outer shadow for depth */
            }

            /* Additional styles to visually separate each tab */
            .stat-bar:not(:last-child) {
                margin-right: 2px;
                /* Space after each bar except the last */
            }

            .stat-bar:first-child {
                margin-left: 2px;
                /* Space before the first bar */
            }


            .stat-tier {
                font-weight: bold;
                margin-bottom: 10px;
            }

            .upgrade-tier-button {
                display: inline-block;
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                cursor: pointer;
                transition: transform 0.3s ease-in-out;
                /* Adding smooth hover scale effect */
            }

            .upgrade-tier-button:hover {
                transform: scale(1.05);
                /* Scaling on hover */
            }

            /* Styling for the icons */
            .stat-icon {
                width: 20px;
                height: 20px;
                margin-right: 10px;
                display: inline-block;
                background-repeat: no-repeat;
                background-size: cover;
            }

            .strength-icon {
                background-image: url('path_to_strength_icon.png');
                /* replace with your icon path */
            }

            .defense-icon {
                background-image: url('path_to_defense_icon.png');
            }

            .agility-icon {
                background-image: url('path_to_agility_icon.png');
            }

            .luck-icon {
                background-image: url('path_to_luck_icon.png');
            }

            /* Styling for the tutorial */
            .tutorial-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 1000;
                display: none;
            }

            .tutorial-content {
                position: absolute;
                background: #282c36;
                padding: 20px;
                border-radius: 5px;
                width: 70%;
                max-width: 400px;
                text-align: center;
                color: #FFF;
            }

            .highlighted {
                position: relative;
                z-index: 1001;
                outline: 3px solid #4CAF50;
            }

            body.no-scroll {
                overflow: hidden;
            }

            .tabs-container {
                display: flex;
                justify-content: center;
                margin-bottom: 20px;
            }

            .tab {
                padding: 10px 20px;
                margin: 0 10px;
                color: black;
                border: none;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .tab:hover {
                background-color: #555;
            }

            .tab.active {
                background-color: #4CAF50;
            }

            .content-container {
                display: flex;
                justify-content: space-between;
                /* Space out the content boxes equally */
                flex-wrap: wrap;
                /* Allows boxes to wrap on smaller screens */
                padding: 20px;
            }

            .content-box {
                width: calc(25% - 20px);
                /* Adjusting width to account for margin */
                padding: 15px 5px 10px;
                /* Specific padding inside the boxes */
                color: black;
                /* Light text color for the content */
                border-radius: 10px;
                /* Rounded corners for the boxes */
                box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
                /* Shadow for depth */
                margin: 10px;
                /* Margin around each box */
                box-sizing: border-box;
                /* Include padding and border in the element's total width and height */
            }

            /* Adjustments for responsiveness */
            @media (max-width: 768px) {
                .content-box {
                    width: calc(50% - 20px);
                    /* Adjust width for smaller screens to display two boxes per row */
                }
            }

            @media (max-width: 480px) {
                .content-box {
                    width: calc(100% - 20px);
                    /* On very small screens, each box takes almost full width */
                }
            }

            .stats-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .stat-box {
                flex-basis: calc(25% - 10px);
                /* Adjust width to 25% minus margins */
                margin: 5px;
                /* Margin for spacing */
                color: black;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
            }

            /* You can continue with the rest of the styling elements as per your current CSS */
            .full-width-container {
                width: 100%;
                /* Ensures the container stretches across the full width */
                margin: 0 auto;
                /* Centers the container if needed */
                padding: 20px 0;
                /* Adds some vertical padding for spacing */
            }

            .full-width-box {
                width: calc(100% - 40px);
                /* Adjusts the width to account for padding */
                margin: 0 auto;
                /* Centers the box within the container */
                padding: 20px;
                /* Adds padding inside the box */
                color: black;
                /* Light text color */
                border-radius: 10px;
                /* Rounded corners */
                box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
                /* Shadow for depth */
            }

            /* Adjusts for smaller screens, if necessary */
            @media (max-width: 768px) {
                .full-width-box {
                    width: calc(100% - 20px);
                    /* Reduces the width adjustment for smaller padding */
                }
            }
        </style>



        <?php
        // Assuming $user_data array is already populated with the user's data
        echo '<div class="contenthead floaty">';

        echo '<h4>Raid Character Upgrades</h4>';
        echo '<div class="contenthead floaty">';


        echo '<div class="content-container">'; // Container for the stat boxes
        
        $stat_descriptions = [
            "raidstrength" => "Strength determines your power in raid battles against Bosses.<br>",
            "raiddefense" => "Defense reduces the damage you take from raid bosses.",
            "raidagility" => "Agility affects your speed in raids. Upgrade to Hit your opponent faster",
            "raidluck" => "Luck increases your chances of getting better loot from raids."
        ];

        $stat_icons = [
            "raidstrength" => "strength-icon",
            "raiddefense" => "defense-icon",
            "raidagility" => "agility-icon",
            "raidluck" => "luck-icon"
        ];

        foreach ($stat_descriptions as $stat_name => $description) {
            echo '<div class="content-box">';
            echo '<span class="stat-icon ' . $stat_icons[$stat_name] . '"></span>';
            echo '<h3>' . ucfirst(str_replace("raid", "", $stat_name)) . ' - Tier: <span class="stat-tier">' . $user_data[$stat_name . "_tier"] . '</span></h3>';
            echo '<p>Level: ' . $user_data[$stat_name] . '</p>';
            echo '<p>' . $description . '</p>';
            echo '<div class="stat-bar-container" data-stat-name="' . $stat_name . '">';
            for ($i = 0; $i < 10; $i++) { // Modify to 5 for a 5-tier system
                $active_class = ($i < $user_data[$stat_name]) ? "active" : "";
                echo '<div class="stat-bar ' . $active_class . '" onclick="upgradeStat(\'' . $stat_name . '\', ' . ($i + 1) . ');"></div>';
            }
            echo '</div>';
            if ($user_data[$stat_name] >= 10) {
                echo '<form method="post" action="raid_stats_page.php">';
                echo '<input type="hidden" name="stat_to_upgrade" value="' . $stat_name . '">';
                echo '<input type="hidden" name="tier_upgrade" value="true">';
                echo '<button type="submit" class="upgrade-tier-button">Upgrade Tier for 10 Raid Tokens</button>';
                echo '</form>';
            }
            echo '</div>'; // Close .content-box
        }

        echo '</div>'; // Close .content-container
        echo '</div>'; // Close .contenthead floaty
        echo '<div class="content-container">'; // Container for the stat boxes
        
        // Adding your text in a content box
        echo '<div class="contenthead floaty full-width-container">';
        echo '<h4>Available Raid Points: <font color=yellow>' . $user_data['raidpoints'] . '</font></h4>';
        echo '<h4>Available Raid Tokens:<font color=red> ' . $user_data['raidtokens'] . '</font></h4>';
        if (isset($error_msg)) {
            echo '<p style="color: red;">' . $error_msg . '</p>';
        }
        echo '</div>'; // Close this specific content box
        echo '</div>'; // Close the full-width container
        
        // ... [Your existing scripts for the tutorial and bar hover] ...
        ?>
        <!-- JavaScript for the tutorial -->
        <script>
            let tutorialSteps = [
                {
                    element: '.raid-stats-card h2',
                    message: 'This is where you can see and upgrade your raid character stats.'
                },
                {
                    element: '.raid-stats-card p',
                    message: 'Here you can see your available raid points and tokens.'
                },
                {
                    element: '.stat-section',
                    message: 'Each of these sections represents a stat. You can upgrade them using your raid points.'
                },
                {
                    element: '.stat-bar-container',
                    message: 'These bars show the progress of each stat. You can upgrade them one by one.'
                },
                {
                    element: '.stat-bar-container',
                    message: 'Once you fully upgrade a stat to 10/10, you can then upgrade its tier at the cost of 10 Raid Tokens.'
                }];
            let currentStep = 0;
            let overlay = null;
            let content = null;

            function startTutorial() {
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.className = 'tutorial-overlay';
                    document.body.appendChild(overlay);

                    content = document.createElement('div');
                    content.className = 'tutorial-content';
                    overlay.appendChild(content);
                }
                document.body.classList.add('no-scroll'); // Prevent scrolling
                showStep(tutorialSteps[currentStep]);
            }

            function showStep(step) {
                let targetElement = document.querySelector(step.element);
                targetElement.classList.add('highlighted');
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                content.innerHTML = step.message + '<br><button onclick="nextStep()">Next</button><button onclick="endTutorial()">Close</button>';
                let rect = targetElement.getBoundingClientRect();
                content.style.top = (rect.bottom + 20) + 'px';
                content.style.left = rect.left + 'px';
                overlay.style.display = 'block';
            }

            function nextStep() {
                let prevElement = document.querySelector(tutorialSteps[currentStep].element);
                prevElement.classList.remove('highlighted');
                currentStep++;
                if (currentStep < tutorialSteps.length) {
                    showStep(tutorialSteps[currentStep]);
                } else {
                    endTutorial();
                }
            }

            function endTutorial() {
                let prevElement = document.querySelector(tutorialSteps[currentStep].element);
                if (prevElement) {
                    prevElement.classList.remove('highlighted');
                }
                overlay.style.display = 'none';
                document.body.classList.remove('no-scroll'); // Restore scrolling
                currentStep = 0; // Reset the tutorial step so you can restart it
            }
        </script>
        <!-- JavaScript to handle precise cascading hover effect and multiple upgrades -->
        <script>
            function handleSingleBarHover(bar, availablePoints, currentLevel, barIndex) {
                let upgradeCost = 100 * (currentLevel + 1);
                let tempPoints = availablePoints;

                for (let i = currentLevel; i <= barIndex; i++) {
                    if (tempPoints >= upgradeCost) {
                        bar.parentNode.children[i].classList.add("hover-possible");
                        tempPoints -= upgradeCost;
                        upgradeCost += 100;
                    } else if (i === barIndex) {
                        bar.classList.add("hover-not-possible");
                        break;
                    }
                }
            }

            function resetHover(bar, currentLevel, barIndex) {
                for (let i = currentLevel; i <= barIndex; i++) {
                    bar.parentNode.children[i].classList.remove("hover-possible", "hover-not-possible");
                }
            }

            function upgradeStat(statName, targetLevel) {
                let stats = {
                    'raidstrength': <?php echo $user_data['raidstrength']; ?>,
                    'raiddefense': <?php echo $user_data['raiddefense']; ?>,
                    'raidagility': <?php echo $user_data['raidagility']; ?>,
                    'raidluck': <?php echo $user_data['raidluck']; ?>
                };

                let currentLevel = stats[statName];
                let availablePoints = <?php echo $user_data['raidpoints']; ?>;
                let upgradeCost = 100 * (currentLevel + 1);
                let totalCost = 0;

                while (currentLevel < targetLevel) {
                    totalCost += upgradeCost;
                    currentLevel++;
                    upgradeCost = 100 * (currentLevel + 1);
                }

                if (confirm('Do you want to upgrade this stat to level ' + targetLevel + ' for ' + totalCost + ' raid points?')) {
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'raid_stats_page.php';

                    var inputStat = document.createElement('input');
                    inputStat.type = 'hidden';
                    inputStat.name = 'stat_to_upgrade';
                    inputStat.value = statName;

                    var inputTarget = document.createElement('input');
                    inputTarget.type = 'hidden';
                    inputTarget.name = 'target_level';
                    inputTarget.value = targetLevel;

                    form.appendChild(inputStat);
                    form.appendChild(inputTarget);
                    document.body.appendChild(form);
                    form.submit();
                }
            }


            function loadContent(tab) {
                let xhr = new XMLHttpRequest();
                let url = "";

                // Determine the URL based on the tab
                if (tab === 'bosses') {
                    url = 'raids2.php';  // Replace with the actual path
                } else if (tab === 'stats') {
                    url = 'raid_stats_page.php';
                }

                xhr.open('GET', url, true);
                xhr.onload = function () {
                    if (this.status == 200) {
                        document.getElementById('contentContainer').innerHTML = this.responseText;

                        // Highlight the active tab
                        let tabs = document.querySelectorAll('.tab');
                        tabs.forEach(function (t) {
                            if (t.innerText.toLowerCase().includes(tab)) {
                                t.classList.add('active');
                            } else {
                                t.classList.remove('active');
                            }
                        });
                    }
                }
                xhr.send();
            }


        </script>
    </div>