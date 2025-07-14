<?php
include 'header.php';
?>

<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead">Slot Machine</td>
</tr>
<tr>
	<td class="contentcontent">
		<?php

		if (isset($_POST['play'])) {


			$money = $_POST["money"];
			if ($user_class->money < $money) {
				echo Message("You can't bet more than you have.<br><br>
<a href='slot_machine.php'>Back</a>");
				include('nlifooter.php');
				die();
			}
			if ($money < 1) {
				echo Message("You can't bet nothing.<br><br>
<a href='slot_machine.php'>Back</a>");
				include('nlifooter.php');
				die();
			}

			$newmoney = $user_class->money - 100;
			perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id` = ?", [$newmoney, $user_class->id]);
			$user_class = new User($_SESSION['id']);

			$slot[1] = "<font style='font-family:courier new;font-size:18px;'>1</font>";
			$slot[2] = "<font style='font-family:courier new;font-size:18px;'>2</font>";
			$slot[3] = "<font style='font-family:courier new;font-size:18px;'>3</font>";

			$slot1 = rand(1, 3);
			$slot2 = rand(1, 3);
			$slot3 = rand(1, 3);

			echo 'You spun:<br><br>';
			echo $slot[$slot1];
			echo $slot[$slot2];
			echo $slot[$slot3];
			echo "</td></tr>";

			if ($slot1 == $slot2 && $slot2 == $slot3) {
				$newmoney = $user_class->money + 1000;
				perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id` = ?", [$newmoney, $user_class->id]);
				$user_class = new User($_SESSION['id']);
				echo Message("Congratulations, you have won $1,000!");
			} else {
				echo Message("You didn't win anything, sorry.");
			}
		}

		$slotmoney = 145 * $user_class->level;

		if ($slotmoney > 2500) {
			$slotmoney = 2500;
		}

		?>

<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead">Slot Machine</td>
</tr>
<tr>
	<td class="contentcontent">
		<form method='post'>
			To play, enter the amount of turns you wish to use, and the money you wish to bet per turn, then press
			spin.<br><br>
			You have <?php echo $user_class->slots; ?> slot machine turns left.<br><br>
			Use
			<input type='text' name='turns' value=''> Turns<br><br>

			$<input type='text' name='money' value='<?php echo $slotmoney; ?>'> [max:
			$<?php echo $slotmoney; ?>]<br><br>
			<input type='submit' name='play' value='Spin'>
		</form>
	</td>
</tr>
<?php
include 'footer.php';
?>