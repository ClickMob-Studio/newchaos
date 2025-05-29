<?php
include 'header.php';
$result = mysql_query("SELECT * FROM `cars` WHERE `userid`='" . $user_class->id . "'");
$howmany = mysql_num_rows($result);
if ($howmany == 0) {
	echo Message("You don't have a car. You can't drive without a car.");
	include 'footer.php';
	die();
}


if ($_GET['go'] != "") {
	$result = mysql_query("SELECT * FROM `cars` WHERE `userid`='" . $user_class->id . "'");
	$howmany = mysql_num_rows($result);

	$error = ($howmany == 0) ? "You don't have a car. You can't drive somewhere without a car." : $error;
	$error = ($user_class->jail > 0) ? "You can't drive somewhere if you are in jail." : $error;
	$error = ($_GET['go'] == $user_class->city) ? "You are already there." : $error;

	$result = mysql_query("SELECT * FROM `cities` WHERE `id`='" . $_GET['go'] . "'");
	$worked = mysql_fetch_array($result);

	$cost = round($worked['price'] / 3, -2);

	$error = ($worked['name'] == "") ? "That city doesn't exist." : $error;
	$error = ($_GET['go'] == 1 && $user_class->admin == 1) ? "That city doesn't exist." : $error;
	$error = ($user_class->level < $worked['levelreq']) ? "You are not a high enough level to go there." : $error;
	$error = ($user_class->money < $cost) ? "You dont have enough money to go there." : $error;
	$error = ($user_class->country != $worked['country']) ? "That city isn't in this country." : $error;
	$error = ($worked['rmonly'] == 1 && $user_class->rmdays < 1) ? "You need to be a Respected Mobster to go there." : $error;


	if (!isset($error)) {
		$cost = round($worked['price'] / 3, -2);
		$newmoney = $user_class->money - $cost;
		perform_query("UPDATE `grpgusers` SET `city` = ?, `money` = ? WHERE `id` = ?", [$_GET['go'], $newmoney, $user_class->id]);
		echo Message("You successfully paid " . prettynum($cost, 1) . " for gas and drove to your destination.");
	} else {
		echo Message($error);
	}

}
?>

<style type="text/css">
	.rm {
		color: #FFCC00;
		font-size: 8px;
		vertical-align: 3px;
		font-weight: 600;
	}
</style>

<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead">Drive</td>
</tr>
<tr>
	<td class="contentcontent">
		<center>
			Tired of <?php echo $user_class->cityname ?>? Well drive somewhere else in your car.<br /><br />
			[<a href="airport.php">Airport</a>]&nbsp;&nbsp;[<a href="bus.php">Bus Station</a>]&nbsp;&nbsp;[<a
				href="fly.php">Fly</a>]
		</center>
		<br />
		<table width="100%">
			<tr>
				<td><b>Name</b></td>
				<td><b>Cost</b></td>
				<td><b>Level</b></td>
				<td><b>Population</b></td>
			</tr>
			<?php
			if ($user_class->admin == 1) {
				$result = mysql_query("SELECT * FROM `cities` WHERE `country` = '" . $user_class->country . "' ORDER BY `levelreq` ASC");
				while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
					$population1 = mysql_query("SELECT * FROM `grpgusers` WHERE `city` = '" . $line['id'] . "'");
					$population = mysql_num_rows($population1);
					$cost = round($line['price'] / 3, -2);
					if ($line['rmonly'] == 1) {
						echo "<tr><td><a href='drive.php?go=" . $line['id'] . "'>" . $line['name'] . "</a>&nbsp;<span class='rm'><a href='rmstore.php'>RM ONLY</a></span></td><td>" . prettynum($cost, 1) . "</td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
					} else {
						echo "<tr><td><a href='drive.php?go=" . $line['id'] . "'>" . $line['name'] . "</a></td><td>" . prettynum($cost, 1) . "</td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
					}
				}
			} else {
				$result = mysql_query("SELECT * FROM `cities` WHERE `country` = '" . $user_class->country . "' ORDER BY `levelreq` ASC");
				while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
					$population1 = mysql_query("SELECT * FROM `grpgusers` WHERE `city` = '" . $line['id'] . "'");
					$population = mysql_num_rows($population1);
					$cost = round($line['price'] / 3, -2);
					if ($line['id'] != 1) {
						if ($line['rmonly'] == 1) {
							echo "<tr><td><a href='drive.php?go=" . $line['id'] . "'>" . $line['name'] . "</a>&nbsp;<span class='rm'><a href='rmstore.php'>RM ONLY</a></span></td><td>" . prettynum($cost, 1) . "</td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
						} else {
							echo "<tr><td><a href='drive.php?go=" . $line['id'] . "'>" . $line['name'] . "</a></td><td>" . prettynum($cost, 1) . "</td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
						}
					}
				}
			}
			?>
		</table>
		<?php
		include 'footer.php';
		?>