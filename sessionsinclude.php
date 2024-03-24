<?php
mysql_select_db('mafialor_game', mysql_connect('localhost', 'mafialor_game', 'mickeybraden321'));
$sess = mysql_fetch_array(mysql_query("SELECT * FROM sessions WHERE userid={$_SESSION['id']}"));
if ($sess[1] != $_COOKIE['PHPSESSID']) {
    $sessid = $_SESSION['id'];
    session_unset();
    session_destroy();
}
?>