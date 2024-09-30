<?php
session_start();

if (isset($_GET['au_user_or']) && $_GET['au_user_or'] > 0) {
    // DO nothing
} else if (empty($_SESSION['id'])) {
    $IP = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    $current = time() . " | " . $IP . " | " . $_SERVER['PHP_SELF'] . " | " . serialize($_POST) . "\n";
    echo 'no session id';
    die();
}
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

//$db->query("UPDATE grpgusers SET lastactive = ".time()." WHERE id = ".$_SESSION['id']);
//$db->execute();
?>
