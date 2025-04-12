<?php
include 'header.php';
if ($user_class->level < 10)
	diefun("You must be level 10 or higher to use the credit market.");
if ($_POST['buy']) {
	$id = security($_POST['buy']);
	$qty = security($_POST['qty']);
	if ($qty <= 0)
		diefun("You entered an invalid quantity.");
	$db->query("SELECT * FROM creditsmarket WHERE id = ?");
	$db->execute(array(
		$id
	));
	$row = $db->fetch_row(true);
	if (empty($row))
		diefun("Market listing does not exist.");
	if ($qty > $row['amount'])
		diefun("You are trying to buy more credits than there are listed.");
	$cost = $row['price'] * $qty;
	if ($cost > $user_class->points)
		diefun("You do not have enough points to buy these credits.");
	if ($user_class->id == $row['owner'])
		diefun("You cannot buy your own credits.");
	$db->query("UPDATE grpgusers SET credits = credits + ?, points = points - ? WHERE id = ?");
	$db->execute(array(
		$qty,
		$cost,
		$user_class->id
	));
	$db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
	$db->execute(array(
		$cost,
		$row['owner']
	));
	if ($qty >= $row['amount']) {
		$db->query("DELETE FROM creditsmarket WHERE id = ?");
		$db->execute(array(
			$id
		));
	} else {
		$db->query("UPDATE creditsmarket SET amount = amount - ? WHERE id = ?");
		$db->execute(array(
			$qty,
			$id
		));
	}
	Send_Event($row['owner'], "[-_USERID_-] bought " . prettynum($qty) . " credits from you for " . prettynum($cost) . " points.", $user_class->id);
	echo Message("You have purchased " . prettynum($qty) . " credits for " . prettynum($cost) . " points.");
	$user_class->credits += $qty;
	$user_class->points -= $cost;
} elseif ($_POST['remove']) {
	$id = security($_POST['remove']);
	$qty = security($_POST['qty']);
	$db->query("SELECT * FROM creditsmarket WHERE id = ?");
	$db->execute(array(
		$id
	));
	$row = $db->fetch_row(true);
	if (empty($row))
		diefun("Market listing does not exist.");
	if ($row['owner'] != $user_class->id)
		diefun("You are not the owner of these credits.");
	if ($qty > $row['amount'])
		diefun("You are trying to remove more credits than you have on the market.");
	if ($qty >= $row['amount']) {
		$db->query("DELETE FROM creditsmarket WHERE id = ?");
		$db->execute(array(
			$id
		));
	} else {
		$db->query("UPDATE creditsmarket SET amount = amount - ? WHERE id = ?");
		$db->execute(array(
			$qty,
			$id
		));
	}
	$db->query("UPDATE grpgusers SET credits = credits + ? WHERE id = ?");
	$db->execute(array(
		$qty,
		$user_class->id
	));
	$user_class->credits += $qty;
} elseif ($_POST['add']) {
	$qty = security($_POST['qty']);
	$price = security($_POST['price']);
	if ($qty > 10000 || $qty <= 0)
		diefun("Error :: invalid amount of credits added.");
	if ($price > 50000)
		diefun("Error :: invalid price entered.");
	if ($qty > $user_class->credits)
		diefun("Error :: You're tring to add more credits than you own.");
	$db->query("UPDATE grpgusers SET credits = credits - ? WHERE id = ?");
	$db->execute(array(
		$qty,
		$user_class->id
	));
	$db->query("INSERT INTO creditsmarket (owner, amount, price) VALUES (?, ?, ?)");
	$db->execute(array(
		$user_class->id,
		$qty,
		$price
	));
	echo Message("You have added " . prettynum($qty) . " credits to the market for " . prettynum($price) . " points each.");
	Send_Event2($user_class->id, "Has Added <font color=red><b>" . prettynum($qty) . "</b></font> credits To The Market for " . prettynum($price) . " points each.", $user_class->id);
	$user_class->credits -= $qty;
}
echo '<h3>Add Credits to the Market</h3>';
echo '<hr>';
echo '<div class="floaty" style="width:50%;">';
echo '<form method="post">';
echo '<table style="margin:auto;text-align:center;">';
echo '<tr>';
echo '<td>Credits: </td>';
echo '<td><input type="text" name="qty" size="5" value="' . min($user_class->credits, 100) . '" /></td>';
echo '<td>(Max: 10,000)</td>';
echo '</tr>';
echo '<tr>';
echo '<td>Price: </td>';
echo '<td><input type="text" size="5" name="price" value="0" /></td>';
echo '<td>(Points) (Max: 50,000)</td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan="3"><input type="submit" name="add" value="Add Credits to Market" /></td>';
echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';
echo '<h3>Credit Market</h3>';
echo '<hr>';
echo '<div class="floaty" style="width:85%;">';
echo '<table id="newtables" style="margin:auto;width:100%;">';
echo '<tr>';
echo '<th>Seller</th>';
echo '<th>Credits</th>';
echo '<th>Cost ea.</th>';
echo '<th>Cost tot.</th>';
echo '<th>Buy</th>';
echo '</tr>';
$db->query("SELECT * FROM creditsmarket WHERE `type` = 2 ORDER BY price ASC");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
	echo '<tr>';
	echo '<td>' . formatName($row['owner']) . '</td>';
	echo '<td>' . prettynum($row['amount']) . '</td>';
	echo '<td>' . prettynum($row['price']) . ' Points</td>';
	echo '<td>' . prettynum($row['price'] * $row['amount']) . ' Points</td>';
	echo '<td>';
	echo '<form method="post">';
	if ($row['owner'] == $user_class->id) {
		echo '<input type="text" size="5" name="qty" value="' . $row['amount'] . '" />';
		echo '<input type="hidden" name="remove" value="' . $row['id'] . '" />';
		echo '<input type="submit" value="Remove" />';
	} else {
		echo '<input type="text" size="5" name="qty" value="' . min(floor($user_class->points / $row['price']), $row['amount']) . '" />';
		echo '<input type="hidden" name="buy" value="' . $row['id'] . '" />';
		echo '<input type="submit" value="Buy" />';
	}
	echo '</form>';
	echo '</td>';
	echo '</tr>';
}
echo '</table>';
echo '</div>';
include 'footer.php';
?>