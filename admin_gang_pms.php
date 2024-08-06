<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}


$db->query("SELECT * FROM gangs");
$db->execute();
$gangs = $db->fetch_row();

$pms = null;

if ($_GET['gang_id']) {
    $db->query("SELECT g.*, avatar FROM gangmail g JOIN grpgusers u ON g.playerid = u.id WHERE gangid = ? ORDER BY timesent DESC" . $pages->limit());
    $db->execute(array(
        $_GET['gang_id']
    ));
    $rows = $db->fetch_row();
}

?>

<?php if ($rows): ?>
    <h1>Mails</h1>
    <div class="table-container">
        <table class="new_table" id="newtables" style="width:100%;">
            <thead>
            <tr>
                <th>User</th>
                <th>Date</th>
                <th>Message</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <?php echo formatName($row['playerid']); ?>
                        </td>
                        <td>
                            <?php howlongago($row['timesent']) . ' ago' ?>
                        </td>
                        <td>
                            <?php echo $row['type'] ?>
                        </td>
                        <td>
                            <?php echo $row['body'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
<?php endif; ?>

<?php
include 'footer.php';
?>
