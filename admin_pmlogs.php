<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$query = mysql_query("SELECT * FROM `pm` ORDER BY `timesent` DESC LIMIT 200");
?>

<h1>Send Logs</h1>
<div class="table-container">
    <table class="new_table" id="newtables" style="width:100%;">
        <thead>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>Date</th>
            <th>Subject</th>
            <th>Content</th>
        </tr>
        </thead>
        <tbody>
        <?php while($res = mysql_fetch_array($query, MYSQL_ASSOC)): ?>
            <tr>
                <td>
                    <?php echo formatName($res['from']) ?><br />
                </td>
                <td>
                    <?php echo formatName($res['to']) ?><br />
                </td>
                <td>
                    <?php echo date('d M Y h:i:s', $res['timesent']) ?>
                </td>
                <td>
                    <?php echo $res['subject'] ?>
                </td>
                <td>
                    <?php echo $res['msgtext'] ?>
                </td>
            </tr>
        <?php endwhile; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>
