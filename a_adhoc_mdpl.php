<?php

include 'header.php';

if ($_GET['wekey'] === 'herewego') {
    $startDate = new \DateTime();
    $startDate->setDate(2024, 04, 01);
    $startDate->setTime(00, 00,00);
    echo $startDate->getTimestamp(); exit;
    var_dump($date); exit;
}