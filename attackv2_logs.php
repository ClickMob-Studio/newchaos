<?php
include 'header.php';

$db->query("SELECT * FROM attack_v2 WHERE attacking_user_id = " . $user_class->id . " OR defending_user_id = " . $user_class->id . " ORDER BY timestamp DESC LIMIT 50");
$db->execute();
$attacks = $db->fetch_row();

?>
<div class="box_top">Attacks</div>
<div class="box_middle">
    <div class="pad">
        <table id="newtables" style="width:100%;">
            <tr>
                <th>Time</td>
                <th>Attacker</th>
                <th>Defender</th>
                <th>Winner</th>
                <th>View Logs</th>
            </tr>
            <?php foreach ($attacks as $attack): ?>
                <?php $time = date("F d, Y g:ia", $attack['attack_time']); ?>
                <tr>
                    <td width='28%'><?php echo $time ?></td>
                    <td width='20%'><?php echo formatName($attack['attacking_user_id']) ?></td>
                    <td width='20%'><?php echo formatName($attack['defending_user_id']) ?></td>
                    <td width='20%'><?php echo formatName($attack['winning_user_id']) ?></td>
                    <th><a href="view_attack.php?id=<?php echo $attack['id'] ?>">View</a></th>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php
include 'footer.php';
?>