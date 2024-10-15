<?php

include 'header.php';


$now = new \DateTime();

$halloweenUserList = getHalloweenUserList($user_class->id);
var_dump($halloweenUserList);