<?php
session_start();
if (empty($_SESSION['id'])) {
    $file = '/var/www/logs/ajax_ml2.txt';
    $IP = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    $current = time() . " | " . $IP . " | " . $_SERVER['PHP_SELF'] . " | " . serialize($_POST) . "\n";
    file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
    echo 'no session id';
    die();
}
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);
?>
