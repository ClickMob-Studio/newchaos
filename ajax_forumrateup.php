<?php
include "header.php";
$fpid = abs((int) $_GET['fpid']);
$check = mysql_fetch_array(mysql_query("SELECT * FROM forumpostrates WHERE userid=$user_class->id AND fpid=$fpid"));
if (!$check) {
    perform_query("INSERT INTO forumpostrates (fpid, userid, rate) VALUES(?, ?, ?)", [$fpid, $user_class->id, 'up']);
    perform_query("UPDATE ftopics SET rateup=rateup+1 WHERE forumid=?", [$fpid]);
}
?>