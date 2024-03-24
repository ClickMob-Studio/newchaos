<?php

include "ajax_header.php";

$jailer = $_POST['jailer'];
$debug = $_POST['debug'];
$userid = $_POST['user'];
$time = time();
$coords = $jailer . "," . $debug;

require 'vendor/autoload.php';

$logger = new Katzgrau\KLogger\Logger('/var/www/logs/jail', Psr\Log\LogLevel::INFO, array(
    'prefix' => $userid . "-",
)
);

$debug = array(
    'id' => $userid,
    'jailer' => $jailer,
    'coords' => $coords,
    'time' => $time
);

$logger->info("", $debug);

//$db->query("INSERT INTO jail_log (coords, `timestamp`, userid) VALUES ('$coords', $time, $userid)");
//$db->execute();

// if ($userid == 174) {
//     echo 1;
// } else {
//     echo "test";
// }

?>