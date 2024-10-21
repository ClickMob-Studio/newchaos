<?php
include 'header.php';

$db->query("SELECT * FROM training_dummy");
$db->execute();
$trainingDummies = $db->fetch_row();

$trainingDummiesIndexed = array();
foreach ($trainingDummies as $trainingDummy) {
    $trainingDummiesIndexed[$trainingDummy['id']] = $trainingDummy;
}

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

$userPrestigeSkills = getUserPrestigeSkills($user_class);

$trainingDummyUsersIndexed = array();
foreach ($trainingDummyUsers as $trainingDummyUser) {
    $trainingDummyUsersIndexed[$trainingDummyUser['training_dummy_id']] = $trainingDummyUser;
}

if (isset($_GET['attack']) && (int)$_GET['attack'] && (int)$_GET['attack'] > 0) {
    $attack = (int)$_GET['attack'];

    if ($user_class->hospital > 0) {
        diefun('You can\'t attack a training dummy when your in hospital. <a href="trainingdummies.php">Go Back</a>.');
    }

    if ($user_class->jail > 0) {
        diefun('You can\'t attack a training dummy when your in jail. <a href="trainingdummies.php">Go Back</a>.');
    }

    if ($user_class->energy < $user_class->maxenergy) {
        diefun('You need full energy to perform a training dummy attack! <a href="trainingdummies.php">Go Back</a>.');
    }

    if (!isset($trainingDummyUsersIndexed[$attack])) {
        if (isset($trainingDummiesIndexed[$attack])) {
            $db->query("INSERT INTO training_dummy_user (training_dummy_id, user_id, level, exp, is_fight_available) VALUES (?, ?, 1, 0, 1)");
            $db->execute(array($attack, $user_class->id));

            header('Location: trainingdummies.php?attack=' . $attack);
            exit();
        } else {
            diefun('Something went wrong. Please DM an admin if this issue persists. <a href="trainingdummies.php">Go Back</a>.');
        }
    }

    $trainingDummyToUse = $trainingDummiesIndexed[$attack];
    $trainingDummyUserToUse = $trainingDummyUsersIndexed[$attack];

    $nextFightTime = $trainingDummyUserToUse['last_fight_time'] + 7200;
    if ($nextFightTime > time()) {
        diefun('You can only fight each training dummy once every two hours. <a href="trainingdummies.php">Go Back</a>.');
    }

    $totalUserHealth = $user_class->hp;
    $totalUserSpeed = $user_class->moddedspeed;
    $totalUserDefense = $user_class->moddeddefense;
    $totalUserStrength = $user_class->moddedstrength;

    $botStrength = $trainingDummyToUse['strength'] * $trainingDummyToUse['level'];
    $botDefence = $trainingDummyToUse['defence'] * $trainingDummyToUse['level'];
    $botSpeed = $trainingDummyToUse['speed'] * $trainingDummyToUse['level'];
    $bossHp = $trainingDummyToUse['health'] * $trainingDummyToUse['level'];

    while ($totalUserHealth > 0 && $bossHp > 0) {
        $damage = $botStrength - $totalUserDefense;
        $damage = ($damage < 1) ? 1 : $damage;
        $damage = rand(1, $damage);

        if ($damage == 1) {
            if ($botSpeed < 100) {
                $damage = rand(1, 3);
            } else if ($botSpeed < 500) {
                $damage = rand(2, 6);
            } elseif ($botSpeed < 2500) {
                $damage = rand(5, 25);
            } elseif ($botSpeed < 25000) {
                $damage = rand(25, 100);
            } else {
                $damage = rand(100, 200);
            }
        }

        $totalUserHealth = $totalUserHealth - $damage;

        if ($totalUserHealth > 0) {
            $damage = $totalUserStrength - $botDefence;
            $damage = ($damage < 1) ? 1 : $damage;
            $damage = rand(1, $damage);
            if ($damage == 1) {
                if ($totalUserSpeed < 100) {
                    $damage = rand(1, 3);
                } elseif ($totalUserSpeed < 500) {
                    $damage = rand(2, 6);
                } elseif ($totalUserSpeed < 2500) {
                    $damage = rand(5, 25);
                } elseif ($totalUserSpeed < 25000) {
                    $damage = rand(25, 100);
                } else {
                    $damage = rand(100, 200);
                }
            }

            $bossHp = $bossHp - $damage;
        }
    }

    if ($bossHp <= 0) {
        // Won Fight
        $expBoost = mt_rand(2,5);
        if ($attack == 8) {
            $expBoost * 3;
        }

        $newExp = $trainingDummyUserToUse['exp'] + $expBoost;
        if ($newExp > 100) {
            Give_Item($trainingDummyToUse['reward_item_id'], $user_class->id, 1);

            Send_Event($user_class->id, 'You earned the special reward for beating the training dummy and 1 x ' . Item_Name($trainingDummyToUse['reward_item_id']) . ' has been added to your inventory');

            $newExp = 0;
        }
        $expReward = $user_class->maxexp / 10000;
        $expReward = $expReward * mt_rand(1,2);
        $expReward = ceil($expReward);
        if ($expReward < 10) {
            $expReward = mt_rand(1,10);
        }

        $cashReward = mt_rand(50000, 250000);
        if ($userPrestigeSkills['training_dummy_cash_unlock'] > 0) {
            $cashReward = $cashReward + ($cashReward / 100 * 20);
            $cashReward = ceil($cashReward);
        }

        if ($attack == 8) {
            $cashReward = $cashReward * 5;
        }

        $pointsReward = 0;
        if ($attack == 8) {
            $pointsReward = mt_rand(100,200);
        }

        $db->query('UPDATE grpgusers SET energy = 0, money = money + ?, exp = exp + ?, points = points + ? WHERE id = ?');
        $db->execute(array($cashReward, $expReward, $pointsReward, $user_class->id));

        $db->query('UPDATE training_dummy_user SET level = level + 1, exp = ' . $newExp . ', last_fight_time = ' . time() . ' WHERE id = ' . $trainingDummyUserToUse['id']);
        $db->execute();

        if ($attack == 8) {
            diefun('You have successfully beaten the training dummy and you have been rewarded ' . number_format($expReward, 0) . ' EXP, ' . number_format($pointsReward, 0) . ' points & $' . number_format($cashReward, 0) .'! <a href="trainingdummies.php">Go Back</a>.');
        } else {
            diefun('You have successfully beaten the training dummy and you have been rewarded ' . number_format($expReward, 0) . ' EXP & $' . number_format($cashReward, 0) .'! <a href="trainingdummies.php">Go Back</a>.');
        }
    } else {
        // Lost Fight
        $db->query('UPDATE grpgusers SET hp = 0, energy = 0, hospital = 300 WHERE id = ?');
        $db->execute(array($user_class->id));


        diefun('You lost to the training dummy and you will now need to spend some time in hospital! <a href="trainingdummies.php">Go Back</a>.');
    }
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

<div class='box_top'>City Goons</div>
<div class='box_middle'>
    <div class='pad'>
        <p>Welcome to the City Goons! Here you will find some of the goons that have been lurking around Chaos City.</p>
        <p>
            You can attack each City Goon every 2 hours and from every attack win you'll earn a EXP & cash prize. You'll also
            earn progress towards winning the reward item that the City Goon payouts.
        </p>
        <p>
            Be careful though, every time you win an attack against a City Goon, they get stronger!
        </p>
        <div class="table-responsive">
            <table class="new_table" id="newtables">
                <tr>
                    <th>Dummy</th>
                    <th>Progress</th>
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
                        <td>
                            <center>
                                <div class="tiers text-center">
                                    <img height="65px" width="65px" src="<?php echo $trainingDummy['image'] ?>" />
                                </div>
                            </center>

                        </td>
                        <td>
                            <div class="progress" style="margin-top: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" aria-label="Success example" title="<?php echo $progressWidth ?>%" style="width: <?php echo $progressWidth ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo $progressWidth ?>%
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <center>
                                <div class="tiers text-center">
                                    <img height="65px" width="65px" src="<?php echo Item_Image($trainingDummy['reward_item_id']) ?>" />
                                </div>
                            </center>
                        </td>
                        <td width="20%">
                            <?php
                            $trainingDummyUserToUse = $trainingDummyUsersIndexed[$trainingDummy['id']];

                            $nextFightTime = $trainingDummyUserToUse['last_fight_time'] + 7200;
                            ?>

                            <?php if ($nextFightTime > time()): ?>
                                <?php echo howlongtil($nextFightTime) ?> Until Next Attack
                            <?php else: ?>
                                <a href="trainingdummies.php?attack=<?php echo $trainingDummy['id'] ?>" class="btn btn-primary">Attack</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
