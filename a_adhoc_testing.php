<?php

include 'header.php';

if (isset($_GET['key']) && $_GET['key'] === 'wetesters') {
    $king_result = mysql_query("SELECT `id`, `city` FROM `grpgusers` WHERE `king` > 0");
    while ($line = mysql_fetch_array($king_result)) {
        print_r($king_result);
        $cityId = $king_result['city'];
        echo $cityId . '-';

        $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($king_result['city']) . "' LIMIT 1");
        $city_result = mysql_fetch_assoc($city_query);

        if ($city_result['owned_points'] > 0) {
            echo $city_result['owned_points'];
            mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $city_result['owned_points'] . " WHERE `id` = " . $king_result['id']);
            Send_event($king_result['id'], "You have won " . number_format($city_result['owned_points'], 0) . " points for being the King!");
        }
    }
}