<?php
mysql_select_db('game', mysql_connect('localhost', 'root', 'mickeybraden321'));
if (isset($_GET['email'])) {
    $email = strip_tags($_GET['email']);
    $email = addslashes($email);
    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
        die("fail");
    $q = mysql_query("SELECT id FROM grpgusers WHERE email LIKE '$email'");
    $r = mysql_fetch_array($q);
    if (!empty($r))
        die("fail");
    die("pass");
}
if (isset($_GET['username'])) {
    $username = strip_tags($_GET['username']);
    $username = addslashes($username);
    $q = mysql_query("SELECT id FROM grpgusers WHERE username LIKE '$username' OR loginame LIKE '$username'");
    $r = mysql_fetch_array($q);
    if (!empty($r))
        die("fail");
    die("pass");
}
?>
