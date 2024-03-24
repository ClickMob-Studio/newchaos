<?php
include 'gmheader.php';
	echo'<div class="floaty">';
	echo'<span style="color:red;font-weight:bold;">Manage Tickets</span>';
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<table id="newtables" style="width:100%">';
		echo'<tr>';
			echo'<th>Ticket ID</th>';
			echo'<th>Player</th>';
			echo'<th>Subject</th>';
			echo'<th>Date</th>';
			echo'<th>Status</th>';
		echo'</tr>';
		$db->query("SELECT * FROM tickets ORDER BY field(status, 'OPEN', 'CLOSED'), timesent DESC");
		$db->execute();
		$rows = $db->fetch_row();
		foreach($rows as $row){
			echo'<tr>';
				echo'<td>' . $row['ticketid'] . '</td>';
				echo'<td>' . formatName($row['playerid']) . '</td>';
				echo'<td><a href="mviewticket.php?ticketid=' . $row['ticketid'] . '">' . $row['subject'] . '</a></td>';
				echo'<td>' . date("d F Y, g:ia", $row['timesent']) . '</td>';
				echo'<td>' . $row['status'] . '</td>';
			echo'</tr>';
		}
		echo'</table>';
include("footer.php");
?>