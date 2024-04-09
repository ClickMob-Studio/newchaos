<?php

require "header.php";

if($user_class->admin < 1){
    exit();
}

$check = mysql_query("SELECT ip, COUNT(*) as user_count FROM grpgusers GROUP BY ip HAVING COUNT(*) > 1)");    

while($row = mysql_fetch_assoc($check)) {
    echo "IP: " . $row["ip"]. " - Users: " . $row["user_count"]. "<br>";
}
