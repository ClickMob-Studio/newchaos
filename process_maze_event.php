<?php
require_once 'dbcon.php';
include 'ajax_header.php';

$user_class = new User($_SESSION['id']);
error_reporting(1);

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

    $db->query("SELECT * FROM citygame");
    $db->execute();
    $events = $db->fetch_row();
    if (empty($events)) {
        die(json_encode(['error' => 'Failed to find maze events, contact administrator.']));
    }

    // Time right now
    $currentTime = time();
    $easter_events = [41, 42, 43]; // Easter events IDs

    // Create a weighted array
    $weightedEvents = [];
    foreach ($events as $event) {
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

    $user_boosts = get_skill_boosts($user_class->skills);

    $description = "";
    // Handle the event
    switch ($event['event_type']) {
        case 'text':
            $description = $event['description_template'];
            break;

        case 'money':
            $money = rand($event['min_value'], $event['max_value']);

            if (isset($user_boosts['maze_earnings'])) {
                $money = $money * $user_boosts['maze_earnings'];
            }

            $description = str_replace('[money_amount]', "<span style='color: green;'>$" . $money . "</span>", $event['description_template']);

            $db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
            $db->execute([$money, $user_class->id]);
            break;
        case 'credits':
            $credits = rand($event['min_value'], $event['max_value']);
            $description = str_replace('[credits_amount]', "<span style='color: green; font-weight: bold;'>" . $credits . "</span>", $event['description_template']);

            if (isset($user_boosts['maze_earnings'])) {
                $credits = $credits * $user_boosts['maze_earnings'];
            }

            // Log the event in user_logs table with the custom description
            $logDescription = "Has found " . $credits . " Credits whilst searching downtown.";

            $db->query("INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES (?, 'credits', ?, UNIX_TIMESTAMP())");
            $db->execute([$user_class->id, $logDescription]);

            $db->query("UPDATE grpgusers SET credits = credits + ? WHERE id = ?");
            $db->execute([$credits, $user_class->id]);
            break;
        case 'raidtokens':
            $raidtokens = rand($event['min_value'], $event['max_value']);
            $description = str_replace('[raidtokens_amount]', "<span style='color: red; font-weight: bold;'>" . $raidtokens . " raid tokens</span>", $event['description_template']);

            $logDescription = "Has found " . $raidtokens . " Raid Tokens whilst searching downtown.";

            $db->query("INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES (?, 'raidtokens', ?, UNIX_TIMESTAMP())");
            $db->execute([$user_class->id, $logDescription]);

            $db->query("UPDATE grpgusers SET raidtokens = raidtokens + ? WHERE id = ?");
            $db->execute([$raidtokens, $user_class->id]);
            break;
        case 'points':
            $points = rand($event['min_value'], $event['max_value']);

            if (isset($user_boosts['maze_earnings'])) {
                $points = $points * $user_boosts['maze_earnings'];
            }

            $description = str_replace('[points_amount]', $points, $event['description_template']);

            $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
            $db->execute([$points, $user_class->id]);
            break;

        case 'jail':
            $logDescription = "Has landed in some trouble. They are on the way to Jail!.";

            $db->query("INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES (?, 'jail', ?, UNIX_TIMESTAMP())");
            $db->execute([$user_class->id, $logDescription]);

            $jailTime = rand($event['min_value'], $event['max_value']);
            $description = "<strong style='color:red;'>" . $event['description_template'] . "</strong>";

            $db->query("UPDATE grpgusers SET jail = jail + ? WHERE id = ?");
            $db->execute([$jailTime, $user_class->id]);
            break;

        case 'hospital':
            $logDescription = "Has ended up getting hurt. They are on the way to the hospital!.";
            $db->query("INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES (?, 'hospital', ?, UNIX_TIMESTAMP())");
            $db->execute([$user_class->id, $logDescription]);

            $hospitalTime = rand($event['min_value'], $event['max_value']);
            $description = "<strong style='color:red;'>" . $event['description_template'] . "</strong>";

            $db->query("UPDATE grpgusers SET hospital = hospital + ?, `hhow` = 'maze' WHERE id = ?");
            $db->execute([$hospitalTime, $user_class->id]);
            break;
        case 'item':
            $item_name = Item_Name($event['item_id']);
            $description = str_replace('[item_name]', $item_name, $event['description_template']);

            // Log the event in user_logs table with the item name
            $logDescription = "Has found a(n) " . $item_name . " whilst searching downtown.";

            $db->query("INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES (?, 'item', ?, UNIX_TIMESTAMP())");
            $db->execute([$user_class->id, $logDescription]);

            Give_Item($event['item_id'], $user_class->id);
            break;
        // Add more cases as needed
    }

    // Deduct a turn from the user's cityturns
    $db->query("UPDATE grpgusers SET cityturns = cityturns - 1 WHERE id = ?");
    $db->execute([$user_class->id]);

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

    if (isset($jailTime) && $jailTime > 0) {
        $response['jailTime'] = $jailTime;
        $response['jailCost'] = ceil($jailTime / 60);
    } else {
        $response['jailTime'] = 0;
        $response['jailCost'] = 0;
    }

}

if ($response['jailTime'] == 0 && $response['hospitalTime'] == 0) {
    $currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
    if (isset($currentQuestSeason['id'])) {
        $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
        $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
        $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
        if (
            isset($questSeasonMission['requirements']->maze) &&
            (int) $questSeasonMissionUser['progress']->maze < (int) $questSeasonMission['requirements']->maze
        ) {
            updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'maze', 1);
        }
    }
}

// Return the response
echo json_encode($response);
?>