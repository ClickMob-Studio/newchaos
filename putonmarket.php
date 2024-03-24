<?php
include 'header.php';
?>
<div class='box_top'>Put On Market</div>
						<div class='box_middle'>
							<div class='pad'>

<?php
$id = security($_GET['id']);
$howmany = Check_Item($id, $user_class->id);
$db->query("SELECT * FROM items WHERE id = ?");
$db->execute(array(
	$id
));
$row = $db->fetch_row(true);
if (empty($row['itemname']))
	diefun("That isn't a real item.<br /><br /><a href='inventory.php'>Back To Inventory</a>");
if (isset($_POST['market'])){
	$price = security($_POST['price']);
	$qty = security($_POST['qty']);
	$cur = ($_POST['currency'] == 'money') ? 'money' : 'points';
	if($price < 1)
		diefun("Please enter a valid amount of money.");
	if($qty > $howmany)
		diefun("You don't have enough of those.");
	$db->query("INSERT INTO itemmarket (itemid, userid, cost, qty, currency) VALUES (?, ?, ?, ?, ?)");
	$db->execute(array(
		$id,
		$user_class->id,
		$price, 
		$qty,
		$cur
	));
	Take_Item($id, $user_class->id, $qty);
	header("Location: itemmarket.php");
	die();
}
genHead("Item Market");
		echo'<form method="post">';
			echo'<table id="newtables" style="margin:auto;width:50%">';
				echo'<tr>';
					echo'<th colspan="2">You are marketing a ' . $row['itemname'] . '. <span style="color:red;">[' . $howmany . ']</span></th>';
				echo'</tr>';
				echo'<tr>';
					echo'<td width="35%" height="27">Price:&nbsp;</td>';
					echo'<td width="65%"><input name="price" type="text" size="10" value="0"></td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td width="35%" height="27">Currency:&nbsp;</td>';
					echo'<td width="65%">';
						echo'<select name="currency">';
							echo'<option value="money">Money</option>';
							echo'<option value="points">Points</option>';
						echo'</select>';
					echo'</td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td width="35%" height="27">Quantity:&nbsp;</td>';
					echo'<td width="65%"><input name="qty" type="text" size="10" value="0"></td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td colspan="2"><input type="submit" name="market" value="Add to Item Market"></td>';
				echo'</tr>';
			echo'</table>';
		echo'</form>';
	echo'</td>';
echo'</tr>';
include 'footer.php';
?>