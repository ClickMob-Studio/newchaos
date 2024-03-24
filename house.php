<?php
include 'header.php';
if (isset($_GET['buy'])) {
	$buy = security($_GET['buy']);
    $db->query("SELECT * FROM houses WHERE id = ? AND buyable = 1");
    $db->execute(array(
        $buy
    ));
    $row = $db->fetch_row(true);
    if (empty($row))
        diefun("Error, this house was not found.");
    $cost = $row['cost'];
    $houselevel = $row['houselevel'];
    $text = "You have purchased a {$row['name']}. To move into this house, you have to visit the 'Your Properties' link in the mainmenu.";
    if ($cost > ($user_class->money + $oldhouse) && $error != 1)
        diefun("You don't have enough money to buy that house.");
	$user_class->money += floor($oldhouse) - $cost;
    $db->query("UPDATE grpgusers SET money = ?, awake = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->money,
        $user_class->id
    ));
    $db->query("INSERT INTO ownedProperties VALUES('', ?, ?)");
    $db->execute(array(
        $user_class->id,
        $buy
    ));
    echo Message($text);
}
$db->query("SELECT * FROM houses WHERE buyable = 1 ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
	$owned = ($row['id'] == $user_class->house) ? 'background:rgba(0,0,255,.25);' : '';
	echo'<div class="floaty flexcont" style="width:85%;' . $owned . 'margin:2px;">';
		echo'<div class="flexele" style="border-right:thin solid #333;">';
			echo'<img src="images/' . str_replace(array(' ' , '*'), '', strtolower($row['name'])) . '.png" />';
		echo'</div>';
		echo'<div class="flexele">';
			echo $row['name'] . '<br />';
			echo'<br />';
			echo'Awake: ' . prettynum($row['awake']) . '<br />';
			echo'<br />';
			echo'Cost: ' . prettynum($row['cost'], 1);
		echo'</div>';
		echo'<div class="flexele" style="border-left:thin solid #333;line-height:100px;">';
			echo '<a href="house.php?buy=' . $row['id'] . '"><button>Buy</button></a>';
		echo'</div>';
	echo'</div>';
}
include 'footer.php';
?>