<?php
include 'header.php';

// Ensure the OwnedBusiness class is initialized based on the business the user is associated with.
$userId = $_SESSION['id'];
$result = mysql_query("SELECT ownership_id FROM OwnedBusinesses WHERE user_id = '$userId' LIMIT 1");

if (!$result || mysql_num_rows($result) == 0) {
    echo "You're not associated with any business.";
    exit;
}

$row = mysql_fetch_assoc($result);
$ownershipId = $row['ownership_id'];

$business_class = new OwnedBusiness($ownershipId);

// Fetch logs for the business
$query = "SELECT * FROM business_logs WHERE business_id = '$business_class->business_id' ORDER BY timestamp DESC";
$result = mysql_query($query);

if (!$result) {
    echo "MySQL Error: " . mysql_error();
    exit;
}

$logs = array();
while($row = mysql_fetch_assoc($result)) {
    $logs[] = $row;
}
?>

<h2>Business Logs</h2>
<table>
    <thead>
        <tr>
            <th>Date & Time</th>
            <th>Type</th>
            <th>User (If applicable)</th>
            <th>Amount (If applicable)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($logs as $log): ?>
        <tr>
            <td><?php echo $log['timestamp']; ?></td>
            <td><?php echo ucfirst($log['log_type']); ?></td>
            <td>
                <?php
                if($log['user_id']) {
                    $userResult = mysql_query("SELECT username FROM grpgusers WHERE id = '{$log['user_id']}'");
                    $userRow = mysql_fetch_assoc($userResult);
                    echo $userRow['username'];
                } else {
                    echo "N/A";
                }
                ?>
            </td>
            <td><?php echo $log['amount'] ? "$" . $log['amount'] : "N/A"; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
include 'footer.php';
?>