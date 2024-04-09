<?php

require "header.php";

if($user_class->admin < 1){
    exit();
}

$check = mysql_query("SELECT ip, COUNT(*) as user_count FROM grpgusers GROUP BY ip HAVING COUNT(*) > 1)");    
var_dump($check);
while($row = mysql_fetch_array($check)) {
    echo "IP: " . $row["ip"]. " - Users: " . $row["user_count"]. "<br>";
}
