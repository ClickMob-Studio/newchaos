<?php

include 'header.php';


$db->query("SELECT * FROM grpgusers WHERE mission_count = 0 LIMIT 10");
$rows = $db->fetch_row();

foreach ($rows as $row) {
    $missionsQ = mysql_query("SELECT COUNT(id) AS mission_count FROM missions WHERE userid = " . $row['id'] . " AND completed = 'successful'");
    $missionsR = mysql_fetch_assoc($missionsQ);
    $missionsCount = $missionsR['mission_count'];

    if ($missionsCount < 1) {
        $missionsCount = 1;
    }

    $db->query("UPDATE grpgusers SET mission_count = " . $missionsCount . " WHERE id = " . $row['id']);
    $db->execute();

    echo 'Updated ' . $row['id'] . ' to ' . $missionsCount . ' missions<br>';
}
