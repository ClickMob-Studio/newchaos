#! /usr/bin/php
<?php

if($_GET['key'] != 'cron94'){
    die();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbcon.php';
include 'classes.php';
include 'database/pdo_class.php';
print "working";

$activeMissionsQuery = "SELECT agm.id AS mission_id, agm.gangid, agm.time, agm.end_time, agm.kills, agm.busts, agm.crimes, agm.mugs, agm.backalleys 
        FROM active_gang_missions agm 
        JOIN gang_missions gm ON agm.mission_id = gm.id 
        WHERE agm.completed = 0";
$activeMissionsResult = mysql_query($activeMissionsQuery);

if ($activeMissionsResult) {
    $currentTime = time();

    while ($mission = mysql_fetch_assoc($activeMissionsResult)) {
        $missionId = $mission['mission_id'];
        $gangId = $mission['gangid'];
        $startTime = strtotime($mission['time']);
        $endTime = $mission['end_time'];

        // Fetch the target criteria from the gang_missions table
        $missionDetailsQuery = "SELECT gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.backalleys AS target_backalleys, gm.reward 
            FROM gang_missions gm 
            WHERE gm.id = (SELECT mission_id FROM active_gang_missions WHERE id = $missionId) LIMIT 1";
        $missionDetailsResult = mysql_query($missionDetailsQuery);
        if (!$missionDetailsResult) {
            continue;  // Skip if details can't be fetched
        }
        $missionDetails = mysql_fetch_assoc($missionDetailsResult);

        $allTargetsMet = true;

        // Check each mission type separately
        foreach (['kills', 'busts', 'crimes', 'mugs', 'backalleys'] as $type) {
           if ($mission[$type] < $missionDetails['target_' . $type]) {
                $allTargetsMet = false;
                break;  // No need to check further if any target is not met
            }
        }

        if ($allTargetsMet) {
            // Update gang points and notify members about successful mission completion
            $rewardQuery = "UPDATE gangs SET pointsvault = pointsvault + {$missionDetails['reward']} WHERE id = $gangId";
            mysql_query($rewardQuery);

            $successMessage = "Your gang has successfully completed the mission and earned a reward of " . number_format($missionDetails['reward'], 0). " points.";

            $gangMembersQuery = "SELECT id FROM grpgusers WHERE gang = $gangId";
            $gangMembersResult = mysql_query($gangMembersQuery);
            while ($member = mysql_fetch_assoc($gangMembersResult)) {
                $userId = $member['id'];
                Send_event($userId, "Congratulations! " . $successMessage);
            }

            // Mark the mission as completed
            $markCompletedQuery = "UPDATE active_gang_missions SET completed = 1 WHERE id = $missionId";
            mysql_query($markCompletedQuery);
        } elseif ($currentTime > $endTime) {
            // Notify gang members about mission failure due to time running out
            $failureMessage = "Your gang missions was not completed in-time resulting in failure.";

            $gangMembersQuery = "SELECT id FROM grpgusers WHERE gang = $gangId";
            $gangMembersResult = mysql_query($gangMembersQuery);
            while ($member = mysql_fetch_assoc($gangMembersResult)) {
                $userId = $member['id'];
                Send_event($userId, $failureMessage);
            }

            // Mark the mission as completed
            $markCompletedQuery = "UPDATE active_gang_missions SET completed = 1 WHERE id = $missionId";
            mysql_query($markCompletedQuery);
        }
    }
}


mysql_query("UPDATE `grpgusers` SET `jail` = 120 WHERE `is_jail_bot` = 1");

$result = mysql_query("SELECT * FROM `grpgusers`", $conn);
while ($line = mysql_fetch_assoc($result)) {
    $updates_user = new User($line['id']);
    $result = mysql_query("SELECT `id` FROM `grpgusers`", $conn) or die(mysql_error());
    while ($line = mysql_fetch_assoc($result)) {
        $updates_user = new User($line['id']);
        if ($updates_user->rmdays > 0) {
            if ($updates_user->donationmonth >= 200) {
                $mul = .4;
            } elseif ($updates_user->donationmonth >= 100) {
                $mul = .35;
            } elseif ($updates_user->donationmonth >= 50) {
                $mul = .3;
            } else {
                $mul = .2;
            }
        } else {
            if ($updates_user->donationmonth >= 200) {
                $mul = .35;
            } elseif ($updates_user->donationmonth >= 100) {
                $mul = .3;
            } elseif ($updates_user->donationmonth >= 50) {
                $mul = .25;
            } else {
                $mul = .15;
            }
        }

        // Correctly fetch and check the gang's upgrade9 level
if ($updates_user->gang > 0) {
    $gangResult = mysql_query("SELECT upgrade9 FROM gangs WHERE id = " . intval($updates_user->gang), $conn);
    if ($gangRow = mysql_fetch_assoc($gangResult)) {
        $gangUpgradeLevel = intval($gangRow['upgrade9']);
    } else {
        $gangUpgradeLevel = 0; // Default to 0 if the gang or upgrade9 level is not found
    }
} else {
    $gangUpgradeLevel = 0; // User is not in a gang
}

// Calculate the bonus multiplier based on the gang's upgrade9 level
$bonusMultiplier = 1 + ($gangUpgradeLevel * 0.10); // 10% bonus per upgrade9 level

// Apply the bonus multiplier to the existing multiplier
$mul *= $bonusMultiplier;

// Now, perform the update with the adjusted multiplier
mysql_query("UPDATE grpgusers SET 
    awake = LEAST(awake + ($updates_user->maxawake * $mul), $updates_user->maxawake),
    energy = LEAST(energy + ($updates_user->maxenergy * $mul), $updates_user->maxenergy),
    nerve = LEAST(nerve + ($updates_user->maxnerve * $mul), $updates_user->maxnerve),
    hp = LEAST(hp + ($updates_user->maxhp * 0.25), $updates_user->maxhp)
    WHERE id = " . intval($updates_user->id), $conn) or die(mysql_error());
    }
}




// Get the last giveaway time
$result = mysql_query("SELECT `value` FROM `settings` WHERE `key` = 'last_giveaway_time'");
if (!$result) {
    die('Invalid query: ' . mysql_error());
}
$lastGiveawayRow = mysql_fetch_assoc($result);

// Check if there was a row returned
if (!$lastGiveawayRow) {
    die('No settings entry for last_giveaway_time found.');
}

$lastGiveawayTime = $lastGiveawayRow['value'];

// Check if an hour has passed since the last giveaway
if (strtotime($lastGiveawayTime) <= strtotime('-1 hour')) {
    // Select users who were online in the last hour
    $onlineUsersResult = mysql_query("SELECT `id` FROM `grpgusers` WHERE `lastactive` > UNIX_TIMESTAMP() - 3600");
    $onlineUsers = array();
    while ($row = mysql_fetch_assoc($onlineUsersResult)) {
        $onlineUsers[] = $row['id'];
    }

    // Shuffle the array and pick the first 3 users if we have enough users
    if (count($onlineUsers) >= 3) {
        shuffle($onlineUsers);
        $winners = array_slice($onlineUsers, 0,3);

        // Reward the first user with points
       mysql_query("UPDATE `grpgusers` SET `points` = `points` + 1000 WHERE `id` = " . $winners[0]);
        Send_event($winners[0], "You have been randomly selected this hour! You won 1,000 Points!");

        // Reward the second user with money
        mysql_query("UPDATE `grpgusers` SET `money` = `money` + 500000 WHERE `id` = " . $winners[1]);
        Send_event($winners[1], "You have been randomly selected this hour! You won $500,000!");

        // Reward the third user with credits
       //mysql_query("UPDATE `grpgusers` SET `credits` = `credits` + 25 WHERE `id` = " . $winners[2]);
        //        Send_event($winners[2], "You have been randomly selected this hour! You won 25 Gold!");

                  // Reward the third user with Tokens
        mysql_query("UPDATE `grpgusers` SET `raidtokens` = `raidtokens` + 10 WHERE `id` = " . $winners[2]);
               Send_event($winners[3], "You have been randomly selected this hour! You won 10 Raid Tokens!");


        // Update the last giveaway time in the settings
$updateQuery = "UPDATE `settings` SET `value` = DATE_ADD(NOW(), INTERVAL 5 HOUR) WHERE `key` = 'last_giveaway_time'";
$result = mysql_query($updateQuery);


}

}

function getItemName($item_id) {
    $query = "SELECT itemname FROM items WHERE id = " . $item_id; // Using the provided table and column names
    $result = mysql_query($query);
    $item = mysql_fetch_assoc($result);
    return $item['itemname'];
}


$raids_query = "SELECT ar.*, b.name AS boss_name, b.stat_limit, b.hp AS boss_hp FROM active_raids ar JOIN bosses b ON ar.boss_id = b.id WHERE TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(ar.summoned_at, INTERVAL 15 MINUTE)) <= 0 AND ar.completed = 0";
$raids_result = mysql_query($raids_query);

$found_items_log = []; // Initialize this array only once at the top level.

while ($raid = mysql_fetch_assoc($raids_result)) {
    echo "Processing raid ID: " . $raid['id'] . "\n";

    // Calculate the total stats of all participants
    $participants_stats_query = "SELECT SUM(u.total) as total_stats FROM raid_participants rp JOIN grpgusers u ON rp.user_id = u.id WHERE rp.raid_id = " . $raid['id'];
    $participants_stats_result = mysql_query($participants_stats_query);
    $participants_stats_row = mysql_fetch_assoc($participants_stats_result);
    $total_stats = $participants_stats_row['total_stats'];

// Determine the raid's success chance
$success_chance = min($total_stats / $raid['stat_limit'], 0.9) * 100;  // Multiply by 100 to make it a percentage
$random_chance = rand(0, 100);  //
    // Determine if the raid was successful
    $raid_successful = ($random_chance <= $success_chance);

    // Mark this raid as completed
    $update_query = "UPDATE active_raids SET completed = 1 WHERE id = " . $raid['id'];
    mysql_query($update_query);


 // Your existing initialization code should be here


/// Your existing initialization code should be here

// Your existing initialization code should be here
// New simulated battle logic
$boss_hp = $raid['boss_hp'];  // Make sure you have this value from the database
$boss_name = $raid['boss_name'];  // Make sure you have this value from the database
$battle_log = "";
$found_items_log = []; // This will store logs about found items

// Fetch the participants of this raid along with their equipped weapons and strength
$participants_query = "SELECT rp.*, u.hp, u.strength, u.eqweapon, i.itemname FROM raid_participants rp JOIN grpgusers u ON rp.user_id = u.id LEFT JOIN items i ON u.eqweapon = i.id WHERE rp.raid_id = " . $raid['id'];
$participants_result = mysql_query($participants_query);


$participants = [];
$total_strength = 0;

while ($participant = mysql_fetch_assoc($participants_result)) {
    $participants[] = $participant;
    $total_strength += $participant['strength'];
}

$tempItemUse = getItemTempUse($raid['summoned_by']);
if ($tempItemUse && $tempItemUse['raid_pass'] > 0) {
    $raid_successful = 1;
    $boss_hp = 0;

    removeItemTempUse($raid['summoned_by'], 'raid_pass', 1);
}

/// Turn-based battle simulation
while ($boss_hp > 0) {
    foreach ($participants as $key => &$participant) {
        // HP is ignored in this version
        $user_hp = 100000000;

        // Determine weapon
        $weapon = $participant['itemname'] ? $participant['itemname'] : "fists";

        // Player's turn
        $strength_percentage = ($participant['strength'] / $total_strength);
        $base_damage = 100 * $strength_percentage;
        $damage_variation = $base_damage * 0.2;  // 20% variation
        $damage_to_boss = round(rand($base_damage - $damage_variation, $base_damage + $damage_variation));

        $boss_hp -= $damage_to_boss;

        $formatted_name = formatName($participant['user_id']);
        $battle_log .= "$formatted_name dealt $damage_to_boss damage to the boss with $weapon.\n";

        // Boss's turn
        $damage_to_user = rand(5, 15);
        $user_hp -= $damage_to_user;

        // The following line can be uncommented if you want to keep track of the HP for some other reason
        // $participant['hp'] = $user_hp;

        $battle_log .= "$boss_name hit $formatted_name for $damage_to_user damage.\n";
    }
}

// Add this right after the simulated battle loop ends
if ($raid_successful) {
    $battle_log .= "The raid was successful!\n";
} else {
    if ($boss_hp > 0 && empty($participants)) {
        $battle_log .= "The boss has won.\n";
    } else {
        $battle_log .= "The raid failed. You were defeated by the boss.\n";
    }
}

// Append the found items log to the battle log
//$battle_log .= "\nItems Found During the Raid:\n" . $found_items_log;

// Insert battle log into raid_battle_logs table

    $participants_query = "SELECT * FROM raid_participants WHERE raid_id = " . $raid['id'];
    $participants_result = mysql_query($participants_query);
    $participants = [];
    while ($participant = mysql_fetch_assoc($participants_result)) {
        $participants[] = $participant;
    }

    // Fetch the loot for this boss
    $loot_query = "SELECT * FROM loot WHERE boss_id = " . $raid['boss_id'];
    $loot_result = mysql_query($loot_query);
    $loot_table = [];
    while ($loot = mysql_fetch_assoc($loot_result)) {
        $loot_table[] = $loot;
    }

    echo "Loot Table: ";
    print_r($loot_table);

    $items_won_global = [];

    // For each participant of the raid
    foreach ($participants as $participant) {
        echo "Debug: Processing participant with ID: " . $participant['user_id'] . "\n";  // Debug line
        $formatted_name = formatName($participant['user_id']);
        echo "Debug: formatted_name = $formatted_name\n";

        // Initialize formatted_name here.


        if ($raid_successful) {

            addToUserCompLeaderboard($participant['user_id'], 'raids_complete', 1);

            $currentQuestSeason = getCurrentQuestSeasonForUser($participant['user_id']);
            if (isset($currentQuestSeason['id'])) {
                $questSeasonUser = getQuestSeasonUser($participant['user_id'], $currentQuestSeason['id']);
                $questSeasonMissionUser = getQuestSeasonMissionUser($participant['user_id'], $currentQuestSeason['id']);
                $questSeasonMission = getQuestSeasonMission($participant['user_id'], $currentQuestSeason['id']);

                if (isset($questSeasonMission['requirements']->raids)) {
                    updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'raids', 1);
                }
            }

            $total_min_points = 0;
            $total_max_points = 0;
            $total_min_money = 0;
            $total_max_money = 0;
  $total_min_raidpoints = 0;  // New
        $total_max_raidpoints = 0;  // New


            foreach ($loot_table as $loot) {
                $total_min_points += $loot['min_points'];
                $total_max_points += $loot['max_points'];
                $total_min_money += $loot['min_money'];
                $total_max_money += $loot['max_money'];
 $total_min_raidpoints += $loot['min_raidpoints'];  // New
            $total_max_raidpoints += $loot['max_raidpoints'];  // New
            }

            $points_won = rand($total_min_points, $total_max_points);
            $money_won = rand($total_min_money, $total_max_money);
            $raidpoints_won = rand($total_min_raidpoints, $total_max_raidpoints);  // New

            $raidBoosterInUse = false;
            if ($tempItemUse && $tempItemUse['raid_booster'] > 0) {
                $points_won = $points_won + (($points_won / 100) * 50);
                $money_won = $money_won + (($money_won / 100) * 50);
                $raidpoints_won = $raidpoints_won + (($raidpoints_won / 100) * 50);

                $points_won = ceil($points_won);
                $money_won = ceil($money_won);
                $raidpoints_won = ceil($raidpoints_won);

                $raidBoosterInUse = true;
                removeItemTempUse($raid['summoned_by'], 'raid_booster', 1);
            }

         // First, determine if the user has rmdays greater than 0
$query_check_rmdays = "SELECT rmdays FROM grpgusers WHERE id = " . $participant['user_id'];
$result_check_rmdays = mysql_query($query_check_rmdays);
$row_check_rmdays = mysql_fetch_assoc($result_check_rmdays);

if ($row_check_rmdays['rmdays'] > 0) {
    // If rmdays > 0, update the user's bank account instead of money
    //$rel_user = new User($participant['user_id']);
    //mysql_query("INSERT INTO bank_log VALUES('', ".$participant['user_id'].", $money_won, 'mdep', $rel_user->bank, unix_timestamp())");
    $update_query = "UPDATE grpgusers SET points = points + $points_won, bank = bank + $money_won, raidpoints = raidpoints + $raidpoints_won WHERE id = " . $participant['user_id'];
} else {
    // If rmdays <= 0, update the user's money directly
    $update_query = "UPDATE grpgusers SET points = points + $points_won, money = money + $money_won, raidpoints = raidpoints + $raidpoints_won WHERE id = " . $participant['user_id'];
}

// Execute the update query
mysql_query($update_query);

$event_message = "Your raid against " . $raid['boss_name'] . " has ended. You won $points_won points and $" . ($row_check_rmdays['rmdays'] > 0 ? "in your bank " : "") . "$money_won money.";

            // Determine items from the loot table
$items_won = []; // Store the names of items won
if(!empty($loot_table)){
    foreach ($loot_table as $loot) {
        $random_chance = rand(0, 100); // Generate a number between 0 and 100

        if (isset($raidBoosterInUse) && $raidBoosterInUse) {
            $random_chance = rand(0, 80);
        }

        if ($random_chance <= ($loot['drop_rate'] * 100)) {
            // Attempt to fetch the name of the item
            $itemName = getItemName($loot['item_id']);

            // Check if the item name is valid
            if ($itemName === null || $itemName === "" || $itemName === "Unknown Item") {
                // This means either the item ID is invalid or the item does not exist in the database.
                // You can choose to log this error, notify an admin, or simply continue to the next loot item.
                echo "You Found no items During this Raid";
                continue; // Skip adding this item
            }

            $items_won[] = $itemName;

            // Add to the found items log
            $found_items_log[] = "$formatted_name found a $itemName.\n";
            echo "Debug: Added to found_items_log for $formatted_name\n";
        $check_inv = mysql_query("SELECT * FROM inventory WHERE userid = " . $participant['user_id'] . " AND itemid = " . $loot['item_id']);
        if(mysql_num_rows($check_inv)){
            mysql_query("UPDATE inventory SET quantity = quantity + 1 WHERE userid = " . $participant['user_id'] . " AND itemid = " . $loot['item_id']);

        }else{
            mysql_query("INSERT INTO inventory (userid, itemid, quantity) VALUES ('$participant[user_id]', '$loot[item_id]', '1')");
        }

        $items_won_global = array_merge($items_won_global, $items_won);  // Merge the items won for this participant into the global list
    }
   // var_dump($participant);
}
}
            $bullet = "&bull;";
            // Fetch the number of participants for the raid
            $participant_count_query = "SELECT COUNT(*) as participant_count FROM raid_participants WHERE raid_id = " . $raid['id'];
            $participant_count_result = mysql_query($participant_count_query);
            $participant_count_row = mysql_fetch_assoc($participant_count_result);
            $participant_count = $participant_count_row['participant_count'];

            // Get raid leader's name
            $raid_leader_name = formatName($raid['summoned_by']);
  // Update raidwins and raidsjoined
        $update_stats_query = "UPDATE grpgusers SET raidwins = raidwins + 1, raidcomp = raidcomp + 1 WHERE id = " . $participant['user_id'];
mysql_query($update_stats_query);

// Create the event message
$event_message = "Your Raid, Led by " . $raid_leader_name . " with " . $participant_count . " participants, against " . $raid['boss_name'] . " has ended.";

$event_message .= "<br>&bull; You won $points_won points.";
$event_message .= "<br>&bull; You won $money_won money.";
$event_message .= "<br>&bull; You won $raidpoints_won raid points.";  // New

if (!empty($items_won)) {
    $event_message .= "<br>&bull; You also found: " . implode(", ", $items_won) . ".";
} else {
    $event_message .= "<br>&bull; No items were found during this raid.";
}

// Here, you can send or display $event_message as needed
 // Add a link to view the battle log
            $event_message .= "<br><a href='view_battle_log.php?raid_id=" . $raid['id'] . "'>View Battle Log</a>";

            raidMission($participant['user_id']);

            send_event($participant['user_id'], $event_message);

        } else {
            // Raid failed
  // Update raidwins and raidsjoined
        $update_stats_query = "UPDATE grpgusers SET raidlosses = raidlosses + 1 WHERE id = " . $participant['user_id'];
        mysql_query($update_stats_query);
            $event_message = "Your Raid, led by " . formatName($raid['summoned_by']) . " with " . count($participants) . " participants, against " . $raid['boss_name'] . " has failed!";

 // Add a link to view the battle log
            $event_message .= "<br><a href='view_battle_log.php?raid_id=" . $raid['id'] . "'>View Battle Log</a>";

  send_event($participant['user_id'], $event_message);
        }
    }




    $battle_log .= "\nItems Found During the Raid:\n" . implode("", $found_items_log);
    $insert_battle_log_query = "INSERT INTO raid_battle_logs (raid_id, battle_log) VALUES (" . $raid['id'] . ", '" . mysql_real_escape_string($battle_log) . "')";
    mysql_query($insert_battle_log_query);

    $found_items_log = [];

}



// RM Cities
$rm_cities = [3, 6, 8, 10, 23];
$rm_cities_sql = implode(',', $rm_cities);
$default_city = 1;

$db->query("UPDATE grpgusers SET city = $default_city WHERE city IN ('$rm_cities_sql') AND rmdays = 0");
$db->execute();

$db->query("UPDATE grpgusers SET nerve = 1 WHERE nerve < 0");
$db->execute();



$db->query("UPDATE bans SET days = days - 1 WHERE type = 'gc'");
$db->execute();
$db->query("DELETE FROM bans WHERE days = 0");
$db->execute();
$db->query("UPDATE grpgusers SET apoints = apoints + 1, actions = actions + 1 WHERE lastactive > unix_timestamp() - 61 AND apban = 0");
$db->execute();

$db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = 0 AND apban = 0");
$db->execute();



$db->query("UPDATE grpgusers SET dailytime = dailytime + 1 WHERE lastactive > unix_timestamp() - 61");
$db->execute();
$db->query("UPDATE grpgusers SET totaltime = totaltime + 1 WHERE lastactive > unix_timestamp() - 61");
$db->execute();
$db->query("UPDATE grpgusers SET gangwait = gangwait - 1 WHERE gangwait > 0");
$db->execute();
$db->query("UPDATE grpgusers SET bustpill = bustpill - 1 WHERE bustpill > 0");
$db->execute();
$db->query("UPDATE grpgusers SET cityturns = cityturns + 1 WHERE cityturns < 30");
$db->execute();

$db->query("UPDATE grpgusers SET outofjail = outofjail - 1 WHERE outofjail > 0");
$db->execute();
$db->query("UPDATE grpgusers SET fbi = fbi - 1 WHERE fbi > 0");
$db->execute();
$db->query("UPDATE grpgusers SET fbitime = fbitime - 1 WHERE fbitime > 0");
$db->execute();

$db->query("UPDATE grpgusers SET nightvision = nightvision - 1 WHERE nightvision > 0");
$db->execute();


// Update pack1 to 0 for users whose pack1timetill is in the past
$db->query("UPDATE grpgusers SET pack1 = 0 WHERE pack1time <= UNIX_TIMESTAMP()");
$db->execute();

$db->query("UPDATE gamebonus SET Time = Time - 1 WHERE Time > 0");
$db->execute();







if (time() <= 1703577599) {

    $_add = 5;
    if (time() <= 1676466000) {
        $_add = 10;
    }
    $db->query("UPDATE grpgusers SET epoints = epoints + $_add WHERE lastactive > unix_timestamp() - 61 AND apban = 0 AND relationship = 0");
    $db->execute();

    $_add = $_add + 5;

    $db->query("UPDATE grpgusers SET epoints = epoints + $_add WHERE lastactive > unix_timestamp() - 61 AND apban = 0 AND relationship > 0");
    $db->execute();


    $db->query("SELECT id FROM grpgusers WHERE epoints >= 1000");
    $db->execute();
    $_rows = $db->fetch_row();
    foreach($_rows as $_row) {
        Give_Item(198, $_row['id'], 1);

        Send_event($_row['id'], "You have been awarded a Snowball! Throw these at other players!");
        $db->query("UPDATE grpgusers SET epoints = 0 WHERE id = ?");
        $db->execute(array(



            $_row['id']
        ));

    }
}


if ($user_class->id >= 999) {

    $db->query("SELECT id FROM grpgusers WHERE epoints >= 1000");
    $db->execute();
    $_rows = $db->fetch_row();
    foreach($_rows as $_row) {
        Give_Item(198, $_row['id'], 1);
        Send_event($_row['id'], "You have been awarded a Snowball! Throw these at other players!");
        $db->query("UPDATE grpgusers SET epoints = 0 WHERE id = ?");
        $db->execute(array(
            $_row['id']
        ));
    }
}




// Check expired auctions
$expiredAuctionsQuery = "SELECT * FROM auction_house WHERE end_time <= UNIX_TIMESTAMP(NOW()) AND status = 'active'";
$expiredAuctions = mysql_query($expiredAuctionsQuery, $conn);

while ($auction = mysql_fetch_assoc($expiredAuctions)) {
    $highestBidderId = $auction['highest_bidder_id'];
    $sellerId = $auction['seller_id'];
    $itemId = $auction['item_id'];
    $quantity = $auction['quantity'];
    $currentBid = $auction['current_bid'];

    // Fetch item name
    $itemNameQuery = "SELECT itemname FROM items WHERE id = $itemId";
    $itemNameResult = mysql_query($itemNameQuery, $conn);
    if($itemNameRow = mysql_fetch_assoc($itemNameResult)) {
        $itemName = $itemNameRow['itemname'];
    } else {
        $itemName = "Unknown Item";
    }

    // If there's a highest bidder, award them the item and notify them
    if (!empty($highestBidderId)) {
        // Transfer item to highest bidder's inventory
        $checkInventoryQuery = "SELECT id FROM inventory WHERE userid = $highestBidderId AND itemid = $itemId";
        $checkInventory = mysql_query($checkInventoryQuery, $conn);
        if (mysql_num_rows($checkInventory) > 0) {
            $inventoryId = mysql_fetch_assoc($checkInventory)['id'];
            $updateInventoryQuery = "UPDATE inventory SET quantity = quantity + $quantity WHERE id = $inventoryId";
            mysql_query($updateInventoryQuery, $conn);
        } else {
            $insertInventoryQuery = "INSERT INTO inventory (userid, itemid, quantity) VALUES ($highestBidderId, $itemId, $quantity)";
            mysql_query($insertInventoryQuery, $conn);
        }

        // Send event to seller and highest bidder
        Send_Event($sellerId, "Your auction for $quantity x $itemName has ended. {$highestBidderId} won with a bid of $currentBid.");
        Send_Event($highestBidderId, "You've won the auction for $quantity x $itemName with a bid of $currentBid.");
    } else {
        // If no highest bidder, return item to seller's inventory
        $checkInventoryQuery = "SELECT id FROM inventory WHERE userid = $sellerId AND itemid = $itemId";
        $checkInventory = mysql_query($checkInventoryQuery, $conn);
        if (mysql_num_rows($checkInventory) > 0) {
            $inventoryId = mysql_fetch_assoc($checkInventory)['id'];
            $updateInventoryQuery = "UPDATE inventory SET quantity = quantity + $quantity WHERE id = $inventoryId";
            mysql_query($updateInventoryQuery, $conn);
        } else {
            $insertInventoryQuery = "INSERT INTO inventory (userid, itemid, quantity) VALUES ($sellerId, $itemId, $quantity)";
            mysql_query($insertInventoryQuery, $conn);
        }

        // Send event to seller
        Send_Event($sellerId, "Your auction for $quantity x $itemName has ended without any bids. The item has been returned to your inventory.");
    }

    // Update the status column in the auction_house table to 'finished'
    $updateStatusQuery = "UPDATE auction_house SET status = 'finished' WHERE auction_id = " . $auction['auction_id'];
    mysql_query($updateStatusQuery, $conn);
}


$db->query("UPDATE pets
SET
nerve = LEAST(
    LEVEL + 4,
    CEIL((nerve + ((LEVEL + 4) * .2)))
),
hp = LEAST(
    LEVEL * 50,
    CEIL((hp + ((LEVEL * 50) * .25)))
),
energy = LEAST(
    LEVEL + 9,
    CEIL((energy + ((LEVEL + 9) * .2)))
),
awake = LEAST(
    CEIL(awake + (maxawake * .2)),
    maxawake
)");
$db->execute();

$db->query("UPDATE grpgusers SET hospital = GREATEST(hospital - 60, 0) WHERE hospital > 0");
$db->execute();
$db->query("UPDATE grpgusers SET jail = GREATEST(jail - 60, 0) WHERE jail > 0");
$db->execute();
$db->query("UPDATE grpgusers SET delay = delay - 1 WHERE delay > 0");
$db->execute();
$db->query("UPDATE grpgusers SET stamina = stamina + 1 WHERE stamina < 10");
$db->execute();
$db->query("UPDATE grpgusers SET invincible = invincible - 1 WHERE invincible > 0");
$db->execute();
$db->query("UPDATE pets SET jail = GREATEST(jail - 60, 0), hospital = GREATEST(hospital - 60, 0) WHERE hospital <> 0 OR jail <> 0");
$db->execute();
$db->query("UPDATE pets SET nerref = 0, nerreftime = 0 WHERE nerreftime < unix_timestamp() - 86400");
$db->execute();
$db->query("UPDATE grpgusers SET nerref = 0, nerreftime = 0 WHERE nerreftime < unix_timestamp() - 86400");
$db->execute();
$db->query("UPDATE grpgusers SET ngyref = 0, ngyreftime = 0 WHERE ngyreftime < unix_timestamp() - 86400");
$db->query("SELECT userid, mid FROM missions m JOIN mission h ON mid = h.id WHERE completed = 'no' AND timestamp + time < unix_timestamp()");
$db->execute();
$rows = $db->fetch_row();
$db->query("UPDATE missions INNER JOIN mission h ON mid = h.id SET completed = 'failed' WHERE completed = 'no' AND timestamp + time < unix_timestamp()");
$db->execute();
foreach($rows as $row) {
    Send_event($www['userid'], "You failed your mission!");
    switch($www['mid']) {
        case 1:
            $mname = "Starter Mission";
            break;
        case 2:
            $mname = "Rookie Mission";
            break;
        case 3:
            $mname = "Basic Mission";
            break;
        case 4:
            $mname = "Normal Mission";
            break;
        case 5:
            $mname = "Hardened Mission";
            break;
        default:
            continue;
    }
    $db->query("INSERT INTO missionlog VALUES ('', ?, unix_timestamp())");
    $db->execute(array(
        "[x] failed their $mname,{$www['userid']}"
    ));
}
// srand(time()); // Not necessary for modern PHP versions as the random number generator is automatically seeded.
$rand = rand(1, 16); // Randomly select how many users to affect, between 1 to 3.
$db->query("UPDATE grpgusers SET jail = 120 WHERE lastactive < unix_timestamp() - 86400 AND id >= 321 AND id <= 338 ORDER BY RAND() LIMIT $rand");
$db->execute();


$db->query("SELECT * FROM bloodbath WHERE endtime < unix_timestamp() AND winners = ''");
$db->execute();
$bbinfo = $db->fetch_row(true);
if (!empty($bbinfo)) {
    $db->query("SELECT b.* FROM bbusers b LEFT JOIN grpgusers g ON userid = id WHERE g.admin = 0");
    $db->execute();
    $rows = $db->fetch_row();
    foreach($rows as $row) {
        $info[] = $row;
    }
    $db->query("UPDATE bloodbath SET winners = ? WHERE id = ?");
    $db->execute(array(
        serialize($info),
        $bbinfo['id']
    ));
    $db->query("INSERT INTO bloodbath VALUES ('', unix_timestamp() + (86400 * 7), '', 0)");
    $db->execute();
    $db->query("TRUNCATE TABLE bbusers");
    $db->execute();
}
$db->query("SELECT * FROM gangwars WHERE timeending < unix_timestamp() AND accepted = 1");
$db->execute();
$rows = $db->fetch_row();
foreach($rows as $r) {
    if ($r['gang1score'] > $r['gang2score']) {
        $winninggang = $r['gang1'];
        $losinggang = $r['gang2'];
    } else {
        $winninggang = $r['gang2'];
        $losinggang = $r['gang1'];
    }
    $msgwin = "Your gang won the war! [+ " . prettynum($r['bet'], 1) . "]";
    $msglose = "Your gang lost the war! Step your game up gang.";
    $db->query("INSERT INTO gangmail VALUES ('', ?, 1, unix_timestamp(), 'No Subject', ?)");
    $db->execute(array(
        $winninggang,
        $msgwin
    ));
    $db->execute(array(
        $losinggang,
        $msglose
    ));
    //	$db->query("INSERT INTO gangevents VALUES ('', ?, unix_timestamp(), ?, 0)");
    //	$db->execute(array(
    //		$winninggang,
    //		$msg
    //	));
    $db->execute(array(
        $losinggang,
        $msglose
    ));
    $db->query("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
    $db->execute(array(
        $r['bet'],
        $winninggang
    ));
    $db->query("UPDATE grpgusers SET gangmail = gangmail + 1 WHERE gang IN (?, ?)");
    $db->execute(array(
        $winninggang,
        $losinggang
    ));
    $db->query("UPDATE gangwars SET accepted = 2 WHERE warid = ?");
    $db->execute(array(
        $r['warid']
    ));
}
// Fetch the latest Bloodbath results where rewards haven't been distributed yet and winners column has data
$query = "SELECT * FROM bloodbath WHERE is_paid = 0 AND winners != '' AND endtime < ".time()." ORDER BY endtime DESC LIMIT 1";
$result = mysql_query($query);
$latest_bloodbath = mysql_fetch_assoc($result);

if ($latest_bloodbath) {



$winners_data = unserialize($latest_bloodbath['winners']);

// Transform the data
$transformed_data = [];
foreach ($winners_data as $user_data) {
    $userid = $user_data['userid'];
    foreach ($user_data as $key => $value) {
        if ($key !== 'userid') {
            $transformed_data[$key][$userid] = $value;
        }
    }
}
    $points_distribution = [
        1 => 20000,
        2 => 10000,
        3 => 5000
    ];


    foreach ($transformed_data as $category => $users) {
        arsort($users); // Sorting users by their value in descending order

        $top_3_users = array_slice($users, 0, 3, true);

        $position = 1;
        foreach ($top_3_users as $user_id => $value) {
            if ($user_id && $value) {

                if ($category === 'donator') {
                    if ($position == 1) {
                        $awardedCredits = $value / 100 * 30;
                    } else if ($position == 2) {
                        $awardedCredits = $value / 100 * 20;
                    } else {
                        $awardedCredits = $value / 100 * 10;
                    }

                    // Award points to the user
                    $update_query = "UPDATE grpgusers SET credits = credits + " . $awardedCredits . " WHERE `id` = " . $user_id;
                    mysql_query($update_query);

                    // Send an event to the user
                    $event_message = "You have won the " . $category . " category and placed " . $position . " and won " . $awardedCredits . " Credits.";
                    send_event($user_id, $event_message);

                } else {
                    // Award points to the user
                    $update_query = "UPDATE grpgusers SET points = points + " . $points_distribution[$position] . " WHERE `id` = " . $user_id;
                    mysql_query($update_query);

                    // Send an event to the user
                    $event_message = "You have won the " . $category . " category and placed " . $position . " and won " . $points_distribution[$position] . " Points.";
                    send_event($user_id, $event_message);

                    // Print out the winners for each category for verification
                    echo "Category: " . $category . "<br>";
                    echo "Position: " . $position . "<br>";
                    echo "Username: " . formatName($user_id) . "<br>";
                    echo "Points Awarded: " . $points_distribution[$position] . "<br>";
                    echo "------------------------<br>";

                }

                $position++;
            }
        }
    }

    // Update the bloodbath entry to indicate that the rewards have been distributed
    $update_is_paid_query = "UPDATE bloodbath SET is_paid = 1 WHERE id = " . $latest_bloodbath['id'];
    mysql_query($update_is_paid_query);

$db->query("SELECT * FROM bloodbath WHERE endtime < unix_timestamp() AND winners = ''");
$db->execute();
$bbinfo = $db->fetch_row(true);
if (!empty($bbinfo)) {
    $db->query("SELECT * FROM bbusers");
    $db->execute();
    $rows = $db->fetch_row();
    foreach($rows as $row) {
        $info[] = $row;
    }
    $db->query("UPDATE bloodbath SET winners = ? WHERE id = ?");
    $db->execute(array(
        serialize($info),
        $bbinfo['id']
    ));
    $db->query("INSERT INTO bloodbath VALUES ('', unix_timestamp() + (86400 * 7), '')");
    $db->execute();
    $db->query("TRUNCATE TABLE bbusers");
    $db->execute();
}


}

$csrf = mdd5(uniqid(rand(), true));
$_SESSION['csrf'] = $csrf;

$deleteDuplicatesQuery = "DELETE a FROM `attackladder` a
    JOIN (SELECT MIN(id) as id, `user`, `spot`
          FROM `attackladder`
          GROUP BY `user`, `spot`
          HAVING COUNT(*) > 1) as b
    ON a.user = b.user AND a.spot = b.spot
    WHERE a.id != b.id";

$result = mysql_query($deleteDuplicatesQuery);

if (!$result) {
    echo "Error deleting duplicate rows: " . mysql_error();
} else {
    echo "Duplicate rows deleted successfully.";
}




