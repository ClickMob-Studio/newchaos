<?php
include 'header.php';
if (isset($_POST['qty'])) {
    $qty = security($_POST['qty']);
	$item = security($_POST['item']);
	if($qty <= 0)
		diefun();
	$db->query("SELECT * FROM items WHERE id = ? AND buyable = 1");
	$db->execute(array(
		$item
	));
	$row = $db->fetch_row(true);
    if (!empty($row['id'])){
		$cost = $row['cost'] * $qty;
        if ($user_class->money >= $cost) {
			$db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
			$db->execute(array(
				$cost,
				$user_class->id
			));
            Give_Item($item, $user_class->id, $qty);
            diefun("You have purchased a {$row['itemname']} [x$qty].");
            $$user_class->money -= $cost;
        } else
            diefun("You do not have enough money to buy a {$row['itemname']}.");
    } else
        diefun("That isn't a real item.");
}
$db->query("SELECT * FROM items WHERE (heal > 0 || reduce > 0) && buyable = 1");
$db->execute();
$rows = $db->fetch_row();
echo '<div class="contenthead floaty">';
echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>General Pharmacy</h4></span>';
foreach(array_chunk($rows, 2) as $subrows){
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<div style="display:flex;">';
	foreach($subrows as $row){
		echo'<div style="flex:1;border-left:thin solid #333;">';
			echo item_popup($row['itemname'], $row['id']) . '<br>';
			echo'<img src="' . $row['image'] . '" width="100" height="100" style="border: 0px solid #000000">';
		echo'</div>';
		echo'<div style="flex:1;border-right:thin solid #333;">';
			echo'<br />';
			echo prettynum($row['cost'], 1) . '<br>';
			echo'<br>';
			echo'<form method="post">';
				echo'<input type="text" size="5" name="qty" value="' . floor($user_class->money / $row['cost']) . '" /><br />';
				echo'<input type="hidden" name="item" value="' . $row['id'] . '" />';
				echo'<br />';
				echo'<input type="submit" value="Buy" />';
			echo'</form>';
		echo'</div>';
	}
	echo'</div>';
}
echo'<hr style="border:0;border-bottom:thin solid #333;" />';
print "</div>
</td></tr>";
include 'footer.php';
?>