<?php
include 'header.php';


if (!isset($_GET['id']) || !(int)$_GET['id']) {
    diefun('Something went wrong, if this issue persists please DM an admin.');
}

security($_GET['id']);

$db->query("SELECT * FROM attack_v2 WHERE id = " . $_GET['id'] . ' LIMIT 1');
$db->execute();
$attack = $db->fetch_row(true);

if (!$attack) {
    diefun('Something went wrong, if this issue persists please message an Admin.');
}

if ($user_class->id != $attack['attacking_user_id'] && $user_class->id !== $attack['defending_user_id'] && $user_class->admin < 1) {
    diefun('Your trying to view an attack you was not involved in');
}

$attacker = new User($attack['attacking_user_id']);
$defender = new User($attack['defending_user_id']);
$winner = new User($attack['winning_user_id']);

?>
    <br /><br /><hr />

    <style>
        .imgProfile{
            width:60px;
        }
        .players{
            text-align:center;
        }
        .player{
            display:inline-block;
        }
        .imgProfile.winner {
            border-radius: 8px;
            overflow: hidden;
            border: 3px solid green;
        }

        .imgProfile.looser {
            border-radius: 8px;
            overflow: hidden;
            border: 3px solid red;
        }

        .players {
            display: flex;
            justify-content: space-around;
        }
        .players .player {
            text-align: center;
        }
        .players .player img.imgProfile {
            margin: 6px 0;
        }
        .players .player .playerWin {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .players .player .playerWin.winner {
            color: green;
            text-shadow: 0 0 25px green;
        }
        .players .player .playerWin.looser {
            color: red;
            text-shadow: 0 0 25px red;
        }
        tr.sectionProfile {
            margin: 4px 0;
            border-radius: 5px;
        }

        td.sectionProfile {
            padding: 8px 8px;
            border-bottom: 2px solid #0f0f0f;
            background: #1c1c1c;
        }

        .speedSection {
            background: #1c1c1c;
            border-radius: 5px;
            text-align: center;
            padding: 16px 12px 16px 12px;
            margin: 12px 0;
            width: auto;
        }

        .fightTable {
            border-radius: 5px;
            overflow: hidden;
        }


        .fightTable tr:last-child td {
            border-bottom: 0;
        }
        /* end AttackReport */
    </style>

    <h1>Attack Results</h1>

    <div class='contentBox' >
        <div class="players">
            <div class="player">
                <div class="playerWin <?php if ($attack['attacking_user_id'] == $attack['winning_user_id']): ?> winner <?php else: ?> looser <?php endif; ?>">
                    Attacker
                    <?php if ($attack['attacking_user_id'] == $attack['winning_user_id']): ?>
                        <i class="fa-solid fa-trophy"></i>
                    <?php endif; ?>
                </div>
                <div class="playerName">
                    <?php echo $attacker->formattedname ?>
                </div>
            </div>
            <div class="player">Vs</div>
            <div class="player">
                <div class="playerWin <?php if ($attack['defending_user_id'] == $attack['winning_user_id']): ?> winner <?php else: ?> looser <?php endif; ?>">
                    Defender<br />
                    <?php if ($attack['defending_user_id'] == $attack['winning_user_id']): ?>
                        <i class="fa-solid fa-trophy"></i>
                    <?php endif; ?>
                </div>
                <div class="playerName">
                    <?php echo $defender->formattedname ?>
                </div>
            </div>
        </div>

        <?php
        $db->query("SELECT * FROM attack_turn_log WHERE attack_id = " . $attack['id']);
        $db->execute();
        $attackTurnLogs = $db->fetch_row();
        ?>

        <br />
        <table class="fightTable" style="width: 100%;" cellpadding="0" cellspacing="0">
            <tbody>
            <?php foreach ($attackTurnLogs as $attackTurnLog): ?>
                <?php
                if ($attackTurnLog['attacking_user_id'] == $attacker->id) {
                    $turnAttacker = $attacker;
                    $turnDefender = $defender;
                } else {
                    $turnAttacker = $defender;
                    $turnDefender = $attacker;
                }
                ?>

                <tr class="sectionProfile">
                    <td class="sectionProfile" style="text-align: center;">
                        <?php if ($attackTurnLog['is_first_attack']): ?>
                            Due to their impeccable speed, <strong><?php echo $turnAttacker->formattedname ?></strong> attack first and dealt
                            <strong><?php echo number_format($attackTurnLog['damage'], 0) ?></strong> damage against <strong><?php echo $turnDefender->formattedname ?></strong>.
                        <?php elseif (!$attackTurnLog['is_hit']): ?>
                            <strong><?php echo $turnAttacker->formattedname ?></strong> attempted an attack but <strong><?php echo $turnDefender->formattedname ?></strong> used their agility to avoid the attack, taking 0 damage.
                        <?php elseif ($attackTurnLog['is_critical_hit']): ?>
                            <strong><?php echo $turnAttacker->formattedname ?></strong> launched a <span style="color: red; font-weight: bold;">CRITICAL HIT</span>
                            and dealt <strong><?php echo number_format($attackTurnLog['damage'], 0) ?></strong> damage against
                            <strong><?php echo $turnDefender->formattedname ?></strong>.
                        <?php else: ?>
                            <strong><?php echo $turnAttacker->formattedname ?></strong> attack and dealt <strong><?php echo number_format($attackTurnLog['damage'], 0) ?></strong> damage against
                            <strong><?php echo $turnDefender->formattedname ?></strong>.
                        <?php endif; ?>
                        <br />
                        <br />
                        <strong>Attackers Health:</strong> <?php echo number_format($attackTurnLog['yourhp'], 0) ?><br />
                        <strong>Defenders Health:</strong> <?php echo number_format($attackTurnLog['theirhp'], 0) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="sectionProfile">
                <td class="sectionProfile" style="text-align: center;">
                    After an intense battle, <?php echo $winner->formattedname ?> has come out victorious.
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <br /><hr /><br /><br />

<?php

include 'footer.php';