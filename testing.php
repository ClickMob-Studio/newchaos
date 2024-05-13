<?php
require "vendor/autoload.php";
use \Mailjet\Resources;

// or

$apikey = '7dc2ad83e7f15563b1dee7d48109dbb7';
$apisecret = '15326068ed7ef53039e03ca05662bde2';

$mj = new \Mailjet\Client($apikey, $apisecret);
?>