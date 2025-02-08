<?php

include 'header.php';


$db->query("SELECT * FROM gprgusers WHERE relationship > 0 AND relplayer > 0");
$rows = $db->fetch_row();

$usersComplete = array();
foreach ($rows as $row) {
    if (!in_array($row['id'], $usersComplete)) {
        $usersComplete[] = $row['id'];
        $usersComplete[] = $row['relplayer'];

        $db->query("INSERT INTO rel_comp_leaderboard (user_id, two_user_id) VALUES (" . $row['id'] . ", " . $row['relplayer'] . ")");
        $db->execute();
    }
}
