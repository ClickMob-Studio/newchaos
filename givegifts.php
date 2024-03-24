<?php
include "header.php";
if(empty($_GET['id']) || $_GET['id'] == $user_class->id)
	diefun("Illegal use of file.");





$gifts = array(
	'Thank You' => 250,
	'Strawberry' => 500,
	'Pie' => 750,
	'Band-Aid' => 1000,
	'Coffee' => 1250,
	'Beer' => 1900,
	'Tissues' => 2500,
	'Feather' => 3750,
	'Kiss' => 5000,
	'Cigars' => 7500,
	'Dom Perignon' => 10000,
	'Crown Royal-XO' => 12500,
	'Red Rose' => 15000,
	'Black Rose' => 17500,
	'Engagement Ring' => 20000,
	'Watch' => 22500,
	'Diamond' => 25000
);
$plurals = array(
	'Strawberry' => 'Strawberries',
	'Tissues' => 'Tissues',
	'Kiss' => 'Kisses',
	'Cigars' => 'Cigars',
	'Watch' => 'Watches',
	'Jar of Peanut Butter' => 'Jars of Peanut Butter'
);
$id = security($_GET['id']);
if(isset($_POST['itemname'])){
	$qty = security($_POST['qty']);
	$index = $_POST['itemname'];
	$cost = $qty * $gifts[$index];
	if($qty == 0)
		diefun("fuck you");
	if($id == 0 || $user_class->id == 0)
		$cost = 0;
	if($cost > $user_class->bank)
		diefun("You do not have enough money in the bank to send these gifts.");
	else{
		$db->query("UPDATE grpgusers SET bank = bank - ? WHERE id = ?");
		$db->execute(array(
			$cost,
			$user_class->id
		));
		$db->query("INSERT INTO user_gifts VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE qty = qty + ?");
		$db->execute(array(
			$id,
			$index,
			$qty,
			$qty
		));
		$user_class->bank -= $cost;
		$note = (!empty($_POST['note'])) ? $_POST['note'] : "";
		Send_event($id, "[-_USERID_-] sent you [x$qty] " . pluralize($index, $qty) . "! $note", $user_class->id);
		echo'<div id="success">You have sent [x' . $qty . '] ' . pluralize($index, $qty) . ' to ' . formatName($id) . '</div>';
	}
}


$db->query("SELECT blocker FROM ignorelist WHERE blocker = ? AND blocked = ? LIMIT 1");
    $db->execute(array(
        $id,
        $user_class->id
    ));
    if ($db->num_rows('You cannot send gifts to this user because they have you on their ignore list.'))
        diefun();

echo'<br />';
echo'<span style="font-size:22px;">You are sending gifts to ' . formatName($id) . '!</span>';
echo'<br />';
echo'<br />';
echo'<table id="newtables" style="width:100%;">';
	foreach(array_chunk($gifts, 4, true) as $smgifts){
		echo'<tr>';
		foreach($smgifts as $name => $price){
			echo'<td style="padding:8px;">';
				echo $name . ' (<span style="color:green;">' . prettynum($price, 1) . '</span> ea)<br />';
				echo'<img src="images/gifts/' . str_replace(' ', '', $name) . '.png" />';
				echo'<form method="post">';
					echo'Send: <input type="text" size="5" maxlength="5" name="qty" value="0" /><br />';
					echo'<br />';
					echo'Note: <input type="text" size="15" name="note" placeholder="Send note with gift." /><br />';
					echo'<br />';
					echo'<input type="hidden" name="itemname" value="' . $name . '" />';
					echo'<input type="submit" value="Send!" />';
				echo'</form>';
			echo'</td>';
		}
		echo'</tr>';
	}
echo'</table>';
include "footer.php";
function pluralize($index, $num){
	global $plurals;
	if($num == 1)
		return $index;
	return (isset($plurals[$index])) ? $plurals[$index] : $index . 's';
}
?>