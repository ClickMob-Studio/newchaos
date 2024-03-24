<?php
include "ajax_header.php";
if(isset($_GET['show'])){
	$db->query("UPDATE grpgusers SET hideemojis = 0 WHERE id = ?");
	$db->execute(array(
		$_SESSION['id']
	));
}
if(isset($_GET['hide'])){
	$db->query("UPDATE grpgusers SET hideemojis = 1 WHERE id = ?");
	$db->execute(array(
		$_SESSION['id']
	));
}
?>