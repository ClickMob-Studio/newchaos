<?php
include "ajax_header.php";
$uid = security($_POST['uid'], 'num');
$user_class = new User($_SESSION['id']);
$db->query("SELECT * FROM bans WHERE type = 'mail' AND id = ?");
$db->execute(array(
	$user_class->id
));
if ($db->fetch_row())
    die("<tr><td colspan='2'><span style='color:red'>You are mail banned.</span></td></tr>");
$db->query("SELECT * FROM ignorelist WHERE blocker = ? AND blocked = ?");
$db->execute(array(
	$uid,
	$user_class->id
));
if ($db->fetch_row())
    die("<tr><td colspan='2'><span style='color:red'>You are on this yobster's ignore list.</span></td></tr>");
if ($user_class->level < 5)
    die("<tr><td colspan='2'><span style='color:red'>You must be level 5 to post on walls.</span></td></tr>");
$_POST['msg'] = isset($_POST['msg']) && is_string($_POST['msg']) ? trim($_POST['msg']) : null;
$db->startTrans();
Send_event($uid, "[-_USERID_-] commented on your profile.", $user_class->id);
$db->query("INSERT INTO wallcomments (userid, posterid, msg, timestamp) VALUES (?, ?, ?, unix_timestamp())");
$db->execute(array(
	$uid,
	$user_class->id,
	addslashes($_POST['msg'])
));
print "<tr><td>" . formatName($_SESSION['id']) . "</td><td>" . BBCodeParse(strip_tags(stripslashes($_POST['msg']))) . "</td></tr>";
$db->endTrans();
?>