<?php
include 'header.php';

// Assuming user's ID is available as $user_id
$user_id = $_SESSION['user_id'];  // Replace with the actual method of getting user's ID

// Fetch user's current raid stats
$query = "SELECT * FROM raid_character_stats WHERE user_id = $user_id";
$result = mysql_query($query);
$stats = mysql_fetch_assoc($result);

// Fetch user's raidpoints from the grpgusers table
$user_query = "SELECT raidpoints FROM grpgusers WHERE id = $user_id";
$user_result = mysql_query($user_query);
$user_data = mysql_fetch_assoc($user_result);
$raidpoints = $user_data['raidpoints'];
?>

<!-- Styling for the card, table, and buttons -->
<style>
    .raid-stats-card {
        background-color: #282c36;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        max-width: 600px;
        margin: 50px auto;
        color: #FFF;
    }
    .raid-stats-card h2 {
        margin-top: 0;
        font-size: 24px;
    }
    .raid-stats-card table {
        width: 100%;
        border-collapse: collapse;
    }
    .raid-stats-card th, .raid-stats-card td {
        padding: 10px 15px;
        border-bottom: 1px solid #394049;
    }
    .raid-stats-card th {
        background-color: #333740;
    }
    .upgrade-btn {
        background-color: #4a90e2;
        color: #FFF;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .upgrade-btn:hover {
        background-color: #357ab7;
    } .tier-bar-container {
        display: flex;
        gap: 2px;
    }
    .tier-bar {
        flex: 1;
        height: 10px;
        background-color: #555;
        border-radius: 2px;
    }
    .tier-bar.active {
        background-color: #4a90e2;
    }
</style>

<!-- Raid Character Stats Card -->
<div class="raid-stats-card">
    <h2>Raid Character Stats</h2>
    <p>Available Raid Points: <?php echo $raidpoints; ?></p>
    <table>
        <tr>
            <th>Stat</th>
            <th>Current Tier/Level</th>
            <th>Progress</th>
            <th>Cost to Upgrade</th>
            <th>Action</th>
        </tr>
        <?php
        $stat_names = ["raid_strength" => "Strength", "raid_defense" => "Defense", "raid_speed" => "Speed", "raid_agility" => "Agility"];
        foreach ($stat_names as $stat_db_name => $display_name) {
            $tier = $stats[$stat_db_name . '_tier'];
            $level = $stats[$stat_db_name . '_level'];
            $next_level_cost = ($tier * 10 + $level + 1) * 100;
            echo "<tr>";
            echo "<td>" . $display_name . "</td>";
            echo "<td>Tier " . $tier . " / Level " . $level . "</td>";
            echo '<td>';
            echo '<div class="tier-bar-container">';
            for ($i = 0; $i < 10; $i++) {
                if ($i < $level) {
                    echo '<div class="tier-bar active"></div>';
                } else {
                    echo '<div class="tier-bar"></div>';
                }
            }
            echo '</div>';
            echo '</td>';
            echo "<td>" . $next_level_cost . "</td>";
            echo '<td><button class="upgrade-btn" data-stat="' . $stat_db_name . '">Upgrade</button></td>';
            echo "</tr>";
        }
        ?>
    </table>
</div>