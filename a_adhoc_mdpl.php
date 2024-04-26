<?php

include 'header.php';

if ($_GET['wekey'] === 'herewego') {
    $date = new \DateTime();
    var_dump($date); exit;
}