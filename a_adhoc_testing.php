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

print_r($rows); exit;
