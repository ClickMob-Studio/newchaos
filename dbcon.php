<?php

date_default_timezone_set('UTC');
$conn = mysql_connect("localhost", "chaoscity_co", '3lrKBlrfMGl2ic14') or die("<b>SQL ERROR:&nbsp;</b>" . mysql_error());
$db = mysql_select_db("game");
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);
if (!isset($_SESSION['id']) || $_SESSION['id'] != 1) {
    error_reporting(0);
}


