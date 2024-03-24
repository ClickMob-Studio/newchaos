<?php
include "header.php";


if ($user_class->prestige < 5) {
    echo Message("You need to be prestige 5 to be here!.");
    include 'footer.php';
    die();
}


$places = array(
	'China',
	'Australia',
	'Mexico',
	'Korea',
	'Egypt',
	'Italy'
);
if ($user_class->jail > 0)
    diefun("You cant go points smuggling while in prison.");
if ($user_class->hospital > 0)
	diefun("You cant go points smuggling while in hospital.");
if ($user_class->psmuggling2 == 0)
    diefun("You have smuggled as many points as you can today.");
if (isset($_GET['smug'])) {
	if(!in_array($_GET['smug'], $places))
		diefun("Where the FUCK did this country come from?");
    $stole = rand(0, 1000);
	$db->query("UPDATE grpgusers SET points = points + ?, psmuggling2 = psmuggling2 - 1 WHERE id = ?");
	$db->execute(array(
		$stole,
		$user_class->id
	));
    diefun("You went to " . $_GET['smug'] . " and stole $stole points.<br><a href='megasmuggling.php'>Back</a>");
}
echo'<h3>Welcome to the Mega Points Smuggling Docks. You can goto these locations and smuggle:</h3>';
	echo'<hr>';
echo'<div class="floaty">';
	
	foreach($places as $place)
		echo'[<a href="megasmuggling.php?smug=' . $place . '">' . $place . '</a>]<br />';
echo'</div>';
include 'footer.php';
?>
