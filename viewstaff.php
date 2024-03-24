<?php
include 'header.php';
echo'<h3>Staff List</h3>';
echo'<hr>';
echo'<div class="floaty" style="margin:2px;width:95%;">';
	
echo'</div>';
$db->query("SELECT id, avatar, lastactive, admin, gm, fm, pg, eo, st FROM grpgusers WHERE admin + gm + fm + pg + eo + st > 0 ORDER BY admin DESC, gm DESC, id ASC");
$db->execute();
$rows = $db->fetch_row();
foreach($rows as $row){
	$avatar = ($row['avatar']) ? '<img src="' . $row['avatar'] . '" style="width:75px;height:75px;" />' : '';
	echo'<div class="floaty flexcont" style="width:90%;margin:2px;">';
		echo'<div class="flexele" style="border-right:thin solid #333;">';
			echo $avatar;
		echo'</div>';
		echo'<div class="flexele" style="border-right:thin solid #333;line-height:75px;">';
			echo formatName($row['id']);
		echo'</div>';
		echo'<div class="flexele" style="border-right:thin solid #333;line-height:75px;">';
			echo'<b>';
			if($row['admin'])
				echo'<span style="color:red;">Admin</span>';
			elseif($row['gm'])
				echo'<span style="color:yellow;">Game Moderator</span>';
			elseif($row['fm'])
				echo'Forum Moderator';
			elseif($row['pg'])
				echo'<span style="color:cyan;">Player Guide</span>';
			elseif($row['eo'])
				echo'Entertainer';
			echo'</b>';
		echo'</div>';
		echo'<div class="flexele">';
			echo'<b>Last Active:</b> ' . howlongago($row['lastactive']) . '<br />';
			echo'<br />';
			echo'<a href="pms.php?view=new&to=' . $row['id'] . '">[Mail]</a><br />';
		echo'</div>';
	echo'</div>';
}
include 'footer.php';
?>