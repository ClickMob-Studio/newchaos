<?php
include 'header.php';

$db->query("SELECT * FROM training_dummy");
$db->execute();
$trainingDummies = $db->fetch_row();

$db->query("SELECT * FROM training_dummy_user WHERE user_id = ?");
$db->execute(array($user_class->id));
$trainingDummyUsers = $db->fetch_row();

if (count($trainingDummyUsers) < 1) {
    foreach ($trainingDummies as $trainingDummy) {
        $db->query("INSERT INTO training_dummy_user (training_dummy_id, user_id, level, exp, is_fight_available) VALUES (?, ?, 1, 0, 1)");
        $db->execute(array($trainingDummy['id'], $user_class->id));

        header('Location: trainingdummies.php');
        exit();
    }
}
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
                        <td><?php echo $trainingDummy['name'] ?></td>
                        <td>
                            <!-- TODO -->
                        </td>
                        <td><?php var_dump(Item_Name($trainingDummy['item_id'])) ?></td>
                        <td>
                            <a href="#" class="btn btn-primary">Attack</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
