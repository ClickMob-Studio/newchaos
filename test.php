<?php

require "header.php";

if($user_class->admin < 1){
    exit();
}
$prize1 = 1000;
$prize2 = 1000;


$query = "SELECT id, username, raidcomp FROM grpgusers ORDER BY raidcomp DESC LIMIT 3";


$result = mysql_query($query);


$count = 1;

while ($row = mysql_fetch_assoc($result)) {
    echo $row['username'];
    echo "<br>";
    

    if ($count === 1) {
        echo "Prize: $" . $prize1;
    } else if ($count === 2) {
        echo "Prize: $" . $prize2;
    }
    
   
    $count++;
    echo "<br>";
}