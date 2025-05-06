<?php
include 'ajax_header.php'; // Use AJAX compatible header
error_reporting(0);
ini_set('display_errors', 0);
$response = array("success" => false, "message" => "");
$user_class = new User($_SESSION['id']); // Assuming session contains the user ID
function add_rm_days($days, $money, $points)
{
    global $db, $user_class;
    $db->query("UPDATE grpgusers SET rmdays = rmdays + ?, bank = bank + $money, points = points + $points WHERE id = ?");
    $db->execute(array(
        $days,
        $user_class->id
    ));
}
function druggie($num)
{
    global $db, $user_class;
    $drugs = explode("|", $user_class->drugs);
    $drugs[0] = !empty($drugs[0]) ? $drugs[0] : 0;
    $drugs[1] = !empty($drugs[1]) ? $drugs[1] : 0;
    $drugs[2] = !empty($drugs[2]) ? $drugs[2] : 0;
    $drugs[$num] = time();
    $db->query("UPDATE grpgusers SET drugs = ? WHERE id = ?");
    $db->execute(array(
        implode("|", $drugs),
        $user_class->id
    ));
}
// Check if item usage is requested
if (isset($_GET['use'])) {
    $id = security($_GET['use']);
    $howmany = check_items($id);

    if ($howmany) {
        $failed = false;
        switch ($id) {
            case 4:
                $db->query("UPDATE grpgusers SET awake = ? WHERE id = ?");
                $db->execute(array($user_class->maxawake, $user_class->id));
                $response['success'] = true;
                $response['message'] = "You successfully used an awake pill to refill your awake to 100%.";
                break;

            case 8:
                $timeAgo = time() - 900;
                if ($user_class->last_mug_time > $timeAgo) {
                    $response['message'] = 'You have performed a mug in the last 15 minutes. You need to wait before using this protection.';
                    echo json_encode($response);
                    exit;
                }

                $itemDailyLimit = getItemDailyLimit($user_class->id);
                if ($itemDailyLimit['mug_protection'] >= 4) {
                    $response['message'] = 'You can only use 4 mug protections per day.';
                    echo json_encode($response);
                    exit;
                }

                addItemDailyLimit($user_class, 'mug_protection');
                $db->query("UPDATE grpgusers SET mprotection = unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You are now protected from mugs for 1 hour.";
                break;

            case 9:
                $timeAgo = time() - 900;
                if ($user_class->last_attack_time > $timeAgo) {
                    $response['message'] = 'You have performed an attack in the last 15 minutes. You need to wait before using this protection.';
                    echo json_encode($response);
                    exit;
                }

                $itemDailyLimit = getItemDailyLimit($user_class->id);
                if ($itemDailyLimit['attack_protection'] >= 4) {
                    $response['message'] = 'You can only use 4 attack protections per day.';
                    echo json_encode($response);
                    exit;
                }

                addItemDailyLimit($user_class, 'attack_protection');
                $db->query("UPDATE grpgusers SET aprotection = unix_timestamp() + 3600, king = 0, queen = 0 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You are now protected from attacks for 1 hour.";
                break;

            case 10:
                $db->query("UPDATE grpgusers 
            SET exppill = IF(exppill > unix_timestamp(), exppill + 3600, unix_timestamp() + 3600) 
            WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You will receive double exp on crimes for 1 hour.";
                break;

            case 196:
                $db->query("UPDATE grpgusers 
                SET nightvision = IF(nightvision > unix_timestamp(), nightvision + 900, unix_timestamp() + 900) 
                WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You have added 15 minutes to your Night Vision!";
                break;

            case 168:
                $db->query("UPDATE grpgusers SET fbi = fbi + 30 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You are now being watched by the FBI for an extra 30 Minutes!";
                break;

            case 169:
                if ($user_class->fbitime == 0) {
                    $response['message'] = "You are currently not in Fed Jail!";
                    echo json_encode($response);
                    exit;
                }
                $db->query("UPDATE grpgusers SET fbitime = 0 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You have escaped FBI jail using the escape item!";
                break;

            case 14: // Med Cert Case
                // Check for already sent headers
                if (headers_sent($file, $line)) {
                    error_log("Headers already sent in $file on line $line");
                    $response['success'] = false;
                    $response['message'] = "Headers already sent, cannot process the request.";
                    echo json_encode($response);
                    exit;
                }

                // Clear any buffered output to avoid conflicts with JSON
                if (ob_get_length()) {
                    ob_clean();
                }

                // Validate user health and hospital status
                if ($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital) {
                    $response['success'] = false;
                    $response['message'] = "You already have full HP and are not in the hospital.";
                    echo json_encode($response);
                    exit;
                }

                // Check if the user is in a bombed state
                if (in_array($user_class->hhow, ["bombed", "cbombed", "abombed"])) {
                    $response['success'] = false;
                    $response['message'] = "These won't help you when you're in bits. You have to wait it out.";
                    echo json_encode($response);
                    exit;
                }

                // Fetch item details
                $db->query("SELECT * FROM items WHERE id = ?");
                $db->execute(array($id));
                $row = $db->fetch_row(true);

                if (!$row) {
                    $response['success'] = false;
                    $response['message'] = "Item not found.";
                    echo json_encode($response);
                    exit;
                }

                // Calculate hospital time reduction (if hospital is 0, this will remain 0)
                $hosp = floor(($user_class->hospital / 100) * $row['reduce']);
                $newhosp = max($user_class->hospital - $hosp, 0); // Ensure hospital time is non-negative

                // Calculate HP healing
                $hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
                $hp = min($user_class->purehp + $hp, $user_class->puremaxhp); // Cap HP at max HP

                // Update the database with new hospital and HP values
                $db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
                $db->execute(array($newhosp, $hp, $user_class->id));

                // Prepare and send the JSON response
                $response['success'] = true;
                $response['message'] = "You successfully used a Med Cert. HP is now {$hp}, and hospital time reduced to {$newhosp}.";
                echo json_encode($response);
                exit;



            case 27:
                druggie(0);
                $response['success'] = true;
                $response['message'] = "You successfully used some Meth. Your speed has been increased for 15 minutes.";
                break;

            case 28:
                druggie(1);
                $response['success'] = true;
                $response['message'] = "You successfully used some Adrenalin. Your defense has been increased for 15 minutes.";
                break;

            case 29:
                druggie(2);
                $response['success'] = true;
                $response['message'] = "You successfully used some PCP. Your strength has been increased for 15 minutes.";
                break;

            case 235:
                druggie(3);
                $response['success'] = true;
                $response['message'] = "You successfully used some Serenity Serum. Your strength, defense and speed have been increased for 15 minutes.";
                break;

            // case 38:
            //     if (empty($_GET['cityid'])) {
            //         $db->query("SELECT id, name, levelreq FROM cities WHERE country = 1 ORDER BY levelreq DESC");
            //         $db->execute();
            //         $rows = $db->fetch_row();
            //         $opts = "";
            //         foreach ($rows as $row)
            //             $opts .= "<option value='{$row['id']}'>{$row['name']} (LVL: {$row['levelreq']})</option>";
            //         $response['success'] = false;
            //         $response['message'] = '<form method="get"><select name="cityid">' . $opts . '</select><input type="hidden" name="use" value="38" /><input type="submit" value="Move to City" /></form>';
            //         echo json_encode($response);
            //         exit;
            //     } else {
            //         $cid = security($_GET['cityid']);
            //         $db->query("SELECT * FROM cities WHERE id = ? AND pres = 0");
            //         $db->execute(array($cid));
            //         if ($db->fetch_row()) {
            //             $db->query("UPDATE grpgusers SET city = ? WHERE id = ?");
            //             $db->execute(array($cid, $user_class->id));
            //             $response['success'] = true;
            //             $response['message'] = "You have moved cities for free!";
            //         } else {
            //             $response['message'] = "City does not exist.";
            //         }
            //     }
            //     break;

            case 42:
                $randnum = rand(0, 100);
                if ($randnum <= 30) {
                    $randpoints = rand(1000, 5000);
                    $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                    $db->execute(array($randpoints, $user_class->id));
                    $response['message'] = "You open the mystery box and find  " . number_format($randpoints, 0) . " Points.";
                } elseif ($randnum <= 55) {
                    $randraidtokens = mt_rand(10, 200);
                    $db->query("UPDATE grpgusers SET raidtokens = raidtokens + " . $randraidtokens . " WHERE id = " . $user_class->id);
                    $response['message'] = "You open the mystery box and find " . number_format($randraidtokens, 0) . " Raid Tokens.";
                } elseif ($randnum <= 80) {
                    $randcash = rand(1000000, 5000000);
                    $db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
                    $db->execute(array($randcash, $user_class->id));
                    $response['message'] = "You open the mystery box and find $" . number_format($randcash, 0) . ".";
                } elseif ($randnum <= 95) {
                    $itemid = 252;
                    Give_Item($itemid, $user_class->id, 1);
                    $response['message'] = "You open the mystery box and find 1 x Raid Booster.";
                } else {
                    $itemid = 163;
                    Give_Item($itemid, $user_class->id, 1);
                    $response['message'] = "You open the mystery box and find 1 x Police Badge.";
                }
                $response['success'] = true;
                break;

            case 51:
                add_rm_days(30, 150000, 750);
                $response['success'] = true;
                $response['message'] = "You have added 30 RM days to your account.";
                break;

            case 103:
                add_rm_days(60, 300000, 1500);
                $response['success'] = true;
                $response['message'] = "You have added 60 RM days to your account.";
                break;

            case 104:
                add_rm_days(90, 450000, 2500);
                $response['success'] = true;
                $response['message'] = "You have added 90 RM days to your account.";
                break;

            case 163:
                $db->query("UPDATE grpgusers SET bustpill = bustpill + 60 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You have added 60 Minutes to your Police Pass.";
                break;

            case 166:
                $db->query("UPDATE grpgusers SET outofjail = outofjail + 20 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You have added 20 Minutes to your Out of Jail Pass.";
                break;

            case 197: // Nuke item
                if (isset($_POST['selected_city'])) {
                    $selectedCity = security($_POST['selected_city']);
                    $usersQuery = "SELECT id, money FROM grpgusers WHERE city = ?";
                    $db->query($usersQuery);
                    $db->execute(array($selectedCity));
                    $usersResult = $db->fetch_row();

                    $affectedUsers = 0;
                    $totalDeductedMoney = 0;

                    foreach ($usersResult as $user) {
                        $userId = $user['id'];
                        $userMoney = $user['money'];
                        $deductedMoney = $userMoney * 0.2; // 20% deduction

                        hospitalize_user($userId);

                        $newMoney = $userMoney - $deductedMoney;
                        $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                        $db->execute(array($newMoney, $userId));

                        send_event($userId, "You have been nuked!");

                        $affectedUsers++;
                        $totalDeductedMoney += $deductedMoney;
                    }

                    $response['success'] = true;
                    $response['message'] = "Nuke deployed! Affected users: $affectedUsers. Total cash earned: $" . number_format($totalDeductedMoney);
                } else {
                    $response['message'] = "Please select a city.";
                }
                break;

            case 251:
                addItemTempUse($user_class, 'raid_pass');
                $response['success'] = true;
                $response['message'] = "You have used your raid pass. The next raid you host will be successful.";
                break;
            case 252:
                addItemTempUse($user_class, 'raid_booster');
                $response['success'] = true;
                $response['message'] = "You have used your raid booster. All payouts in your next raid will be boosted.";
                break;
            case 163:
                $db->query("UPDATE grpgusers SET bustpill = bustpill + 60 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You have added 60 Minutes to your Police Pass.";
                break;
            case 253:
                $goldRushCredits = 10;
                if (isset($user_class->completeUserResearchTypesIndexedOnId[6])) {
                    $goldRushCredits += 5;
                }
                if (isset($user_class->completeUserResearchTypesIndexedOnId[15])) {
                    $goldRushCredits += 5;
                }
                $db->query("UPDATE user_ba_stats SET gold_rush_credits = gold_rush_credits + ? WHERE user_id = ?");
                $db->execute(array($goldRushCredits, $user_class->id));
                $response['success'] = true;
                $response['message'] = "Head to the Backalley now and start your Gold Rush!";
                break;
            case 254:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['crime_potion_time'] > $now) {
                    $response['message'] = 'You already have a crime potion active.';
                    echo json_encode($response);
                    exit;
                }
                if ($tempItemUse['crime_booster_time'] > $now) {
                    $response['message'] = 'You cannot stack a Crime Potion & Crime Booster.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 3600;
                addItemTempUse($user_class, 'crime_potion_time', $newTime);
                $response['success'] = true;
                $response['message'] = "You drank the crime potion, for the next hour you will gain an extra 10% EXP from crimes!";
                break;
            case 255:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['crime_booster_time'] > $now) {
                    $response['message'] = 'You already have a crime booster active.';
                    echo json_encode($response);
                    exit;
                }
                if ($tempItemUse['crime_potion_time'] > $now) {
                    $response['message'] = 'You cannot stack a Crime Potion & Crime Booster.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 3600;
                addItemTempUse($user_class, 'crime_booster_time', $newTime);
                $response['success'] = true;
                $response['message'] = "You used the crime booster, for the next hour you will gain an extra 20% EXP from crimes!";
                break;
            case 256:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['nerve_vial_time'] > $now) {
                    $response['message'] = 'You already have a nerve vial active.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 1800;
                addItemTempUse($user_class, 'nerve_vial_time', $newTime);
                $response['success'] = true;
                $response['message'] = "You drank the nerve vial, for the next 30 minutes you will have double nerve!";
                break;
            case 257:
                if ($user_class->gang < 1) {
                    $response['message'] = 'You are not in a gang.';
                    echo json_encode($response);
                    exit;
                }
                $db->query("SELECT * FROM grpgusers WHERE gang = ?");
                $db->execute(array($user_class->gang));
                $uRes = $db->fetch_row();
                foreach ($uRes as $ur) {
                    $uClass = new User($ur['id']);
                    addItemTempUse($uClass, 'gang_double_exp_hours', 4);
                }
                $response['success'] = true;
                $response['message'] = "You used your double EXP pill and your whole gang will enjoy 4 hours of double EXP!";
                break;
            case 276:
                $db->query("SELECT * FROM `user_research_type` WHERE `user_id` = ? AND `duration_in_days` > 0 LIMIT 1");
                $db->execute(array($user_class->id));
                $activeUserResearchType = $db->fetch_row(true);
                if ($activeUserResearchType) {
                    $db->query("UPDATE `user_research_type` SET `duration_in_days` = `duration_in_days` - 1 WHERE `duration_in_days` > 0 AND `user_id` = ?");
                    $db->execute(array($user_class->id));
                    $response['success'] = true;
                    $response['message'] = "You used your research token and knocked 1 day off of your current research time!";
                } else {
                    $response['message'] = "You do not have any active research at the moment.";
                }
                break;
            case 258:
                $db->query("UPDATE grpgusers SET points = points + 400000 WHERE id = ?");
                $db->execute(array($user_class->id));
                Give_Item(10, $user_class->id, 5);
                Give_Item(255, $user_class->id, 5);
                Give_Item(256, $user_class->id, 2);
                $response['success'] = true;
                $response['message'] = "You opened your loot crate and found 400,000 points, 5 x Double EXPs, 5 x Crime Boosters, and 2 x Nerve Vials!";
                break;
            case 277:
                addItemTempUse($user_class, 'mission_passes', 1);
                $response['success'] = true;
                $response['message'] = "You used your mission pass. Now you can reset any mission you have already completed today!";
                break;
            case 279:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['gym_protein_bar_time'] > $now) {
                    $response['message'] = 'You already have a gym protein bar active.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 900;
                addItemTempUse($user_class, 'gym_protein_bar_time', $newTime);
                $response['success'] = true;
                $response['message'] = "You ate the protein bar, for the next 15 minutes you will gain an extra 20% in the gym!";
                break;
            case 281:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['gym_super_pills_time'] > $now) {
                    $response['message'] = 'You already have a gym super pill active.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 900;
                addItemTempUse($user_class, 'gym_super_pills_time', $newTime);
                $response['success'] = true;
                $response['message'] = "You took your gym super pills, for the next 15 minutes you will have an extra 10% awake!";
                break;
            case 282:
                $db->query("UPDATE grpgusers SET points = points + 400000 WHERE id = ?");
                $db->execute(array($user_class->id));
                Give_Item(279, $user_class->id, 5);
                Give_Item(281, $user_class->id, 5);
                Give_Item(278, $user_class->id, 1);
                $response['success'] = true;
                $response['message'] = "You opened your gym crate and found 400,000 points, 5 x Protein Bars, 5 x Gym Super Pills, and 1 x Sound System!";
                break;
            case 283:
                Give_Item(253, $user_class->id, 10);
                $response['success'] = true;
                $response['message'] = "You opened your Gold Rush Token Chest and found 10 x Gold Rush Tokens!";
                break;
            case 284:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['ghost_vacuum_time'] > $now) {
                    $response['message'] = 'You already have a ghost vacuum active.';
                    echo json_encode($response);
                    exit;
                }

                $newTime = time() + 900;

                addItemTempUse($user_class, 'ghost_vacuum_time', $newTime);

                $response['success'] = true;
                $response['message'] = "You use your ghost vacuum and you feel ready to hunt ghosts for the next 15 minutes!";
                break;
            case 286:
                $db->query("UPDATE grpgusers SET points = points + 400000, money = money + 1000000000 WHERE id = " . $user_class->id);
                $db->execute();


                Give_Item(285, $user_class->id, 100);
                Give_Item(284, $user_class->id, 1);
                Give_Item(293, $user_class->id, 1);

                $response['success'] = true;
                $response['message'] = "You open your halloween crate and inside find 400,000 points, $1,000,000,000, 100 x Dracula Blood Bag, 1 x Ghost Vacuum & 1 x Dracula Statue!";
                break;
            case 288:
                $expRand = ceil($user_class->maxexp / mt_rand(10000, 30000));
                if ($expRand < 10) {
                    $expRand = 10;
                }

                $db->query("UPDATE grpgusers SET exp = exp + " . $expRand . " WHERE id = " . $user_class->id);
                $db->execute();

                $response['success'] = true;
                $response['message'] = "You eat your Cotton Candy and gain " . number_format($expRand) . " EXP!";
                break;
            case 289:
                $moneyRand = mt_rand(500, 20000);

                $db->query("UPDATE grpgusers SET money = money + " . $moneyRand . " WHERE id = " . $user_class->id);
                $db->execute();

                $response['success'] = true;
                $response['message'] = "You search inside the crate and find $" . number_format($moneyRand) . "!";
                break;
            case 290:
                $tempItemUse = getItemTempUse($user_class->id);

                addItemTempUse($user_class, 'toffee_apples', 1);

                $response['success'] = true;
                $response['message'] = "You eat your Toffee Apple and now your ready to go and attack some City Goons.";
                break;
            case 292:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['trick_or_treat_pass_time'] > $now) {
                    $response['message'] = 'You already have a Trick or Treat Pass active.';
                    echo json_encode($response);
                    exit;
                }

                $newTime = time() + 900;

                addItemTempUse($user_class, 'trick_or_treat_pass_time', $newTime);

                $response['success'] = true;
                $response['message'] = "You use your trick or treat pass and you feel ready to go searching player profiles for the next 15 minutes!";
                break;
            case 294:
                $db->query("UPDATE grpgusers SET points = points + 800000, money = money + 1000000000 WHERE id = " . $user_class->id);
                $db->execute();


                Give_Item(285, $user_class->id, 50); // Blood Bags
                Give_Item(293, $user_class->id, 1); // Dracula Statue
                Give_Item(278, $user_class->id, 1); // Sound System
                Give_Item(277, $user_class->id, 5); // Mission Passes
                Give_Item(283, $user_class->id, 5); // Gold Rush Token Chest

                $response['success'] = true;
                $response['message'] = "You open your black friday crate and inside find 800,000 points, $1,000,000,000, 50 x Dracula Blood Bag, 1 x Dracula Statue, 1 x Sound System, 5 x Gold Rush Token Chest & 5 x Mission Passes!";
                break;
            case 305:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['double_gym_time'] > $now) {
                    $response['message'] = 'You already have a Double Gym Injection active.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 1800;
                addItemTempUse($user_class, 'double_gym_time', $newTime);
                $response['success'] = true;
                $response['message'] = "You shoot up, for the next 30 minutes you will have double gym!";
                break;
            case 306:
                $db->query("UPDATE grpgusers SET points = points + 1000000, money = money + 1250000000 WHERE id = " . $user_class->id);
                $db->execute();


                Give_Item(279, $user_class->id, 1); // Protein Bar
                Give_Item(281, $user_class->id, 1); // Gym Super Pills
                Give_Item(305, $user_class->id, 1); // Double Gym Injection
                Give_Item(278, $user_class->id, 1); // Sound System
                Give_Item(277, $user_class->id, 5); // Mission Passes
                Give_Item(283, $user_class->id, 5); // Gold Rush Token Chest

                $response['success'] = true;
                $response['message'] = "You open your new year box and inside find 1,000,000 points, $1,250,000,000, 1 x Protein Bar, 1 x Gym Super Pill, 1 x Double Gym Injection, 1 x Sound System, 5 x Gold Rush Token Chest & 5 x Mission Passes!";
                break;
            case 322:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['love_potions_time'] > $now) {
                    $response['message'] = 'You already have a Love Potion active.';
                    echo json_encode($response);
                    exit;
                }
                $newTime = $now + 120;
                addItemTempUse($user_class, 'love_potions_time', $newTime);

                $response['success'] = true;
                $response['message'] = "Your attacks for the next two minutes won't cost any energy!";
                break;
            case 324:
                $tempItemUse = getItemTempUse($user_class->id);

                addItemTempUse($user_class, 'perfume', 1);

                $response['success'] = true;
                $response['message'] = "You spray your perfume and feel fantastic! You'll earn double EXP from your next mission!";
                break;
            case 326:
                $db->query("UPDATE grpgusers SET points = points + 1200000, bank = bank + 1500000000 WHERE id = " . $user_class->id);
                $db->execute();

                Give_Item(305, $user_class->id, 1); // Double Gym Injection
                Give_Item(325, $user_class->id, 1); // Love Heart Bed
                Give_Item(277, $user_class->id, 5); // Mission Passes
                Give_Item(283, $user_class->id, 5); // Gold Rush Token Chest
                Give_Item(322, $user_class->id, 5); // Love Heart Potion
                Give_Item(324, $user_class->id, 5); // Perfume


                $response['success'] = true;
                $response['message'] = "You open your new year box and inside find 1,200,000 points, $1,500,000,000, 1 x Double Gym Injection, 1 x Love Heart Bed, 5 x Gold Rush Token Chest, 5 x Mission Passes, 5 x Love Heart Potions & 5 x Perfumes!";
                break;
            case 327:
                $db->query("UPDATE grpgusers SET points = points + 1250000, money = money + 1250000000 WHERE id = " . $user_class->id);
                $db->execute();


                Give_Item(279, $user_class->id, 1); // Protein Bar
                Give_Item(281, $user_class->id, 1); // Gym Super Pills
                Give_Item(305, $user_class->id, 1); // Double Gym Injection
                Give_Item(321, $user_class->id, 1); // Hitman Statue
                Give_Item(277, $user_class->id, 10); // Mission Passes
                Give_Item(283, $user_class->id, 10); // Gold Rush Token Chest
                Give_Item(324, $user_class->id, 5); // Perfume
                Give_Item(290, $user_class->id, 5); // Toffee Apple
                Give_Item(284, $user_class->id, 1); // Ghost Vacuum

                $response['success'] = true;
                $response['message'] = "You open your Golden Chest and inside find 1,250,000 points, $1,250,000,000, 1 x Protein Bar, 1 x Gym Super Pill, 1 x Double Gym Injection, 1 x Hitman Statue, 10 x Gold Rush Token Chest, 10 x Mission Passes, 5 x Toffee Apple, 1 Ghost Vacuum & 5 x Perfume!";
                break;
            case 351: # Cleaner's Supply Crate
                $points = 1000000;

                $bells = Check_Item(352, $user_class->id);
                if ($bells < 5) {
                    Give_Item(352, $user_class->id, 1);
                } else {
                    $points += 100000;
                }

                $armchairs = Check_Item(353, $user_class->id);
                if ($armchairs < 5) {
                    Give_Item(353, $user_class->id, 1);
                } else {
                    $points += 100000;
                }

                $db->query("UPDATE grpgusers SET points = points + ?, money = money + 1000000000 WHERE id = " . $user_class->id);
                $db->execute([$points]);

                Give_Item(356, $user_class->id, 50);
                Give_Item(277, $user_class->id, 5);
                Give_Item(284, $user_class->id, 2);
                Give_Item(10, $user_class->id, 1);
                Give_Item(283, $user_class->id, 10);

                $response['success'] = true;
                $response['message'] = "You open your Cleaner's Supply Crate and inside find " + prettynum($points) + " points, $1,000,000,000, ";

                if ($bells < 5) {
                    $response['message'] = $response['message'] . "1 x Polished Brass Butler Bell, ";
                }

                if ($armchairs < 5) {
                    $response['message'] = $response['message'] . "1 x Immaculate Leather Armchair, ";
                }

                $response['message'] = $response['message'] . "5 x Mission Pass, 2 x Ghost Vacuum, 1 x Double EXP Pill, and 10 x Gold Rush Token Chest!";
                break;
            case 333: # Nerve Tonic
                $nr = give_nerve(100);
                if ($nr == 0) {
                    $response['message'] = "You cannot use the Nerve Tonic when you are at full nerve.";
                    echo json_encode($response);
                    exit;
                } else {
                    $response['success'] = true;
                    $response["message"] = "You have used the Nerve Tonic and replenished " . $nr . " nerve!";
                }
                break;
            case 334: # Balls of Steel
                $nr = give_nerve(250);
                if ($nr == 0) {
                    $response['message'] = "You cannot use the Balls of Steel when you are at full nerve.";
                    echo json_encode($response);
                    exit;
                } else {
                    $response['success'] = true;
                    $response["message"] = "You have used the Balls of Steel and replenished " . $nr . " nerve!";
                }
                break;
            case 339: # Common Gem Bag
                $gems = open_gem_bag([0, 0, 0, 1]);
                $response = gem_bag_response($response, $gems, 0);
                break;
            case 340: # Rare Gem Bag
                $gems = open_gem_bag([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2]);
                $response = gem_bag_response($response, $gems, 1);
                break;
            case 341: # Ultra Rare Gem Bag
                $gems = open_gem_bag([1, 1, 1, 1, 1, 2, 2, 2, 2, 3]);
                $response = gem_bag_response($response, $gems, 2);
                break;
            case 342:
                $db->query("UPDATE grpgusers SET points = points + 1250000, money = money + 1000000000 WHERE id = " . $user_class->id);
                $db->execute();

                Give_Item(344, $user_class->id, 100);
                Give_Item(243, $user_class->id, 1);
                Give_Item(284, $user_class->id, 1);
                Give_Item(333, $user_class->id, 5);
                Give_Item(334, $user_class->id, 2);
                Give_Item(343, $user_class->id, 1);

                $response['success'] = true;
                $response['message'] = "You open your easter crate and inside find 1,250,000 points, $1,000,000,000, 100 x Rare Egg Baskets, 1 x Easter Statue, 5 x Nerve Tonic, 2 x Balls of Steel & 1 x Ghost Vacuum!";
                break;
            case 345:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['easter_bead'] > $now) {
                    $response['message'] = 'You already have an easter bead active.';
                    echo json_encode($response);
                    exit;
                }

                addItemTempUse($user_class, 'easter_bead', $now + 7200);

                $response['success'] = true;
                $response['message'] = "You break the easter bead, a magical aura wraps you and you feel the direction of easter eggs more clearly for the next 2 hours!";
                break;
            case 346:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['maze_boost'] > $now) {
                    $response['message'] = 'You already have a maze boost active.';
                    echo json_encode($response);
                    exit;
                }

                addItemTempUse($user_class, 'maze_boost', $now + 864000);
                give_maze_turns(25);

                $response['success'] = true;
                $response['message'] = "You consume the Maze Boost and feel a tingling sense in your feet for the next 10 days!";
                break;
            case 348:
                # Decide how many points to give between 250 - 1500
                $rewardPoints = rand(250, 1500);

                # Decide how much money to give between $1,000,000 - $10,000,000
                $rewardMoney = rand(1000000, 10000000);

                # Decide how much gold to give between 0 - 5
                $rewardCredits = rand(0, 5);

                # Decide if there should be a Gold Rush Token
                $rewardToken = rand(0, 10) <= 2 ? 1 : 0;
                if ($rewardToken) {
                    Give_Item(253, $user_class->id, 1);
                }

                $db->query("UPDATE `grpgusers` SET `points` = `points` + ?, `money` = `money` + ?, `credits` = `credits` + ? WHERE `id` = ?");
                $db->execute([$rewardPoints, $rewardMoney, $rewardCredits, $user_class->id]);

                $response["success"] = true;
                $response["message"] = "You hammer the golden egg open, and receive " . ($rewardCredits ? $rewardCredits . " gold, " : "") . number_format($rewardPoints, 0) . " points," . ($rewardToken ? "" : " and") . " $" . number_format($rewardMoney, 0) . "" . ($rewardToken ? " and 1 Gold Rush Token." : ".");

                break;
            default:
                $failed = true;
                $response['message'] = "Item not recognized or cannot be used.";
                break;
        }

        if (!$failed) {
            // Remove the item from inventory after use
            Take_Item($id, $user_class->id);
        }
    } else {
        $response['message'] = "You don't have the item in your inventory.";
    }
}

// Output the response in JSON format
echo json_encode($response);
exit;


function gem_bag_response($response, $gems, $quality)
{
    $bag_names = [
        "Common Gem Bag",
        "Rare Gem Bag",
        "Ultra Rare Gem Bag",
    ];

    $gems_rewarded = [];
    foreach ($gems as $gem) {
        $name = get_gem_name_from_id($gem);
        if (isset($gems_rewarded[$name])) {
            $gems_rewarded[$name] += 1;
        } else {
            $gems_rewarded[$name] = 1;
        }
    }

    $gem_message = "";
    foreach ($gems_rewarded as $gem_name => $amount) {
        $gem_message .= $amount . "x " . $gem_name . ", ";
    }

    $response['success'] = true;
    $response["message"] = "You have opened the " . $bag_names[$quality] . " and received: " . $gem_message . "what a load of gems!";
    return $response;
}