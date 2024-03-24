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
		if($row['city'] != $user_class->city)
			diefun("You are not in the correct city to buy this item.");
        if ($user_class->money >= $cost) {
			$db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
			$db->execute(array(
				$cost,
				$user_class->id
			));
            Give_Item($item, $user_class->id, $qty);
            echo Message("You have purchased a {$row['itemname']} [x$qty].");
            $$user_class->money -= $cost;
        } else
            echo Message("You do not have enough money to buy a {$row['itemname']}.");
    } else
        echo Message("That isn't a real item.");
}
$db->query("SELECT * FROM items WHERE speed > 0 && buyable = 1 && city = ?");
$db->execute(array(
	$user_class->city
));
$rows = $db->fetch_row();
print '
<table id="newtables" style="width:100%;">
    <tr>
        <th colspan="4">Shoes Store</th>
    </tr>';
foreach(array_chunk($rows, 2) as $subrows){
	echo'<tr>';
	foreach($subrows as $row){
        echo'<td style="width:50%;">';
            echo'<div style="display:flex;">';
                echo'<div style="flex:auto;">';
                    echo item_popup($row['itemname'], $row['id']) . ' (+' . $row['speed'] . '%)<br>';
                    echo'<img src="' . $row['image'] . '" width="100" height="100" style="border: 0px solid #000000">';
                echo'</div>';
                echo'<div style="flex:auto;">';
                    echo'<br />';
                    echo prettynum($row['cost'], 1) . '<br>';
                    echo'<br>';
                    echo'<form method="post">';
                        echo'<input type="text" size="5" name="qty" value="1" /><br />';
                        echo'<input type="hidden" name="item" value="' . $row['id'] . '" />';
                        echo'<br />';
                        echo'<input type="submit" value="Buy" />';
                    echo'</form>';
                echo'</div>';
            echo'</div>';
        echo'</td>';
	}
	echo'</tr>';
}
print "</table>
</td></tr>";
include 'footer.php';
?>