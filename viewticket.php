<?php
include 'header.php';
$ticketid = security($_GET['ticketid']);
if (isset($_POST['reply'])) {
	$body = nl2br(strip_tags(str_replace('"', '', $_POST['body'])));
	$db->query("INSERT INTO ticketreplies (playerid, timesent, ticketid, body) VALUES (?, unix_timestamp(), ?, ?)");
	$db->execute(array(
		$user_class->id,
		$ticketid,
		$body
	));

	$db->query("SELECT playerid FROM tickets WHERE ticketid = ?");
	$db->execute([$ticketid]);
	$n = $db->fetch_single();
	Send_Event($n, "A reply to you ticket has been added");

	Send_Event(1034, "A reply to a support ticket has been added");
	Send_Event(1059, "A reply to a support ticket has been added");
	echo Message("You have submitted a reply.");
}
$db->query("SELECT * FROM tickets WHERE ticketid = ?");
$db->execute(array(
	$ticketid
));
$row = $db->fetch_row(true);
if (empty($row))
	diefun("This ticket ID could not be found in our database, sorry.");
$db->query("SELECT avatar FROM grpgusers WHERE id = ?");
$db->execute(array(
	$row['playerid']
));
$avatar = $db->fetch_single();
$avatar = ($avatar == "") ? "/images/no-avatar.png" : $avatar;
if ($user_class->admin == 1 || $user_class->gm == 1) {
	$db->query("UPDATE tickets SET viewed = 1 WHERE ticketid = ?");
	$db->execute(array(
		$ticketid
	));
}
if ($row['playerid'] != $user_class->id && $user_class->admin < 1)
	diefun("This support ticket doesn't belong to you.");


echo '<div class="floaty">';
echo '<div class="flexcont">';
echo '<div class="flexele">' . date("d F Y, g:ia", $row['timesent']) . '</div>';
echo '<div class="flexele"></div>';
echo '<div class="flexele"><span style="color:red;font-weight:bold;">' . $row['subject'] . '</span></div>';
echo '</div>';
echo '<hr style="border:0;border-bottom:thin solid #333;" />';
echo '<div class="flexcont">';
echo '<div class="flexele" style="border-right:thin solid #333;">';
echo '<img src="' . $avatar . '" height="100" width="100" /><br />' . formatName($row['playerid']);
echo '</div>';
echo '<div class="flexele" style="flex:3;">' . BBCodeParse(strip_tags($row['body'])) . '</div>';
echo '</div>';
echo '</div>';
$db->query("SELECT t.*, avatar FROM ticketreplies t JOIN grpgusers g ON t.playerid = g.id WHERE ticketid = ?");
$db->execute(array(
	$ticketid
));
$rrows = $db->fetch_row();
echo '<div class="floaty">';
echo '<span style="color:red;font-weight:bold;">Replies</span>';
echo '</div>';
if (count($rrows))
	foreach ($rrows as $rrow) {
		$avatar = ($rrow['avatar'] == "") ? "/images/no-avatar.png" : $rrow['avatar'];
		echo '<div class="floaty">';
		echo '<div class="flexcont">';
		echo '<div class="flexele">' . date("d F Y, g:ia", $rrow['timesent']) . '</div>';
		echo '<div class="flexele"></div>';
		echo '<div class="flexele"></div>';
		echo '</div>';
		echo '<hr style="border:0;border-bottom:thin solid #333;" />';
		echo '<div class="flexcont">';
		echo '<div class="flexele" style="border-right:thin solid #333;">';
		echo '<img src="' . $avatar . '" height="100" width="100" /><br />' . formatName($rrow['playerid']);
		echo '</div>';
		echo '<div class="flexele" style="flex:3;">' . BBCodeParse(strip_tags($rrow['body'])) . '</div>';
		echo '</div>';
		echo '</div>';
	} else {
	echo '<div class="floaty">';
	echo 'There have been no replies to this support ticket yet!';
	echo '</div>';
}
if ($row['status'] != "CLOSED") {
	echo '<div class="floaty">';
	echo '<span style="color:red;font-weight:bold;">Add Reply</span>';
	echo '<hr style="border:0;border-bottom:thin solid #333;" />';
	echo '<table width="100%">';
	echo '<form method="post">';
	echo '<tr>';
	echo '<td>Message:</td>';
	echo '<td><textarea name="body" cols="66" rows="5"></textarea></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td style="text-align:center;" colspan="2"><input type="submit" name="reply" value="Add Reply" /></td>';
	echo '</tr>';
	echo '</form>';
	echo '</table>';
	echo '</div>';
} else {
	echo '<div class="floaty">';
	echo '<span style="color:red;font-weight:bold;">Add Reply</span>';
	echo '<hr style="border:0;border-bottom:thin solid #333;" />';
	echo '<table width="100%">';
	echo '<form method="post">';
	echo '<tr>';
	echo '<td align="center">This ticked has been closed.</td>';
	echo '</tr>';
	echo '</form>';
	echo '</table>';
	echo '</div>';
}
include("footer.php");
?>