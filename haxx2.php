<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$test_cases = array(
    array(10000000000, 1000000000),
    array(1000000000, 10000000000),
    array(5000000000, 5000000000)
);
foreach ($test_cases as $test_case) {
    echo 'Attacker: ' . number_format($test_case[0]) . ', Defender: ' . number_format($test_case[1]) . ', Payout: ' . max(.1, min(1, ($test_case[1] / $test_case[0] / 2))) . '<br />';
}