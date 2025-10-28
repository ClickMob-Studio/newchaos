<?php
include 'header.php';
?>

<div class='box_top'>Points Dealer</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		$pts = array(
			array(
				"first",
				100,
				2500000
			),
			array(
				"second",
				1000,
				23000000
			),
			array(
				"third",
				3000,
				62500000
			),
			array(
				"forth",
				10000,
				200000000
			)
		);
		if (isset($_GET['spend'])) {
			foreach ($pts as $pt)
				if ($_GET['spend'] == $pt[0])
					points($pt[1], $pt[2]);
		}
		echo '<div class="floaty">';
		echo '<span style="color:red;font-weight:bold;">Welcome to the Points Dealer, You can spend your money on points</span>';
		echo '<hr style="border:0;border-bottom:thin solid #333;" />';
		echo '<span style="color:red;font-size:10px;line-height:10px;">Its Cheaper to buy from Players</span>';
		echo '<table id="newtables">';
		echo '<tr>';
		echo '<th colspan="1">Points</th>';
		echo '<th colspan="2">Cost</th>';
		echo '</tr>';
		foreach ($pts as $pt) {
			echo '<tr>';
			echo '<td>' . prettynum($pt[1]) . ' Points</td>';
			echo '<td>' . prettynum($pt[2], 1) . '</td>';
			echo '<td>';
			echo '<button onClick="ptsConfirm(' . $pt[1] . ',\'Points Pack\',\'' . prettynum($pt[2]) . '\',\'?spend=' . $pt[0] . '\');">';
			echo 'Buy';
			echo '</button>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
		include 'footer.php';
		function points($points, $money)
		{
			global $db, $user_class;
			if ($user_class->money < $money)
				echo Message("You do not have enough money.");
			else {
				$user_class->points += $points;
				$user_class->money -= $money;
				$db->query("UPDATE grpgusers SET points = points + ?, money = money - ? WHERE id = ?");
				$db->execute(array(
					$points,
					$money,
					$user_class->id
				));
				echo Message("You have paid $" . prettynum($money) . " for " . prettynum($points) . " points.");
			}
		}
		?>