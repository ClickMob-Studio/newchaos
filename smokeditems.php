<?php
include "header.php";
if(!in_array($user_class->id, array(1, 480)))
	diefun();
if(isset($_GET['id'])){
	$id = security($_GET['id']);
	$addsql = " WHERE userid = $id";
}
if(isset($_POST['playerid'])){
	$pid = security($_POST['playerid']);
	$iid = security($_POST['itemid']);
	$db->query("UPDATE customitems SET image = ? WHERE userid = ? AND itemid = ?");
	$db->execute(array(
		$_POST['image'],
		$pid,
		$iid
	));
}
$db->query("SELECT * FROM customitems$addsql");
$db->execute();
$rows = $db->fetch_row();
echo'<form>';
	echo'<input type="text" size="5" name="id" />';
	echo'<input type="submit" value="Search by ID" />';
echo'</form>';
echo'<table id="newtables" style="width:100%;">';
	echo'<tr>';
		echo'<th>Yobster</th>';
		echo'<th>Item Type</th>';
		echo'<th>Item Name</th>';
		echo'<th>Current Item Image</th>';
		echo'<th>New Image URL?</th>';
	echo'</tr>';
foreach($rows as $row){
	switch($row['itemid']){
		case 105:
			$itype = "Weapon";
			break;
		case 106:
			$itype = "Armor";
			break;
		case 107:
			$itype = "Shoes";
			break;
	}
	echo'<tr>';
		echo'<td>' . formatName($row['userid']) . '</td>';
		echo'<td>' . $itype . '</td>';
		echo'<td>' . $row['name'] . '</td>';
		echo'<td><img src="' . $row['image'] . '" style="width:100px;height:100px;" /></td>';
		echo'<td>';
			echo'<form method="post">';
				echo'<input type="hidden" name="playerid" value="' . $row['userid'] . '" />';
				echo'<input type="hidden" name="itemid" value="' . $row['itemid'] . '" />';
				echo'<input type="text" name="image" value="' . $row['image'] . '" /><br />';
				echo'<input type="submit" value="Change Image" />';
			echo'</form>';
		echo'</td>';
	echo'</tr>';
}
echo'</table>';
include "footer.php";
?>