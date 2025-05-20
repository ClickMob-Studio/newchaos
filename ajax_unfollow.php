<?php
include "header.php";
$ftid = abs((int) $_GET['ftid']);
mysql_query("DELETE FROM forumfollows WHERE userid=$user_class->id AND ftid=$ftid");
?>