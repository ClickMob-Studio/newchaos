<?php
require "header.php";

$qu = "SELECT * FROM `user_logs` WHERE user_id = 5";


$res = $db->query($qu);
$db->execute();
$rows = $db->fetch_row();

foreach ($rows as $row) {
    $p  = mysql_query("SELECT * FROM `attacklog` WHERE `timestamp` = ".$row['timestamp']." AND `user_id` = 5");
    $r = mysql_fetch_assoc($p);

    echo "multi";
}

