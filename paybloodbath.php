<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch the latest Bloodbath results where rewards haven't been distributed yet and winners column has data
$db->query("SELECT * FROM bloodbath WHERE is_paid = 0 AND winners != '' ORDER BY endtime DESC LIMIT 1");
$latest_bloodbath = $db->fetch_row(true);
if (empty($latest_bloodbath)) {
    echo "No unpaid Bloodbath records found or the winners column is empty.";
    exit;
}

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

if (isset($_POST['perform_payout']) && $_POST['perform_payout'] && $latest_bloodbath) {
    $points_distribution = [
        1 => 20000,
        2 => 10000,
        3 => 5000
    ];

    echo "<h2>Payout Summary:</h2>";

    foreach ($transformed_data as $category => $users) {
        arsort($users); // Sorting users by their value in descending order

        $top_3_users = array_slice($users, 0, 3, true);

        $position = 1;
        foreach ($top_3_users as $user_id => $value) {
            if ($user_id && $value) {
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

                $position++;
            }
        }
    }

    // Update the bloodbath entry to indicate that the rewards have been distributed
    perform_query("UPDATE bloodbath SET is_paid = 1 WHERE id = ?", [$latest_bloodbath['id']]);
} else {
    echo "<h2>Current Winners:</h2>";

    foreach ($transformed_data as $category => $users) {
        arsort($users); // Sorting users by their value in descending order

        $top_3_users = array_slice($users, 0, 3, true);

        echo "<strong>" . strtoupper($category) . ":</strong><br>";

        $position = 1;
        foreach ($top_3_users as $user_id => $value) {
            if ($user_id && $value) {
                echo "Position: " . $position . " - " . formatName($user_id) . " with " . $value . "<br>";
                $position++;
            }
        }

        echo "<br>";
    }

    echo '<form method="POST" action="">
        <input type="hidden" name="perform_payout" value="1">
        <input type="submit" value="Perform Payout">
    </form>';
}

include 'footer.php';
?>