<?php
include("header.php");
$query = mysql_query("SELECT * FROM `grpgusers`");

while ($row = mysql_fetch_array($query)) {
    ob_start();
    mysql_query("UPDATE `grpgusers` SET `ip1` = `ip`, `ip2` = `ip`, `ip3` = `ip`, `ip4` = `ip`, `ip5` = `ip` WHERE `id` = '" . $row['id'] . "'");
    echo("Populated user: " . $row['username']);
    ob_flush();
}

include("footer.php");
?>