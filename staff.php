<?php
include("dbcon.php");
include("gradient.class.php");
include("colourgradient.class.php");
include("classes.php");

$db->query("SELECT * FROM `grpgusers` WHERE `gm` = '1' OR `fm` = '1' OR `cm` = '1' OR `eo` = '1'");
$results = $db->fetch_row();
foreach ($results as $line) {
    $staff_class = new User($line['id']);
    if ($staff_class->gm == 1) {
        $newpoints = $staff_class->points + 50;
    } else if ($staff_class->fm == 1) {
        $newpoints = $staff_class->points + 40;
    } else if ($staff_class->cm == 1) {
        $newpoints = $staff_class->points + 30;
    } else if ($staff_class->eo == 1) {
        $newpoints = $staff_class->points + 30;
    }

    perform_query("UPDATE `grpgusers` SET `points` = ? WHERE `id` = ?", [$newpoints, $line['id']]);
    Send_Event($line['id'], "You have been paid " . $newpoints . " points for doing a great job as a staff member.");
}
?>