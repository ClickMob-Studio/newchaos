<?php
include 'ajax_header.php'; // Use AJAX compatible header

$response = array("success" => false, "message" => "");
$user_class = new User($_SESSION['id']); // Assuming session contains the user ID

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = (int) $_POST['item_id'];
    $quantity = (int) $_POST['quantity'];
    $howmany = check_items($item_id);

    if ($howmany && $howmany >= $quantity) {
        switch ($item_id) {
            case 10:  // Double EXP Item case
                $db->query("UPDATE grpgusers 
                    SET exppill = IF(exppill > unix_timestamp(), exppill + 3600, unix_timestamp() + 3600) 
                    WHERE id = ?");
                $db->execute(array($user_class->id));
                $response['success'] = true;
                $response['message'] = "You will receive double exp on crimes for 1 hour.";
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

            case 42: // Mystery Box functionality
                // Initialize variables to accumulate total rewards
                $total_points = 0;
                $total_raidtokens = 0;
                $total_cash = 0;
                $raid_boosters = 0;
                $police_badges = 0;

                // Loop for each mystery box usage
                for ($i = 0; $i < $quantity; $i++) {
                    $randnum = rand(0, 100);
                    if ($randnum <= 30) {
                        $randpoints = rand(1000, 5000);
                        $total_points += $randpoints;

                    } elseif ($randnum <= 55) {
                        $randraidtokens = mt_rand(10, 200);
                        $total_raidtokens += $randraidtokens;

                    } elseif ($randnum <= 80) {
                        $randcash = rand(1000000, 5000000);
                        $total_cash += $randcash;

                    } elseif ($randnum <= 95) {
                        $raid_boosters++;

                    } else {
                        $police_badges++;
                    }
                }

                // Update the database with accumulated totals
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
                    Give_Item(252, $user_class->id, $raid_boosters); // Assuming item ID 252 is Raid Booster
                }
                if ($police_badges > 0) {
                    Give_Item(163, $user_class->id, $police_badges); // Assuming item ID 163 is Police Badge
                }

               
                $message = "You opened $quantity mystery box(es) and found:";
                if ($total_points > 0) {
                    $message .= " <span style='color:red;font-weight:bold;'>$total_points</span> Points,";
                }
                if ($total_raidtokens > 0) {
                    $message .= " <span style='color:red;font-weight:bold;'>$total_raidtokens</span> Raid Tokens,";
                }
                if ($total_cash > 0) {
                    $message .= " $<span style='color:red;font-weight:bold;'>$total_cash</span>,";
                }
                if ($raid_boosters > 0) {
                    $message .= " <span style='color:red;font-weight:bold;'>$raid_boosters</span> Raid Booster(s),";
                }
                if ($police_badges > 0) {
                    $message .= " <span style='color:red;font-weight:bold;'>$police_badges</span> Police Badge(s),";
                }
                $response['message'] = rtrim($message, ',');
                $response['success'] = true;

           
                Take_Item($item_id, $user_class->id, $quantity);
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

// Output the response in JSON format
echo json_encode($response);
exit;
