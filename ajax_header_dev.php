<?php
session_start();
if (empty($_SESSION['id'])) {
    echo 'no session id';
    die();
}
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";
include "includes/functions.php";
$m = new Memcached();
$m->addServer('127.0.0.1', 11211, 33);