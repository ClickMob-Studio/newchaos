<?php
include 'header.php';
if ($user_class->level < 10 && $user_class->prestige < 1)
	diefun("You must be level 10 or higher to use the points market.");
if ($_POST['buy']) {
	$id = security($_POST['buy']);
	$qty = security($_POST['qty']);
	if ($qty <= 0)
		diefun("You entered an invalid quantity.");
	$db->query("SELECT * FROM pointsmarket WHERE id = ?");
	$db->execute(array(
		$id
	));
	$row = $db->fetch_row(true);
	if (empty($row))
		diefun("Market listing does not exist.");
	if ($qty > $row['amount'])
		diefun("You are trying to buy more points than there are listed.");
	$cost = $row['price'] * $qty;
	if ($cost > $user_class->money)
		diefun("You do not have enough money to buy these points.");
	if ($user_class->id == $row['owner'])
		diefun("You cannot buy your own points.");
	$db->query("SELECT rmdays FROM grpgusers WHERE id = ?");
	$db->execute(array(
		$row['owner']
	));
	$rmdays = $db->fetch_single();
	$whichpayment = ($rmdays) ? 'bank' : 'money';
	$db->query("UPDATE grpgusers SET points = points + ?, money = money - ? WHERE id = ?");
	$db->execute(array(
		$qty,
		$cost,
		$user_class->id
	));
	$db->query("UPDATE grpgusers SET $whichpayment = $whichpayment + ? WHERE id = ?");
	$db->execute(array(
		$cost,
		$row['owner']
	));

	$db->query("INSERT INTO buyptmarketlog (owner, buyer, amount, price, timestamp) VALUES (?, ?, ?, ?, ?)");
	$db->execute(array(
		$row['owner'],
		$user_class->id,
		$qty,
		$cost,
		time()
	));

	if ($qty >= $row['amount']) {
		$db->query("DELETE FROM pointsmarket WHERE id = ?");
		$db->execute(array(
			$id
		));
	} else {
		$db->query("UPDATE pointsmarket SET amount = amount - ? WHERE id = ?");
		$db->execute(array(
			$qty,
			$id
		));
	}
	Send_Event($row['owner'], "[-_USERID_-] bought " . prettynum($qty) . " points from you for " . prettynum($cost, 1) . ".", $user_class->id);
	echo Message("You have purchased " . prettynum($qty) . " points for " . prettynum($cost, 1) . ".");
	$user_class->points += $qty;
	$user_class->money -= $cost;
} elseif ($_POST['remove']) {
	$id = security($_POST['remove']);
	$qty = security($_POST['qty']);
	$db->query("SELECT * FROM pointsmarket WHERE id = ?");
	$db->execute(array(
		$id
	));
	$row = $db->fetch_row(true);
	if (empty($row))
		diefun("Market listing does not exist.");
	if ($row['owner'] != $user_class->id)
		diefun("You are not the owner of these points.");
	if ($qty > $row['amount'])
		diefun("You are trying to remove more points than you have on the market.");
	if ($qty >= $row['amount']) {
		$db->query("DELETE FROM pointsmarket WHERE id = ?");
		$db->execute(array(
			$id
		));

		$db->query("INSERT INTO removeptmarketlog (owner, amount, price, timestamp) VALUES (?, ?, ?, ?)");
		$db->execute(array(
			$user_class->id,
			$qty,
			$row['price'],
			time()
		));

	} else {
		$db->query("UPDATE pointsmarket SET amount = amount - ? WHERE id = ?");
		$db->execute(array(
			$qty,
			$id
		));

		$db->query("INSERT INTO removeptmarketlog (owner, amount, price, timestamp) VALUES (?, ?, ?, ?)");
		$db->execute(array(
			$user_class->id,
			$qty,
			$row['price'],
			time()
		));
	}

	$db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
	$db->execute(array(
		$qty,
		$user_class->id
	));
	$user_class->points += $qty;
} elseif ($_POST['add']) {
	$qty = security($_POST['qty']);
	$price = security($_POST['price']);
	if ($qty <= 0)
		diefun("Error :: invalid amount of points added.");
	if ($price > 50000 || $price <= 0)
		diefun("Error :: invalid price entered.");
	if ($qty > $user_class->points)
		diefun("Error :: You're tring to add more points than you own.");
	$db->query("UPDATE grpgusers SET points = points - ? WHERE id = ?");
	$db->execute(array(
		$qty,
		$user_class->id
	));
	$db->query("INSERT INTO pointsmarket (owner, amount, price, `type`) VALUES (?, ?, ?)");
	$db->execute(array(
		$user_class->id,
		$qty,
		$price,
		2
	));
	$db->query("INSERT INTO addptmarketlog (owner, amount, price, timestamp) VALUES (?, ?, ?, ?)");
	$db->execute(array(
		$user_class->id,
		$qty,
		$price,
		time()
	));
	echo Message("You have added " . prettynum($qty) . " points to the market for " . prettynum($price, 1) . " each.");
	Send_Event2($user_class->id, "Has Added <font color=red><b>" . prettynum($qty) . "</b></font> Points To The Market for " . prettynum($price, 1) . " Per Point.", $user_class->id);
	$user_class->points -= $qty;
}
echo '<h3>Add Points to the Market</h3>';
echo '<hr>';

echo '<div class="floaty" style="width:99%; text-align:center">
		<h3>Caution<br>As a Non-Respected player any sales money will go into hand - Be mindful of those muggers! <br> <a href="rmstore.php">Donate</a> to become a Respected Member</h3>
	</div>';

echo '<div class="floaty" style="width:50%;">';
echo '<form method="post">';
echo '<table style="margin:auto;text-align:center;">';
echo '<tr>';
echo '<td>Points: </td>';
echo '<td><input type="text" name="qty" size="5" value="0" /></td>';
echo '<td></td>';
echo '</tr>';
echo '<tr>';
echo '<td>Price: </td>';
echo '<td><input type="text" size="5" name="price" value="0" /></td>';
echo '<td>(Max: $50,000)</td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan="3"><input type="submit" name="add" value="Add Points to Market" /></td>';
echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';
echo '<h3>Point Market</h3>';
echo '<hr>';
echo '<div class="floaty" style="width:85%;">';
echo '<table id="newtables" style="margin:auto;width:100%;">';
echo '<tr>';
echo '<th>Seller</th>';
echo '<th>Points</th>';
echo '<th>Cost Each</th>';
echo '<th>Cost Total</th>';
echo '<th>Buy</th>';
echo '</tr>';
$db->query("SELECT * FROM pointsmarket WHERE `type` = 2 ORDER BY price ASC");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
	echo '<tr>';
	echo '<td>' . formatName($row['owner']) . '</td>';
	echo '<td>' . prettynum($row['amount']) . '</td>';
	echo '<td>' . prettynum($row['price'], 1) . '</td>';
	echo '<td>' . prettynum($row['price'] * $row['amount'], 1) . '</td>';
	echo '<td>';
	echo '<form method="post">';
	if ($row['owner'] == $user_class->id) {
		echo '<input type="text" size="5" name="qty" value="' . $row['amount'] . '" />';
		echo '<input type="hidden" name="remove" value="' . $row['id'] . '" />';
		echo '<input type="submit" value="Remove" />';
	} else {
		echo '<input type="text" size="5" name="qty" value="' . min(floor($user_class->money / $row['price']), $row['amount']) . '" />';
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