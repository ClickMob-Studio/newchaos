<?php
include 'gmheader.php';
$ticketid = security($_GET['ticketid']);
$opts = array('OPEN', 'CLOSED', 'REQUEST ADMIN', 'PENDING');
if (isset($_POST['submit'])) {
	$subject = strip_tags(str_replace('"', '', $_POST['subject']));
	$body = nl2br(strip_tags(str_replace('"', '', $_POST['body'])));
	$status = (in_array($_POST['status'], $opts)) ? $_POST['status'] : 'OPEN';
	$db->query("UPDATE tickets SET subject = ?, body = ?, status = ? WHERE ticketid = ?");
	$db->execute(array(
		$subject,
		$body,
		$status,
		$ticketid
	));
	echo Message("You have successfully edited this support ticket.");
	$m->delete('tickCount');
}
if (isset($_POST['delete'])) {
	$replyid = security($_POST['replyid']);
	$db->query("DELETE FROM ticketreplies WHERE replyid = ?");
	$db->execute(array(
		$replyid
	));
	echo Message("The post you requested was deleted.");
}
if (isset($_POST['deleteticket'])) {
	$db->query("DELETE FROM tickets WHERE ticketid = ?");
	$db->execute(array(
		$ticketid
	));
	$db->query("DELETE FROM ticketreplies WHERE ticketid = ?");
	$db->execute(array(
		$ticketid
	));
	diefun("The ticket you requested was deleted.<br /><br /><a href='tickets.php'>Go Back</a>");
}
if (isset($_POST['reply'])) {
    $body = nl2br(strip_tags(str_replace('"', '', $_POST['body'])));
    // Insert the reply into the database
    $db->query("INSERT INTO ticketreplies (playerid, timesent, ticketid, body) VALUES (?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $user_class->id,
        $ticketid,
        $body
    ));
    echo Message("You have submitted a reply.");
    
    // Fetch the playerid of the user who created the ticket
    $db->query("SELECT playerid FROM tickets WHERE ticketid = ?");
    $db->execute(array($ticketid));
    $ticketCreator = $db->fetch_row(true);

    // Send an event notification to the ticket creator if the ticket creator exists
    if ($ticketCreator) {
        Send_Event($ticketCreator['playerid'], "Your ticket has received a response. <a href='viewticket.php?ticketid=" . htmlspecialchars($ticketid) . "'>View here</a>.");
    }
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
echo'<div class="floaty">';
	echo'<div class="flexcont">';
		echo'<div class="flexele">' . date("d F Y, g:ia", $row['timesent']) . '</div>';
		echo'<div class="flexele"></div>';
		echo'<div class="flexele"><span style="color:red;font-weight:bold;">' . $row['subject'] . '</span></div>';
	echo'</div>';
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<div class="flexcont">';
		echo'<div class="flexele" style="border-right:thin solid #333;">';
			echo'<img src="' . $avatar . '" height="100" width="100" /><br />' . formatName($row['playerid']);
		echo'</div>';
		echo'<div class="flexele" style="flex:3;">' . BBCodeParse(strip_tags($row['body'])) . '</div>';
	echo'</div>';
echo'</div>';
echo'<div class="floaty">';
	echo'<span style="color:red;font-weight:bold;">Edit Ticket</span>';
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<table width="100%">';
		echo'<form method="post">';
			echo'<tr>';
				echo'<td>Subject:</td>';
				echo'<td><input type="text" name="subject" size="50" value="' . $row['subject'] . '" /></td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Message:</td>';
				echo'<td><textarea name="body" cols="66" rows="5">' . strip_tags($row['body']) . '</textarea></td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Status:</td>';
				echo'<td>';
					echo'<select name="status">';
						echo'<option value="OPEN"' , ($row['status'] == "OPEN") ? ' selected' : '' , '>Open</option>';
						echo'<option value="PENDING"' , ($row['status'] == "PENDING") ? ' selected' : '' , '>Pending</option>';
						echo'<option value="CLOSED"' , ($row['status'] == "CLOSED") ? ' selected' : '' , '>Closed</option>';
						echo'<option value="REQUEST ADMIN"' , ($row['status'] == "REQUEST ADMIN") ? ' selected' : '' , '>Request Admin</option>';
					echo'</select>';
				echo'</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td style="text-align:center;" colspan="2">';
					echo'<input type="submit" name="submit" value="Edit Ticket" /> ';
					echo'<input type="submit" name="deleteticket" value="Delete Ticket" />';
				echo'</td>';
			echo'</tr>';
		echo'</form>';
	echo'</table>';
echo'</div>';
$db->query("SELECT t.*, avatar FROM ticketreplies t JOIN grpgusers g ON t.playerid = g.id WHERE ticketid = ?");
$db->execute(array(
	$ticketid
));
$rrows = $db->fetch_row();
echo'<div class="floaty">';
	echo'<span style="color:red;font-weight:bold;">Replies</span>';
echo'</div>';
if(count($rrows))
	foreach($rrows as $rrow){
		$avatar = ($rrow['avatar'] == "") ? "/images/no-avatar.png" : $rrow['avatar'];
		echo'<div class="floaty">';
			echo'<div class="flexcont">';
				echo'<div class="flexele">' . date("d F Y, g:ia", $rrow['timesent']) . '</div>';
				echo'<div class="flexele"></div>';
				echo'<div class="flexele"></div>';
			echo'</div>';
			echo'<hr style="border:0;border-bottom:thin solid #333;" />';
			echo'<div class="flexcont">';
				echo'<div class="flexele" style="border-right:thin solid #333;">';
					echo'<img src="' . $avatar . '" height="100" width="100" /><br />' . formatName($rrow['playerid']);
				echo'</div>';
				echo'<div class="flexele" style="flex:3;">' . BBCodeParse(strip_tags($rrow['body'])) . '</div>';
			echo'</div>';
		echo'</div>';
	}
else{
	echo'<div class="floaty">';
		echo'There have been no replies to this support ticket yet!';
	echo'</div>';
}
if ($row['status'] != "CLOSED") {
	echo'<div class="floaty">';
		echo'<span style="color:red;font-weight:bold;">Add Reply</span>';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<table width="100%">';
			echo'<form method="post">';
				echo'<tr>';
					echo'<td>Message:</td>';
					echo'<td><textarea name="body" cols="66" rows="5"></textarea></td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td style="text-align:center;" colspan="2"><input type="submit" name="reply" value="Add Reply" /></td>';
				echo'</tr>';
			echo'</form>';
		echo'</table>';
	echo'</div>';
} else {
	echo'<div class="floaty">';
		echo'<span style="color:red;font-weight:bold;">Add Reply</span>';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<table width="100%">';
			echo'<form method="post">';
				echo'<tr>';
					echo'<td align="center">This ticked has been closed.</td>';
				echo'</tr>';
			echo'</form>';
		echo'</table>';
	echo'</div>';
}
include("footer.php");
?>