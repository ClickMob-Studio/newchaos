<?php
include 'header.php';

if ($user_class->gang == 0) {
    diefun('Your not in a gang');
}


if (!isset($_GET['id']) || !(int)$_GET['id']) {
    diefun('Something went wrong, if this issue persists please DM an admin.');
}

security($_GET['id']);

$db->query("SELECT * FROM gang_territory_zone_battle WHERE id = " . $_GET['id'] . ' LIMIT 1');
$db->execute();
$gangTerritoryZoneBattle = $db->fetch_row(true);

if (!$gangTerritoryZoneBattle) {
    diefun('Something went wrong, if this issue persists please message an Admin.');
}

if ($user_class->gang != $gangTerritoryZoneBattle['defending_gang_id'] && $user_class->gang !== $gangTerritoryZoneBattle['attacking_gang_id'] && $user_class->admin < 1) {
    diefun('Your gang is not involved in this racket takeover');
}

if (!$gangTerritoryZoneBattle['is_complete']) {
    header('Location: /gang_territory_battle.php?id=' . $gangTerritoryZoneBattle['id']);
    exit;
}

$db->query("SELECT * FROM gang_territory_zone WHERE id = " . $gangTerritoryZoneBattle['gang_territory_zone_id']);
$db->execute();
$gangTerritoryZone = $db->fetch_row(true);

$attackingGang = new Gang($gangTerritoryZoneBattle['attacking_gang_id']);
$defendingGang = new Gang($gangTerritoryZoneBattle['defending_gang_id']);
$winningGang = new Gang($gangTerritoryZoneBattle['winning_gang_id']);

$strengthAttackingUser = null;
if ($gangTerritoryZoneBattle['strength_attacking_user_id']) {
    $strengthAttackingUser = new User($gangTerritoryZoneBattle['strength_attacking_user_id']);
}
$speedAttackingUser = null;
if ($gangTerritoryZoneBattle['speed_attacking_user_id']) {
    $speedAttackingUser = new User($gangTerritoryZoneBattle['speed_attacking_user_id']);
}
$defenseAttackingUser = null;
if ($gangTerritoryZoneBattle['defense_attacking_user_id']) {
    $defenseAttackingUser = new User($gangTerritoryZoneBattle['defense_attacking_user_id']);
}
$strengthDefendingUser = null;
if ($gangTerritoryZoneBattle['strength_defending_user_id']) {
    $strengthDefendingUser = new User($gangTerritoryZoneBattle['strength_defending_user_id']);
}
$speedDefendingUser = null;
if ($gangTerritoryZoneBattle['speed_defending_user_id']) {
    $speedDefendingUser = new User($gangTerritoryZoneBattle['speed_defending_user_id']);
}
$defenseDefendingUser = null;
if ($gangTerritoryZoneBattle['defense_defending_user_id']) {
    $defenseDefendingUser = new User($gangTerritoryZoneBattle['defense_defending_user_id']);
}

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

    <?php if ($user_class->admin > 0): ?>
        <h1>Admin Logs</h1>

        <ul>
            <li><strong>Initial Wait:</strong> <?php echo $gangTerritoryZoneBattle['initial_wait'] ?></li>
            <li><strong>Attacking Strength:</strong> <?php echo number_format($gangTerritoryZoneBattle['attacking_strength'], 0) ?></li>
            <li><strong>Defending Strength:</strong> <?php echo number_format($gangTerritoryZoneBattle['defending_strength'], 0) ?></li>
            <li><strong>Attacking Speed:</strong> <?php echo number_format($gangTerritoryZoneBattle['attacking_speed'], 0) ?></li>
            <li><strong>Defending Speed:</strong> <?php echo number_format($gangTerritoryZoneBattle['defending_speed'], 0) ?></li>
            <li><strong>Attacking Defense:</strong> <?php echo number_format($gangTerritoryZoneBattle['attacking_defense'], 0) ?></li>
            <li><strong>Defending Defense:</strong> <?php echo number_format($gangTerritoryZoneBattle['defending_defense'], 0) ?></li>
        </ul>
    <?php endif; ?>

    <h1>Protection Racket Battle Result</h1>

    <div class='contentBox' >
        <div class="players">
            <div class="player">
                <div class="playerWin <?php if ($gangTerritoryZoneBattle['attacking_gang_id'] == $gangTerritoryZoneBattle['winning_gang_id']): ?> winner <?php else: ?> looser <?php endif; ?>">
                    Attacking Gang
                    <?php if ($gangTerritoryZoneBattle['attacking_gang_id'] == $gangTerritoryZoneBattle['winning_gang_id']): ?>
                        <i class="fa-solid fa-trophy"></i>
                    <?php endif; ?>
                    <br />
                    <?php echo $attackingGang->formattedname ?>
                </div>
                <div class="playerName">
                    <strong>Strength Attacker:</strong>
                    <?php if ($strengthAttackingUser): ?>
                        <?php echo $strengthAttackingUser->formattedname ?>
                        <?php if ($gangTerritoryZoneBattle['is_strength_attacking_user']): ?>
                            <i class="fa-solid fa-check text-success" title="This mobster was available for the fight"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times text-danger" title="This mobster was unavailable for the fight"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                    <br />

                    <strong>Defense Attacker:</strong>
                    <?php if ($defenseAttackingUser): ?>
                        <?php echo $defenseAttackingUser->formattedname ?>
                        <?php if ($gangTerritoryZoneBattle['is_defense_attacking_user']): ?>
                            <i class="fa-solid fa-check text-success" title="This mobster was available for the fight"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times text-danger" title="This mobster was unavailable for the fight"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                    <br />

                    <strong>Speed Attacker:</strong>
                    <?php if ($speedAttackingUser): ?>
                        <?php echo $speedAttackingUser->formattedname ?>
                        <?php if ($gangTerritoryZoneBattle['is_speed_attacking_user']): ?>
                            <i class="fa-solid fa-check text-success" title="This mobster was available for the fight"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times text-danger" title="This mobster was unavailable for the fight"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </div>
            </div>
            <div class="player">Vs</div>
            <div class="player">
                <div class="playerWin <?php if ($gangTerritoryZoneBattle['defending_gang_id'] == $gangTerritoryZoneBattle['winning_gang_id']): ?> winner <?php else: ?> looser <?php endif; ?>">
                    Defending Gang<br />
                    <?php if ($gangTerritoryZoneBattle['defending_gang_id'] == $gangTerritoryZoneBattle['winning_gang_id']): ?>
                        <i class="fa-solid fa-trophy"></i>
                    <?php endif; ?>
                    <?php echo $defendingGang->formattedname ?>
                </div>
                <div class="playerName">
                    <strong>Strength Defender:</strong>
                    <?php if ($strengthDefendingUser): ?>
                        <?php echo $strengthDefendingUser->formattedname ?>
                        <?php if ($gangTerritoryZoneBattle['is_strength_defending_user']): ?>
                            <i class="fa-solid fa-check text-success" title="This mobster was available for the fight"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times text-danger" title="This mobster was unavailable for the fight"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                    <br />

                    <strong>Defense Defender:</strong>
                    <?php if ($defenseDefendingUser): ?>
                        <?php echo $defenseDefendingUser->formattedname ?>
                        <?php if ($gangTerritoryZoneBattle['is_defense_defending_user']): ?>
                            <i class="fa-solid fa-check text-success" title="This mobster was available for the fight"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times text-danger" title="This mobster was unavailable for the fight"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                    <br />

                    <strong>Speed Defender:</strong>
                    <?php if ($speedDefendingUser): ?>
                        <?php echo $speedDefendingUser->formattedname ?>
                        <?php if ($gangTerritoryZoneBattle['is_speed_defending_user']): ?>
                            <i class="fa-solid fa-check text-success" title="This mobster was available for the fight"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-times text-danger" title="This mobster was unavailable for the fight"></i>
                        <?php endif; ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
        $db->query("SELECT * FROM gang_territory_zone_battle_log WHERE gang_territory_zone_battle_id = " . $gangTerritoryZoneBattle['id']);
        $db->execute();
        $gangTerritoryZoneBattleLogs = $db->fetch_row();
        ?>

        <br />
        <table class="fightTable" style="width: 100%;" cellpadding="0" cellspacing="0">
            <tbody>
            <?php foreach ($gangTerritoryZoneBattleLogs as $gangTerritoryZoneBattleLog): ?>
                <?php
                $btzblAttackingGang = new Gang($gangTerritoryZoneBattleLog['attacking_gang_id']);
                $btzblDefendingGang = new Gang($gangTerritoryZoneBattleLog['defending_gang_id']);
                ?>

                <tr class="sectionProfile">
                    <td class="sectionProfile" style="text-align: center;">
                        <?php if ($gangTerritoryZoneBattleLog['is_first_attack']): ?>
                            Due to their impeccable speed, <strong><?php echo $btzblAttackingGang->formattedname ?></strong> attack first and deal
                            <strong><?php echo number_format($gangTerritoryZoneBattleLog['damage'], 0) ?></strong> damage against <strong><?php echo $btzblDefendingGang->formattedname ?></strong>.
                        <?php else: ?>
                            <strong><?php echo $btzblAttackingGang->formattedname ?></strong> attack and deal <strong><?php echo number_format($gangTerritoryZoneBattleLog['damage'], 0) ?></strong> damage against
                            <strong><?php echo $btzblDefendingGang->formattedname ?></strong>.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="sectionProfile">
                <td class="sectionProfile" style="text-align: center;">
                    <?php if ($winningGang->id == $attackingGang->id): ?>
                        After an intense battle, <?php echo $winningGang->formattedname ?> have come out victorious, successfully taking
                        over the territory <?php echo $gangTerritoryZone['name'] ?>.
                    <?php else: ?>
                        After an intense battle, <strong><?php echo $winningGang->formattedname ?></strong> have come out victorious, successfully defending
                        the territory <strong><?php echo $gangTerritoryZone['name'] ?></strong>.
                    <?php endif; ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

<br /><hr /><br /><br />

<?php
include("gangheaders.php");
include 'footer.php';