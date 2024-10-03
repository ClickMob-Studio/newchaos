<?php
require "header.php";
if ($user_class->admin < 1) {
    die();
}
$db->query("SELECT * FROM attack_v2 ORDER BY attack_time DESC LIMIT 100");
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