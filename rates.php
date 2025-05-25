<?php
include "header.php";
$db->query("SELECT * FROM rating WHERE rater = ?");
$db->execute(array(
	$user_class->id
));
$rows = $db->fetch_row();
$ids = array();
foreach ($rows as $row) {
	$ids[] = $row['user'];
}
$ids[] = 2;
$db->query("SELECT id FROM grpgusers WHERE id NOT IN (" . explode(',', $ids) . ")");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
	mysql_query("UPDATE grpgusers SET rating = rating + 1 WHERE id = {$row['id']}");
	mysql_query("INSERT INTO rating (user, rater) VALUES ({$row['id']}, $user_class->id)");
	Send_Event($row['id'], "You have been Rated <font color=white><b>UP</b></font> By " . $user_class->formattedname . ". Rate them back? <a href='profiles.php?id=$user_class->id&rate=up'><img src='images/up.png'></img></a> : <a href='profiles.php?id=$user_class->id&rate=down'><img src='images/down.png'></img></a> ", $row['id']);
}
include "footer.php";
?>