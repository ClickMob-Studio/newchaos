<?php
require_once 'includes/functions.php';

start_session_guarded();

if (empty($_SESSION['id'])) {
    $IP = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    $current = time() . " | " . $IP . " | " . $_SERVER['PHP_SELF'] . " | " . serialize($_POST) . "\n";
    echo 'no session id';
    die();
}

$captcha = checkAJAXCaptchaRequired($_SESSION['id']);
if ($captcha) {
    die();
}

include_once "classes.php";
include_once "codeparser.php";
include_once "database/pdo_class.php";
include_once "dbcon.php";

?>