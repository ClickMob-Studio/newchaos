<?php
require "header.php";

$qu = mysql_query("SELECT a.timestamp AS attack_time, u.timestamp AS user_log_time
FROM user_logs u
JOIN attacklog a ON u.timestamp = a.timestamp AND a.attacker = 91
WHERE u.user_id = 5");
$count = 0;
while($row = mysql_fetch_array($qu)){
    echo $count .") Attack Time: ".$row['attack_time']." BA Time: ".$row['user_log_time'];
    echo "<br>";
    $count++;
    }


