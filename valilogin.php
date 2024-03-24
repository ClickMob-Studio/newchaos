<?php
mysql_select_db('aa', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));
$pass = sha1($_POST['pw']);
$q = mysql_query("SELECT id FROM grpgusers WHERE username = '{$_POST['un']}' AND password = '$pass'");
$r = mysql_fetch_array($q);
if (empty($r))
    die("Error, account not found.");
die("pass");
?>