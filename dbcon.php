<?php

date_default_timezone_set('UTC');
$conn = mysql_connect("localhost", "mafialor_game", 'mickeybraden321') or die("<b>SQL ERROR:&nbsp;</b>" . mysql_error());
$db = mysql_select_db("mafialor_game");
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);
if (!isset($_SESSION['id']) || $_SESSION['id'] != 1) {
    error_reporting(0);
}


