<?php
include 'header.php';
?>
<div class='box_top'>Gang Upgrade</div>
						<div class='box_middle'>
							<div class='pad'>
<?php
if ($user_class->gang != 0) {
    include("gangheaders.php");
    $user_rank = new GangRank($user_class->grank);
    if ($user_rank->upgrade != 1) {
        echo Message("You don't have permission to be here!");
        include 'footer.php';
        die();
    }
    $gang_class = new Gang($user_class->gang);
    $result = mysql_query("SELECT * from `gangs` WHERE `id` = '" . $user_class->gang . "'");
    $worked = mysql_fetch_array($result);

    if ($_POST['size'] != "") {
        if ($gang_class->moneyvault < 500000) {
            echo Message("You don't have enough money in your vault to upgrade.");
            include 'footer.php';
            die();
        }
        if ($gang_class->capacity >= 20) {
            echo Message("You can only have a maximum of 20 members.");
            include 'footer.php';
            die();
        }
        $newcapacity = $gang_class->capacity + 1;
        $newvault = $gang_class->moneyvault - 500000;
        $result = mysql_query("UPDATE `gangs` SET `capacity` = '" . $newcapacity . "', `moneyvault` = '" . $newvault . "' WHERE `id`='" . $user_class->gang . "'");
        echo '<meta http-equiv="refresh" content="0;url=gangupgrade.php">';
    }

    if ($_POST['banner'] != "") {
        if ($gang_class->moneyvault < 1000000) {
            echo Message("You don't have enough money in your vault to upgrade.");
            include 'footer.php';
            die();
        }
        $banner = $gang_class->boughtbanner + 1;
        $newvault = $gang_class->moneyvault - 1000000;
        $result = mysql_query("UPDATE `gangs` SET `boughtbanner` = '" . $banner . "', `moneyvault` = '" . $newvault . "' WHERE `id`='" . $user_class->gang . "'");
        echo '<meta http-equiv="refresh" content="0;url=gangupgrade.php">';
    }

    // Fetch the upgrade levels from the gangs table
    $result = mysql_query("SELECT * from `gangs` WHERE `id` = '" . $user_class->gang . "'");
    // Debugging code to inspect the `$upgrades_data` array and check for database errors
    if (!$result) {
        die('Invalid query: ' . mysql_error());
    }
    $upgrades_data = mysql_fetch_assoc($result);
       // Define upgrade details and tooltips
    $upgrade_details = array(
'upgrade1' => array(
    'name' => 'Strength Upgrade',
    'benefit' => isset($upgrades_data['upgrade1']) ? 'You are currently getting ' . ($upgrades_data['upgrade1'] * 20) . '% Bonus to Strength during battles!' : 'Upgrade information not available'
),
       'upgrade2' => array(
    'name' => 'Defense Upgrade',
    'benefit' => isset($upgrades_data['upgrade2']) ? 'You are currently getting ' . ($upgrades_data['upgrade2'] * 20) . '% Bonus to Defense during battles!' : 'Upgrade information not available'
),

       'upgrade3' => array(
    'name' => 'Speed Upgrade',
    'benefit' => isset($upgrades_data['upgrade3']) ? 'You are currently getting ' . ($upgrades_data['upgrade3'] * 20) . '% Bonus to Speed during battles!' : 'Upgrade information not available'
),
             'upgrade4' => array(
    'name' => 'Raid Drop Chance',
    'benefit' => isset($upgrades_data['upgrade3']) ? 'You are currently getting ' . ($upgrades_data['upgrade4'] * 10) . '% Bonus to finding items in raids!' : 'Upgrade information not available'
),
           );

    if ($user_class->admin > 0) {
        $upgrade_details['upgrade_agility'] = array(
                'name' => 'Agility Upgrade',
                'benefit' => isset($upgrades_data['upgrade_agility']) ? 'You are currently getting ' . ($upgrades_data['upgrade_agility'] * 20) . '% Bonus to Agility during battles!' : 'Upgrade information not available'
        );
    }

    $tooltips = array(
        'upgrade1' => array(),
        'upgrade2' => array(),
        'upgrade3' => array(),
        'upgrade4' => array(),

    );

    // Define costs for each star level
    $costs = array(
        1 => 10000000,
        2 => 50000000,
        3 => 200000000,
        4 => 1000000000,
        5 => 5000000000,
        6 => 25000000000,
        7 => 75000000000,
        8 => 150000000000,
        9 => 250000000000,
        10 => 1000000000000,
    );

    // Add this after the banner upgrade check, but before the HTML rendering:
    if ($user_class->admin > 0) {
        $upgrade_keys = ['upgrade1', 'upgrade2', 'upgrade3', 'upgrade_agility', 'upgrade4'];
    } else {
        $upgrade_keys = ['upgrade1', 'upgrade2', 'upgrade3', 'upgrade4'];
    }

    foreach ($upgrade_keys as $key) {
        if (isset($_POST[$key])) {
            $current_star_level = intval($upgrades_data[$key]);

            // Check if upgrade is maxed out
            if ($current_star_level >= 10) {
                echo Message("Your {$upgrade_details[$key]['name']} is already at the maximum level!");
                continue;
            }

            // Check if the gang has enough money to upgrade
            if ($gang_class->moneyvault < $costs[$current_star_level + 1]) {
                echo Message("You don't have enough money in your vault to upgrade {$upgrade_details[$key]['name']}.");
                continue;
            }

            // Deduct the upgrade cost from the vault and increase the upgrade level
            $newVaultAmount = $gang_class->moneyvault - $costs[$current_star_level + 1];
            $newUpgradeLevel = $current_star_level + 1;

            // Update the database
            $upgrade_query = "UPDATE `gangs` SET `$key` = '$newUpgradeLevel', `moneyvault` = '$newVaultAmount' WHERE `id`='" . $user_class->gang . "'";
            $result = mysql_query($upgrade_query);

            // If the query was successful, show a success message
            if ($result) {
                echo Message("Successfully upgraded {$upgrade_details[$key]['name']} to level $newUpgradeLevel!");
            } else {
                echo Message("There was an error upgrading {$upgrade_details[$key]['name']}. Please try again.");
            }
        }
    }
    ?>

    <tr><td class="contentcontent">
        <table width='100%'>
            <form method='post'>
                <tr>
                    <td width='15%'><b>Size:</b></td>
                    <td width='35%'><?php echo $gang_class->capacity; ?> members</td>
                    <td width='15%'><b>Banner:</b></td>
                    <?php echo ($gang_class->boughtbanner == 1) ? "<td width='35%'>Yes</td>" : "<td width='35%'>No</td>"; ?>
                </tr>
                <tr>
                    <td width='15%'>Upgrade Size</td>
                    <?php echo ($gang_class->capacity == 25) ? "<td width='35%'>Upgraded</td>" : "<td width='35%'><input type='submit' name='size' value='$500,000'></td>"; ?>
                    <td width='15%'>Banner Usage</td>
                    <?php echo ($gang_class->boughtbanner == 1) ? "<td width='35%'>Upgraded</td>" : "<td width='35%'><input type='submit' name='banner' value='$1,000,000'></td>"; ?>
                </tr>
</table>
                <!-- New section for upgrade1, upgrade2, and upgrade3 -->
                <style>
                    /* Styles borrowed from auction_house.php for consistency */
                    .info-box {
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    padding: 5px 20px;
    margin: 20px 0;
    width: 90%;
    margin-left: auto;
    margin-right: auto;
}

/* Container to hold all upgrades */
.upgrade-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    gap: 20px;
    padding: 20px;
    margin: 20px auto;
    width: calc(100% - 40px);
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}

/* Individual upgrade package styling */
.upgrade-package {
    flex: 0 1 calc(50% - 20px); /* Adjust width to allow two items per row */
    padding: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    margin-bottom: 20px;
    border-radius: 10px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column; /* Stack the children vertically */
}

/* Styles for the upgrade details */
.upgrade-details {
    margin-bottom: 10px; /* Space between the details and stars */
}

/* Styles for the upgrade button */
.upgrade-button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 10px; /* Space between button and stars */
    color: white; /* Text color for the button */
    background-color: #2f2f2f; /* Button background */
}

/* Color coding for funds availability */
.sufficient-funds {
    padding: 10px 20px;
    background-color: green; /* Or whatever your color is for sufficient funds */
    color: white;
    border: none;
    text-align: center;
    display: inline-block;
    font-size: 16px;
    cursor: pointer;
}

.insufficient-funds {
    padding: 10px 20px;
    background-color: red; /* Red for insufficient funds */
    color: white;
    border-radius: 10px;
    text-align: center;
    display: inline-block;
    font-size: 16px;
    cursor: not-allowed; /* Change the cursor to indicate that it's not clickable */
}

/* Disable button styling if needed */
.upgrade-button:disabled {
    background-color: grey; /* Background color for disabled button */
    cursor: not-allowed; /* Cursor style for disabled button */
}

/* Styles for the stars, now in their own row */
/* Styles for filled stars */
.star-filled:before {
    content: '\2605'; /* Unicode filled star */
    color: #FF0000; /* Filled stars will be green */
}

/* Styles for empty stars with a white border */
.star-empty:before {
    content: '\2605'; /* Unicode star */
    color: black; /* Empty stars will be white */
    -webkit-text-stroke: 2px white; /* White border for Webkit browsers */
    text-stroke: 2px white; /* Standard property for text border, if supported */
}

/* Ensure compatibility for browsers that don't support text-stroke */
.star-empty {
    position: relative;
}

.star-empty:before {
    position: absolute;
    top: 0;
    left: 0;
}

.star-empty:after {
    content: '\2606'; /* Unicode empty star */
    position: absolute;
    top: 0;
    left: 0;
    color: black; /* Color behind the star, can be adjusted or removed */
    z-index: -1;
}
/* Styles for the tooltip */
.tooltip {
    display: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    white-space: nowrap;
    font-size: 12px;
    z-index: 1000;
}

.star:hover .tooltip {
    display: block;
}
                </style>


             <div class="info-box">
    <p>Enhance your gang's capabilities by upgrading your gangs upgrades with points</p>
</div>
<div class="upgrade-container">
    <form method="post">
        <?php foreach ($upgrade_keys as $key):
            $current_star_level = intval($upgrades_data[$key]);
            $canUpgrade = $current_star_level < 10 && ($gang_class->moneyvault >= $costs[$current_star_level + 1]);
        ?>
            <div class="upgrade-package">
                <h3><?= $upgrade_details[$key]['name'] ?>:</h3>
                <div class="upgrade-description"><?= $upgrade_details[$key]['benefit'] ?></div>
                <?php if ($current_star_level == 10): ?>
                    <span class="insufficient-funds">Max Level</span>
               <?php elseif ($canUpgrade): ?>
    <input class="upgrade-button sufficient-funds" type='submit' name='<?= $key ?>' value='Upgrade for $<?= number_format($costs[$current_star_level + 1]) ?>'>
<?php else: ?>
    <span class="insufficient-funds">Insufficient Cash ($<?= number_format($costs[$current_star_level + 1]) ?>  Required)</span>
<?php endif; ?>
                <div class="stars">
    <?php
    for ($i = 1; $i <= 10; $i++) {
        // Set star color to white by default, use green for current star level or below
        $starColor = ($i <= $current_star_level) ? 'green' : 'black';
        $starStyle = "color: $starColor; font-size: 20px; background-color: transparent;";

        // Check if the tooltip for this star level exists in the $tooltips array for the current $key
        if (isset($tooltips[$key][$i])) {
            $tooltipText = $tooltips[$key][$i];
        } else {
            $tooltipText = "No benefit defined";
        }
        ?>
        <span class="star" style="<?= $starStyle ?>">&#9733;<span class="tooltip"><?= $tooltipText ?></span></span>
    <?php
    }
    ?>
</div>

            </div>
        <?php endforeach; ?>
    </form>
</div>

    <?php
} else {
    echo Message("You aren't in a gang.");
}
require "footer.php";
?>
