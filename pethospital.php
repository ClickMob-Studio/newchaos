<?php
include 'header.php';
$pet_class = new Pet($user_class->id);
$cost = $pet_class->hospital / 60;
if (isset($_GET['heal'])) {
    if (!$pet_class->hospital)
        diefun("Your pet is not in the hospital.");
    if ($user_class->points < $pet_class->hospital)
        diefun("You need $cost points");
	$db->query("UPDATE grpgusers SET points = points - ? WHERE id = ?");
	$db->execute(array(
		$cost,
		$user_class->id
	));
	$db->query("UPDATE pets SET hospital = 0, hp = level * 50 WHERE userid = ?");
	$db->execute(array(
		$user_class->id
	));
	$user_class->points -= $cost;
    echo Message("You've paid for your pet to get out of the hospital.");
}
		echo'<center>';
			if($pet_class->hospital)
				echo'<a href="?heal"<span style="color:red;font-size:18px;"> &gt; Buy your pet out of the hospital. (' . $cost . ' points) &lt; </span>';
			echo'<table id="newtables">';
				echo'<tr>';
					echo'<th colspan="4">Pet Hospital</th>';
				echo'</tr>';
				echo'<tr>';
					echo'<th>Pet Name</th>';
					echo'<th>Owner</th>';
					echo'<th>Time Left</th>';
			echo'</tr>';
			$db->query("SELECT userid FROM pets WHERE hospital > 0 ORDER BY hospital DESC");
			$db->execute();
			$rows = $db->fetch_row();
			if (!count($row)){
				echo'<tr>';
					echo'<td colspan="4" class="center">There are no pets in the hospital</td>';
				echo'</tr>';
			} else {
				foreach($rows as $row){
					$pet   = new Pet($row['userid']);
					echo'<tr>';
						echo'<td>' . $pet->formatName() . '</td>';
						echo'<td>' . formatName($row['userid']) . '</td>';
						echo'<td>' . ceil($pet->hospital / 60) . '</td>';
					echo'</tr>';
				}
			}
			echo'</table>';
		echo'</center>';
	echo'</td>';
echo'</tr>';
include 'footer.php';