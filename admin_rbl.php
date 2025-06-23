<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$db->query("SELECT * FROM `raid_battle_logs` ORDER BY `timestamp` DESC LIMIT 100");
$db->execute();
$rows = $db->fetch_row();
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
            <?php foreach ($rows as $res): ?>
                <?php
                $db->query("SELECT * FROM raid_participants WHERE raid_id = ?");
                $db->execute([$res['raid_id']]);
                $participants = $db->fetch_row();
                ?>

                <tr>
                    <td>
                        <strong>Participants:</strong>
                        <?php foreach ($participants as $participant): ?>
                            <p><?php echo formatName($participant['user_id']) ?></p>
                        <?php endforeach; ?>
                        <br />

                        <hr />

                        <strong>Logs:</strong><br />
                        <?php echo $res['battle_log'] ?>
                        <hr />
                    </td>
                </tr>
            <?php endforeach; ?>


        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>