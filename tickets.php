<?php
include 'header.php';
?>

<div class='box_top'>Support</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		if (isset($_POST['submit'])) {
			$subject = strip_tags(str_replace('"', '', $_POST['subject']));
			$body = nl2br(strip_tags(str_replace('"', '', $_POST['body'])));
			$db->query("INSERT INTO tickets (playerid, timesent, status, subject, body) VALUES (?, unix_timestamp(), ?, ?, ?)");
			$db->execute(array(
				$user_class->id,
				"OPEN",
				$subject,
				$body
			));
			echo Message("Your ticket has been Submitted, please check back tommorow to see if it has been attended to.");
		}

		if (isset($_GET['viewopen'])) {
			if ($user_class->admin < 1) {
				diefun("You can not view this page");
			}
			echo '<table id="newtables" style="width:100%;">';
			echo '<tr>';
			echo '<th>Ticket ID</th>';
			echo '<th>Subject</th>';
			echo '<th>Date</th>';
			echo '<th>Status</th>';
			echo '</tr>';

			$db->query("SELECT * FROM tickets WHERE `status` = 'OPEN' ORDER BY ticketid");
			$db->execute();
			$rows = $db->fetch_row();
			foreach ($rows as $row) {
				echo '<tr>';
				echo '<td>' . $row['ticketid'] . '</td>';
				echo '<td><a href="viewticket.php?ticketid=' . $row['ticketid'] . '">' . $row['subject'] . '</a></td>';
				echo '<td>' . date("d F Y, g:ia", $row['timesent']) . '</td>';
				echo '<td>';
				echo '<span style="color:', ($row['status'] == 'OPEN' ? 'green' : 'red'), ';">' . $row['status'] . '</span>';
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';


		}
		if ($user_class->admin > 0) {
			echo "<a href='?viewopen'>View Open Tickets</a>";
		}
		echo '<div class="contenthead floaty">';

		echo 'Welcome to the Help Desk. ';
		echo 'Here you can report anything such as, bugs, hacks, exploits and general questions, etc... ';
		echo 'Please do not Spam this area as you will be banned from using this. ';
		echo 'Please Fill Out The Subject And Your Issue Below. Thank you.';
		echo '<form method="post">';
		echo '<table>';
		echo '<tr>';
		echo '<td>Subject:</td>';
		echo '<td><input type="text" name="subject" size="50" required/></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>Message:</td>';
		echo '<td><textarea name="body" cols="66" rows="5" required></textarea></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2" style="text-align:center;">';
		echo '<input type="submit" name="submit" value="Submit Ticket" />';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
		echo '</div>';
		echo '<div class="contenthead floaty">';
		echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase';
		echo 'font-size: 20px; text-align: left; text-indent: 25px;">';
		echo '<h4>My Tickets</h4></span>';
		echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
		echo '<th>Ticket ID</th>';
		echo '<th>Subject</th>';
		echo '<th>Date</th>';
		echo '<th>Status</th>';
		echo '</tr>';
		$db->query("SELECT * FROM tickets WHERE playerid = ? ORDER BY ticketid");
		$db->execute(array(
			$user_class->id
		));
		$rows = $db->fetch_row();
		foreach ($rows as $row) {
			echo '<tr>';
			echo '<td>' . $row['ticketid'] . '</td>';
			echo '<td><a href="viewticket.php?ticketid=' . $row['ticketid'] . '">' . $row['subject'] . '</a></td>';
			echo '<td>' . date("d F Y, g:ia", $row['timesent']) . '</td>';
			echo '<td>';
			echo '<span style="color:', ($row['status'] == 'OPEN' ? 'green' : 'red'), ';">' . $row['status'] . '</span>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
		include("footer.php");
		?>