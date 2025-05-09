<?php
include 'ajax_header.php';

$response = array("success" => false, "message" => "", "itemid" => 0, "quantity" => 0);
$user_class = new User($_SESSION['id']); // Ensure $_SESSION['id'] is set

// Verify request method and necessary POST variables
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['quantity'])) {
    $item_id = (int) $_POST['item_id'];
    $quantity = (int) $_POST['quantity'];

    $response['itemid'] = $item_id;
    $response['quantity'] = $quantity;

    $howmany = check_items($item_id);

    // Check if user has enough items
    if ($howmany && $howmany >= $quantity) {
        switch ($item_id) {
            case 251: // Raid Pass
                addItemTempUse($user_class, 'raid_pass', $quantity);
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You have used your raid pass.";
                break;

            case 253: // Gold Rush Credits
                $goldRushCredits = 10 * $quantity;
                if (isset($user_class->completeUserResearchTypesIndexedOnId[6])) {
                    $goldRushCredits += 5 * $quantity;
                }
                if (isset($user_class->completeUserResearchTypesIndexedOnId[15])) {
                    $goldRushCredits += 5 * $quantity;
                }
                $db->query("UPDATE user_ba_stats SET gold_rush_credits = gold_rush_credits + ? WHERE user_id = ?");
                $db->execute(array($goldRushCredits, $user_class->id));
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You have gained $goldRushCredits Gold Rush Credits.";
                break;

            case 42: // Mystery Box
                $total_points = 0;
                $total_raidtokens = 0;
                $total_cash = 0;
                $raid_boosters = 0;
                $police_badges = 0;

                for ($i = 0; $i < $quantity; $i++) {
                    $randnum = rand(0, 100);
                    if ($randnum <= 30) {
                        $total_points += rand(1000, 5000);
                    } elseif ($randnum <= 55) {
                        $total_raidtokens += mt_rand(10, 200);
                    } elseif ($randnum <= 80) {
                        $total_cash += rand(1000000, 5000000);
                    } elseif ($randnum <= 95) {
                        $raid_boosters++;
                    } else {
                        $police_badges++;
                    }
                }

                // Update user data with accumulated totals
                if ($total_points > 0) {
                    $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                    $db->execute(array($total_points, $user_class->id));
                }
                if ($total_raidtokens > 0) {
                    $db->query("UPDATE grpgusers SET raidtokens = raidtokens + ? WHERE id = ?");
                    $db->execute(array($total_raidtokens, $user_class->id));
                }
                if ($total_cash > 0) {
                    $db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
                    $db->execute(array($total_cash, $user_class->id));
                }
                if ($raid_boosters > 0) {
                    Give_Item(252, $user_class->id, $raid_boosters);
                }
                if ($police_badges > 0) {
                    Give_Item(163, $user_class->id, $police_badges);
                }

                // Construct response message
                $message = "You opened $quantity mystery box(es) and found:";
                if ($total_points > 0)
                    $message .= " $total_points Points,";
                if ($total_raidtokens > 0)
                    $message .= " $total_raidtokens Raid Tokens,";
                if ($total_cash > 0)
                    $message .= " $$total_cash,";
                if ($raid_boosters > 0)
                    $message .= " $raid_boosters Raid Booster(s),";
                if ($police_badges > 0)
                    $message .= " $police_badges Police Badge(s),";

                $response['message'] = rtrim($message, ',');
                $response['success'] = true;
                Take_Item($item_id, $user_class->id, $quantity);
                break;

            case 10: // Double EXP item
                $timeToAdd = 3600 * $quantity;
                $db->query("UPDATE grpgusers 
                            SET exppill = IF(exppill > unix_timestamp(), exppill + ?, unix_timestamp() + ?) 
                            WHERE id = ?");
                $db->execute(array($timeToAdd, $timeToAdd, $user_class->id));
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You will receive double exp on crimes for $quantity hour(s).";
                break;

            case 163: // Police Pass
                $db->query("UPDATE grpgusers SET bustpill = bustpill + 60 * ? WHERE id = ?");
                $db->execute(array($quantity, $user_class->id));
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You have added " . (60 * $quantity) . " minutes to your Police Pass.";
                break;
            case 252:
                // Add temporary use for 'raid_booster'
                addItemTempUse($user_class, 'raid_booster', $quantity);
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;

                $response['message'] = ("You have used " . $quantity . " x raid boosters. All payouts in your next raid will be boosted.");
                break;

            case 256: // Nerve Vial
                $newTime = time() + (1800 * $quantity);
                addItemTempUse($user_class, 'nerve_vial_time', $newTime);
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You drank from the nerve vial, gaining double nerve for " . (30 * $quantity) . " minutes!";
                break;
            case 283:
                $amount = $quantity * 10;
                Give_Item(253, $user_class->id, $amount);
                Take_Item($item_id, $user_class->id, $quantity);
                $response["success"] = true;
                $response["message"] = ("You open " . $quantity . "x Gold Rush Token Chests and find " . $amount . " x Gold Rush Tokens inside.");
                break;
            case 288:
                $expRand = ceil($user_class->maxexp / mt_rand(10000, 30000));
                if ($expRand < 10) {
                    $expRand = 10;
                }
                $expRand = $expRand * $quantity;

                $db->query("UPDATE grpgusers SET exp = exp + " . $expRand . " WHERE id = " . $user_class->id);
                $db->execute();

                Take_Item($item_id, $user_class->id, $quantity);
                $response["success"] = true;
                $response["message"] = ("You eat " . $quantity . " x Cotton Candies and gain " . number_format($expRand) . " EXP!");
                break;
            case 289:
                $moneyRand = mt_rand(500, 20000);
                $moneyRand = $moneyRand * $quantity;

                $db->query("UPDATE grpgusers SET money = money + " . $moneyRand . " WHERE id = " . $user_class->id);
                $db->execute();


                Take_Item($item_id, $user_class->id, $quantity);
                $response["success"] = true;
                $response["message"] = ("You search inside " . $quantity . " x crates and find $" . number_format($moneyRand) . "!");
                break;
            case 279: // Protein Bar
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['gym_protein_bar_time'] > $now) {
                    $response['message'] = 'You already have a gym protein bar active.';
                    echo json_encode($response);
                    exit;
                }

                $newTime = $now + (900 * $quantity);
                addItemTempUse($user_class, 'gym_protein_bar_time', $newTime);
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You ate the protein bar, for the next " . (15 * $quantity) . " minutes you will gain an extra 20% in the gym!";
                break;
            case 281: // Gym Super Pills
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['gym_super_pills_time'] > $now) {
                    $response['message'] = 'You already have a gym super pill active.';
                    echo json_encode($response);
                    exit;
                }

                $newTime = $now + (900 * $quantity);
                addItemTempUse($user_class, 'gym_super_pills_time', $newTime);
                Take_Item($item_id, $user_class->id, $quantity);
                $response['success'] = true;
                $response['message'] = "You took your gym super pills, for the next " . (15 * $quantity) . " minutes you will have an extra 10% awake!";
                break;
            default:
                $response['message'] = "Item not recognized or cannot be used.";
                break;
        }

    } else {
        $response['message'] = "You don't have enough of the item in your inventory.";
    }
} else {
    $response['message'] = "Invalid request.";
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
