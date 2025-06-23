<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$db->query("SELECT * FROM `mission_daily_payout_logs`");
$db->execute();
$rows = $db->fetch_row();

$dailyMissionPayoutLogsIndexedByDate = array();
$userIdsIndexedByDate = array();

foreach ($rows as $res) {
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
    $dailyMissionPayoutLogsIndexedByDate[$res['date']]['total_points_earned'] += $res['total_points_earned'];
    $dailyMissionPayoutLogsIndexedByDate[$res['date']]['total_profit_earned'] += $res['total_profit_earned'];
}

foreach ($dailyMissionPayoutLogsIndexedByDate as $date => $values) {
    $dailyMissionPayoutLogsIndexedByDate[$date]['average_points_earned'] = $dailyMissionPayoutLogsIndexedByDate[$date]['total_points_earned'] / $dailyMissionPayoutLogsIndexedByDate[$date]['total_users'];
    $dailyMissionPayoutLogsIndexedByDate[$date]['average_profit_earned'] = $dailyMissionPayoutLogsIndexedByDate[$date]['total_profit_earned'] / $dailyMissionPayoutLogsIndexedByDate[$date]['total_users'];
}
?>

<h1>Mission Payout Logs</h1>
<div class="table-container">
    <table class="new_table" id="newtables" style="width:100%;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Users</th>
                <th>Total Points Earned</th>
                <th>Total Profit Earned</th>
                <th>Average Points Earned</th>
                <th>Average Profit Earned</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailyMissionPayoutLogsIndexedByDate as $date => $row): ?>
                <tr>
                    <td><?php echo $date ?></td>
                    <td><?php echo number_format($row['total_users'], 0) ?></td>
                    <td><?php echo number_format($row['total_points_earned'], 0) ?></td>
                    <td><?php echo number_format($row['total_profit_earned'], 0) ?></td>
                    <td><?php echo number_format($row['average_points_earned'], 0) ?></td>
                    <td><?php echo number_format($row['average_profit_earned'], 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>