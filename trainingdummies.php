<?php
include 'header.php';

$db->query("SELECT * FROM training_dummy");
$db->execute();
$trainingDummies = $db->fetch_row();

?>

<div class='box_top'>Training Dummies</div>
<div class='box_middle'>
    <div class='pad'>
        <div class="table-responsive">
            <table class="new_table" id="newtables">
                <tr>
                    <th>Dummy</th>
                    <th>EXP</th>
                    <th>Reward</th>
                    <th>&nbsp;</th>
                </tr>
                <?php foreach ($trainingDummies as $trainingDummy): ?>
                    <tr>
                        <td><?php echo $trainingDummies['name'] ?></td>
                        <td>
                            <!-- TODO -->
                        </td>
                        <td><?php echo Item_Name($trainingDummies['item_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
