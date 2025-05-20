<?php
include "header.php";
$postid = security($_GET['postid']);
$db->query("SELECT playerid FROM freplies WHERE postid = ?");
$db->execute(array(
	$postid
));
if ($db->fetch_single() == $user_class->id)
	die();
$db->query("SELECT id FROM forumreplyrates WHERE userid = ? AND postid = ?");
$db->execute(array(
	$user_class->id,
	$postid
));
if (!$db->fetch_single()) {
	$db->query("INSERT INTO forumreplyrates VALUES ('', ?, ?, 'up')");
	$db->execute(array(
		$postid,
		$user_class->id
	));
	$db->query("UPDATE freplies SET rateup = rateup + 1 WHERE postid = ?");
	$db->execute(array(
		$postid
	));
}
?>