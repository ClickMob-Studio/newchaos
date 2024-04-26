<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

echo 'here';

$query = mysql_query("SELECT * FROM `mission_daily_payout_logs`");

$dailyMissionPayoutLogsIndexedByDate = array();
$userIdsIndexedByDate = array();

while($res = mysql_fetch_array($query, MYSQL_ASSOC)) {
    if (!isset($dailyMissionPayoutLogsIndexedByDate[$res['date']])) {
        $dailyMissionPayoutLogsIndexedByDate[$res['date']] = array();
        $dailyMissionPayoutLogsIndexedByDate[$res['date']]['total_users'] = 0;
        $dailyMissionPayoutLogsIndexedByDate[$res['date']]['total_points_earned'] = 0;
        $dailyMissionPayoutLogsIndexedByDate[$res['date']]['total_profit_earned'] = 0;
    }

    if (!isset($userIdsIndexedByDate[$res['date']])) {
        $userIdsIndexedByDate[$res['date']] = array();
    }

    if (!in_array($res['user_id'], $userIdsIndexedByDate[$res['date']])) {
        $dailyMissionPayoutLogsIndexedByDate[$res['date']]['total_users'] += 1;
    }
}
var_dump($dailyMissionPayoutLogsIndexedByDate); exit;



?>