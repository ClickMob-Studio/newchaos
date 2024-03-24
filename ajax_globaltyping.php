<?php
include "ajax_header.php";
mysql_select_db('mafialor_game', mysql_connect('localhost', 'mafialor_game', 'mickeybraden321'));
$t = ($_GET['is'] == 1) ? 1 : 0;
mysql_query("UPDATE gcusers SET typing = $t WHERE userid = {$_SESSION['id']}");
?>