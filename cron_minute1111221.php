#! /usr/bin/php
<?php

if ($_GET['key'] != 'cron94') {
    die();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'dbcon.php';
include_once 'classes.php';
include_once 'database/pdo_class.php';
include_once 'includes/functions.php';

print "working";

$db->query("SELECT agm.id AS mission_id, agm.gangid, agm.time, agm.end_time, agm.kills, agm.busts, agm.crimes, agm.mugs, agm.backalleys FROM active_gang_missions agm JOIN gang_missions gm ON agm.mission_id = gm.id WHERE agm.completed = 0");
$db->execute();
$activeMissions = $db->fetch_row();
if (!empty($activeMissions)) {
    $currentTime = time();

    foreach ($activeMissions as $mission) {
        $missionId = $mission['mission_id'];
        $gangId = $mission['gangid'];
        $startTime = strtotime($mission['time']);
        $endTime = $mission['end_time'];

        // Fetch the target criteria from the gang_missions table
        $db->query("SELECT gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.backalleys AS target_backalleys, gm.reward FROM gang_missions gm WHERE gm.id = (SELECT mission_id FROM active_gang_missions WHERE id = ?) LIMIT 1");
        $db->execute([$missionId]);
        $missionDetails = $db->fetch_row(true);
        if (empty($missionDetails)) {
            continue;  // Skip if details can't be fetched
        }

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
            perform_query("UPDATE gangs SET pointsvault = pointsvault + ? WHERE id = ?", [$missionDetails['reward'], $gangId]);

            $successMessage = "Your gang has successfully completed the mission and earned a reward of " . number_format($missionDetails['reward'], 0) . " points.";

            $db->query("SELECT id FROM grpgusers WHERE gang = ?");
            $db->execute([$gangId]);
            $gangMembersResult = $db->fetch_row();
            foreach ($gangMembersResult as $member) {
                $userId = $member['id'];
                Send_Event($userId, "Congratulations! " . $successMessage);
            }

            // Mark the mission as completed
            perform_query("UPDATE active_gang_missions SET completed = 1 WHERE id = ?", [$missionId]);
        } elseif ($currentTime > $endTime) {
            // Notify gang members about mission failure due to time running out
            $failureMessage = "Your gang missions was not completed in-time resulting in failure.";

            $db->query("SELECT id FROM grpgusers WHERE gang = ?");
            $db->execute([$gangId]);
            $gangMembersResult = $db->fetch_row();
            foreach ($gangMembersResult as $member) {
                $userId = $member['id'];
                Send_Event($userId, $failureMessage);
            }

            // Mark the mission as completed
            perform_query("UPDATE active_gang_missions SET completed = 1 WHERE id = ?", [$missionId]);
        }
    }
}

perform_query("UPDATE `grpgusers` SET `jail` = 120 WHERE `is_jail_bot` = 1");

$db->query("SELECT * FROM grpgusers");
$db->execute();
$users = $db->fetch_row();
foreach ($users as $line) {
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
    $gangUpgradeLevel = 0; // Default to 0 if the gang or upgrade9 level is not found
    if ($updates_user->gang > 0) {
        $db->query("SELECT upgrade9 FROM gangs WHERE id = ?");
        $db->execute([$updates_user->gang]);
        $row = $db->fetch_row(true);
        if ($row) {
            $gangUpgradeLevel = intval($row['upgrade9']);
        }
    }

    // Calculate the bonus multiplier based on the gang's upgrade9 level
    $bonusMultiplier = 1 + ($gangUpgradeLevel * 0.10); // 10% bonus per upgrade9 level

    // Apply the bonus multiplier to the existing multiplier
    $mul *= $bonusMultiplier;

    // Now, perform the update with the adjusted multiplier
    $db->query("UPDATE grpgusers SET 
        awake = LEAST(awake + (:maxawake * :mul), :maxawake),
        energy = LEAST(energy + (:maxenergy * :mul), :maxenergy),
        nerve = LEAST(nerve + (:maxnerve * :mul), :maxnerve),
        hp = LEAST(hp + (:maxhp_quarter), :maxhp)
    WHERE id = :id");

    $db->bind(':maxawake', $updates_user->maxawake);
    $db->bind(':maxenergy', $updates_user->maxenergy);
    $db->bind(':maxnerve', $updates_user->maxnerve);
    $db->bind(':maxhp', $updates_user->maxhp);
    $db->bind(':maxhp_quarter', $updates_user->maxhp * 0.25);
    $db->bind(':mul', $mul);
    $db->bind(':id', $updates_user->id, PDO::PARAM_INT);

    $db->execute();
}

// Get the last giveaway time
$db->query("SELECT `value` FROM `settings` WHERE `key` = 'last_giveaway_time'");
$db->execute();
$lastGiveawayRow = $db->fetch_row(true);

// Check if there was a row returned
if (isset($lastGiveawayRow)) {
    $lastGiveawayTime = $lastGiveawayRow['value'];

    // Check if an hour has passed since the last giveaway
    if (strtotime($lastGiveawayTime) <= strtotime('-1 hour')) {
        // Select users who were online in the last hour
        $db->query("SELECT `id` FROM grpgusers WHERE `lastactive` > UNIX_TIMESTAMP() - 3600");
        $db->execute();
        $onlineUsers = $db->fetch_row();

        // Shuffle the array and pick the first 3 users if we have enough users
        if (count($onlineUsers) >= 3) {
            shuffle($onlineUsers);
            $winners = array_slice($onlineUsers, 0, 3);

            // Reward the first user with points
            perform_query("UPDATE `grpgusers` SET `points` = `points` + 1000 WHERE `id` = ?", [$winners[0]['id']]);
            Send_Event($winners[0]['id'], "You have been randomly selected this hour! You won 1,000 Points!");

            // Reward the second user with money
            perform_query("UPDATE `grpgusers` SET `money` = `money` + 500000 WHERE `id` = ?", [$winners[1]['id']]);
            Send_Event($winners[1]['id'], "You have been randomly selected this hour! You won $500,000!");

            // Reward the third user with Tokens
            perform_query("UPDATE `grpgusers` SET `raidtokens` = `raidtokens` + 10 WHERE `id` = ?", [$winners[2]['id']]);
            Send_Event($winners[2]['id'], "You have been randomly selected this hour! You won 10 Raid Tokens!");

            // Update the last giveaway time in the settings
            perform_query("UPDATE `settings` SET `value` = DATE_ADD(NOW(), INTERVAL 5 HOUR) WHERE `key` = 'last_giveaway_time'");
        }
    }
}

$db->query("SELECT ar.*, b.name AS boss_name, b.stat_limit, b.hp AS boss_hp FROM active_raids ar JOIN bosses b ON ar.boss_id = b.id WHERE ar.summoned_at <= NOW() - INTERVAL 15 MINUTE AND ar.completed = 0");
$db->execute();
$raids = $db->fetch_row();

$found_items_log = []; // Initialize this array only once at the top level.
foreach ($raids as $raid) {
    echo "Processing raid ID: " . $raid['id'] . "\n";

    // Calculate the total stats of all participants
    $db->query("SELECT SUM(u.total) as total_stats FROM raid_participants rp JOIN grpgusers u ON rp.user_id = u.id WHERE rp.raid_id = ?");
    $db->execute([$raid['id']]);
    $participants_stats_row = $db->fetch_row(true);
    $total_stats = $participants_stats_row['total_stats'];

    // Determine the raid's success chance
    $success_chance = min($total_stats / $raid['stat_limit'], 0.9) * 100;  // Multiply by 100 to make it a percentage
    $random_chance = rand(0, 100);  //
    // Determine if the raid was successful
    $raid_successful = ($random_chance <= $success_chance);

    // Mark this raid as completed
    perform_query("UPDATE active_raids SET completed = 1 WHERE id = ?", [$raid['id']]);

    // New simulated battle logic
    $boss_hp = $raid['boss_hp'];  // Make sure you have this value from the database
    $boss_name = $raid['boss_name'];  // Make sure you have this value from the database
    $battle_log = "";
    $found_items_log = []; // This will store logs about found items

    // Fetch the participants of this raid along with their equipped weapons and strength
    $db->query("SELECT rp.*, u.hp, u.strength, u.eqweapon, i.itemname FROM raid_participants rp JOIN grpgusers u ON rp.user_id = u.id LEFT JOIN items i ON u.eqweapon = i.id WHERE rp.raid_id = ?");
    $db->execute([$raid['id']]);
    $participants_result = $db->fetch_row();

    $participants = [];
    $total_strength = 0;
    foreach ($participants_result as $participant) {
        $participants[] = $participant;
        $total_strength += $participant['strength'];
    }

    if ($raid['used_booster']) {
        $battle_log .= 'A raid booster was used, boosting the earnings, and chance of finding items.\n';
    }

    if ($raid['used_pass']) {
        $raid_successful = 1;
        $boss_hp = 0;
        $battle_log .= 'A raid pass was used and the raid was successful.\n';
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

    $db->query("SELECT * FROM raid_participants WHERE raid_id = ?");
    $db->execute([$raid['id']]);
    $participants = $db->fetch_row();

    // Fetch the loot for this boss
    $db->query("SELECT * FROM loot WHERE boss_id = ?");
    $db->execute([$raid['boss_id']]);
    $loot_table = $db->fetch_row();

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
            $raidUser = new User($participant['user_id']);

            addToUserCompLeaderboard($participant['user_id'], 'raids_complete', 1);
            addToUserOperations($raidUser, 'raids', 1);

            $currentQuestSeason = getCurrentQuestSeasonForUser($participant['user_id']);
            if (isset($currentQuestSeason['id'])) {
                $questSeasonUser = getQuestSeasonUser($participant['user_id'], $currentQuestSeason['id']);
                $questSeasonMissionUser = getQuestSeasonMissionUser($participant['user_id'], $currentQuestSeason['id']);
                $questSeasonMission = getQuestSeasonMission($participant['user_id'], $currentQuestSeason['id']);

                if (isset($questSeasonMission['requirements']['raids'])) {
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

            if ($raid['used_booster']) {
                $points_won = $points_won + ($points_won / 2);
                $money_won = $money_won + ($money_won / 2);
                $raidpoints_won = $raidpoints_won + ($raidpoints_won / 2);

                $points_won = ceil($points_won);
                $money_won = ceil($money_won);
                $raidpoints_won = ceil($raidpoints_won);
            }

            // First, determine if the user has rmdays greater than 0
            $db->query("SELECT rmdays FROM grpgusers WHERE id = ?");
            $db->execute([$participant['user_id']]);
            $row_check_rmdays = $db->fetch_single();

            if ($row_check_rmdays > 0) {
                perform_query("UPDATE grpgusers SET points = points + ?, bank = bank + ?, raidpoints = raidpoints + ? WHERE id = ?", [$points_won, $money_won, $raidpoints_won, $participant['user_id']]);
            } else {
                perform_query("UPDATE grpgusers SET points = points + ?, money = money + ?, raidpoints = raidpoints + ? WHERE id = ?", [$points_won, $money_won, $raidpoints_won, $participant['user_id']]);
            }

            $event_message = "Your raid against " . $raid['boss_name'] . " has ended. You won $points_won points and $" . ($row_check_rmdays > 0 ? "in your bank " : "") . "$money_won money.";

            // Determine items from the loot table
            $items_won = []; // Store the names of items won
            $pet_items_won = []; // Store the names of items won

            $raid_boost_statues = Check_Item(357, $participant['user_id']);

            if (!empty($loot_table)) {
                foreach ($loot_table as $loot) {
                    $random_chance = rand(0, 100 - $raid_boost_statues); // Generate a number between 0 and 100

                    if ($raid['used_booster']) {
                        $random_chance = rand(0, 80 - $raid_boost_statues);
                    }

                    if ($random_chance <= ($loot['drop_rate'] * 100)) {
                        // Attempt to fetch the name of the item
                        $itemName = Item_Name($loot['item_id']);

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

                        Give_Item($loot['item_id'], $participant['user_id'], 1);

                        $items_won_global = array_merge($items_won_global, $items_won);  // Merge the items won for this participant into the global list
                    }
                }

                if ($participant['leashed_pet_id']) {
                    foreach ($loot_table as $loot) {
                        $random_chance = rand(0, 100); // Generate a number between 0 and 100

                        if ($random_chance <= ($loot['drop_rate'] * 100)) {
                            // Attempt to fetch the name of the item
                            $itemName = Item_Name($loot['item_id']);

                            // Check if the item name is valid
                            if ($itemName === null || $itemName === "" || $itemName === "Unknown Item") {
                                // This means either the item ID is invalid or the item does not exist in the database.
                                // You can choose to log this error, notify an admin, or simply continue to the next loot item.
                                echo "You Found no items During this Raid";
                                continue; // Skip adding this item
                            }

                            $pet_items_won[] = $itemName;

                            Give_Item($loot['item_id'], $participant['user_id'], 1);
                        }
                    }

                }
            }
            $bullet = "&bull;";
            // Fetch the number of participants for the raid
            $db->query("SELECT COUNT(*) as participant_count FROM raid_participants WHERE raid_id = ?");
            $db->execute([$raid['id']]);
            $participant_count = $db->fetch_single();

            // Get raid leader's name
            $raid_leader_name = formatName($raid['summoned_by']);
            // Update raidwins and raidsjoined
            perform_query("UPDATE grpgusers SET raidwins = raidwins + 1, raidsjoined = raidsjoined + 1 WHERE id = ?", [$participant['user_id']]);

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


            if ($participant['leashed_pet_id']) {
                if (!empty($pet_items_won)) {
                    $event_message .= "<br>&bull; Your Pet also found: " . implode(", ", $pet_items_won) . ".";
                } else {
                    $event_message .= "<br>&bull; No items were found by your pet during this raid.";
                }
            }

            // Check if they have token and this raid is Miss Yolk or Don Egghopper
            if ($raid['boss_id'] == 23) {
                $missYolkToken = Check_Item(349, $participant['user_id']);
                if ($missYolkToken == 0) {
                    Give_Item(349, $participant['user_id'], 10);
                }
            } else if ($raid['boss_id'] == 24) {
                $donToken = Check_Item(350, $participant['user_id']);
                if ($donToken == 0) {
                    Give_Item(350, $participant['user_id'], 10);
                }
            }

            // Here, you can send or display $event_message as needed
            // Add a link to view the battle log
            $event_message .= "<br><a href='view_battle_log.php?raid_id=" . $raid['id'] . "'>View Battle Log</a>";

            raidMission($participant['user_id']);

            send_event($participant['user_id'], $event_message);

        } else {
            // Raid failed
            // Update raidwins and raidsjoined
            perform_query("UPDATE grpgusers SET raidlosses = raidlosses + 1 WHERE id = ?", [$participant['user_id']]);

            $event_message = "Your Raid, led by " . formatName($raid['summoned_by']) . " with " . count($participants) . " participants, against " . $raid['boss_name'] . " has failed!";

            // Add a link to view the battle log
            $event_message .= "<br><a href='view_battle_log.php?raid_id=" . $raid['id'] . "'>View Battle Log</a>";

            send_event($participant['user_id'], $event_message);
        }
    }

    $battle_log .= "\nItems Found During the Raid:\n" . implode("", $found_items_log);

    perform_query("INSERT INTO raid_battle_logs (raid_id, battle_log) VALUES (?, ?)", [$raid['id'], $battle_log]);

    $found_items_log = [];
}

// RM Cities
$default_city = 1;
$db->query("UPDATE grpgusers SET city = $default_city WHERE city IN (3, 6, 8, 10, 23) AND rmdays = 0");
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

// Logic for Maze turns, take into consideration the maze boost
$currentTime = time();

// +2 up to 50 for users with an active boost
$db->query("UPDATE grpgusers AS g
  JOIN item_temp_use AS t ON t.user_id = g.id
  SET g.cityturns = LEAST(50, g.cityturns + 2)
  WHERE t.maze_boost > :now AND g.cityturns < 50");
$db->bind(':now', $currentTime);
$db->execute();

// +1 up to 30 for users without an active boost (or expired)
$db->query("UPDATE grpgusers AS g
  JOIN item_temp_use AS t ON t.user_id = g.id
  SET g.cityturns = LEAST(30, g.cityturns + 1)
  WHERE t.maze_boost <= :now AND g.cityturns < 30");
$db->bind(':now', $currentTime);
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
    foreach ($_rows as $_row) {
        Give_Item(198, $_row['id'], 1);

        Send_Event($_row['id'], "You have been awarded a Snowball! Throw these at other players!");
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
    foreach ($_rows as $_row) {
        Give_Item(198, $_row['id'], 1);
        Send_Event($_row['id'], "You have been awarded a Snowball! Throw these at other players!");
        $db->query("UPDATE grpgusers SET epoints = 0 WHERE id = ?");
        $db->execute(array(
            $_row['id']
        ));
    }
}

// Check expired auctions
$db->query("SELECT * FROM auction_house WHERE end_time <= UNIX_TIMESTAMP(NOW()) AND status = 'active'");
$db->execute();
$expiredAuctions = $db->fetch_row();
foreach ($expiredAuctions as $auction) {
    $highestBidderId = $auction['highest_bidder_id'];
    $sellerId = $auction['seller_id'];
    $itemId = $auction['item_id'];
    $quantity = $auction['quantity'];
    $currentBid = $auction['current_bid'];

    // Fetch item name
    $itemName = Item_Name($itemId);

    // If there's a highest bidder, award them the item and notify them
    if (!empty($highestBidderId)) {
        // Transfer item to highest bidder's inventory
        Give_Item($itemId, $highestBidderId, $quantity);

        // Send event to seller and highest bidder
        Send_Event($sellerId, "Your auction for $quantity x $itemName has ended. {$highestBidderId} won with a bid of $currentBid.");
        Send_Event($highestBidderId, "You've won the auction for $quantity x $itemName with a bid of $currentBid.");
    } else {
        // If no highest bidder, return item to seller's inventory
        Give_Item($itemId, $sellerId, $quantity);

        // Send event to seller
        Send_Event($sellerId, "Your auction for $quantity x $itemName has ended without any bids. The item has been returned to your inventory.");
    }

    // Update the status column in the auction_house table to 'finished'
    perform_query("UPDATE auction_house SET status = 'finished' WHERE auction_id = ?", [$auction['auction_id']]);
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
$db->execute();
$db->query("SELECT userid, mid FROM missions m JOIN mission h ON mid = h.id WHERE completed = 'no' AND timestamp + time < unix_timestamp()");
$db->execute();
$rows = $db->fetch_row();
$db->query("UPDATE missions INNER JOIN mission h ON mid = h.id SET completed = 'failed' WHERE completed = 'no' AND timestamp + time < unix_timestamp()");
$db->execute();
foreach ($rows as $row) {
    Send_Event($row['userid'], "You failed your mission!");
    switch ($row['mid']) {
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
            continue 2;
    }
    $db->query("INSERT INTO missionlog VALUES ('', ?, unix_timestamp())");
    $db->execute(array(
        "[x] failed their $mname,{$row['userid']}"
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
    foreach ($rows as $row) {
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
foreach ($rows as $r) {
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

$addCredits = 50;
$addPoints = 100;
if (time() < 1746439200) {
    $addCredits = 100;
    $addPoints = 200;
}

$db->query("SELECT * FROM `referrals` WHERE `credited` = 0");
$db->execute();
$referrals = $db->fetch_row();
foreach ($referrals as $line) {
    $referred_class = new User($line['referred']);
    if ($referred_class->level < 10) {
        continue;
    }

    bloodbath('referrals', $line['referrer']);
    $db->query("UPDATE grpgusers SET credits = credits + ?, points = points + ?, referrals = referrals + 1, refcomp = refcomp + 1, refcount = refcount + 1 WHERE id = ?");
    $db->execute([
        $addCredits,
        $addPoints,
        $line['referrer'],
    ]);

    $db->query("UPDATE referrals SET credited = 1, viewed = 1 WHERE referred = ?");
    $db->execute([$line['referred']]);

    Send_Event($line['referrer'], "You have been credited $addCredits Credits & $addPoints Points for referring [-_USERID_-]. Keep up the good work!", $line['referred']);
    Send_Event(1059, 'USER ID: ' . $line['referred'] . ' referral for ' . $referred_class->formattedname . ' payed out');
    Send_Event(1034, 'USER ID: ' . $line['referred'] . ' referral for ' . $referred_class->formattedname . ' payed out');
}

// Fetch the latest Bloodbath results where rewards haven't been distributed yet and winners column has data
$db->query("SELECT * FROM bloodbath WHERE is_paid = 0 AND winners != '' AND endtime < unix_timestamp() ORDER BY endtime DESC LIMIT 1");
$db->execute();
$latest_bloodbath = $db->fetch_row(true);
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
                    perform_query("UPDATE grpgusers SET credits = credits + ? WHERE id = ?", [$awardedCredits, $user_id]);

                    // Send an event to the user
                    $event_message = "You have won the " . $category . " category and placed " . $position . " and won " . $awardedCredits . " Credits.";
                    send_event($user_id, $event_message);

                } else {
                    // Award points to the user
                    perform_query("UPDATE grpgusers SET points = points + ? WHERE id = ?", [$points_distribution[$position], $user_id]);

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
    perform_query("UPDATE bloodbath SET is_paid = 1 WHERE id = ?", [$latest_bloodbath['id']]);

    $db->query("SELECT * FROM bloodbath WHERE endtime < unix_timestamp() AND winners = ''");
    $db->execute();
    $bbinfo = $db->fetch_row(true);
    if (!empty($bbinfo)) {
        $db->query("SELECT * FROM bbusers");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
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

$csrf = md5(uniqid(rand(), true));
$_SESSION['csrf'] = $csrf;

perform_query("DELETE a FROM `attackladder` a
    JOIN (
        SELECT MIN(id) as id, `user`, `spot`
        FROM `attackladder`
        GROUP BY `user`, `spot`
        HAVING COUNT(*) > 1
    ) as b ON a.user = b.user AND a.spot = b.spot
    WHERE a.id != b.id");
