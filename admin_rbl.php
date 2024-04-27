<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$query = mysql_query("SELECT * FROM `raid_battle_logs` ORDER BY `timestamp` DESC LIMIT 100");
?>

<h1>Send Logs</h1>
<div class="table-container">
    <table class="new_table" id="newtables" style="width:100%;">
        <thead>
        <tr>
            <th>Log</th>
        </tr>
        </thead>
        <tbody>
        <?php while($res = mysql_fetch_array($query, MYSQL_ASSOC)): ?>
            <tr>
                <td>
                    <?php echo $res['battle_log'] ?>
                    <hr />
                </td>
            </tr>
        <?php endwhile; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>
