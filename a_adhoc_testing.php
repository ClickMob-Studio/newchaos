<?php
include 'header.php';
$activityContestTypes = array(
    'crimes',
    'backalley',
    'attacks',
    'mugs',
    'busts',
);
$typeToUse = $activityContestTypes[mt_rand(0, count($activityContestTypes) - 1)];

mysql_query("UPDATE `activity_contest` SET `type` = " . $typeToUse);
?>