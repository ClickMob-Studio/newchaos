<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$db->query("SELECT * FROM transferlog ORDER BY timestamp DESC LIMIT 100")
$db->exectue();
$results = $db->fetch_row();
?>

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
            <?php foreach ($results as $res): ?>
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
            <?php endforeach; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>
