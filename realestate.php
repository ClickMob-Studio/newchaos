<?php
include 'header.php';

if ($user_class->rmdays < 1) {
	echo Message("This feature is for Respected Mobsters only. You can purchase RM days at the <a href='rmstore.php'>Upgrade Store</a>.<br /><br /><a href='city.php'>Back to City</a>");
	include("footer.php");
	die();
}

$result = mysql_query("SELECT * FROM `cities` WHERE `id`='" . $user_class->city . "'");
$worked = mysql_fetch_array($result);

if ($_POST['buyland']) {

	//Validate Money
	$notallowed = array('$', '-', '_', '+', '=', '<', '>');
	$_POST['amount'] = str_replace($notallowed, "", $_POST['amount']);
	//End

	$price = $worked['landprice'];
	$amount = $worked['landleft'];
	$totalcost = $price * $_POST['amount'];
	$newlandtotal = $amount - $_POST['amount'];
	$currentland = Check_Land($user_class->city, $user_class->id);

	$error = ($_POST['amount'] > $amount) ? "There is not that much land available.<br /><br /><a href='realestate.php'>Go Back</a>" : $error;
	$error = ($_POST['amount'] < 1) ? "Please enter a valid amount of land.<br /><br /><a href='realestate.php'>Go Back</a>" : $error;
	$error = ($totalcost > $user_class->money) ? "You don't have enough money.<br /><br /><a href='realestate.php'>Go Back</a>" : $error;

	if (isset($error)) {
		echo Message($error);
		include 'footer.php';
		die();
	}

	echo Message("You have bought " . prettynum($_POST['amount']) . " acres of land in " . $user_class->cityname . " for $" . prettynum($totalcost));

	Give_Land($user_class->city, $user_class->id, $_POST['amount']);

	$newmoney = $user_class->money - $totalcost;
	perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id` = ?", [$newmoney, $user_class->id]);
	$user_class = new User($_SESSION['id']);

	perform_query("UPDATE `cities` SET `landleft` = ? WHERE `id` = ?", [$newlandtotal, $user_class->city]);
}

$result = mysql_query("SELECT * FROM `cities` WHERE `id`='" . $user_class->city . "'");
$worked = mysql_fetch_array($result);
?>
<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead">Real Estate Agency</td>
</tr>
<tr>
	<td class="contentcontent">
		Welcome to the Real Estate Agency! If we have any land left available, you can purchase it from
		here.<br /><br />
		<b>Land available in <?php echo $user_class->cityname ?>:</b>&nbsp;<?php echo prettynum($worked['landleft']) ?>
		acres.<br />
		<b>Price per acre:</b>&nbsp;$<?php echo prettynum($worked['landprice']) ?>.
	</td>
</tr>
<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead">Buy Land</td>
</tr>
<tr>
	<td class="contentcontent">
		<?php
		if ($worked['landleft'] != 0) {
			echo "<table width='50%'><form method='post'><tr><td width='15%'><b>Land:</b>&nbsp;</td><td width='35%'><input type='text' name='amount' size='5' maxlength='20' value='" . $worked['landleft'] . "'> [acres]</td></tr><tr><td></td></tr><tr><td width='15%'>&nbsp;</td><td width='35%'><input type='submit' name='buyland' value='Buy Land'></td></tr></form></table>";
		} else {
			echo "There is currently no land available in " . $user_class->cityname . ".";
		}
		?>
	</td>
</tr>
<?php
include 'footer.php';
?>