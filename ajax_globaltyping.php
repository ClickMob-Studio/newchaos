<?php
include "ajax_header.php";
mysql_select_db('chaoscit_game', mysql_connect('localhost', 'chaoscit_user', '3lrKBlrfMGl2ic14'));
$t = ($_GET['is'] == 1) ? 1 : 0;
mysql_query("UPDATE gcusers SET typing = $t WHERE userid = {$_SESSION['id']}");
?>