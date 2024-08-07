<?php

include 'header.php';


$db->query("DELETE FROM `events` ORDER BY `timesent` ASC LIMIT 100000");
$db->execute();
