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