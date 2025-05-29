<?php
include 'header.php';

if ($user_class->rmdays < 1) {
	echo Message("This feature is for Respected Mobsters only. You can purchase RM days at the <a href='rmstore.php'>Upgrade Store</a>.<br /><br /><a href='city.php'>Back to City</a>");
	include("footer.php");
	die();
}

if ($_GET['sell'] != "") {
	$gain = $user_class->marijuana * 100;

	$error = ($user_class->rmdays == 0) ? "You must be a Respected Mobster to sell drugs." : $error;
	$error = ($gain == 0) ? "You don't have any." : $error;
	if (isset($error)) {
		echo Message($error);
		include 'footer.php';
		die();
	}
	$newmoney = $user_class->money + $gain;

	perform_query("UPDATE `grpgusers` SET `money` = ?, `marijuana` = 0 WHERE `id` = ?", [$newmoney, $user_class->id]);
	echo Message("You sold all your weed and got $" . prettynum($gain) . ".");
}

if ($_GET['buy'] != "") {
	$cost = 0;

	$cost = ($_GET['buy'] == "potseeds") ? 5000 : $cost;

	$error = ($user_class->rmdays == 0) ? "You must be a Respected Mobster to buy drugs." : $error;
	$error = ($user_class->money < $cost) ? "You do not have enough money!" : $error;
	$error = ($cost == 0) ? "You didn't pick a real drug." : $error;
	if (isset($error)) {
		echo Message($error);
		include 'footer.php';
		die();
	}
	$newmoney = $user_class->money - $cost;
	perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id`= ?", [$newmoney, $user_class->id]);
	echo Message("You have purchased 100 marijuana seeds.");

	if ($_GET['buy'] == "potseeds") {
		$newamount = $user_class->potseeds + 100;
		perform_query("UPDATE `grpgusers` SET `potseeds` = ? WHERE `id`= ?", [$newamount, $user_class->id]);
	}
}

?>

<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead">Drug Dealer</td>
</tr>

<tr>
	<td class="contentcontent">

		<?php echo ($user_class->rmdays > 0) ? "Hey there buddy. How about getting into the drug dealing business? For $5,000 I will give you 100 marijuana seeds. That's enough to plant an acre of sweet sticky weed. I will also buy your weed at $100 per ounce! That means you could make a $5,000 profit!<br><br><center><a href='buydrugs.php?buy=potseeds'>Buy Marijuana Seeds</a> | <a href='buydrugs.php?sell=pot'>Sell all Weed</a> | <a href='city.php'>No thanks!</a></center>" : "Hmm... How do I know you won't squeal? You aren't respected enough to buy from me. Come back when you are a respected mobster.<br><br><a href='city.php'>Go Back</a>"; ?>

	</td>
</tr>

<?php

include 'footer.php';

?>