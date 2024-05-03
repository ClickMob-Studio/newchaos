<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$query = mysql_query("SELECT * FROM `gang_vault_log` ORDER BY `id` DESC LIMIT 100");
?>

<h1>Send Logs</h1>
<div class="table-container">
    <table class="new_table" id="newtables" style="width:100%;">
        <thead>
        <tr>
            <th>Gang</th>
            <th>User</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php while($res = mysql_fetch_array($query, MYSQL_ASSOC)): ?>
            <?php $gang_class = new Gang($res['gang_id']); ?>
            <tr>
                <td>
                    <?php echo $gang_class->name; ?>
                </td>
                <td>
                    <?php echo formatName($res['user_id']) ?>
                </td>
                <td>
                    <?php echo $res['type'] ?>
                </td>
                <td>
                    <?php echo number_format($res['amount'], 0) ?>
                </td>
                <td>
                    <?php echo number_format($res['balance'], 0) ?>
                </td>
            </tr>
        <?php endwhile; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>
