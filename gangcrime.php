<?php
include 'header.php'; ?>
<div class='box_top'>Manage Gang Crime</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		if ($user_class->gang != 0) {
			$user_rank = new GangRank($user_class->grank);
			if ($user_rank->crime != 1 && !$user_class->admin)
				diefun("You don't have permission to be here!");
			$gang = new Gang($user_class->gang);
			if (isset($_GET['reset']) && $gang->leader == $user_class->id) {
				$db->query("UPDATE grpgusers SET cur_gangcrime = 0 WHERE gang = ?");
				$db->execute(array(
					$user_class->gang
				));
			}
			if (isset($_GET['c'])) {
				$id = security($_GET['c']);
				$db->query("SELECT * FROM gangcrime WHERE id = ?");
				$db->execute(array(
					$id
				));
				$row = $db->fetch_row(true);
				if (empty($row))
					diefun("That isn't a real crime!<br /><br /><a href='gangcrime.php'>Go Back</a>");
				if ($gang->crime != 0)
					diefun("Your gang is already currently running a crime!<br /><br /><a href='gangcrime.php'>Go Back</a>");
				if ($gang->members < $row['members'])
					diefun("You don't have enough gang members to attempt this crime!<br /><br /><a href='gangcrime.php'>Go Back</a>");
				$ends = time() + (($row['duration'] * 60) * 60);
				$crime = $_GET['c'];
				$db->query("UPDATE gangs SET crime = ?, ending = ?, crimestarter = ? WHERE id = ?");
				$db->execute(array(
					$crime,
					$ends,
					$user_class->id,
					$user_class->gang
				));
				$db->query("UPDATE grpgusers SET gangcrimes = gangcrimes + 1, cur_gangcrime = cur_gangcrime + 1 WHERE id = ?");
				$db->execute(array(
					$user_class->id
				));
				Crime_Event($user_class->gang, $row['name'], "In Progress...", $user_class->id);
				diefun("Gang Crime successfully started.<br /><br /><a href='gangcrime.php'>Go Back</a>");
			}
			if ($gang->crime < 1) {
				echo '<table id="newtables" style="width:100%;">';
				echo '<tr>';
				echo '<th colspan="5">Gang Crimes</th>';
				echo '</tr>';
				echo '<tr>';
				echo '<th>Name</th>';
				echo '<th>Members</th>';
				echo '<th>Reward</th>';
				echo '<th>Time</th>';
				echo '<th>Action</th>';
				echo '</tr>';
				$db->query("SELECT * FROM gangcrime ORDER BY members ASC");
				$db->execute();
				$rows = $db->fetch_row();
				foreach ($rows as $row) {
					echo '<tr>';
					echo '<td>' . $row['name'] . '</td>';
					echo '<td>' . $row['members'] . '</td>';
					echo '<td>' . prettynum($row['reward'], 1) . '</td>';
					echo '<td>' . $row['duration'] . ' hrs</td>';
					echo '<td><a href="gangcrime.php?action=start&c=' . $row['id'] . '"><b>Attempt</b></a></td>';
					echo '</tr>';
				}
			} else {
				$db->query("SELECT * FROM gangcrime WHERE id = ?");
				$db->execute(array(
					$gang->crime
				));
				$row = $db->fetch_row(true);
				$timeleft = crimeleft($gang->crimeend) . " left";
				if ($gang->crimeend <= time())
					$timeleft = "<a href='completegc.php'>[Complete Crime]</a>";
				echo '<table id="newtables" style="width:100%;table-layout:fixed;">';
				echo '<tr>';
				echo '<th colspan="3">Gang Crime</th>';
				echo '</tr>';
				echo '<tr>';
				echo '<th>Name</th>';
				echo '<th>Reward</th>';
				echo '<th>Time Left</th>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>' . $row['name'] . '</td>';
				echo '<td>' . prettynum($row['reward'], 1) . '</td>';
				echo '<td>' . $timeleft . '</td>';
				echo '</tr>';
			}
			echo '</table>';
			genHead("Users That Have Set Up Gang Crimes");
			echo 'The following list shows you how many gang crimes each member of your gang have set up.<br /><br />';
			if ($gang->leader == $user_class->id)
				echo '<center><button onClick="if(confirm(\'Are you sure you want to reset your gang\\\'s gang crime statistics.\')){window.location.href = \'?reset\';}">Reset Gang Crime Statistics</button></center>';
			echo '<table id="newtables" style="width:100%;">';
			echo '<tr>';
			echo '<th rowspan="2">Rank</th>';
			echo '<th rowspan="2">Member</th>';
			echo '<th colspan="2">Gang Crimes</th>';
			echo '</tr>';
			echo '<tr>';
			echo '<th style="width:15%;">This Gang</th>';
			echo '<th style="width:15%;">Total</th>';
			echo '</tr>';
			$rank = 0;
			$db->query("SELECT id, gangcrimes, cur_gangcrime FROM grpgusers WHERE gang = ? ORDER BY cur_gangcrime DESC, gangcrimes DESC");
			$db->execute(array(
				$user_class->gang
			));
			$rows = $db->fetch_row();
			foreach ($rows as $row) {
				echo '<tr>';
				echo '<td width="7%">' . ++$rank . '</td>';
				echo '<td>' . formatName($row['id']) . '</td>';
				echo '<td>' . prettynum($row['cur_gangcrime']) . '</td>';
				echo '<td>' . prettynum($row['gangcrimes']) . '</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</td>';
			echo '</tr>';
		} else
			diefun("You aren't in a gang.");
		include("gangheaders.php");
		include 'footer.php';
		?>