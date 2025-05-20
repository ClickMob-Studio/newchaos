<?php
require_once 'includes/functions.php';

start_session_guarded();

if (empty($_SESSION['id'])) {
    echo 'no session id';
    die();
}
include_once "classes.php";
include_once "codeparser.php";
include_once "database/pdo_class.php";
