<?php
include 'ajax_header.php'; // Use AJAX compatible header
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
                $db->query("UPDATE grpgusers SET exppill = unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You will receive double exp on crimes for 1 hour.";
                break;

            case 196:
                $db->query("UPDATE grpgusers SET nightvision = nightvision + 15 WHERE id = ?");
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

            case 13:
            case 14:
                if ($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital)
                   $response['message'] = "You already have full HP and are not in the hospital.";
                   $response['success'] = false;
                   exit;
                if ($user_class->hhow == "bombed" || $user_class->hhow == "cbombed" || $user_class->hhow == "abombed")
                $response['message'] = "These won't help you when you are in bits.. you are going to have to wait it out.";
                $response['success'] = false;
                exit;
                $db->query("SELECT * FROM items WHERE id = ?");
                $db->execute(array($id));
                $row = $db->fetch_row(true);
                $hosp = floor(($user_class->hospital / 100) * $row['reduce']);
                $newhosp = $user_class->hospital - $hosp;
                $newhosp = ($newhosp < 0) ? 0 : $newhosp;
                $hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
                $hp = $user_class->purehp + $hp;
                $hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
                $db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
                $db->execute(array($newhosp, $hp, $user_class->id));
                $response['success'] = true;
                $response['message'] = "You successfully used a {$row['itemname']}.";
                break;

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

            case 38:
                if (empty($_GET['cityid'])) {
                    $db->query("SELECT id, name, levelreq FROM cities WHERE country = 1 ORDER BY levelreq DESC");
                    $db->execute();
                    $rows = $db->fetch_row();
                    $opts = "";
                    foreach ($rows as $row)
                        $opts .= "<option value='{$row['id']}'>{$row['name']} (LVL: {$row['levelreq']})</option>";
                    $response['success'] = false;
                    $response['message'] = '<form method="get"><select name="cityid">' . $opts . '</select><input type="hidden" name="use" value="38" /><input type="submit" value="Move to City" /></form>';
                    echo json_encode($response);
                    exit;
                } else {
                    $cid = security($_GET['cityid']);
                    $db->query("SELECT * FROM cities WHERE id = ? AND pres = 0");
                    $db->execute(array($cid));
                    if ($db->fetch_row()) {
                        $db->query("UPDATE grpgusers SET city = ? WHERE id = ?");
                        $db->execute(array($cid, $user_class->id));
                        $response['success'] = true;
                        $response['message'] = "You have moved cities for free!";
                    } else {
                        $response['message'] = "City does not exist.";
                    }
                }
                break;

            case 42:
                $randnum = rand(0, 100);
                if ($randnum <= 30) {
                    $randpoints = rand(1000, 5000);
                    $db->query("UPDATE grpgusers SET points = points + " . $randpoints . " WHERE id = " . $user_class->id);
                    $response['message'] = "You open the mystery box and find <span style='color:green;font-weight:bold;'>$randpoints</span> Points.";
                } elseif ($randnum <= 55) {
                    $randraidtokens = mt_rand(10, 200);
                    $db->query("UPDATE grpgusers SET raidtokens = raidtokens + " . $randraidtokens . " WHERE id = " . $user_class->id);
                    $response['message'] = "You open the mystery box and find <span style='color:green;font-weight:bold;'>$randraidtokens</span> Raid Tokens.";
                } elseif ($randnum <= 80) {
                    $randcash = rand(1000000, 5000000);
                    $db->query("UPDATE grpgusers SET money = money + " . $randcash . " WHERE id = " . $user_class->id);
                    $response['message'] = "You open the mystery box and find $<span style='color:green;font-weight:bold;'>$randcash</span>.";
                } elseif ($randnum <= 95) {
                    $itemid = 252;
                    Give_Item($itemid, $user_class->id, 1);
                    $response['message'] = "You open the mystery box and find <span style='color:green;font-weight:bold;'>1 x Raid Booster</span>.";
                } else {
                    $itemid = 163;
                    Give_Item($itemid, $user_class->id, 1);
                    $response['message'] = "You open the mystery box and find <span style='color:green;font-weight:bold;'>1 x Police Badge</span>.";
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

            // Add remaining cases similarly
            // ...
            
            default:
                $response['message'] = "Item not recognized or cannot be used.";
                break;
        }

        // Remove the item from inventory after use
        Take_Item($id, $user_class->id);
    } else {
        $response['message'] = "You don't have the item in your inventory.";
    }
}

// Output the response in JSON format
echo json_encode($response);
exit;
