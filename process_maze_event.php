<?php
require_once 'dbcon.php';
include 'ajax_header.php'; // I assume this contains your database connection and other needed functions

$user_class = new User($_SESSION['id']);
error_reporting(1);
//file_put_contents("post_log.txt", print_r($_POST, true));

$tempItemUse = getItemTempUse($user_class->id);

// Create a default response
$response = [
    'direction' => 'unknown',
    'description' => 'An unexpected error occurred.',
    'turnsLeft' => 'unknown turns left',
    'searchResult' => '',
];
// When user decides to search the street
if (isset($_POST['direction'])) {

    // Check if the user is in jail or the hospital
    if ($user_class->jail > 0) {
        die(json_encode(['error' => 'You cannot search whilst you are in Jail!']));
    }

    if ($user_class->hospital > 0) {
        die(json_encode(['error' => 'You cannot search whilst you are in Hospital!']));
    }

    if ($user_class->cityturns == 0) {
        die(json_encode(['error' => 'You cannot search if you have no turns!']));
    }


    $chosenDirection = $_POST['direction']; // Store the direction

    // Fetch all events from the citygame table
    $query = "SELECT * FROM citygame";
    $result = mysql_query($query);
    if (!$result) {
        die(json_encode(['error' => 'Invalid query: ' . mysql_error()]));
    }

    // Time right now
    $currentTime = time();
    $easter_events = [41, 42, 43]; // Easter events IDs

    // Create a weighted array
    $weightedEvents = [];
    while ($event = mysql_fetch_assoc($result)) {
        if ($event['probability'] < 0) {
            if ($user_class->admin < 1) {
                continue; // Skip events with negative probability
            }

            // If the user is an admin, we reverse the negative probability to a positive one
            $event['probability'] = abs($event['probability']);
        }

        if ($tempItemUse['easter_bead'] > $currentTime && in_array($event['id'], $easter_events)) {
            // Double the probability for Easter events if the user has the item enabled
            $event['probability'] = $event['probability'] * 2;
        }

        $probability = (float) $event['probability'] * 10; // We multiply by 10 to make sure eg. 0.1 becomes 1
        for ($i = 0; $i < $probability; $i++) {
            $weightedEvents[] = $event;
        }
    }

    // Randomly select an event from the weighted array
    $event = $weightedEvents[array_rand($weightedEvents)];

    $description = "";
    // Handle the event
    switch ($event['event_type']) {
        case 'text':
            $description = $event['description_template'];
            break;

        case 'money':
            $money = rand($event['min_value'], $event['max_value']);
            $description = str_replace('[money_amount]', "<span style='color: green;'>$" . $money . "</span>", $event['description_template']);
            // Add the money to the user's account
            $money_query = "UPDATE grpgusers SET money = money + $money WHERE id = " . $user_class->id;
            mysql_query($money_query);
            break;
        case 'credits':
            $credits = rand($event['min_value'], $event['max_value']);
            $description = str_replace('[credits_amount]', "<span style='color: green; font-weight: bold;'>" . $credits . "</span>", $event['description_template']);


            // Log the event in user_logs table with the custom description
            $logDescription = "Has found " . $credits . " Credits whilst searching downtown.";
            $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'credits', '{$logDescription}', UNIX_TIMESTAMP())";
            mysql_query($log_query);


            // Add the credits to the user's account
            $credits_query = "UPDATE grpgusers SET credits = credits + $credits WHERE id = " . $user_class->id;
            mysql_query($credits_query);
            break;



        case 'raidtokens':
            $raidtokens = rand($event['min_value'], $event['max_value']);
            $description = str_replace('[raidtokens_amount]', "<span style='color: red; font-weight: bold;'>" . $raidtokens . " raid tokens</span>", $event['description_template']);

            $logDescription = "Has found " . $raidtokens . " Raid Tokens whilst searching downtown.";
            $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'raidtokens', '{$logDescription}', UNIX_TIMESTAMP())";
            mysql_query($log_query);

            // Add the raid tokens to the user's account
            $raidtokens_query = "UPDATE grpgusers SET raidtokens = raidtokens + $raidtokens WHERE id = " . $user_class->id;
            mysql_query($raidtokens_query);
            break;

        case 'points':
            $points = rand($event['min_value'], $event['max_value']);
            $description = str_replace('[points_amount]', $points, $event['description_template']);
            // Add the points to the user's account
            $points_query = "UPDATE grpgusers SET points = points + $points WHERE id = " . $user_class->id;
            mysql_query($points_query);
            break;

        case 'jail':

            $logDescription = "Has landed in some trouble. They are on the way to Jail!.";
            $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'jail', '{$logDescription}', UNIX_TIMESTAMP())";
            mysql_query($log_query);

            $jailTime = rand($event['min_value'], $event['max_value']);
            $description = "<strong style='color:red;'>" . $event['description_template'] . "</strong>";
            $jail_query = "UPDATE grpgusers SET jail = jail + $jailTime WHERE id = " . $user_class->id;
            mysql_query($jail_query);
            break;

        case 'hospital':
            $logDescription = "Has ended up getting hurt. They are on the way to the hospital!.";
            $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'hospital', '{$logDescription}', UNIX_TIMESTAMP())";
            mysql_query($log_query);


            $hospitalTime = rand($event['min_value'], $event['max_value']);
            $description = "<strong style='color:red;'>" . $event['description_template'] . "</strong>";
            $hospital_query = "UPDATE grpgusers SET hospital = hospital + $hospitalTime, `hhow` = 'maze' WHERE id = " . $user_class->id;
            mysql_query($hospital_query);
            break;

        case 'item':
            $item_name = Item_Name($event['item_id']);
            $description = str_replace('[item_name]', $item_name, $event['description_template']);

            // Log the event in user_logs table with the item name
            $logDescription = "Has found a(n) " . $item_name . " whilst searching downtown.";
            $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'item', '{$logDescription}', UNIX_TIMESTAMP())";
            mysql_query($log_query);

            // Check if user already has this item in their inventory
            $inventory_check_query = "SELECT id, quantity FROM inventory WHERE userid = '{$user_class->id}' AND itemid = '{$event['item_id']}'";
            $inventory_check_result = mysql_query($inventory_check_query);
            if ($inventory_item = mysql_fetch_assoc($inventory_check_result)) {
                // User already has the item, increment quantity
                $update_inventory_query = "UPDATE inventory SET quantity = quantity + 1 WHERE id = '{$inventory_item['id']}'";
                mysql_query($update_inventory_query);
            } else {
                // User doesn't have the item, insert a new row
                $insert_inventory_query = "INSERT INTO inventory (userid, itemid, quantity) VALUES ('{$user_class->id}', '{$event['item_id']}', 1)";
                mysql_query($insert_inventory_query);
            }
            break;
        // Add more cases as needed
    }

    // Deduct a turn from the user's cityturns
    $turns_query = "UPDATE grpgusers SET cityturns = cityturns - 1 WHERE id = " . $user_class->id;
    mysql_query($turns_query);

    // Display the description to the user
    //echo "<p><strong>Search Result:</strong><br>";
    //echo "You walked " . $chosenDirection . ".<br>"; // Display the chosen direction
    //echo $description . "</p>";




    // Display remaining turns
    //echo "You have " . $user_class->cityturns . " turns left to search the streets.</p>";

    // Construct the search result and turns left messages
    $searchResult = "<p><strong>Search Result:</strong><br>";
    $searchResult .= "You walked " . $chosenDirection . ".<br>";
    $searchResult .= $description . "</p>";
    $turnsLeft = "You have " . $user_class->cityturns . " turns left to search the Maze.";

    // Populate the response array
    $response['direction'] = $chosenDirection;
    $response['description'] = $description;
    $response['turnsLeft'] = $turnsLeft;
    $response['searchResult'] = $searchResult;

    if (isset($hospitalTime) && $hospitalTime > 0) {
        $response['hospitalTime'] = $hospitalTime;
    } else {
        $response['hospitalTime'] = 0;
    }

}
//file_put_contents("response_log.txt", print_r($response, true));

// Return the response
echo json_encode($response);
?>