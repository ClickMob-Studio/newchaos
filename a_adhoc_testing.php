<?php

include 'header.php';

$prizes = array();
$prizes[1] = 3000;
$prizes[2] = 1500;
$prizes[3] = 500;

$db->query("SELECT * FROM petladder WHERE attacks > 0 ORDER BY attacks DESC LIMIT 3");
$rows = $db->fetch_row();

$i = 1;
foreach ($row as $row) {
    $pet = $db->query("SELECT * FROM pets WHERE id = " . $row['pet_id'] . " LIMIT 1");
    $pet = $db->fetch_row(true);

    if (isset($prizes[$i])) {
        $prize = $prizes[$i];

        $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $pet['userid']);
        $db->execute();

        Send_Event($pet['userid'], "You have won " . $prize . " points for being in spot " . $i . " of the pet ladder for attacks.");
    }

    $i++;
}

$db->query("SELECT * FROM petladder WHERE gym > 0 ORDER BY gym DESC LIMIT 3");
$rows = $db->fetch_row();

$i = 1;
foreach ($row as $row) {
    $pet = $db->query("SELECT * FROM pets WHERE id = " . $row['pet_id'] . " LIMIT 1");
    $pet = $db->fetch_row(true);

    if (isset($prizes[$i])) {
        $prize = $prizes[$i];

        $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $pet['userid']);
        $db->execute();

        Send_Event($pet['userid'], "You have won " . $prize . " points for being in spot " . $i . " of the pet ladder for Gym.");
    }

    $i++;
}

$db->query("SELECT * FROM petladder WHERE exp > 0 ORDER BY exp DESC LIMIT 3");
$rows = $db->fetch_row();

$i = 1;
foreach ($row as $row) {
    $pet = $db->query("SELECT * FROM pets WHERE id = " . $row['pet_id'] . " LIMIT 1");
    $pet = $db->fetch_row(true);

    if (isset($prizes[$i])) {
        $prize = $prizes[$i];

        $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $pet['userid']);
        $db->execute();

        Send_Event($pet['userid'], "You have won " . $prize . " points for being in spot " . $i . " of the pet ladder for Crime EXP.");
    }

    $i++;
}

$db->query("UPDATE petladder SET exp = 0, gym = 0, attacks = 0");
$db->execute();
