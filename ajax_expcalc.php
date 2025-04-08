<?php

include "ajax_header.php";
if (!ctype_digit($_POST['level']))
	die('Numbers only...');

$db->query("SELECT level, exp FROM grpgusers WHERE id = ?");
$db->execute(array(
	$_SESSION['id']
));
$row = $db->fetch_row(true);

$expneeded = 0;
$goal = min(10000, security($_POST['level']));
for ($i = $row['level'] + 1; $i <= $goal; $i++) {
	$expneeded += experience($i);
}
$expneeded -= $row['exp'];
if ($expneeded < 0)
	$expneeded = 0;

echo 'You need ' . prettynum($expneeded) . ' EXP to get to level ' . $goal . '.';

?>