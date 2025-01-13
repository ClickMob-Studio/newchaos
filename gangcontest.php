<?php
include 'header.php';
?>
<div class='box_top'>Gang Contest</div>
						<div class='box_middle'>
							<div class='pad'>
<?php
$db->query("SELECT leader FROM gangs WHERE id = ?");
$db->execute(array(
	$user_class->gang
));
$leader = $db->fetch_single();
if ($leader == $user_class->id || $user_class->admin) {
    if (isset($_GET['reset'])) {
		$db->query("UPDATE gangcontest SET mugs = 0, exp = 0, busts = 0, kills = 0, tax = 0, crimes = 0 WHERE gangid = ?");
		$db->execute(array(
			$user_class->gang
		));
    }
}
$db->query("SELECT id FROM grpgusers WHERE gang = ?");
$db->execute(array(
	$user_class->gang
));
$members = $db->fetch_row();
foreach($members as $member){
	$db->query("SELECT * FROM gangcontest WHERE userid = ? AND gangid = ?");
	$db->execute(array(
		$member['id'],
		$user_class->gang
	));
    if (empty($db->fetch_row())) {
		$db->query("DELETE FROM gangcontest WHERE userid = ? AND gangid <> ?");
		$db->execute(array(
			$member['id'],
			$user_class->gang
		));
		$db->query("INSERT INTO gangcontest (userid, gangid) VALUES (?, ?)");
		$db->execute(array(
			$member['id'],
			$user_class->gang
		));
    }
}
echo'<a href="?reset" style="color:orange;font-size:1.5em;">[Reset]</a> - ';
echo'<a href="?" style="color:orange;font-size:1.5em;">[Regular]</a> - ';
echo'<a href="?totals" style="color:orange;font-size:1.5em;">[Totals]</a> - ';
echo'<a href="?presnap" style="color:orange;font-size:1.5em;">[Snapshots]</a><br />';
if(isset($_GET['presnap'])) {
	echo'<br />';
	$db->query("SELECT timestamp FROM gangcontest_snapshots WHERE gangid = ? GROUP BY timestamp");
	$db->execute(array(
		$user_class->gang
	));
	$rows = $db->fetch_row();
	foreach($rows as $row){
		echo'<a href="?snapshot=' . $row['timestamp'] . '">' . date('d F, Y g:ia', $row['timestamp']) . '</a><br />';
	}
} elseif(isset($_GET['snapshot'])) {
	$ss = security($_GET['snapshot']);
	echo'<br />';
	echo'Snapshot from: ' . date('d F, Y g:ia', $ss);
	echo'<br />';
	echo'<table style="width:100%;" id="newtables">';
	echo'<tr>';
		echo'<th>Gang Mate</th>';
		echo'<th>Exp Gained</th>';
		echo'<th>Crimes</th>';
		echo'<th>Mugs</th>';
		echo'<th>Kills</th>';
		echo'<th>Busts</th>';
		echo'<th>Tax</th>';
	echo'</tr>';
	$db->query("SELECT * FROM gangcontest_snapshots WHERE gangid = ? AND timestamp = ? ORDER BY exp DESC");
	$db->execute(array(
		$user_class->gang,
		$ss
	));
	$conusers = $db->fetch_row();
	foreach($conusers as $user){
		echo'<tr>';
			echo'<td>' . formatName($user['userid']) . '</td>';
            if ($user_class->admin > 0) {
                echo'<td>--' . number_format_short($user['exp']) . '</td>';
            } else {
                echo'<td>' . prettynum($user['exp']) . '</td>';
            }
			echo'<td>' . prettynum($user['crimes']) . '</td>';
			echo'<td>' . prettynum($user['mugs']) . '</td>';
			echo'<td>' . prettynum($user['kills']) . '</td>';
			echo'<td>' . prettynum($user['tax']) . '</td>';
		echo'</tr>';
	}
} elseif(!isset($_GET['totals'])) {
	echo'<br />';
	echo'<table style="width:100%;s" id="newtables">';
		echo'<tr>';
			echo'<th>Gang Mate</th>';
			echo'<th>Exp Gained</th>';
			echo'<th>Crimes</th>';
			echo'<th>Mugs</th>';
			echo'<th>Kills</th>';
			echo'<th>Busts</th>';
			echo'<th>Tax</th>';
		echo'</tr>';
	$db->query("SELECT * FROM gangcontest WHERE gangid = ? ORDER BY exp DESC");
	$db->execute(array(
		$user_class->gang
	));
	$conusers = $db->fetch_row();
	foreach($conusers as $user){
		echo'<tr>';
			echo'<td>' . formatName($user['userid']) . '</td>';
            echo'<td>' . number_format_short($user['exp'], 0) . '</td>';
            echo'<td>' . prettynum($user['crimes']) . '</td>';
			echo'<td>' . prettynum($user['mugs']) . '</td>';
			echo'<td>' . prettynum($user['kills']) . '</td>';
			echo'<td>' . prettynum($user['busts']) . '</td>';
			echo'<td>' . prettynum($user['tax']) . '</td>';
		echo'</tr>';
	}
} else {
	echo'<br />';
	echo'<table style="width:100%;" id="newtables">';
	echo'<tr>';
		echo'<th>Gang Mate</th>';
		echo'<th>Exp Gained</th>';
		echo'<th>Crimes</th>';
		echo'<th>Mugs</th>';
		echo'<th>Kills</th>';
		echo'<th>Busts</th>';
		echo'<th>Tax</th>';
	echo'</tr>';
	$db->query("SELECT * FROM gangcontest WHERE gangid = ? ORDER BY total_exp DESC");
	$db->execute(array(
		$user_class->gang
	));
	$conusers = $db->fetch_row();
	foreach($conusers as $user){
		echo'<tr>';
			echo'<td>' . formatName($user['userid']) . '</td>';
            if ($user_class->admin > 0) {
                echo'<td>' . number_format_short($user['total_exp']) . '</td>';
            } else {
                echo'<td>' . prettynum($user['total_exp']) . '</td>';
            }
			echo'<td>' . prettynum($user['total_crimes']) . '</td>';
			echo'<td>' . prettynum($user['total_mugs']) . '</td>';
			echo'<td>' . prettynum($user['total_kills']) . '</td>';
			echo'<td>' . prettynum($user['total_busts']) . '</td>';
			echo'<td>' . prettynum($user['total_tax']) . '</td>';
		echo'</tr>';
	}
}
print "</table>";
include("gangheaders.php");
include 'footer.php';
?>
