<?php
include 'header.php';
if (isset($_GET['buy'])) {
	$buy = security($_GET['buy']);
	$db->query("SELECT * FROM carlot WHERE id = ? AND buyable = 1");
	$db->execute(array(
		$buy
	));
	$row = $db->fetch_row(true);
	$db->query("SELECT * FROM usercars WHERE userid = ? AND carid = ?");
	$db->execute(array(
		$user_class->id,
		$buy
	));
	$check = $db->fetch_row(true);
    if (!empty($row)) {
        if (empty($check)) {
            if ($user_class->money >= $row['cost']) {
                $user_class->money -= $row['cost'];
				$db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
				$db->execute(array(
					$row['cost'],
					$user_class->id
				));
				$db->query("INSERT INTO usercars VALUES ('', ?, ?)");
				$db->execute(array(
					$user_class->id,
					$buy
				));
                echo Message("You have purchased a {$row['name']}.");
            } else
                echo Message("You do not have enough money to buy a {$row['name']}.");
        } else
            echo Message("You have already purchased a {$row['name']}.");
    } else
        echo Message("That isn't a real item.");
}
if (isset($_GET['sell'])) {
    $sell = security($_GET['sell']);
	$db->query("SELECT cost FROM usercars u JOIN carlot c ON c.id = carid WHERE userid = ? AND u.id = ?");
	$db->execute(array(
		$user_class->id,
		$sell
	));
	$cost = $db->fetch_single();
    if (isset($cost) AND $cost > 0) {
        $cost *= .6;
		$db->query("DELETE FROM usercars WHERE userid = ? AND id = ?");
		$db->execute(array(
			$user_class->id,
			$sell
		));
		$db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
		$db->execute(array(
			$cost,
			$user_class->id
		));
		$user_class->money += $cost;
    }
}
$db->query("SELECT image, cost, u.id, discount, name FROM carlot c JOIN usercars u ON c.id = carid WHERE userid = $user_class->id ORDER BY cost DESC");
$db->execute();
$rows = $db->fetch_row();
echo'<div class="floaty">';
	echo'<h3>My Cars</h3>';
	foreach(array_chunk($rows, 4) as $subrow){
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<div class="flexcont">';
		foreach($subrow as $row){
			echo'<div class="flexele" style="border-left:thin solid #333;border-right:thin solid #333;">';
				echo'<img src="' . $row['image'] . '" /><br />';
				echo'Name: ' . car_popup($row['name'], $row['id']) . '<br />';
				echo'Discount: ' . $row['discount'] . '%<br />';
				echo'<a href="?sell=' . $row['id'] . '" style="color:yellow;">[sell for ' . prettynum($row['cost'] * .6, 1) . ']</a>';
			echo'</div>';
		}
		echo'</div>';
	}
echo'<hr>';
echo'</div>';
$db->query("SELECT * FROM carlot WHERE buyable = 1");
$db->execute();
$rows = $db->fetch_row();
echo'<div class="floaty">';
	echo'<span style="color:red;font-weight:bold;">Car Dealership</span>';
	foreach(array_chunk($rows, 2) as $subrow){
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<div class="flexcont" style="align-items:initial;">';
		foreach($subrow as $row){
			echo'<div class="flexele" style="border-left:thin solid #333;">';
				echo'<img src="' . $row['image'] . '" /><br />';
				echo'<span style="color:yellow;">Name:</span> ' . car_popup($row['name'], $row['id']) . '<br />';
				echo'<span style="color:yellow;">Cost:</span> $' . prettynum($row['cost']) . '<br />';
				echo'<span style="color:yellow;">Discount:</span> ' . $row['discount'] . '%<br />';
			echo'</div>';
			echo'<div class="flexele" style="border-right:thin solid #333;line-height:125px;">';
				echo'[<a href="carlot.php?buy=' . $row['id'] . '">Buy</a>]';
			echo'</div>';
		}
		echo'</div>';
	}
echo'<hr style="border:0;border-bottom:thin solid #333;" />';
echo'</div>';
include 'footer.php';
?>