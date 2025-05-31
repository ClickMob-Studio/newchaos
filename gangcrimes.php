<?php
include 'header.php';
if ($user_class->gang != 0) {
	$user_rank = new GangRank($user_class->grank);
	if ($user_rank->crime != 1 && !$user_class->admin)
		diefun("You don't have permission to be here!");
	$gang = new Gang($user_class->gang);
	if (isset($_GET['reset']) && $gang_class->leader == $user_class->id) {
		$db->query("UPDATE grpgusers SET cur_gangcrime = 0 WHERE gang = ?");
		$db->execute(array(
			$user_class->gang
		));
	}
	if ($_GET['action'] == 'setup') {
		$id = security($_GET['c']);
		$db->query("SELECT * FROM gangcrimes WHERE id = ?");
		$db->execute(array(
			$id
		));
		$row = $db->fetch_row(true);
		if (empty($row))
			diefun("That isn't a real crime!<br /><br /><a href='gangcrimes.php'>Go Back</a>");
		if ($gang->crime != 0)
			diefun("Your gang is already currently running a crime!<br /><br /><a href='gangcrimes.php'>Go Back</a>");
		if ($gang->members < $row['members'])
			diefun("You don't have enough gang members to attempt this crime!<br /><br /><a href='gangcrimes.php'>Go Back</a>");
		$ends = time() + (($row['duration'] * 60) * 60);

		$db->query("SELECT id, username FROM grpgusers WHERE gang = ?");
		$db->execute([$user_class->gang]);
		$members = $db->fetch_row();

		echo "Required Members: " . $row['members'];
		for ($i = 1; $i <= $row['members']; $i++) {
			echo "<br/><br>Member " . $i . " ";
			echo "<select>";
			foreach ($members as $member) {
				echo "<option>" . $member['username'] . "</option>";
			}
			echo "</select>";
		}
		exit();



	}

	if ($gang->crime < 1) {
		echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
		echo '<th colspan="7">Gang Crimes</th>';
		echo '</tr>';
		echo '<tr>';
		echo '<th>Name</th>';
		echo '<th>Members</th>';
		echo '<th>+EXP</th>';
		echo '<th>+Money</th>';
		echo '<th>+Respect</th>';
		echo '<th>Time</th>';
		echo '<th>Action</th>';
		echo '</tr>';
		$db->query("SELECT * FROM gangcrimes ORDER BY members ASC");
		$db->execute();
		$rows = $db->fetch_row();
		foreach ($rows as $row) {
			echo '<tr>';
			echo '<td>' . $row['name'] . '</td>';
			echo '<td>' . $row['members'] . '</td>';
			echo '<td>' . number_format($row['reward_exp'], 0) . '</td>';
			echo '<td>' . prettynum($row['reward_money'], 1) . '</td>';
			echo '<td>' . number_format($row['reward_respect'], 0) . '</td>';
			echo '<td>' . $row['duration'] . ' hrs</td>';
			echo '<td><a href="gangcrimes.php?action=setup&c=' . $row['id'] . '"><b>Setup</b></a></td>';
			echo '</tr>';
		}
	} else {
		$db->query("SELECT * FROM gangcrimes WHERE id = ?");
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
	if ($gang_class->leader == $user_class->id)
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