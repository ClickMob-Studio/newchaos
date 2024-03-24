<?php
include "ajax_header.php";
$id = security($_POST['id']);
$db->query("UPDATE contactlist SET notes = ? WHERE id = ? AND playerid = ?");
$db->execute(array(
	$_POST['note'],
	$id,
	$_SESSION['id']
));
echo'<div class="floaty" style="margin:0;background:rgba(0,128,0,.25);">';
	echo'Contact note updated successfully';
echo'</div>';
?>