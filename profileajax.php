<?php

include_once "classes.php";
include_once "database/pdo_class.php";

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Make sure user_id is an integer
    if (filter_var($user_id, FILTER_VALIDATE_INT)) {
        $db->query("SELECT lastactive, money FROM grpgusers WHERE id = ?");
        $db->execute([$user_id]);
        $row = $db->fetch_row(true);
        if (isset($row)) {
            $lastactive = $row['lastactive'];
            $money = $row['money'];

            $formattedLastActive = howlongago($lastactive);
            echo json_encode(['lastActive' => $formattedLastActive, 'money' => $money]);
        }
    } else {
        echo "Error: user_id is not an integer";
    }
} else {
    echo "Error: user_id parameter not received";
}
?>

<?php

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