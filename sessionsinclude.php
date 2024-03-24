<?php
mysql_select_db('game', mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14'));
$sess = mysql_fetch_array(mysql_query("SELECT * FROM sessions WHERE userid={$_SESSION['id']}"));
if ($sess[1] != $_COOKIE['PHPSESSID']) {
    $sessid = $_SESSION['id'];
    session_unset();
    session_destroy();
}
?>