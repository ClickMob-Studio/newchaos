<?php
include "header.php";
if(!$user_class->admin && !$user_class->eo && !$user_class->gm)
	diefun("You do not have permission to be here.");
$db->query("SELECT value FROM settings WHERE name = 'ent_pts_bank'");
$db->execute();
$points = $db->fetch_single();
$db->query("SELECT value FROM settings WHERE name = 'ent_cash_bank'");
$db->execute();
$money = $db->fetch_single();
if(isset($_POST['userid'])){
	$userid = security($_POST['userid']);
	$send_points = security($_POST['points']);
	$send_money = security($_POST['money']);
	if(empty($userid))
		diefun("Error, no userid entered.");
	if($send_points && $send_money)
		$currency = prettynum($send_points) . " points and " . prettynum($send_money, 1);
	elseif($send_points)
		$currency = prettynum($send_points) . " points";
	elseif($send_money)
		$currency = prettynum($send_money, 1);
	else
		diefun("Error, no currency submitted.");
	if($send_money > $money)
		diefun("You do not have enough money in the entertainment bank.");
	if($send_points > $points)
		diefun("You do not have enough points in the entertainment bank.");
	Send_Event($userid, formatName($user_class->id) . " has sent you $currency.");
	$db->query("UPDATE grpgusers SET points = points + ?, bank = bank + ? WHERE id = ?");
	$db->execute(array(
		$send_points,
		$send_money,
		$userid
	));
	$db->query("UPDATE settings SET value = value - ? WHERE name = 'ent_cash_bank'");
	$db->execute(array(
		$send_money
	));
	$db->query("UPDATE settings SET value = value - ? WHERE name = 'ent_pts_bank'");
	$db->execute(array(
		$send_points
	));
	$db->query("INSERT INTO ent_trans VALUES ('', ?, ?, ?, ?, unix_timestamp())");
	$db->execute(array(
		$user_class->id,
		$userid,
		$send_points,
		$send_money
	));
	$money -= $send_money;
	$points -= $send_points;
	echo'<div id="success">You have sent ' . formatName($userid) . ' ' . $currency . '.</div>';
}
genHead("Entertainment Payouts.");
echo'<div id="success">You have ' . prettynum($points) . ' points and ' . prettynum($money, 1) . ' in the entertainment fund.</div>';
echo'<form method="post">';
	echo'<table style="margin:auto;text-align:center;">';
		echo'<tr>';
			echo'<td>Userid:</td>';
			echo'<td><input type="text" name="userid" value="' , (isset($_GET['userid'])) ? $_GET['userid'] : '' , '" /></td>';
		echo'</tr>';
		echo'<tr>';
			echo'<td>Money:</td>';
			echo'<td><input type="text" name="money" value="0" /></td>';
		echo'</tr>';
		echo'<tr>';
			echo'<td>Points:</td>';
			echo'<td><input type="text" name="points" value="0" /></td>';
		echo'</tr>';
		echo'<tr>';
			echo'<td colspan="2"><input type="submit" value="Submit Request"even  /></td>';
		echo'</tr>';
	echo'</table>';
echo'</form>';
echo'<table id="newtables" style="margin:auto;width:90%;">';
	echo'<tr>';
		echo'<th>Sender</th>';
		echo'<th>Receiver</th>';
		echo'<th>Points</th>';
		echo'<th>Money</th>';
		echo'<th>Date/Time</th>';
	echo'</tr>';
	$db->query("SELECT * FROM ent_trans ORDER BY timestamp DESC, id DESC LIMIT 100");
	$rows = $db->fetch_row();
	foreach($rows as $row){
		echo'<tr>';
			echo'<td>' . formatName($row['sender']) . '</td>';
			echo'<td>' . formatName($row['receiver']) . '</td>';
			echo'<td>' . prettynum($row['points']) . '</td>';
			echo'<td>' . prettynum($row['money'], 1) . '</td>';
			echo'<td>' . date('d F Y\<\b\r \/\>g:ia', $row['timestamp']) . '</td>';
		echo'</tr>';
	}
	echo'</table>';
include "footer.php";
?>