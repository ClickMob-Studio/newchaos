<?php
include 'header.php';
?>

<div class='box_top'>Store</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
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
$db->query("SELECT * FROM items WHERE (offense > 0 OR defense > 0 OR speed > 0 OR agility > 0) AND buyable = 1 AND city = ? ORDER BY offense DESC, defense DESC, speed DESC");
$db->execute(array(
	$user_class->city
));
$rows = $db->fetch_row();
$lasttype = "";
foreach($rows as $row){
	if($row['offense']){
		$thistype = 'Weapon';
		$which = 'offense';
		$stat = 'Strength';
	}elseif($row['defense']) {
        $thistype = 'Armor';
        $which = 'defense';
        $stat = 'Defense';
    }elseif($row['agility']){
        $thistype = 'Gloves';
        $which = 'agility';
        $stat = 'Agility';
	}else{
		$thistype = 'Shoe';
		$which = 'speed';
		$stat = 'Speed';
	}
	if($lasttype != $thistype && $lasttype != '')
		echo'</div>';
	if($lasttype != $thistype)
		echo'<div class="floaty"><h1>' . $thistype . ' Store</h1>';
	$lasttype = $thistype;
	echo'<hr style="border:0;border-bottom:thin solid #fff;" />';
	echo'<div style="display:flex;">';
		echo'<div style="flex:1;border-right:thin solid #333;">';
			echo item_popup($row['itemname'], $row['id']) . ' (+' . $row[$which] . '% ' . $stat . ')<br>';
			echo'<img src="' . $row['image'] . '" width="100" height="100" style="border: 0px solid #000000">';
		echo'</div>';
		echo'<div style="flex:1;">';
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

}

// Easter 2025 Store
if ($user_class->admin == 1) {
	$egg_name_by_id = array(
		336 => 'Common Easter Egg',
		337 => 'Rare Easter Egg',
		338 => 'Ulra Rare Easter Egg',
	);

	echo'<div class="floaty"><h1>Easter 2025</h1>';

	$db->query("SELECT * FROM easter_store ORDER BY egg_id ASC, quantity ASC");
	$db->execute();
	$rows = $db->fetch_row();
	foreach ($rows as $row) {
		$image = '';
		$item_name = '';
		if ($row['item_id'] != 0) {
			// Select the item from the database
			$db->query("SELECT * FROM items WHERE id = ?");
			$db->execute(array(
				$row['item_id']
			));
			$item_row = $db->fetch_row(true);
			$item_name = $item_row['itemname'];
			$image = '<img src="' . $item_row['image']. '" width="100" height="100" style="border: 0px solid #000000">';
		} else {
			$image = 'No image available';
		}

		if ($row['points' != 0]) {
			$item_name = $row['points'] . ' Points';
		} else if ($row['maze'] != 0) {
			$item_name = $row['maze'] . ' Maze searches';
		} else if ($row['achievement'] == 1) {
			$item_name = 'Easter 2025 Achievement';
		} else if ($row['achievement'] == 2) {
			$item_name = 'You had no life during Easter 2025 Achievement';
		}

		echo'<hr style="border:0;border-bottom:thin solid #fff;" />';
		echo'<div style="display:flex;">';
			echo'<div style="flex:1;border-right:thin solid #333;">';
			
			if ($row['item_id'] != 0) {
				echo item_popup($item_name, $row['item_id']) . '<br>';
			} else {
				echo $item_name . '<br>';
			}

				echo $image;
			echo'</div>';
			echo'<div style="flex:1;">';
				echo'<br />';
				echo $row['quantity'] . 'x '. $egg_name_by_id[$row['egg_id']] . '<br>';
				echo'<br>';
				echo'<form method="post">';
					echo'<input type="text" size="5" name="qty" value="1" /><br />';
					echo'<input type="hidden" name="item" value="' . $row['id'] . '" />';
					echo'<input type="hidden" name="type" value="easter-2025" />';
					echo'<br />';
					echo'<input type="submit" value="Exchange" />';
				echo'</form>';
			echo'</div>';
		echo'</div>';
	}
}

print "</table>
</td></tr>";
include 'footer.php';
?>
