<?php
include 'header.php';
?>
	
	<div class='box_top'>Item Market t</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
if ($_POST['buy']) {
	$id = security($_POST['id']);
	$qty = security($_POST['qty']);
	if($qty <= 0)
		diefun("Invalid quantity entered.");
	$db->query("SELECT im.*, itemname FROM itemmarket im JOIN items i on i.id = im.itemid WHERE im.id = ?");
	$db->execute(array(
		$id
	));
	$row = $db->fetch_row(true);

	if($qty > $row['qty'])
		diefun("Not enough items on the market.");
    $price = $row['cost'] * $qty;
    if ($row['userid'] == $user_class->id) {
        Give_Item($row['itemid'], $user_class->id, $qty);
		$db->query("UPDATE itemmarket SET qty = qty - ? WHERE id = ?");
		$db->execute(array(
			$qty,
			$id
		));
		$db->query("DELETE FROM itemmarket WHERE qty <= 0");
		$db->execute();
		diefun("You have taken <span style='color:red;'>[x$qty]</span> " . $row['itemname'] . " off the market.");
    }
    if ($row['itemid'] == 271 || $row['itemid'] == 272 || $row['itemid'] == 278) {
        if (Check_Item($row['itemid'], $user_class->id) > 5) {
            diefun('You already have the maximum amount for this item in your inventory.');
        }
    }
	if ($price > $user_class->{$row['currency']}) {
        echo Message("You don't have enough {$row['currency']}.");
    } else {
		if($row['currency'] == 'money'){
			$var1 = 'money';
			$var2 = 'bank';
		} else
			$var1 = $var2 = 'points';
		$db->query("UPDATE grpgusers SET $var1 = $var1 - ? WHERE id = ?");
		$db->execute(array(
			$price,
			$user_class->id
		));
		$db->query("UPDATE grpgusers SET $var2 = $var2 + ? WHERE id = ?");
		$db->execute(array(
			$price,
			$row['userid']
		));
		$user_class->{$row['currency']} -= $price;
		$db->query("UPDATE itemmarket SET qty = qty - $qty WHERE id = $id");
		$db->execute();
		$db->query("DELETE FROM itemmarket WHERE qty <= 0");
		$db->execute();
        Give_Item($row['itemid'], $user_class->id, $qty);
		$p = ($row['currency'] == 'money') ? prettynum($price, 1) : prettynum($price) . ' points';
        Send_Event($row['userid'], "[-_USERID_-] has bought your " . $row['itemname'] . " for $p.", $user_class->id);
        echo Message("You have bought a " . $row['itemname'] . "for $p.");
    }
}

echo'<div class="floaty">';
	
	echo'<table id="newtables" style="width:100%;">';
		echo'<tr>';
			echo'<th>Seller</th>';
			echo'<th>Item</th>';
			echo'<th>Price</th>';
			echo'<th>Buy</th>';
		echo'</tr>';
		$db->query("SELECT im.*, itemname FROM itemmarket im JOIN items i on i.id = im.itemid ORDER BY cost ASC");
		$db->execute();
		$rows = $db->fetch_row();
		foreach($rows as $row){
			$submittext = ($row['userid'] == $user_class->id) ? "Remove" : "Buy";
			echo'<tr>';
				echo'<td>' . formatName($row['userid']) . '</td>';
				echo'<td>' . $row['itemname'] . ' <span style="color:red;">[x' . $row['qty'] . ']</span></td>';
				echo'<td>' , ($row['currency'] == 'money') ? prettynum($row['cost'], 1) : prettynum($row['cost']) . ' points' , '</td>';
				echo'<td>';
					echo'<form method="post">';
						echo'<input type="hidden" name="id" value="' . $row['id'] . '">';
						echo'<input type="text" size="5" name="qty" value="' . min(floor(($row['currency'] == 'money' ? $user_class->money : $user_class->points) / $row['cost']), $row['qty']) . '"> ';
						echo'<input type="submit" name="buy" value="' . $submittext . '">';
					echo'</form>';
				echo'</td>';
			echo'</tr>';
		}
    echo'</table>';
echo'</div>';
include 'footer.php';
?>