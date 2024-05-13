<?php

use \Mailjet\Resources;

// getenv will allow us to get the MJ_APIKEY_PUBLIC/PRIVATE variables we created before
$apikey = getenv('MJ_APIKEY_PUBLIC');
$apisecret = getenv('MJ_APIKEY_PRIVATE');

// or

$apikey = '7dc2ad83e7f15563b1dee7d48109dbb7';
$apisecret = '15326068ed7ef53039e03ca05662bde2';

$mj = new \Mailjet\Client($apikey, $apisecret);
?>