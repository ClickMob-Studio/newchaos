<?php
include 'header.php';

if ($user_class->gang == 0) {
    diefun('Your not in a gang');
}

$gangTerritoryZoneBattle = null;
if (isset($_GET['id']) && (int)$_GET['id']) {
    security($_GET['id']);

    $db->query("SELECT * FROM gang_territory_zone_battle WHERE id = " . (int)$_GET['id'] . " LIMIT 1");
    $db->execute();
    $gangTerritoryZoneBattle = $db->fetch_row(true);
}

if (!$gangTerritoryZoneBattle) {
    diefun('Something went wrong, if this issue persists please message an Admin.');
}

if ($user_class->gang != $gangTerritoryZoneBattle['defending_gang_id'] && $user_class->gang !== $gangTerritoryZoneBattle['attacking_gang_id']) {
    diefun('Your regiment is not involved in this territory battle');
}

if ($gangTerritoryZoneBattle['is_complete']) {
    header('Location: /gang_territory_battle_result.php?id=' . $gangTerritoryZoneBattle->id);
    exit;
}

$attackingGang = new Gang($gangTerritoryZoneBattle['attacking_gang_id']);
$defendingGang = new Gang($gangTerritoryZoneBattle['defending_gang_id']);

if (isset($_GET['action']) && $_GET['action'] === 'join' && isset($_GET['spot'])) {
    $spot = $_GET['spot'];
    if ($user_class->gang == $attackingGang->id) {
        $validSpots = array(
            'strength_attacker',
            'defense_attacker',
            'speed_attacker'
        );
    } else {
        $validSpots = array(
            'strength_defender',
            'defense_defender',
            'speed_defender'
        );
    }

    $db->query("
        SELECT 
            id 
        FROM 
            gang_territory_zone_battle
        WHERE
            (strength_defending_user_id = :user_id OR
            defense_defending_user_id = :user_id OR
            speed_defending_user_id = :user_id OR
            strength_attacking_user_id = :user_id OR
            defense_attacking_user_id = :user_id OR
            speed_attacking_user_id = :user_id) AND (is_complete IS NULL OR is_complete = 0)
    ");
    $db->bind(':user_id', $user_class->id);
    $db->execute();
    $userActiveGangTerritoryZoneBattles = $db->fetch_row();

    if ($userActiveGangTerritoryZoneBattles) {
        diefun('You can\'t join another regiment territory battle until your current one has taken place.');
    }

    if ($gangTerritoryZoneBattle['is_complete']) {
        diefun('This battle has already taken place.');
    }

    if (!in_array($spot, $validSpots)) {
        diefun('Please ensure you are trying to fill a valid spot.');
    }

    $url = 'gang_territory_battle.php?id=' . $gangTerritoryZoneBattle['id'];
    if ($spot == 'strength_attacker') {
        if ($gangTerritoryZoneBattle['strength_attacking_user_id']) {
            diefun('Someone has already occupied the spot your trying to fill.');
        } else {
            $db->query("UPDATE gang_territory_zone_battle SET strength_attacking_user_id = " . $user_class->id);
            $db->execute();

            header('Location: ' . $url);
        }
    }
    if ($spot == 'defense_attacker') {
        if ($gangTerritoryZoneBattle['defense_attacking_user_id']) {
            diefun('Someone has already occupied the spot your trying to fill.');
        } else {
            $db->query("UPDATE gang_territory_zone_battle SET defense_attacking_user_id = " . $user_class->id);
            $db->execute();

            header('Location: ' . $url);
        }
    }
    if ($spot == 'speed_attacker') {
        if ($gangTerritoryZoneBattle['speed_attacking_user_id']) {
            diefun('Someone has already occupied the spot your trying to fill.');
        } else {
            $db->query("UPDATE gang_territory_zone_battle SET speed_attacking_user_id = " . $user_class->id);
            $db->execute();

            header('Location: ' . $url);
        }
    }

    if ($spot == 'strength_defender') {
        if ($gangTerritoryZoneBattle['strength_defending_user_id']) {
            diefun('Someone has already occupied the spot your trying to fill.');
        } else {
            $db->query("UPDATE gang_territory_zone_battle SET strength_defending_user_id = " . $user_class->id);
            $db->execute();

            header('Location: ' . $url);
        }
    }
    if ($spot == 'defense_defender') {
        if ($gangTerritoryZoneBattle['defense_defending_user_id']) {
            diefun('Someone has already occupied the spot your trying to fill.');
        } else {
            $db->query("UPDATE gang_territory_zone_battle SET defense_defending_user_id = " . $user_class->id);
            $db->execute();

            header('Location: ' . $url);
        }
    }
    if ($spot == 'speed_defender') {
        if ($gangTerritoryZoneBattle['speed_defending_user_id']) {
            diefun('Someone has already occupied the spot your trying to fill.');
        } else {
            $db->query("UPDATE gang_territory_zone_battle SET speed_defending_user_id = " . $user_class->id);
            $db->execute();

            header('Location: ' . $url);
        }
    }
}

$strengthAttackingUser = null;
if ($gangTerritoryZoneBattle['strength_attacking_user_id']) {
    $strengthAttackingUser = new User($gangTerritoryZoneBattle['strength_attacking_user_id']);
}
$defenseAttackingUser = null;
if ($gangTerritoryZoneBattle['defense_attacking_user_id']) {
    $defenseAttackingUser = new User($gangTerritoryZoneBattle['defense_attacking_user_id']);
}
$speedAttackingUser = null;
if ($gangTerritoryZoneBattle['speed_attacking_user_id']) {
    $speedAttackingUser = new User($gangTerritoryZoneBattle['speed_attacking_user_id']);
}

$strengthDefendingUser = null;
if ($gangTerritoryZoneBattle['strength_defending_user_id']) {
    $strengthDefendingUser = new User($gangTerritoryZoneBattle['strength_defending_user_id']);
}
$defenseDefendingUser = null;
if ($gangTerritoryZoneBattle['defense_defending_user_id']) {
    $defenseDefendingUser = new User($gangTerritoryZoneBattle['defense_defending_user_id']);
}
$speedDefendingUser = null;
if ($gangTerritoryZoneBattle['speed_defending_user_id']) {
    $speedDefendingUser = new User($gangTerritoryZoneBattle['speed_defending_user_id']);
}
?>

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
        /* end AttackReport */
    </style>

    <h1>Protection Racket Takeover Preperation</h1>

    <div class='contentBox' >

        <div class="players">
            <div class="player">
                <div class="playerWin winner">
                    Attacking Gang<br />
                    <?php echo $attackingGang->formattedname ?>
                </div>
                <div class="playerName">
                    <strong>Strength Attacker:</strong>
                    <?php if ($strengthAttackingUser): ?>
                        <?php echo $strengthAttackingUser->formattedname ?>
                    <?php else: ?>
                        <?php if ($gangTerritoryZoneBattle['attacking_gang_id'] == $user_class->gang): ?>
                            <a href="gang_territory_battle.php?id=<?php echo $gangTerritoryZoneBattle['id'] ?>&action=join&spot=strength_attacker" style="color: #ffd800;">Fill Spot</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    <?php endif; ?>
                    <br />

                    <strong>Defense Attacker:</strong>
                    <?php if ($defenseAttackingUser): ?>
                        <?php echo $defenseAttackingUser->formattedname ?>
                    <?php else: ?>
                        <?php if ($gangTerritoryZoneBattle['attacking_gang_id'] == $user_class->gang): ?>
                            <a href="gang_territory_battle.php?id=<?php echo $gangTerritoryZoneBattle['id'] ?>&action=join&spot=defense_attacker" style="color: #ffd800;">Fill Spot</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    <?php endif; ?>
                    <br />

                    <strong>Speed Attacker:</strong>
                    <?php if ($speedAttackingUser): ?>
                        <?php echo $speedAttackingUser->formattedname ?>
                    <?php else: ?>
                        <?php if ($gangTerritoryZoneBattle['attacking_gang_id'] == $user_class->gang): ?>
                            <a href="gang_territory_battle.php?id=<?php echo $gangTerritoryZoneBattle['id'] ?>&action=join&spot=speed_attacker" style="color: #ffd800;">Fill Spot</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="player">Vs</div>
            <div class="player">
                <div class="playerWin looser">
                    Defending Gang
                    <br />
                    <?php echo $defendingGang->formattedname ?>
                </div>
                <div class="playerName">
                    <strong>Strength Defender:</strong>
                    <?php if ($strengthDefendingUser): ?>
                        <?php echo $strengthDefendingUser->formattedname ?>
                    <?php else: ?>
                        <?php if ($gangTerritoryZoneBattle['defending_gang_id'] == $user_class->gang): ?>
                            <a href="gang_territory_battle.php?id=<?php echo $gangTerritoryZoneBattle['id'] ?>&action=join&spot=strength_defender" style="color: #ffd800;">Fill Spot</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    <?php endif; ?>
                    <br />

                    <strong>Defense Defender:</strong>
                    <?php if ($defenseDefendingUser): ?>
                        <?php echo $defenseDefendingUser->formattedname ?>
                    <?php else: ?>
                        <?php if ($gangTerritoryZoneBattle['defending_gang_id'] == $user_class->gang): ?>
                            <a href="gang_territory_battle.php?id=<?php echo $gangTerritoryZoneBattle['id'] ?>&action=join&spot=defense_defender" style="color: #ffd800;">Fill Spot</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    <?php endif; ?>
                    <br />

                    <strong>Speed Defender:</strong>
                    <?php if ($speedDefendingUser): ?>
                        <?php echo $speedDefendingUser->formattedname ?>
                    <?php else: ?>
                        <?php if ($gangTerritoryZoneBattle['defending_gang_id'] == $user_class->gang): ?>
                            <a href="gang_territory_battle.php?id=<?php echo $gangTerritoryZoneBattle['id'] ?>&action=join&spot=speed_defender" style="color: #ffd800;">Fill Spot</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr />
        <br />
        <center>
            <p style="color: green; font-weight:bold;">
                <?php
                // Battles take place 30 minutes after they are started
                $seconds = 30 * 60;

                $battleInitiationTime = $gangTerritoryZoneBattle['time_started'] + $seconds;
                $bit = $battleInitiationTime - time();

                $battleInitiationTimeForDisplay = number_format(($bit / 60), 0) . ' minutes until battle';
                ?>


                <?php if ($battleInitiationTime > time()): ?>
                    There is currently <?php echo $battleInitiationTimeForDisplay ?>. Any spots not filled will be left empty for the battle  resulting in a zero value for the empty stat.
                <?php else: ?>
                    This battle is ready to take place. All gang members will receive an event once the battle has been complete.
                <?php endif; ?>
            </p>
        </center>


    </div>

<?php
include("gangheaders.php");
include 'footer.php';
