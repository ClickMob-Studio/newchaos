<?php
include 'header.php';
$pts = array(
	array("first", 1000, 100),
	array("second", 10000, 700),
	array("third", 50000, 2800),
	array("forth", 200000, 9000),

);
if (isset($_GET['spend'])) {
	if (isset($pts) && is_array($pts)) {
		foreach ($pts as $pt)
			if ($_GET['spend'] == $pt[0])
				points($pt[1], $pt[2]);
	}

	if (isset($rys) && is_array($rys)) {
		foreach ($rys as $ry)
			if ($_GET['spend'] == $ry[0])
				points($ry[1], $ry[2]);
	}

	if (isset($money) && is_array($money)) {
		foreach ($money as $mo)
			if ($_GET['spend'] == $mo[0])
				money($mo[1], $mo[2]);
	}
}
genHead("<h4><center>Vote Shop - You currently have <span style='color:#3ab997;font-weight:bold;'>" . prettynum($user_class->votetokens) . "</span> Vote Tokens</center></h4>");
?>

<div class="contenthead floaty">
	<span
		style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
		<h4>Vote Shop</h4>
	</span>


	Welcome to the vote shop, Voting at Chaos City has never been more Vital! We appreciate your support!<br />
	Each vote helps improve our rank on each of these sites, You can accumulate Vote Tokens
	by voting and overtime can get your hands on some of these great packages!<br /><br />
	<table id="newtables">
		<tr>
			<th colspan="3">Point Packs</th>
		</tr>
		<?php
		foreach ($pts as $pt)
			print "
	<tr>
		<td>" . prettynum($pt[1]) . " Point Pack</td>
		<td>" . prettynum($pt[2]) . " Vote Tokens</td>
		<td><button class='ycbutton' style='padding:2px 10px;' onClick='actConfirm({$pt[1]},\"Points Pack\",{$pt[2]},\"voteshop.php?spend={$pt[0]}\");'>Buy</button></a></td>
	</tr>";
		?>
	</table>
	</center>
	</td>
	</tr>
	<?php
	include 'footer.php';
	function points($points, $votetokens)
	{
		global $user_class;
		if ($user_class->votetokens < $votetokens)
			echo Message("You do not have enough Vote Tokens to buy this package.");
		else {
			$user_class->points += $points;
			$user_class->votetokens -= $votetokens;
			perform_query("UPDATE grpgusers SET points = ?, votetokens = ? WHERE id = ?", [$user_class->points, $user_class->votetokens, $user_class->id]);
			echo Message("You have paid " . prettynum($votetokens) . " Tokens for " . prettynum($points) . " points.");
		}
	}
	function money($money, $votetokens)
	{
		global $user_class;
		if ($user_class->votetokens < $votetokens)
			echo Message("You do not have enough Vote Tokens to buy this package.");
		else {
			$user_class->money += $money;
			$user_class->votetokens -= $votetokens;
			perform_query("UPDATE grpgusers SET money = ?, votetokens = ? WHERE id = ?", [$user_class->money, $user_class->votetokens, $user_class->id]);
			echo Message("You have paid " . prettynum($votetokens) . " Tokens for $" . prettynum($money) . ".");
		}
	}
	function rydays($days, $votetokens)
	{
		global $user_class;
		if ($user_class->votetokens < $votetokens)
			echo Message("You do not have enough Vote Tokens to buy this package.");
		else {
			if ($user_class->rmdays < 1) {
				invalidateFormattedName($user_class->id);
			}

			$user_class->rmdays += $days;
			$user_class->votetokens -= $votetokens;
			perform_query("UPDATE grpgusers SET rmdays = ?, votetokens = ? WHERE id = ?", [$user_class->rmdays, $user_class->votetokens, $user_class->id]);
			echo Message("You have paid " . prettynum($votetokens) . " Tokens for " . prettynum($days) . " RY Day(s).");
		}
	}
	?>