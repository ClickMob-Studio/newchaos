<?php
include "header.php";
$ftid = abs((int) $_GET['ftid']);
$check = mysql_fetch_array(mysql_query("SELECT * FROM forumfollows WHERE userid = $user_class->id AND ftid=$ftid"));
if (!$check) {
    mysql_query("INSERT INTO forumfollows VALUES('',$user_class->id,$ftid)");
}
?>