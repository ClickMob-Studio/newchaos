<?php

// Force redirect to maintenance.php
header('Location: maintenance.php');
die();

require_once 'includes/functions.php';

start_session_guarded();

include 'header.php';

?>

<div class='container mt-3'>

    <?php
    if ($user_class->firstlogin1 == 0) {
        $db->query("UPDATE grpgusers SET firstlogin1 = 1 WHERE id = ?");
        $db->execute([$user_class->id]);
        Send_Event2($user_class->id, "Is the latest thug on the streets.", $user_class->id);
        Send_Event($user_class->id, "<div class='text-white'>Welcome To Chaos City!<br>To get you started we are giving you:</div><div class='fw-bold text-white'>&bull; 3 VIP Days<br>&bull; $100,000 Cash<br>&bull; 1,250 Points</div>", $user_class->id);
    }
    ?>
    <h1>General Information</h1>
    <div class="table-container">
        <table id="newtables" style="width:100%;">
            <tr>
                <th width="10%">Name:</th>
                <td width="30%">
                    <a href="profiles.php?id=<?= $user_class->id ?>"><?= $user_class->formattedname ?></a>
                </td>
                <th width="10%">HP:</th>
                <td width="30%"><?= prettynum($user_class->formattedhp) ?></td>
            </tr>
            <tr>
                <th width="10%">Level:</th>
                <td width="30%"><?= $user_class->level ?></td>
                <th width="10%">Energy:</th>
                <td width="30%"><?= prettynum($user_class->formattedenergy) ?></td>
            </tr>
            <tr>
                <th width="10%">Money:</th>
                <td width="30%">$<?= prettynum($user_class->money) ?></td>
                <th width="10%">Awake:</th>
                <td width="30%"><?= prettynum($user_class->formattedawake) ?></td>
            </tr>
            <tr>
                <th width="10%">Bank:</th>
                <td width="30%">$<?= prettynum($user_class->bank) ?></td>
                <th width="10%">Nerve:</th>
                <td width="30%"><?= prettynum($user_class->formattednerve) ?></td>
            </tr>
            <tr>
                <th width="10%">EXP:</th>
                <td width="30%"><?= prettynum($user_class->formattedexp) ?></td>
                <th width="10%">Work EXP:</th>
                <td width="30%"><?= prettynum($user_class->workexp) ?></td>
            </tr>
            <tr>
                <th width="10%">VIP Days:</th>
                <td width="30%">
                    <?= ($user_class->rmdays > 0 ? prettynum($user_class->rmdays) . ' remaining' : '<a href="store.php#VIP">Buy VIP Days</a>') ?>
                </td>
                <th width="10%">Activity Points:</th>
                <td width="30%"><a href="spendactivity.php">Activity Points Store
                        [<?= prettynum($user_class->apoints) ?> Activity Points]</td>
            </tr>
        </table>

        <h1>Stat Information</h1>
        <table id="newtables" style="width:100%;">
            <tr>
                <th width="15%">Strength:</th>
                <td><?= prettynum($user_class->strength) ?></td>
                <td>[Ranked: <?= getRank("$user_class->id", "strength") ?>]</td>
                <th width="15%">Defense:</th>
                <td><?= prettynum($user_class->defense) ?></td>
                <td>[Ranked: <?= getRank("$user_class->id", "defense") ?>]</td>
            </tr>
            <tr>
                <th width="15%">Speed:</th>
                <td><?= prettynum($user_class->speed) ?></td>
                <td>[Ranked: <?= getRank("$user_class->id", "speed") ?>]</td>
                <th width="15%">Agility:</th>
                <td><?= prettynum($user_class->agility) ?></td>
                <td>[Ranked: <?= getRank("$user_class->id", "agility") ?>]</td>
            </tr>
            <tr>
                <th width="15%">Total:</th>
                <td><?= prettynum($user_class->totalattrib) ?></td>
                <td>[Ranked: <?= getRank("$user_class->id", "total") ?>]</td>
            </tr>
        </table>

        <h1>Modded Stats Information</h1>
        <table id="newtables" style="width:100%;">
            <tr>
                <th width="15%">Modded Strength:</th>
                <td width="25%"><?= prettynum($user_class->moddedstrength) ?></td>
                <th width="15%">Modded Defense:</th>
                <td width="25%"><?= prettynum($user_class->moddeddefense) ?></td>
            </tr>
            <tr>
                <th width="15%">Modded Speed:</th>
                <td width="25%"><?= prettynum($user_class->moddedspeed) ?></td>
                <th width="15%">Modded Agility:</th>
                <td width="25%"><?= prettynum($user_class->moddedagility) ?></td>
            </tr>
            <th width="15%">Modded Total:</th>
            <td width="25%"><?= prettynum($user_class->moddedtotalattrib) ?></td>
            <th width="15%"></th>
            <td width="25%"></td>
        </table>

        <h1>Battle Statistics</h1>
        <table id="newtables" style="width:100%;">
            <tr>
                <th width="10%">Won:</th>
                <td width="30%"><?= prettynum($user_class->battlewon) ?></td>
                <th width="10%">Lost:</th>
                <td width="30%"><?= prettynum($user_class->battlelost) ?></td>
            </tr>
            <tr>
                <th width="10%">Total:</th>
                <td width="30%"><?= prettynum($user_class->battletotal) ?></td>
                <th width="10%">Money Gain:</th>
                <td width="30%">$<?= prettynum($user_class->battlemoney) ?></td>
            </tr>
        </table>

        <h1>Crime Rankings</h1>
        <table id="newtables" style="width:100%;">
            <tr>
                <th width="10%">Succeeded:</th>
                <td width="30%"><?= prettynum($user_class->crimesucceeded) ?></td>
                <th width="10%">Failed:</th>
                <td width="30%"><?= prettynum($user_class->crimefailed) ?></td>
            </tr>
            <tr>
                <th width="10%">Total:</th>
                <td width="30%"><?= prettynum($user_class->crimetotal) ?></td>
                <th width="10%">Money Gain:</th>
                <td width="30%">$<?= prettynum($user_class->crimemoney) ?></td>
            </tr>
        </table>

        <h1>Bonus Stats</h1>
        <table id="newtables" style="width:100%;">
            <tr>
                <th width="10%">Total Tax Paid:</th>
                <td width="30%">$<?= prettynum($user_class->totaltax) ?></td>
                <th width="10%">???:</th>
                <td width="30%"><?= prettynum($user_class->crimefailed) ?></td>
            </tr>
        </table>
        <div class="content-head">
            <h1>EXP Calculator</h1>
            <div class="d-flex">
                <div class="flex-fill p-2" style="border-right:thin solid #333;">
                    What level are you aiming for? <input type="text" oninput="calcEXP();" id="levelcalc" size="8" />
                </div>
                <div class="flex-fill p-2">
                    <span id="levelrtn">
                        You need <?= prettynum(experience($user_class->level + 1) - $user_class->exp); ?>
                        EXP to get to level <?= prettynum($user_class->level + 1); ?>.
                    </span>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <div class="d-flex flex-wrap justify-content-center">
                <a href="achievements.php" class="p-2 text-white bg-secondary m-2">[Achievements]</a>
                <a href="translog.php" class="p-2 text-white bg-secondary m-2">[Transfer Logs]</a>
                <a href="attackv2_logs.php" class="p-2 text-white bg-secondary m-2">[Attack Log NEW]</a>
                <a href="attacklog.php" class="p-2 text-white bg-secondary m-2">[Attack Log]</a>
                <a href="defenselog.php" class="p-2 text-white bg-secondary m-2">[Defense Log]</a>
                <a href="muglog.php" class="p-2 text-white bg-secondary m-2">[Mug Log]</a>
                <a href="spylog.php" class="p-2 text-white bg-secondary m-2">[Spy Log]</a>
            </div>
        </div>
    </div>
    <?php include "footer.php"; ?>