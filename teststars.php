<?php
require "header.php";

$qu = mysql_query("SELECT * FROM `user_logs` WHERE user_id = 5");
$count = 0;
while($row = mysql_fetch_array($qu)){
    
    $p  = mysql_query("SELECT * FROM `attacklog` WHERE `timestamp` = ".$row['timestamp']." AND `user_id` = 5");
    $r = mysql_fetch_assoc($p);
    if($r['timestamp']){
    echo $count .") Attack Time: ".$r['timestamp']." BA Time: ".$row['timestamp'];
    echo "<br>";
    $count++;
    }
}

