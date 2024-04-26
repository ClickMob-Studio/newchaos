<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$query = mysql_query("SELECT * FROM `daily_mission_payout_logs`");

$dailyMissionPayoutLogsIndexedByDate = array();

?>