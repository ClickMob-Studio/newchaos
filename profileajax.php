<?php
// Your database connection
$link = mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14');


if (!$link) {
    die('Could not connect: ' . mysql_error());
}
$db_selected = mysql_select_db('game', $link);
if (!$db_selected) {
    die ('Can\'t use ml2 : ' . mysql_error());
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

      // Make sure user_id is an integer
    if (filter_var($user_id, FILTER_VALIDATE_INT)) {
        $query = sprintf("SELECT lastactive, money FROM grpgusers WHERE id = '%s'",
                         mysql_real_escape_string($user_id));
        $result = mysql_query($query);
        if ($result) {
            $row = mysql_fetch_assoc($result);
            $lastactive = $row['lastactive'];
            $money = $row['money'];

            // Format the last active time
            $formattedLastActive = howlongago($lastactive);

           // Return both the formattedLastActive and money
            echo json_encode(['lastActive' => $formattedLastActive, 'money' => $money]);
         } else {
            // Log SQL error            error_log("SQL Error: " . mysql_error());
            echo "Error: Could not execute SQL query. MySQL error: " . mysql_error();
        }
    } else {
        echo "Error: user_id is not an integer";
    }
} else {
    echo "Error: user_id parameter not received";
}
?>

<?php
// Define the function howlongago here
function howlongago($ts, $stop = 'none')
{
    $ts = time() - $ts;
    if ($ts < 1)
        return " NOW";
    elseif ($ts == 1)
        return $ts . "s";
    elseif ($ts < 60)
        return $ts . "s";
    elseif ($ts < 120)
        return "1m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60)
        return floor($ts / 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 2)
        return "1h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24)
        return floor($ts / (60 * 60)) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24 * 2)
        return "1d " . floor($ts / (60 * 60) % 24) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < (60 * 60 * 24 * 7) or $stop == 'days')
        return floor($ts / (60 * 60 * 24)) . "d " . floor($ts / (60 * 60) % 24) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24 * 30.5)
        return floor($ts / (60 * 60 * 24 * 7)) . " weeks ago";
    elseif ($ts < 60 * 60 * 24 * 365)
        return floor($ts / (60 * 60 * 24 * 30.5)) . " months ago";
    else
        return floor($ts / (60 * 60 * 24 * 365)) . " years ago";

}
?>