<?php
include "ajax_header.php";
//mysql_select_db('game', mysql_connect('localhost', 'root', 'mickeybraden321'));
$user_class = new User($_SESSION['id']);
security($_POST['id'], 'num');
$check = ($user_class->admin == 1) ? "" : " AND (userid = {$_SESSION['id']} OR posterid = {$_SESSION['id']})";
mysql_query("DELETE FROM wallcomments WHERE id = {$_POST['id']}{$check}");
?>