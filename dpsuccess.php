<?php
include 'header.php';

if ($_GET['appcode'] != "" && $_GET['prodcode'] != "" && $_GET['pin'] != "" && $_GET['orderno'] != "") {

    $appcode = addslashes($_GET['appcode']);
    $prodcode = addslashes($_GET['prodcode']);
    $pin = addslashes($_GET['pin']);
    $orderno = addslashes($_GET['orderno']);
    $parent = floor(time() / (uniqid(rand(1, 20), true) + uniqid(rand(1, 200))) - rand(100, 1000));
    $from = $user_class->id;

    $msgtext = "Appcode: $appcode
Product Code: $prodcode
Pin: $pin
Order Number: $orderno";

    perform_query("INSERT INTO `pms` (`parent`, `to`, `from`, `timesent`, `subject`, `msgtext`) VALUES (?, '1', ?, ?, 'DAO PAY Payment', ?)", [$parent, $from, time(), $msgtext]);

    echo Message("Your payment has been completed. Your credits will be credited within 74 hours. Please be patient.<br /><br /><a href='home.php'>Go Home</a>");
}

include 'footer.php';
?>