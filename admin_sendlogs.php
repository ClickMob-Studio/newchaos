<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$query = mysql_query("SELECT * FROM `transferlog` ORDER BY `timestamp` DESC LIMIT 100");
?>

<h1>Send Logs</h1>
<div class="table-container">
    <table class="new_table" id="newtables" style="width:100%;">
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Date</th>
                <th>What</th>
            </tr>
        </thead>
        <tbody>
            <?php while($res = mysql_fetch_array($query, MYSQL_ASSOC)): ?>
                <tr>
                    <td>
                        <?php echo formatName($res['from']) ?><br />
                        <?php echo $res['fromip'] ?>
                    </td>
                    <td>
                        <?php echo formatName($res['to']) ?><br />
                        <?php echo $res['toip'] ?>
                    </td>
                    <td>
                        <?php echo date('d M Y h:i:s', $res['timestamp']) ?>
                    </td>
                    <td>

                    </td>
                </tr>
            <?php endwhile; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>
