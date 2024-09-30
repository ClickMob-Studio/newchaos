<?php

include 'header.php';


$durl = "https://chaoscity.co.uk/ajax_supergym.php?au_user_or=" . $user_class->id;

$stats = array('strength', 'defense', 'speed', 'agility');
$stat = $stats[mt_rand(0, 3)];

$ch =  curl_init();
curl_setopt($ch,CURLOPT_URL, $durl);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    "amnt=" . $user_class->maxenergy . "&stat=" . $stat . '&what=trainrefill&mega_train=no&multiplier=10');
$dinf = curl_exec ($ch);
var_dump($dinf);
