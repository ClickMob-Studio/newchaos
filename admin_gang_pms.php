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

if (isset($_GET['gang_id'])) {
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
                            <?php echo howlongago($row['timesent']) . ' ago' ?>
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
    <h1>Select a gang</h1>
    <ul>
        <?php foreach ($gangs as $gang): ?>
            <?php $gang_class = new Gang($gang['id']); ?>
            <li><a href="admin_gang_pms.php?gang_id=<?php echo $gang_class->id ?>"><?php echo $gang_class->formattedname ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
include 'footer.php';
?>
