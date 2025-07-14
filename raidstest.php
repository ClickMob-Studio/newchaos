<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure a connection to the database is established

// Fetch all the bosses
$query = "SELECT * FROM bosses";
$result = mysql_query($query);
$bosses = [];
while ($row = mysql_fetch_assoc($result)) {
    $bosses[] = $row;
}




// Assuming this is at the top of your raids.php file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_raid_id'])) {
    $raid_id = intval($_POST['join_raid_id']);
    $user_id = $user_class->id;  // Assuming this is how you get the logged-in user's ID

    // Check if the user is already a participant in an ongoing raid
    $check_query = "SELECT rp.raid_id 
                    FROM raid_participants rp 
                    JOIN active_raids ar ON rp.raid_id = ar.id 
                    WHERE rp.user_id = $user_id AND TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(ar.summoned_at, INTERVAL 1 HOUR)) > 0";
    $check_result = mysql_query($check_query);

    if (mysql_num_rows($check_result) > 0) {
        echo "You are already in a raid!";
    } else {
        $join_query = "INSERT INTO raid_participants (raid_id, user_id) VALUES ($raid_id, $user_id)";
        mysql_query($join_query);

        // Update the raidsjoined count
        $update_raidsjoined_query = "UPDATE grpgusers SET raidsjoined = raidsjoined + 1 WHERE id = $user_id";
        mysql_query($update_raidsjoined_query);

        // Get the owner of the raid
        $owner_query = "SELECT summoned_by FROM active_raids WHERE id = $raid_id";
        $owner_result = mysql_query($owner_query);
        $owner_data = mysql_fetch_assoc($owner_result);
        $owner_id = $owner_data['summoned_by'];

        // Convert the joining user's ID to their actual name
        $joining_user_name = formatName($user_id);

        // Create a notification for the raid owner
        $event_message = "$joining_user_name has joined your raid!";
        send_event($owner_id, $event_message);

        echo "Successfully joined the raid!";
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['use_speedup'], $_POST['raid_id'])) {
    $raid_id = intval($_POST['raid_id']);
    $user_id = $user_class->id;

    // Reduce the quantity of the item in the inventory by 1
    $reduce_item_query = "UPDATE inventory SET quantity = quantity - 1 WHERE itemid = 194 AND userid = $user_id AND quantity > 0";
    mysql_query($reduce_item_query);

    // Set the summoned_at column in the active_raids table to the current timestamp
    $end_raid_query = "UPDATE active_raids SET summoned_at = DATE_SUB(NOW(), INTERVAL 1 HOUR) WHERE id = $raid_id";
    mysql_query($end_raid_query);

    header("Location: raids.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['boss_id'], $_POST['difficulty'])) {
    $user_id = $user_class->id;  // Assuming this is how you get the logged-in user's ID

    // Check user's raid tokens
    $token_check_query = "SELECT raidtokens FROM grpgusers WHERE id = $user_id";
    $token_check_result = mysql_query($token_check_query);
    $user_tokens = mysql_fetch_assoc($token_check_result)['raidtokens'];

    if ($user_tokens < 1) {
        echo "<script>alert('You do not have enough raid tokens to summon a raid.');</script>";
        return;  // Exit early so the rest of the code doesn't run
    }

    // Check if the user is already a participant in any active raid
    $check_query = "
        SELECT rp.* FROM raid_participants rp
        JOIN active_raids ar ON rp.raid_id = ar.id
        WHERE rp.user_id = $user_id AND DATE_ADD(ar.summoned_at, INTERVAL 1 HOUR) > NOW()
    ";
    $check_result = mysql_query($check_query);

    if (mysql_num_rows($check_result) > 0) {
        // The user is already in an active raid, display an error message
        echo "<script>alert('You are already in an active raid. You cannot summon another one at the moment.');</script>";
    } else {
        // Fetch boss level
        $boss_id = intval($_POST['boss_id']);
        $boss_level_query = "SELECT level FROM bosses WHERE id = $boss_id";
        $boss_level_result = mysql_query($boss_level_query);
        $boss_level = mysql_fetch_assoc($boss_level_result)['level'];

        // Fetch user level
        $user_level_query = "SELECT level FROM grpgusers WHERE id = $user_id";
        $user_level_result = mysql_query($user_level_query);
        $user_level = mysql_fetch_assoc($user_level_result)['level'];

        if ($user_level < $boss_level) {
            echo "<script>alert('You must be at least level $boss_level to summon this boss.');</script>";
            return;
        }

        $difficulty = $_POST['difficulty'];
        $query = sprintf(
            "INSERT INTO active_raids (boss_id, summoned_by, difficulty) VALUES (%d, %d, '%s')",
            $boss_id,
            $user_id,
            mysql_real_escape_string($difficulty)
        );
        mysql_query($query);

        // Get the ID of the raid that was just inserted
        $raid_id = mysql_insert_id();

        // Insert the user into the raid_participants table
        $insert_participant_query = sprintf(
            "INSERT INTO raid_participants (raid_id, user_id) VALUES (%d, %d)",
            $raid_id,
            $user_id
        );
        mysql_query($insert_participant_query);

        // Deduct one raid token from the user's account
        $deduct_token_query = "UPDATE grpgusers SET raidtokens = raidtokens - 1, raidshosted = raidshosted + 1,  raidsjoined = raidsjoined + 1  WHERE id = $user_id";
        mysql_query($deduct_token_query);
        echo "<script>alert('1 raid token has been spent.');</script>";

        // Redirect to raids.php to refresh the page
        header('Location: raids.php');
        exit;
    }
}



// Fetch all active raids
$active_raids_query = "SELECT ar.*, b.name AS boss_name, b.image_link, TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(ar.summoned_at, INTERVAL 1 HOUR)) AS seconds_remaining FROM active_raids ar JOIN bosses b ON ar.boss_id = b.id WHERE DATE_ADD(ar.summoned_at, INTERVAL 1 HOUR) > NOW()";
$active_raids_result = mysql_query($active_raids_query);
$active_raids = [];
while ($row = mysql_fetch_assoc($active_raids_result)) {
    $active_raids[] = $row;
}

// Display active raids
echo "<h3>Active Raids</h3>";
echo "<hr>";
echo "<div class='active-raids-grid'>";
foreach ($active_raids as $raid) {
    $summoner_name = formatName($raid['summoned_by']);

    echo "<div class='raid-card'>";
    echo "<img src='" . $raid['image_link'] . "' alt='Boss Image' class='boss-image'>";
    echo "<h3>" . $raid['boss_name'] . " (Summoned by " . $summoner_name . ")</h3>";
    echo "<p>Difficulty: " . $raid['difficulty'] . "</p>";
    echo "<p>Time Remaining: <span class='timer' data-seconds='" . $raid['seconds_remaining'] . "'>Calculating...</span></p>";

    // Check if the user is already a participant of this raid
    $participant_query = "SELECT * FROM raid_participants WHERE raid_id = " . $raid['id'] . " AND user_id = " . $user_class->id;
    $participant_result = mysql_query($participant_query);

    if (mysql_num_rows($participant_result) > 0) {
        echo "<button class='btn btn-success' disabled>Joined</button>";
        // Check if the user has item 194 (Raid Speedups)
        $item_check_query = "SELECT SUM(quantity) as total_quantity FROM inventory WHERE itemid = 194 AND userid = " . $user_class->id;
        $item_check_result = mysql_query($item_check_query);
        $item_data = mysql_fetch_assoc($item_check_result);

        if ($item_data['total_quantity'] > 0) {
            echo "<form action='raids.php' method='post'>";
            echo "<input type='hidden' name='use_speedup' value='1'>";
            echo "<input type='hidden' name='raid_id' value='" . $raid['id'] . "'>";
            echo "<button type='submit'>Use Raid Speedups (" . $item_data['total_quantity'] . " left)</button>";
            echo "</form>";
        }
    } else {
        echo "<form action='raids.php' method='post' style='display:inline;'>";
        echo "<input type='hidden' name='join_raid_id' value='" . $raid['id'] . "'>";
        echo "<button type='submit' class='btn btn-primary'>Join Raid</button>";
        echo "</form>";
    }
    // Get the number of participants for this raid
    $count_query = "SELECT COUNT(*) as participant_count FROM raid_participants WHERE raid_id = " . $raid['id'];
    $count_result = mysql_query($count_query);
    $count_row = mysql_fetch_assoc($count_result);
    $participants = $count_row['participant_count'];

    echo "<p>Participants: " . $participants . "</p>";

    echo "</div>";
}
echo "</div>";

?>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const timers = document.querySelectorAll('.timer');

        timers.forEach(timer => {
            const secondsRemaining = timer.getAttribute('data-seconds');
            let countDownDate = new Date().getTime() + secondsRemaining * 1000;

            let x = setInterval(function () {
                let now = new Date().getTime();
                let distance = countDownDate - now;

                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timer.textContent = hours + "h " + minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    clearInterval(x);
                    timer.textContent = "EXPIRED";
                }
            }, 1000);
        });
    });
</script>

<script>
    function showTooltip(event, element) {
        var tooltip = element.nextElementSibling;

        // Get the mouse coordinates and set the tooltip's position
        var left = event.clientX;
        var top = event.clientY;

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';

        // Show the tooltip
        tooltip.style.display = 'block';

        // Hide the tooltip after a short delay (2 seconds in this case)
        setTimeout(function () {
            tooltip.style.display = 'none';
        }, 2000);
    }
</script>
<h3>
    <!-- Styling for the card, bars, and descriptions -->
    <style>
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
        }

        .stat-bar {
            flex: 1;
            height: 20px;
            background-color: #555;
            border-radius: 2px;
            cursor: pointer;
            transition: background-color 0.3s, width 0.3s;
            /* Adding animation */
        }

        .stat-bar.active {
            background-color: #4a90e2;
        }

        .stat-bar.hover-possible {
            background-color: #4CAF50;
        }

        .stat-bar.hover-not-possible {
            background-color: #F44336;
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
            background-color: #333;
            color: white;
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
            padding: 20px;
            background-color: #f4f4f4;
        }
    </style>




    <!-- Raid Stats Card -->
    <div class="raid-stats-card">
        <h2>Raid Character Stats</h2>
        <p>Available Raid Points: <?php echo $user_data['raidpoints']; ?></p>
        <p>Available Raid Tokens: <?php echo $user_data['raidtokens']; ?></p>
        <button onclick="startTutorial()">Show Tutorial</button> <!-- Button to start the tutorial -->
        <?php if (isset($error_msg))
            echo '<p style="color: red;">' . $error_msg . '</p>'; ?>

        <?php
        $stat_descriptions = [
            "raidstrength" => "Strength determines your power in raid battles.",
            "raiddefense" => "Defense reduces the damage you take from raid bosses.",
            "raidagility" => "Agility affects your speed in raids.",
            "raidluck" => "Luck increases your chances of getting better loot from raids."
        ];

        $stat_icons = [  // Associating each stat with its icon
            "raidstrength" => "strength-icon",
            "raiddefense" => "defense-icon",
            "raidagility" => "agility-icon",
            "raidluck" => "luck-icon"
        ];

        foreach ($stat_descriptions as $stat_name => $description) {
            echo '<div class="stat-section">';
            echo '<span class="stat-icon ' . $stat_icons[$stat_name] . '"></span>';  // Displaying the icon
            echo '<h3>' . ucfirst(str_replace("raid", "", $stat_name)) . ' - Tier: <span class="stat-tier">' . $user_data[$stat_name . "_tier"] . '</span></h3>';
            if ($user_data[$stat_name] >= 10) {
                echo '<form method="post" action="raid_stats_page.php">
                    <input type="hidden" name="stat_to_upgrade" value="' . $stat_name . '">
                    <input type="hidden" name="tier_upgrade" value="true">
                    <button type="submit" class="upgrade-tier-button">Upgrade Tier for 10 Raid Tokens</button>
                  </form>';
            }
            echo '<p>' . $description . '</p>';

            echo '<div class="stat-bar-container" data-stat-name="' . $stat_name . '">';
            for ($i = 0; $i < 10; $i++) {
                $active_class = ($i < $user_data[$stat_name]) ? "active" : "";
                echo '<div class="stat-bar ' . $active_class . '" 
                     onmouseover="handleSingleBarHover(this, ' . $user_data['raidpoints'] . ', ' . $user_data[$stat_name] . ', ' . $i . ');"
                     onmouseout="resetHover(this, ' . $user_data[$stat_name] . ', ' . $i . ');" 
                     onclick="upgradeStat(\'' . $stat_name . '\', ' . ($i + 1) . ');"></div>';
            }
            echo '</div>';
            echo '</div>';
        }
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
    Current Statistics
</h3>
<hr>
<div class="raids-container">
    <div style="float:left; width:50%;">

        <?php
        // Fetch the user's raid stats
        $stats_query = "SELECT raidtokens, raidwins, raidlosses, raidsjoined, raidshosted FROM grpgusers WHERE id = " . $user_class->id;
        $stats_result = mysql_query($stats_query);
        $user_stats = mysql_fetch_assoc($stats_result);

        echo "<h3>Raid Wins: " . $user_stats['raidwins'] . "</h3>";
        echo "<h3>Raid Losses: " . $user_stats['raidlosses'] . "</h3>";
        echo "<h3>Raids Joined: " . $user_stats['raidsjoined'] . "</h3>";
        echo "<h3>Raids Hosted: " . $user_stats['raidshosted'] . "</h3>";
        ?>

    </div>

    <div style="float:right; width:50%;">

        <?php
        // Fetching top 5 raiders
        $top_raiders_query = "SELECT * FROM grpgusers ORDER BY raidpoints DESC LIMIT 5";
        $top_raiders_result = mysql_query($top_raiders_query);

        // Displaying the top 5 raiders
        echo "<h2>Top 5 Raiders</h2>";
        echo "<ul>";
        $player_position = 0;
        $rank = 1;
        while ($raider = mysql_fetch_assoc($top_raiders_result)) {
            echo "<li>" . formatName($raider['id']) . " with " . $raider['raidpoints'] . " Raid Points.</li>";
            if ($raider['id'] == $user_class->id) {
                $player_position = $rank;
            }
            $rank++;
        }
        echo "</ul>";

        // Fetch the player's raid points
        $player_raidpoints_query = "SELECT raidpoints FROM grpgusers WHERE id = " . $user_class->id;
        $player_raidpoints_result = mysql_query($player_raidpoints_query);
        $player_raidpoints_data = mysql_fetch_assoc($player_raidpoints_result);

        if ($player_position == 0) {
            // This means the player is not in the top 5. Let's find their exact position.
            $player_exact_position_query = "SELECT COUNT(*) AS position FROM grpgusers WHERE raidpoints > " . $player_raidpoints_data['raidpoints'];
            $player_exact_position_result = mysql_query($player_exact_position_query);
            $player_exact_position_data = mysql_fetch_assoc($player_exact_position_result);
            $player_position = $player_exact_position_data['position'] + 1; // Add 1 because the COUNT(*) gives the number of users above the player
        }

        echo "Your Position: " . $player_position . "<br>";
        echo "Your Raid Points: " . $player_raidpoints_data['raidpoints'];

        ?>

    </div>
    <div style="clear:both;"></div>



</div>

<h3>
    <?php
    include 'header.php';

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
    ?>Welcome to Raids
</h3>
<hr>

<div class="raids-container">



    <?php
    // Fetch the user's raid stats
    $stats_query = "SELECT raidtokens, raidwins, raidlosses, raidsjoined, raidshosted FROM grpgusers WHERE id = " . $user_class->id;
    $stats_result = mysql_query($stats_query);
    $user_stats = mysql_fetch_assoc($stats_result);

    echo "<h3><center>You currently have <font color=yellow>" . $user_stats['raidtokens'] . "</font> Raid Tokens </center></h3>";



    ?>



    <p>
        <font color=red>Raids are intense battles where you summon and face off against formidable bosses. The higher
            your level, the mightier the bosses you can summon. Each boss brings unique challenges and lucrative
            rewards. Choose your difficulty wisely and team up with others to defeat these menacing creatures!</font>
    </p>



    <h2>
        <center>
            <font color=green>Available Bosses</font>
        </center>
    </h2>

    <div class="bosses-grid">
        <?php
        foreach ($bosses as $boss) {
            // If the boss has a timestamp set and the current time has surpassed it, skip displaying this boss
            if (isset($boss['available_unixtimestamp']) && $boss['available_unixtimestamp'] <= time()) {
                continue;
            }

            echo "<div class='boss-card'>";
            echo "<img src='" . $boss['image_link'] . "' alt='Boss Image' class='boss-image'>";
            echo "<h3>" . $boss['name'] . "</h3>";

            // Check if the boss has an availability timestamp and if it's in the future
            if (isset($boss['available_unixtimestamp']) && $boss['available_unixtimestamp'] > time()) {
                $timeLeft = $boss['available_unixtimestamp'] - time();
                $hours = floor($timeLeft / 3600);
                $minutes = floor(($timeLeft / 60) % 60);
                echo "<p>Available for: <strong>$hours hours $minutes minutes</strong></p>";
            }

            echo "<p><strong>Level:</strong> " . $boss['level'] . "</p>";

            // Fetch possible rewards for this boss
            $rewards_query = "SELECT l.*, i.itemname FROM loot l JOIN items i ON l.item_id = i.id WHERE l.boss_id = " . $boss['id'];
            $rewards_result = mysql_query($rewards_query);
            $rewards = [];
            $min_points = 0;
            $max_points = 0;
            $min_money = 0;
            $max_money = 0;
            while ($reward = mysql_fetch_assoc($rewards_result)) {
                $rewards[] = $reward['itemname'];
                $min_points += $reward['min_points'];
                $max_points += $reward['max_points'];
                $min_money += $reward['min_money'];
                $max_money += $reward['max_money'];
            }
            // Display rewards in a dropdown tab
            echo "<div class='rewards-btn' onclick='toggleDropdown(this)'>View Rewards</div>";
            echo "<div class='rewards-dropdown'>";
            echo "<center><font color=green><u>Possible Rewards</u></font></center><br><font color=white>Potential Items:</font><font color=yellow>  " . implode(", ", $rewards) . "</font><br>";
            echo "<font color=white>Points - </font><font color=red> $min_points - $max_points</font><br>";
            echo "<font color=white> Money -</font> <font color=green> $$min_money - $$max_money</font>";
            echo "</div>";
            // Difficulty selection
            echo "<form action='raids.php' method='post' class='difficulty-form'>";
            echo "<label>Select Difficulty: ";
            echo "<select name='difficulty'>";
            echo "<option value='Easy'>Easy</option>";
            echo "<option value='Medium'>Medium</option>";
            echo "<option value='Hard'>Hard</option>";
            echo "</select></label>";
            echo "<input type='hidden' name='boss_id' value='" . $boss['id'] . "'>";
            echo "<input type='submit' value='Summon Boss' class='summon-button'>";
            echo "</form>";

            echo "<p><strong>Stat Limit:</strong> " . $boss['stat_limit'] . "</p>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
        function toggleDropdown(element) {
            var dropdown = element.nextElementSibling;
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }
    </script>

</div>

<style>
    .raids-container {
        background-color: #292727;
        padding: 20px;
        border-radius: 10px;
    }

    .bosses-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    p {
        color: #555;
        font-size: 16px;
        margin-bottom: 30px;
    }

    .boss-card {
        position: relative;
        border: 1px solid #ccc;
        padding: 10px;
        margin: 10px;
        border-radius: 5px;
        transition: all 0.3s;
        cursor: pointer;
    }

    .boss-rewards {
        position: relative;
        ;
        top: 0;
        left: 100%;
        background-color: #f5f5f5;
        border-left: 1px solid #ccc;
        width: 200px;
        height: 100%;
        padding: 10px;
        display: none;
        overflow-y: auto;
    }

    .boss-card:hover .boss-rewards {
        display: block;
    }

    .rewards-btn {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
    }

    .rewards-dropdown {
        display: none;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #2b2a2a;
        border-radius: 4px;
        max-width: 250px;
    }

    .rewards-tooltip {
        display: none;
        position: fixed;
        /* Use fixed positioning instead of absolute */
        white-space: nowrap;
        padding: 10px;
        background-color: #333;
        color: #fff;
        border: 1px solid #222;
        border-radius: 5px;
        z-index: 10;
        transform: translate(-50%, -100%);
        /* Adjust the tooltip to appear slightly above the cursor */
    }

    .boss-card:hover .rewards-tooltip {
        display: block;
        /* Show tooltip when boss-card is hovered */
    }


    .boss-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        margin: 0 auto;
        display: block;
        margin-bottom: 15px;
    }

    .boss-card h3 {
        color: #eee;
        margin-bottom: 10px;
        text-align: center;
    }

    .boss-card p {
        color: #ccc;
        margin-bottom: 10px;
        text-align: center;
    }

    .difficulty-form {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }

    .summon-button {
        background-color: #2d87f0;
        color: #fff;
        padding: 5px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .summon-button:hover {
        background-color: #1f63d0;
    }

    .active-raids-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        /* Adjusted from space-between to flex-start */
        margin-bottom: 30px;
    }

    .active-raids-grid .raid-card {
        flex-basis: calc(33.33% - 20px);
        /* Adjusted for 3 cards per row */
        background-color: #333;
        padding: 15px;
        margin-right: 20px;
        /* Added to create spacing between the cards */
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    }

    .active-raids-grid .boss-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        margin: 0 auto;
        display: block;
        margin-bottom: 15px;
    }

    .active-raids-grid h3,
    .active-raids-grid p {
        text-align: center;
    }

    .active-raids-grid button {
        display: block;
        margin: 10px auto;
        padding: 5px 15px;
        background-color: #2d87f0;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .active-raids-grid button:hover {
        background-color: #1f63d0;
    }
</style>

<?php
include 'footer.php';
?>