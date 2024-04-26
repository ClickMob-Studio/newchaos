<?php

include 'header.php';

if ($_GET['wekey'] === 'herewego') {
    $startDate = new \DateTime();
    $startDate->setDate(2024, 04, 01);
    $startDate->setTime(00, 00,00);

    $endDate = new \DateTime();
    $endDate->setDate(2024, 04, 01);
    $endDate->setTime(23, 59,59);

    $db->query("SELECT * FROM `missions` WHERE `timestamp` BETWEEN " . $startDate->getTimestamp() . " AND " . $endDate->getTimestamp() . " AND `completed` = 'successful'");
    $db->execute();
    $rows = $db->fetch_row();
    echo count($rows); exit;

}