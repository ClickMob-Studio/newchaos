<?php

include 'header.php';

if (isset($_GET['key']) && $_GET['key'] === 'wetesters') {
    $king_result = mysql_query("SELECT `id`, `city` FROM `grpgusers` WHERE `king` > 0");
    while ($line = mysql_fetch_array($king_result)) {
        $cityId = $line['city'];

        $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($line['city']) . "' LIMIT 1");
        $city_result = mysql_fetch_assoc($city_query);

        if ($city_result['owned_points'] > 0) {
            mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $city_result['owned_points'] . " WHERE `id` = " . $line['id']);
            Send_event($line['id'], "You have won " . number_format($city_result['owned_points'], 0) . " points for being the King!");
        }
    }
}