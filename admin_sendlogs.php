<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$db->query("SELECT * FROM `transferlog` ORDER BY `timestamp` DESC LIMIT 100");
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
                <th>What</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $res): ?>
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
                        <?php
                        if ($res['item']) {
                            echo Item_Name($res['item']);
                        } elseif ($res['money']) {
                            echo prettynum($res['money'], 1);
                        } elseif ($res['points']) {
                            echo prettynum($res['points']) . " Points";
                        } elseif ($res['credits']) {
                            echo prettynum($res['credits']) . " Credits";
                        }
                        ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>