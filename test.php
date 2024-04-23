<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}


$queen_result = mysql_query("SELECT `id`, `city` FROM `grpgusers` WHERE `queen` > 0");
while ($line = mysql_fetch_array($queen_result)) {
    $cityId = $line['city'];

    $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($line['city']) . "' LIMIT 1");
    $city_result = mysql_fetch_assoc($city_query);

    if ($city_result['owned_points'] > 0) {
        $owned_points = $city_query['owned_points'];
        $twenty_percent = $owned_points - ($owned_points * 0.20);
      echo $twenty_percent ."<br>";
       // mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $twenty_percent . " WHERE `id` = " . $line['id']);
        //Send_event($line['id'], "You earned " . $twenty_percent . " points for being the *Under Boss!");
    }
}