<?php
include("header.php");
$logfile = fopen("accesslogs.log", "a+");
$wut = fread($logfile, filesize("accesslogs.log"));
fclose($logfile);
echo Message($wut);
include("footer.php");
?>