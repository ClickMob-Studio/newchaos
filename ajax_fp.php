<?php
include "ajax_header.php";
require 'vendor/autoload.php';

//mysql_select_db('ml2', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));
$user_class = new User($_SESSION['id']);

$data = file_get_contents('php://input');

$logger = new Katzgrau\KLogger\Logger('/var/www/logs/fp', Psr\Log\LogLevel::INFO, array (
    'prefix' => $user_class->id . "-",
));
$logger->info("", $data);