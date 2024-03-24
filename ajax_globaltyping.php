<?php
include "ajax_header.php";
mysql_select_db('game', mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14'));
$t = ($_GET['is'] == 1) ? 1 : 0;
mysql_query("UPDATE gcusers SET typing = $t WHERE userid = {$_SESSION['id']}");
?>