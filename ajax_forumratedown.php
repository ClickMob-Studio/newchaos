<?php
include "header.php";
$fpid = abs((int) $_GET['fpid']);
$check = mysql_fetch_array(mysql_query("SELECT * FROM forumpostrates WHERE userid=$user_class->id AND fpid=$fpid"));
if (!$check) {
    mysql_query("INSERT INTO forumpostrates VALUES('',$fpid,$user_class->id,'down')");
    mysql_query("UPDATE ftopics SET ratedown=ratedown+1 WHERE forumid=$fpid");
}
?>