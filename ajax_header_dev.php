<?php
require_once 'includes/functions.php';

start_session_guarded();

if (empty($_SESSION['id'])) {
    echo 'no session id';
    die();
}
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";
include "includes/functions.php";
