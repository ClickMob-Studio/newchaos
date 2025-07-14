<?php
include "header.php";
$itmids = array(105, 106, 107);
if (isset($_POST['updateitems'])) {
	$db->query("INSERT INTO customitems (image, name, userid, itemid) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = ?, image = ?");
	foreach ($itmids as $itemid) {
		if (empty($_POST['itemimage' . $itemid]))
			$_POST['itemimage' . $itemid] = 'images/newimage/customset.png';
		else
			if (!getimagesize($_POST['itemimage' . $itemid]))
				$_POST['itemimage' . $itemid] = 'images/newimage/customset.png';
		$db->execute(array(
			$_POST['itemimage' . $itemid],
			$_POST['itemname' . $itemid],
			$user_class->id,
			$itemid,
			$_POST['itemname' . $itemid],
			$_POST['itemimage' . $itemid]
		));
	}
}
echo '<form method="post">';
echo '<div class="flexcont">';
foreach ($itmids as $itemid) {
	$db->query("SELECT * FROM customitems WHERE itemid = ? AND userid = ?");
	$db->execute(array(
		$itemid,
		$user_class->id
	));
	if (!$row = $db->fetch_row(true)) {
		$row['image'] = "images/newimage/customset.png";
		$row['name'] = "";
	}
	echo '<div class="flexele">';
	echo '<img src="' . $row['image'] . '" style="width:100px;height:100px;" /><br />';
	echo '<br />';
	echo 'Name: <input type="text" value="' . $row['name'] . '" name="itemname' . $itemid . '"  /><br />';
	echo '<br />';
	echo 'Image: <input type="text" value="' . $row['image'] . '" name="itemimage' . $itemid . '"  /><br />';
	echo '<br />';
	echo '</div>';
}
echo '</div>';
echo '<input type="submit" name="updateitems" value="Update Custom Items" />';
echo '</form>';
include "footer.php";
?>