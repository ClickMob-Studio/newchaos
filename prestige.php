<?php

include "header.php";

echo '<style>
.content-area {
    
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

table#newtables {
    width: 100%;
    margin: auto;
    border-spacing: 0;
    border-collapse: collapse;
}

table#newtables th, table#newtables td {
    padding: 10px;
    border: 1px solid #444; /* Subtle borders for the cells */
}


.progress-container {
    width: 100%; /* Full width of the container */
    height: 25px; /* Maintain the original height */
     border-radius: 5px;
    overflow: hidden;
    margin: 20px auto; /* Center the bar and add some vertical spacing */
}

.custom-progress-bar {
    width: 0; /* Start with 0 width and grow as needed */
    height: 100%; /* Full height of the container */
    background-image: linear-gradient(45deg, red 25%, white 25%, white 50%, red 50%, red 75%, white 75%, white); /* Red and white stripes */
    background-size: 50px 50px; /* Size of the stripes */
    animation: move-stripes 2s linear infinite; /* Slower animation */
    display: flex; /* Use flexbox to center content */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    color: black; /* Text color */
    font-size: 20px; /* Increased text size */
    border-radius: 5px;
    transition: width 0.4s ease-in-out;
}

@keyframes move-stripes {
    0% { background-position: 0 0; }
    100% { background-position: 50px 0; }
}



div#error, div#message {
    padding: 20px;
    text-align: center;
    color: red;
    font-size: 1.2em;
    margin-bottom: 20px;
    border: 1px solid #444; /* Subtle borders for the message */
    border-radius: 5px;
}
.custom-button-container {
    padding: 5px; /* Padding around the button */
    text-align: center; /* Keep the button centered */
}


.custom-button:disabled {
    cursor: not-allowed;
    opacity: 0.7; /* Slightly higher opacity for better visibility */
}

stats-contents {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); /* Dark shadow for depth */
    padding: 20px; /* Padding around content */
    margin: 20px 0; /* Margin for spacing around the div */
    border-radius: 10px; /* Rounded corners */
}

.center-text { text-align: center; } /* Class for centering text */
 }
</style>';

$prestigeLevelRequired = 1000;
if ($user_class->prestige > 0) {
    $prestigeLevelRequired = $prestigeLevelRequired + (200 * $user_class->prestige);

    if($user_class->prestige >= 5 ){
        $prestigeLevelRequired = 1000 + (200 * $user_class->prestige) + (500 * ($user_class->prestige - 4));
    }
}

$userPrestigeSkills = getUserPrestigeSkills($user_class);
$prestigeUnlocks = array();
// BA Raid Tokens
$prestigeUnlocks['ba_raidtokens_unlock'] = array(
    'name' => 'BA Raid Tokens',
    'description' => 'Unlocking BA Raid Tokens will allow you to find Raid Tokens with normal Backalley searches, not just in Gold Rush!'
);
// Super Attack
$prestigeUnlocks['speed_attack_unlock'] = array(
    'name' => 'Super Attack',
    'description' => 'Unlocking Super Attack gives you access to a new feature where with one click of a button you\'ll speed attack a random offline player, helping you complete those important kill missions quickly!'
);
// BA Gold Rush Boost
$prestigeUnlocks['ba_gold_rush_unlock'] = array(
    'name' => 'BA Gold Rush Boost',
    'description' => 'Unlocking Ba Gold Rush boost gives you a 10% boosted chance of finding Gold Rush in the Backalley'
);
// Crime Cash
$prestigeUnlocks['crime_cash_unlock'] = array(
    'name' => 'Crime Cash Boost',
    'description' => 'Unlocking Crime Cash Boost gives an extra 10% cash from all crimes you complete.'
);
// Throne Payout Boost
$prestigeUnlocks['throne_points_unlock'] = array(
    'name' => 'Throne Payout Boost',
    'description' => 'Unlocking Throne Payout Boost gives you an extra 20% payouts from all Boss/Under Boss positions held!'
);
// Travel Cost Reduction
$prestigeUnlocks['travel_cost_unlock'] = array(
    'name' => 'Travel Cost Reduction',
    'description' => 'Unlocking Travel Cost Reduction gives you a 20% reduction on all travel costs.'
);
// Travel Cost Reduction
$prestigeUnlocks['travel_cost_unlock'] = array(
    'name' => 'Travel Cost Reduction',
    'description' => 'Unlocking Travel Cost Reduction gives you a 20% reduction on all travel costs.'
);
// BA Cash
$prestigeUnlocks['ba_cash_unlock'] = array(
    'name' => 'Ba Cash Boost',
    'description' => 'Unlocking BA Cash Boost gives an extra 10% cash from all BA cash payouts.'
);
// Training Dummy Cash
$prestigeUnlocks['training_dummy_cash_unlock'] = array(
    'name' => 'City Goons Cash Boost',
    'description' => 'Unlocking City Goons Cash Boost gives an extra 20% cash from all City Goon attacks.'
);
// Crime EXP
$prestigeUnlocks['crime_exp_unlock'] = array(
    'name' => 'Crime EXP Boost',
    'description' => 'Unlocking Crime EXP Boost gives an extra 20% EXP from all crimes.'
);

// Super Mugs
$prestigeUnlocks['super_mugs_unlock'] = array(
    'name' => 'Super Mugs',
    'description' => 'Unlocking Super Mugs gives you a super mug button on a players profile, earning you 10 mugs for just 1 click, allowing to complete all your missions faster!'
);

$prestigeBoosts = array();
$prestigeBoosts['energy_boost_level'] = '+50 Energy Boost';
$prestigeBoosts['crime_cash_boost_level'] = '+2% Crime Cash Boost';
$prestigeBoosts['mission_point_boost_level'] = '+2% Mission Point Boost';
$prestigeBoosts['mission_exp_boost_level'] = '+2% Mission EXP Boost';
$prestigeBoosts['ba_point_boost_level'] = '+1 Backalley Level';
if($user_class->prestige > 4){
    $prestigeBoosts['research_cash_boost_level'] = '-2% Research Cost';
}
//$prestigeBoosts['hourly_searches_boost_level'] = '+10 Hourly Searches';


if (isset($_GET['action']) && $_GET['action'] === 'add_unlock' && isset($_GET['unlock_type'])) {
    $unlockType = $_GET['unlock_type'];
    if (!isset($prestigeUnlocks[$unlockType])) {
        diefun('Something went wrong, please DM an Admin if this issue persists.');
    }

    if ($userPrestigeSkills['prestige_unlocks_available'] < 1) {
        diefun('You do not have any prestige unlocks available');
    }

    if ($userPrestigeSkills[$unlockType] > 0) {
        diefun('You have already activated this unlock');
    }
    $userPrestigeSkills[$unlockType] = 1;

    $db->query('UPDATE user_prestige_skills SET ' . $unlockType . ' = 1, unlock_points_spent = unlock_points_spent + 1 WHERE user_id = ' . $user_class->id);
    $db->execute();

    echo Message("You have successfully unlocked " . $prestigeUnlocks[$unlockType]['name']);
}

if (isset($_GET['action']) && $_GET['action'] === 'add_boost' && isset($_GET['boost_type'])) {
    $boostType = $_GET['boost_type'];
    if (!isset($prestigeBoosts[$boostType])) {
        diefun('Something went wrong, please DM an Admin if this issue persists.');
    }

    if ($userPrestigeSkills['prestige_boosts_available'] < 1) {
        diefun('You do not have any prestige boosts available');
    }

    if ($userPrestigeSkills[$boostType] >= 10) {
        diefun('You have already maxed out this boost.');
    }
    $userPrestigeSkills[$boostType] = $userPrestigeSkills[$boostType] + 1;

    $db->query('UPDATE user_prestige_skills SET ' . $boostType . ' = ' . $boostType . ' + 1, boosts_spent = boosts_spent + 1 WHERE user_id = ' . $user_class->id);
    $db->execute();

    echo Message("You have successfully increased the level of " . $prestigeBoosts[$boostType]);
}

if (isset($_GET['action']) && $_GET['action'] === 'reset_spends') {
    if ($userPrestigeSkills['reset_points'] < 1) {
        diefun('You do not have any resets available. You are only aloud to reset once per prestige.');
    }

    $db->query('DELETE FROM user_prestige_skills WHERE user_id = ' . $user_class->id);
    $db->execute();

    echo Message("You have successfully reset your prestige spends.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user has reached the maximum prestige level
    if ($user_class->prestige >= 6) {
        echo Message("You cannot Prestige again!!");
    } else if ($user_class->level >= $prestigeLevelRequired) {
        if ($user_class->prestige < 1) {
            $bankCashRequired = 500000000;
            $pointsRequired = 250000;
            $totalStatsRequired = 0;
            $statPercentage = 40;
        } else if ($user_class->prestige < 2) {
            $bankCashRequired = 1000000000;
            $pointsRequired = 500000;
            $totalStatsRequired = 0;
            $statPercentage = 50;
        } else if ($user_class->prestige < 3) {
            $bankCashRequired = 5000000000;
            $pointsRequired = 750000;
            $totalStatsRequired = 0;
            $statPercentage = 50;
        } else if ($user_class->prestige < 4) {
            $bankCashRequired = 10000000000;
            $pointsRequired = 1000000;
            $totalStatsRequired = 0;
            $statPercentage = 60;
        } else if ($user_class->prestige < 5) {
            $bankCashRequired = 25000000000;
            $pointsRequired = 1500000;
            $totalStatsRequired = 0;
            $statPercentage = 60;
        } else if ($user_class->prestige < 6) {
            $bankCashRequired = 50000000000;
            $pointsRequired = 5000000;
            $totalStatsRequired = 5000000000000;
            $statPercentage = 70;
        } else if ($user_class->prestige < 7) {
            $bankCashRequired = 100000000000;
            $pointsRequired = 10000000;
            $totalStatsRequired = 10000000000000;
            $statPercentage = 70;
        } else if ($user_class->prestige < 8) {
            $bankCashRequired = 150000000000;
            $pointsRequired = 20000000;
            $totalStatsRequired = 25000000000000;
            $statPercentage = 75;
        } else if ($user_class->prestige < 9) {
            $bankCashRequired = 200000000000;
            $pointsRequired = 30000000;
            $totalStatsRequired = 75000000000000;
            $statPercentage = 80;
        } else if ($user_class->prestige < 10) {
            $bankCashRequired = 500000000000;
            $pointsRequired = 50000000;
            $totalStatsRequired = 100000000000000;
            $statPercentage = 90;
        }

        $totalStats = $user_class->strength + $user_class->defense + $user_class->speed + $user_class->agility;

        if ($user_class->bank < $bankCashRequired) {
            diefun('You do not have enough cash in the bank to prestige.');
        }

        if ($user_class->points < $pointsRequired) {
            diefun('You do not have enough points to prestige.');
        }

        if ($totalStatsRequired > 0 && $totalStats < $totalStatsRequired) {
            diefun('You do not have the required total stats to prestige.');
        }

        $newStrength = $user_class->strength - ($user_class->strength / 100 * $statPercentage);
        $newDefense = $user_class->defense - ($user_class->defense / 100 * $statPercentage);
        $newSpeed = $user_class->speed - ($user_class->speed / 100 * $statPercentage);
        $newAgility = $user_class->agility - ($user_class->agility / 100 * $statPercentage);

        // User is eligible to prestige, and hasn't reached the maximum prestige level
        // Assuming $db is your database connection
        $db->query("UPDATE grpgusers SET prestige = prestige + 1, level = 1, exp = 0, bank = bank - " . $bankCashRequired . ", points = points - " . $pointsRequired . ", strength = " . $newStrength . ", defense = " . $newDefense . ", speed = " . $newSpeed . ", agility = " . $newAgility . "  WHERE id = ?");
        $db->execute([$user_class->id]);

        $db->query("UPDATE user_prestige_skills SET reset_points = reset_points + 1  WHERE user_id = ?");
        $db->execute([$user_class->id]);
        // Assuming the prestige level is updated in the object, you might need to refresh it or adjust the object property accordingly
        echo Message("Congratulations! You have prestiged to level " . ($user_class->prestige + 1) . ".");
        $_SESSION['prestige'] = true;
    } else {
        echo Message("You must be at least level " . $prestigeLevelRequired . " to prestige.");
    }
    include 'footer.php';
    die();
}

?>

<div class='box_top'>Account Prestige</div>
<div class='box_middle'>
    <div class='pad'>
        <p>
            Welcome to Account Prestiges! By increasing your prestige, you allow your level to be reset, as well as pay a forfeit, and in return you receive special bonuses. The first prestige allows you to prestige
            at level 1000, and they increase by 200 thereon. Once you hit prestige 5, it then increases by 700.
        </p>

        <h2>Prestige Unlocks</h2>
        <p>You currently have <?php echo $userPrestigeSkills['prestige_unlocks_available'] ?> prestige unlocks available.</p>
        <hr />
        <div class="row">
            <?php foreach ($prestigeUnlocks as $key => $prestigeUnlock): ?>
                <?php
                $divClass = 'bg-danger';
                $button = '<a href="prestige.php?action=add_unlock&unlock_type=' . $key .'"><button>Unlock</button></a>';
                if ($userPrestigeSkills[$key] > 0) {
                    $divClass = 'bg-success';
                    $button = '';
                }
                ?>
                <div class="col-md-4">
                    <div class="card text-white <?php echo $divClass ?> mb-3" style="min-height: 240px;">
                        <div class="card-header"><?php echo $prestigeUnlock['name'] ?></div>
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo $prestigeUnlock['description'] ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <center>
                                <?php echo $button ?>
                            </center>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <br />
        <h2>Prestige Boosts</h2>
        <p>You currently have <?php echo $userPrestigeSkills['prestige_boosts_available'] ?> prestige boosts available.</p>
        <hr />
        <div class="table-container">
            <table class="new_table" id="newtables" style="width:100%;">
                <thead>
                <tr>
                    <th>Boost</th>
                    <th>Level</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($prestigeBoosts as $key => $name): ?>
                    <tr>
                        <td><?php echo $name ?></td>
                        <td><?php echo $userPrestigeSkills[$key] ?>/10</td>
                        <td><a href="prestige.php?action=add_boost&boost_type=<?php echo $key ?>"><button>Add</button></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($userPrestigeSkills['reset_points'] > 0): ?>
            <br />
            <h2>Reset Prestige Spend</h2>
            <p>If you are unhappy with the bvoosts & unlocks you have picked you can reset your prestige spends, this is only available once per prestige.</p>
            <a href="prestige.php?action=reset_spends">RESET PRESTIGE SPEND</a>
        <?php endif; ?>
    </div>
</div>
<br />

<h2>Prestige</h2>
<hr />
<?php
$prestigeLevel = $user_class->prestige;

// Calculate the bonus percentage for both Training and EXP
$bonusPercentage = $prestigeLevel * 20;

// Start the table for prestige badges
echo '<div class="contenthead floaty">';


// Existing content starts here

echo '<div class="stats-contents">';
echo '<table id="newtables" style="width: 100%; margin: auto; border-collapse: collapse;">';
echo '    <tr>';
echo '        <td colspan="3" class="center-text"><h4>You are currently getting a <span style="color:yellow;">' . $bonusPercentage . '%</span> bonus<br> towards Training</h4></td>';
echo '        <td colspan="3" class="center-text"><h4>You are also getting a <span style="color:yellow;">' . $bonusPercentage . '%</span> bonus<br> towards EXP</h4></td>';
echo '    </tr>';
echo '</table>';
echo '</div>';



echo '<style>
#newtables td {
    text-align: center; /* Center align table cell content */
    vertical-align: middle; /* Middle align table cell content */
}
</style>';

echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <td><img src="images/muscles1.png" style="width:100px; height:100px;"></td>';
echo '        <td><img src="images/exp.png" style="width:100px; height:100px;"></td>';
echo '    </tr>';
// Removed the key.png image and its row as per request.
echo '    </tr>';
echo '    <tr>';
echo '        <th><center> Get An Additional <font color=yellow><b>+20% bonus</b></font> on <font color=red><b>Trains</b></font> per Prestige Level!</th>';
echo '       <th><center> Get An Additional <font color=yellow><b>+20% bonus</b></font> on <font color=red><b>EXP</b></font> per Prestige!</th>';
// Removed the description for the "Access to a prestige city" as per request.
echo '    </tr>';
echo '</table>';

if ($can) {
    echo '<br />';
    echo '<br />';
    echo '<form method="post">';
    echo '    <table id="newtables" style="width: 100%; margin:auto; table-layout: fixed;">';
    echo '        <colgroup>';
    echo '            <col span="1" style="width: 15%;">';
    echo '            <col span="1" style="width: 10%;">';
    echo '            <col span="1" style="width: 10%;">';
    echo '            <col span="1" style="width: 10%;">';
    echo '        </colgroup>';
    echo '        <tbody>';
    echo '            <tr>';
    echo '                <th colspan="4" style="min-height:30px"><input type="submit" value="Prestige!" /></th>';
    echo '            </tr>';
    echo '        </tbody>';
    echo '    </table>';
    echo '</form>';
}

// Calculate the remaining levels to reach 1000 and display it
$levelsToGo = $prestigeLevelRequired - $user_class->level; // Remaining levels to reach 1000
echo '<div style="text-align:center; margin-bottom:20px;">';

?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="newtables" class="table table-bordered" style="width: 100%;">
                    <tr>
                        <?php
                        // Generate cells for badges and descriptions
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<td class="text-center">';
                            echo '<img src="images/skullpres_' . $i . '.png?v=4" class="img-fluid" style="max-width: 80px; height: auto;">';
                            echo '<br><p style="color:#fff">Prestige ' . $i.'</p>';
                            echo '</td>';
                        }
                        ?>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php


// Calculate level percentage for display
$lvlperc = min(100, floor(($user_class->level / $prestigeLevelRequired) * 100));

// Display prestige requirements and progress
echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <th colspan="3" class="center-text"><h4><center>You currently need <span style="color:yellow;">' . $levelsToGo . '</span> levels till your next Prestige.</h4></center></th>';
echo '    </tr>';
echo '    <tr>';
echo '        <td colspan="2"><h4>Current Prestige Level: <span style="color:red;"><b>' . $user_class->prestige . '</b></span></h4></td>';
echo '        <td><h4>' . prettynum($user_class->level) . ' / ' . prettynum($prestigeLevelRequired) . ' (' . number_format_short($prestigeLevelRequired) . ')</h4></td>';
echo '    </tr>';
echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <td colspan="3">'; // Span the progress bar across all columns
echo '            <div class="progress-container">';
echo '                <div class="custom-progress-bar" style="width: ' . $lvlperc . '%;"><h4>' . $lvlperc . '%</h4></div>';
echo '            </div>';
echo '        </td>';
echo '    </tr>';
echo '</table>';

?>

<p>To prestige to the next level, you'll need to pay the following forfeit:</p>

<?php if ($user_class->prestige < 1): ?>
    <ul>
        <li>$500,000,000 from your bank</li>
        <li>250,000 points</li>
        <li>40% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 2): ?>
    <ul>
        <li>$1,000,000,000 from your bank</li>
        <li>500,000 points</li>
        <li>50% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 3): ?>
    <ul>
        <li>$5,000,000,000 from your bank</li>
        <li>750,000 points</li>
        <li>50% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 4): ?>
    <ul>
        <li>$10,000,000,000 from your bank</li>
        <li>1,000,000 points</li>
        <li>60% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 5): ?>
    <ul>
        <li>$25,000,000,000 from your bank</li>
        <li>1,500,000 points</li>
        <li>60% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 5): ?>
    <ul>
        <li>50,000,000,000 from your bank</li>
        <li>2,500,000 points</li>
        <li>70% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 6): ?>
    <ul>
        <li>50,000,000,000 from your bank</li>
        <li>5,000,000 points</li>
        <li>5,000,000,000,000 Total Stats</li>
        <li>70% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 7): ?>
    <ul>
        <li>100,000,000,000 from your bank</li>
        <li>10,000,000 points</li>
        <li>10,000,000,000,000 Total Stats</li>
        <li>70% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 8): ?>
    <ul>
        <li>150,000,000,000 from your bank</li>
        <li>20,000,000 points</li>
        <li>25,000,000,000,000 Total Stats</li>
        <li>75% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 9): ?>
    <ul>
        <li>200,000,000,000 from your bank</li>
        <li>30,000,000 points</li>
        <li>75,000,000,000,000 Total Stats</li>
        <li>80% of your stats</li>
    </ul>
<?php elseif ($user_class->prestige < 10): ?>
    <ul>
        <li>500,000,000,000 from your bank</li>
        <li>50,000,000 points</li>
        <li>100,000,000,000,000 Total Stats</li>
        <li>90% of your stats</li>
    </ul>
<?php endif; ?>

<?php


// Ensure the prestige button is always displayed but disabled unless the user is level 1000 or higher
echo '<center>';
echo '<div class="custom-button-container">';
echo '<form method="post" style="text-align:center;">';
if ($user_class->level >= $prestigeLevelRequired) {
    echo '<input type="submit" class="custom-button" value="Prestige!" />';
} else {
    echo '<input type="submit" class="custom-button" value="Sorry, You Cannot Prestige Yet" disabled />';
}
echo '</form>';
echo '</div>';
echo '</center>';

?>

<script>
    $(".stat_input").change(function(e) {
        console.log($(this));
        var sum = 0;
        $('.stat_input').each(function() {
            sum += Number($(this).val());
        });
        console.log(sum);
        sum = String(sum).replace(/(.)(?=(\d{3})+$)/g,'$1,')
        $("#stat_total").html(sum);
    });
</script>

<?php
include "footer.php";
?>
