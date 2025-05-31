<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$db->query("SELECT * FROM `pms` ORDER BY `timesent` DESC LIMIT 200");
$db->execute();
$rows = $db->fetch_row();
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
            <?php foreach ($rows as $res): ?>
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
            <?php endforeach; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>