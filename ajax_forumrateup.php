<?php
include "header.php";
$fpid = abs((int) $_GET['fpid']);

$db->query("SELECT COUNT(*) FROM forumpostrates WHERE userid = ? AND fpid = ? LIMIT 1");
$db->execute([$user_class->id, $fpid]);
if (!$db->num_rows()) {
    perform_query("INSERT INTO forumpostrates (fpid, userid, rate) VALUES(?, ?, ?)", [$fpid, $user_class->id, 'up']);
    perform_query("UPDATE ftopics SET rateup=rateup+1 WHERE forumid=?", [$fpid]);
}
?>