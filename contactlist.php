<?php
include 'header.php';
?>
	
	<div class='box_top'>Contact List</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
if (isset($_GET['remove'])) {
	$remove = security($_GET['remove']);
	$db->query("SELECT * FROM contactlist WHERE id = ? AND playerid = ?");
	$db->execute(array(
		$remove,
		$user_class->id
	));
	$row = $db>fetch_row(true);
    if (!empty($worked['id'])) {
		$db->query("DELETE FROM contactlist WHERE id = ?");
		$db->execute(array(
			$remove
		));
        echo Message("You have successfully removed " . formatName($row['contactid']) . " from your contact list.");
    } else
        echo Message("That contact doesn't exist.");
}
echo'<div class="contenthead">' , (!isset($_GET['enemy'])) ? "Friend" : "Enemy" , ' List ' . ((!isset($_GET['enemy'])) ? '<a href="?enemy">[Go to Enemy List]</a>' : '<a href="?">[Go to Friend List]</a>') . '</div>';
echo'<tr>';
	echo'<td class="contentcontent">';
		echo'<div id="rtn"><br /><br /></div>';
		echo'<table id="newtables" style="width:100%;">';
			echo'<tr>';
				echo'<th>Username</th>';
				echo'<th>Online</th>';
				echo'<th>Actions</th>';
			echo'</tr>';
		$where = isset($_GET['enemy']) ? " AND type <> 1" : " AND type = 1";
		$db->query("SELECT * FROM contactlist WHERE playerid = ?{$where} ORDER BY id ASC");
		$db->execute(array(
			$user_class->id
		));
		$rows = $db->fetch_row();
		$csrf = md5(uniqid(rand(), TRUE));
		$_SESSION['csrf'] = $csrf;
		foreach($rows as $row){
			$contact_class = new User($row['contactid']);
			switch($row['type']){
				case 1:
					$links = "<a href='pms.php?view=new&to={$row['contactid']}'>Message</a> - <a href='slap.php?id={$row['contactid']}'>Slap</a> - <a href='highfive.php?uid={$row['contactid']}'>High Five</a> - ";
					break;
				default:
					$links = "<a href='attack.php?attack={$row['contactid']}&csrf={$csrf}'>Attack</a> - <a class='ajax-link' href='ajax_mug.php?mug={$row['contactid']}&token={$user_class->macro_token}'>Mug</a> - ";
					break;
			}
			echo'<tr>';
				echo'<td rowspan="2">' . $contact_class->formattedname . '</td>';
				echo'<td>' . $contact_class->formattedonline . '</td>';
				echo'<td>' . $links . '<a href="contactlist.php?remove=' . $row['id'] . '">Remove</a></td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td colspan="2">';
					echo'<input type="text" id="' . $row['id'] . 'notes" value="' . $row['notes'] . '" size="60" /> <button onclick="changeContactNote(' . $row['id'] . ');">Change Note</button>';
				echo'</td>';
			echo'</tr>';
		}
		echo '</table>';
include 'footer.php';