<?php
include 'header.php';
include 'includes/pagination.class.php';
$pages = new pagination();
$pages->items_per_page = 30;
$pages->max_pages = 10;
$db->query("SELECT COUNT(*) FROM spylog WHERE id = ?");
$db->execute(array(
	$user_class->id
));
$pages->items_total = $db->fetch_single();
?>

<div class="box_top">Spy Logs</div>
<div class="box_middle">
	<div class="pad">

<?php
        echo'<table id="newtables" style="width:100%;">';
            echo'<tr>';
                echo'<th>When</th>';
                echo'<th>Player</th>';
                echo'<th>Strength</th>';
                echo'<th>Defense</th>';
                echo'<th>Speed</th>';
                echo'<th>Agility</th>';
                echo'<th>Bank</th>';
                echo'<th>Points</th>';
            echo'</tr>';
		$db->query("SELECT * FROM spylog WHERE id = ? ORDER BY age DESC" . $pages->limit());
		$db->execute(array(
			$user_class->id
		));
		$rows = $db->fetch_row();
		foreach($rows as $row){
			foreach($row as &$a)
				if($a == -1)
					$a = 'Failed';
			echo'<tr>';
				echo'<td>' . howlongago($row['age']) . '</td>';
				echo'<td>' . formatName($row['spyid']) . '</td>';
				echo'<td>' . prettynum($row['strength']) . '</td>';
				echo'<td>' . prettynum($row['defense']) . '</td>';
				echo'<td>' . prettynum($row['speed']) . '</td>';
				echo'<td>' . prettynum($row['agility']) . '</td>';
				echo'<td>' . prettynum($row['bank'], 1) . '</td>';
				echo'<td>' . prettynum($row['points']) . '</td>';
			echo'</tr>';
		}
		echo'</table>';
		echo $pages->displayPages();

		?>
	</div></div>
		<?php
include 'footer.php';
?>


