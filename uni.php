<?php
include 'header.php';
?>

<div class='box_top'>University</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		if (CheckCourse($user_class->id) == 0) {
			if (isset($_GET['start'])) {
				$start = security($_GET['start']);
				$db->query("SELECT * FROM courses WHERE id = ?");
				$db->execute(array(
					$start
				));
				$core = $db->fetch_row(true);
				if (!empty($core)) {
					if ($user_class->gcses < $core['needed'])
						diefun("You don't have the required diplomas for this course.");
					elseif ($user_class->money < $core['cost'])
						diefun("You don't have enough money to take this course.");
					else {
						$finish = time() + $core['duration'] * 86400;
						$user_class->money -= $core['cost'];
						$db->query("INSERT INTO uni (playerid, courseid, finish) VALUES (?, ?, ?)");
						$db->execute(array(
							$user_class->id,
							$start,
							$finish
						));
						$db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
						$db->execute(array(
							$user_class->money,
							$user_class->id
						));
						echo Message("You have successfully started a course. It will finish in {$core['duration']} days.");
					}
				} else
					echo Message("Thats not a real course.");
			}
			echo '<div class="contenthead floaty">';

			echo '&bull; Welcome to the CC College! <br />';
			echo '&bull;Here you can choose courses to take over a period of time to gain Strength, Speed, Defense and Diplomas. <br />';
			echo '&bull;Each course will require a certain number of diplomas and will cost some money.<br />';
			echo '<br />';
			echo '&bull;You currently have <b><font color=red>' . $user_class->gcse . '</font></b> Diploma(s).</b>';
			echo '<hr style="border:0;border-bottom:thin solid #333;" />';
			echo '<table id="newtables" class="altcolors" style="width:100%;">';
			echo '<tr>';
			echo '<th>Course</th>';
			echo '<th>Course Duration(Days)</th>';
			echo '<th>Dip(s) Needed</th>';
			echo '<th>Strength</th>';
			echo '<th>Defense</th>';
			echo '<th>Speed</th>';
			echo '<th>Cost</th>';
			echo '<th>Start</th>';
			echo '</tr>';
			$db->query("SELECT * FROM courses ORDER BY id ASC");
			$db->execute();
			$rows = $db->fetch_row();
			foreach ($rows as $row) {
				echo '<tr>';
				echo '<td>' . $row['name'] . '</td>';
				echo '<td>' . $row['duration'] . '</td>';
				echo '<td>' . $row['needed'] . '</td>';
				echo '<td>' . number_format($row['strength'], 0) . '</td>';
				echo '<td>' . number_format($row['defense'], 0) . '</td>';
				echo '<td>' . number_format($row['speed'], 0) . '</td>';
				echo '<td>$' . number_format($row['cost'], 0) . '</td>';
				echo '<td><a href="uni.php?start=' . $row['id'] . '"><button>Start</button></a></td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</div>';
		} else {
			$db->query("SELECT * FROM uni WHERE playerid = ?");
			$db->execute(array(
				$user_class->id
			));
			$uni = $db->fetch_row(true);
			$db->query("SELECT * FROM courses WHERE id = ?");
			$db->execute(array(
				$uni['courseid']
			));
			$core = $db->fetch_row(true);
			echo '<div class="floaty">';
			echo '<h4>University</h4>';
			echo '<hr style="border:0;border-bottom:thin solid #333;" />';
			echo 'Welcome to the The Ultimate University! ';
			echo 'Here you can choose courses to take over a period of time to gain Strength, Speed, Defense and Diplomas. ';
			echo 'Each course will require a certain number of diplomas and will cost some money.<br />';
			echo '<br />';
			echo '<b>You are currently taking a course in the university.</b><br />';
			echo '<hr style="border:0;border-bottom:thin solid #333;" />';
			echo '<table id="newtables" class="altcolors" style="width:90%;margin:auto;">';
			echo '<tr>';
			echo '<td colspan="2"><b><a href="completeuni.php">Please click here to complete your course if you have finished</a>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><b>Course:</th>';
			echo '<td>' . $core['name'] . '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><b>Time Left:</th>';
			echo '<td>' . howlongtil($uni['finish']) . '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';
		}
		include 'footer.php';
		?>