<?php

include 'header.php';


$db->query("SELECT * FROM `events` WHERE `text` LIKE '%Crimson Canyons Bank%'");
$events = $db->fetch_row();

$rows = array();
foreach ($events as $event) {
    if (!isset($rows[$event['to']])) {
        $rows[$event['to']] = 0;
    }

    $rows[$event['to']]++;
}

foreach ($rows as $userid => $amount) {
    $reward = 150000000 * $amount;
    echo 'UPDATE grpgusers SET bank = bank + ' . $reward . ' WHERE id = ' . $userid;
    echo '<br />';

//    $db->query('UPDATE grpgusers SET bank = bank + ' . $reward . ' WHERE id = ' . $userid);
//    $db->execute();
}
exit;
