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

$trainingDummyUsersIndexed = array();
foreach ($trainingDummyUsers as $trainingDummyUser) {
    $trainingDummyUsersIndexed[$trainingDummyUser['training_dummy_id']] = $trainingDummyUsers;
}
?>


<style>
    .tiers {
        border: 4px solid #ff6218;
        margin-right: 5px;
        margin-top: 5px;
        width: 75px;
        height: 75px;
    }
</style>

<div class='box_top'>Training Dummies</div>
<div class='box_middle'>
    <div class='pad'>
        <div class="table-responsive">
            <table class="new_table" id="newtables">
                <tr>
                    <th>Dummy</th>
                    <th>Reward</th>
                    <th>&nbsp;</th>
                </tr>
                <?php foreach ($trainingDummies as $trainingDummy): ?>
                    <?php
                    $progressWidth = 0;
                    if (isset($trainingDummyUsersIndexed[$trainingDummy['id']])) {
                        $toUse = $trainingDummyUsersIndexed[$trainingDummy['id']];
                        $expRequired = 100;

                        if ((($toUse['exp'] / $expRequired) * 100) > 100) {
                            $progressWidth = 100;
                        }

                        $progressWidth = ($toUse['exp'] / $expRequired) * 100;
                    }
                    ?>

                    <tr>
                        <td><?php echo $trainingDummy['name'] ?></td>
                        <td style="text-align:center;">
                            <center>
                                <div class="tiers text-center">
                                    <img height="65px" width="65px" src="<?php echo Item_Image($trainingDummy['reward_item_id']) ?>" />
                                </div>

                                <div class="progress" style="margin-top: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" aria-label="Success example" title="<?php echo $progressWidth ?>%" style="width: <?php echo $progressWidth ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $progressWidth ?>%
                                    </div>
                                </div>
                            </center>
                        </td>
                        <td>
                            <a href="#" class="btn btn-primary">Attack</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
