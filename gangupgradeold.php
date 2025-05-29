<?php
include 'header.php';
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
        if ($gang_class->capacity >= 15) {
            echo Message("You can only have a maximum of 15 members.");
            include 'footer.php';
            die();
        }
        $newcapacity = $gang_class->capacity + 1;
        $newvault = $gang_class->moneyvault - 500000;
        perform_query("UPDATE `gangs` SET `capacity` = ?, `moneyvault` = ? WHERE `id`= ?", [$newcapacity, $newvault, $user_class->gang]);
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
        perform_query("UPDATE `gangs` SET `boughtbanner` = ?, `moneyvault` = ? WHERE `id`= ?", [$banner, $newvault, $user_class->gang]);
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
            'benefit' => isset($upgrades_data['upgrade3']) ? 'You are currently getting ' . ($upgrades_data['upgrade3'] * 20) . '% Bonus to Strength during battles!' : 'Upgrade information not available'
        ),
        'upgrade4' => array(
            'name' => 'Raid Drop Chance',
            'benefit' => isset($upgrades_data['upgrade3']) ? 'You are currently getting ' . ($upgrades_data['upgrade4'] * 10) . '% Bonus to finding items in raids!' : 'Upgrade information not available'
        ),
        'upgrade5' => array('name' => 'Upgrade 5', 'benefit' => ''),
    );

    $tooltips = array(
        'upgrade1' => array(),
        'upgrade2' => array(),
        'upgrade3' => array(),
        'upgrade4' => array(),
        'upgrade5' => array(),

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
    $upgrade_keys = ['upgrade1', 'upgrade2', 'upgrade3', 'upgrade4', 'upgrade5'];

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
            perform_query("UPDATE `gangs` SET `$key` = ?, `moneyvault` = ? WHERE `id`= ?", [$newUpgradeLevel, $newVaultAmount, $user_class->gang]);

            // If the query was successful, show a success message
            if ($db->affected_rows() > 0) {
                echo Message("Successfully upgraded {$upgrade_details[$key]['name']} to level $newUpgradeLevel!");
            } else {
                echo Message("There was an error upgrading {$upgrade_details[$key]['name']}. Please try again.");
            }
        }
    }
    ?>
    <tr>
        <td class="contentspacer"></td>
    </tr>
    <tr>
        <td class="contenthead">Upgrade</td>
    </tr>
    <tr>
        <td class="contentcontent">
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
                    background-color: #2f2f2f;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
                    padding: 5px 20px;
                    margin: 20px 0;
                    width: 90%;
                    margin-left: auto;
                    margin-right: auto;
                }

                .upgrade-container {
                    background-color: #1e1e1e;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
                    padding: 20px;
                    margin: 20px auto;
                    width: 90%;
                }

                .upgrade-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 15px;
                }

                .upgrade-item h3 {
                    margin: 0;
                    display: inline-block;
                    margin-right: 15px;
                }

                .upgrade-description {
                    flex-grow: 1;
                    margin-left: 15px;
                }

                .upgrade-details {
                    display: flex;
                    align-items: center;
                }

                .upgrade-button {
                    background-color: #2f2f2f;
                    color: #fff;
                    border: none;
                    padding: 5px 10px;
                    border-radius: 5px;
                    margin-left: 10px;
                    cursor: pointer;
                }

                .stars {
                    position: relative;
                    cursor: help;
                }

                .star {
                    position: relative;
                }

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
                <h2>Gang Upgrades</h2>
                <p>Enhance your gang's capabilities by upgrading. Each upgrade provides distinct advantages to your gang.
                </p>
            </div>
            <div class="upgrade-container">
                <?php
                foreach ($upgrade_keys as $key):
                    $current_star_level = intval($upgrades_data[$key]);
                    if ($current_star_level < 10)
                        $canUpgrade = ($gang_class->moneyvault >= $costs[$current_star_level + 1]);
                    ?>
                    <div class="upgrade-item">
                        <h3><?= $upgrade_details[$key]['name'] ?>:</h3>
                        <span class="upgrade-description"><?= $upgrade_details[$key]['benefit'] ?></span>
                        <div class="upgrade-details">
                            <?php if ($current_star_level == 10): ?>
                                <span class="insufficient-funds">Max Level</span>
                            <?php elseif ($canUpgrade): ?>
                                <input class="upgrade-button" type='submit' name='<?= $key ?>'
                                    value='Upgrade for $<?php echo number_format($costs[$current_star_level + 1]) ?>'>
                            <?php else: ?>
                                <span class="insufficient-funds">Insufficient Funds
                                    ($<?= number_format($costs[$current_star_level + 1]) ?>)</span>
                            <?php endif; ?>

                            <?php
                            for ($i = 1; $i <= 10; $i++) {
                                $starStyle = ($i <= $current_star_level) ? 'color: gold; font-size: 20px;' : 'font-size: 20px;';
                                // Check if the tooltip for this star level exists in the $tooltips array for the current $key
                                if (isset($tooltips[$key][$i])) {
                                    $tooltipText = $tooltips[$key][$i];
                                } else {
                                    $tooltipText = "No benefit defined";
                                }
                                ?>
                                <span class="star" style="<?= $starStyle ?>">&#9733;<span
                                        class="tooltip"><?= $tooltipText ?></span></span>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            </form>
            </table>
        </td>
    </tr>
    <?php
} else {
    echo Message("You aren't in a gang.");
}
require "footer.php";
?>