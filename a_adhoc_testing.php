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

$sql = "UPDATE `activity_contest` SET `type` = " . $typeToUse;
echo $sql;
mysql_query("UPDATE `activity_contest` SET `type` = " . $typeToUse);
?>