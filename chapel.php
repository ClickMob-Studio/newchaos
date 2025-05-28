<?php
include 'header.php';
?>
<div class='box_top'>Chapel</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		if (isset($_GET['accept'])) {
			if ($user_class->relationshipended > (time() - 432000)) {
				echo Message("You can only marry once every 5 days");
				include("footer.php");
				die();
			}
			echo Message("You have accepted the relationship request.");
			$get2 = mysql_query("SELECT * FROM rel_requests WHERE reqid = {$_GET['accept']}");
			$get = mysql_fetch_array($get2);
			Send_Event($get['from'], "[-_USERID_-] has accepted your relationship request.", $user_class->id);
			$res1 = mysql_query("UPDATE grpgusers SET relationship = {$get['status']}, relplayer = {$get['from']} WHERE id = $user_class->id");
			$res2 = mysql_query("UPDATE grpgusers SET relationship = {$get['status']}, relplayer = $user_class->id WHERE id = {$get['from']}");
			perform_query("DELETE FROM rel_requests WHERE player IN (?, ?) OR `from` IN (?, ?)", [$user_class->id, $get['from'], $user_class->id, $get['from']]);
		}
		if (isset($_GET['decline'])) {
			$get2 = mysql_query("SELECT * FROM rel_requests WHERE reqid = {$_GET['decline']}");
			$get = mysql_fetch_array($get2);
			Send_Event($get['from'], "[-_USERID_-] has declined your relationship request.", $user_class->id);
			echo Message("You have declined the request.");
			perform_query("DELETE FROM rel_requests WHERE reqid = ? AND player = ?", [$_GET['decline'], $user_class->id]);
		}

		if ($user_class->relationship == 0 || $user_class->relplayer == 0) {
			echo '<table width="100%">';
			echo '<tr>';
			echo '<td>Player</td>';
			echo '<td>Type</td>';
			echo '<td>Accept</td>';
			echo '<td>Decline</td>';
			echo '<td>Time Sent</td>';
			echo '</tr>';
			$result = mysql_query("SELECT * FROM rel_requests WHERE player = $user_class->id");
			while ($line = mysql_fetch_array($result)) {
				if ($line['status'] == 1)
					$type = "Dating";
				else if ($line['status'] == 2)
					$type = "Engaged";
				else if ($line['status'] == 3)
					$type = "Married";
				echo '<tr>';
				echo '<td width="30%">' . formatName($line['from']) . '</td>';
				echo '<td width="13.3%">' . $type . '</td>';
				echo '<td width="13.3%"><a href="?accept=' . $line['reqid'] . '">Accept</a></td>';
				echo '<td width="13.3%"><a href="?decline=' . $line['reqid'] . '">Decline</a></td>';
				echo '<td width="30%">' . date("d F Y, g:ia", $line['timestamp']) . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo '<div class="floaty">';
			echo 'You are in a relationship with ' . formatName($user_class->relplayer) . '.';
			echo '<hr />';
			echo '<br />';
			if ($user_class->house_shared) {
				echo 'You and ' . formatName($user_class->relplayer) . ' both own the same house, therefore you get a 20% awake bonus.';
			} elseif (strpos($user_class->housename, 'Living') !== false) {
				echo 'You are living in ' . formatName($user_class->relplayer) . '\'s house.';
			} elseif ($user_class->house > 0) {
				echo formatName($user_class->relplayer) . ' is living in your house.';
			}
			echo '<br />';
			echo '</div>';

		}
		echo '</td>';
		echo '</tr>';
		include 'footer.php';