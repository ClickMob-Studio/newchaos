<?php

// (PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('');
// chdir("/var/www/s2.themafialife.co.uk");
include 'dbcon.php';
include 'classes.php';
include 'database/pdo_class.php';

$conn = mysql_connect("localhost", "aa_user", 'GmUq38&SVccVSpt') or die("<b>SQL ERROR:&nbsp;</b>" . mysql_error());
$db = mysql_select_db("ml2");
$result = mysql_query("UPDATE `grpgusers` SET energy = 10 WHERE id = 2", $conn);
