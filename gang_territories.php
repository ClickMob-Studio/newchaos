<?php
include 'header.php';

if ($user_class->gang == 0) {
    diefun('Your not in a gang');
}

$gang = new Gang($user_class->gang);
$user_rank = new GangRank($user_class->grank);

$db->query("SELECT * FROM gang_territory_zone_battle WHERE attacking_gang_id = " . $user_class->gang . " AND (is_complete IS NULL OR is_complete = 0)");
$db->execute();
$attackingGangTerritoryBattles = $db->fetch_row();

if (isset($_GET['action']) && $_GET['action'] === 'claim' && isset($_GET['id']) && (int)$_GET['id']) {
    $claimId = (int)$_GET['id'];

    security($claimId);

    $db->query("SELECT * FROM gang_territory_zone WHERE id = " . $claimId);
    $db->execute();
    $gangTerritoryZone = $db->fetch_row(true);

    if ($gangTerritoryZone['owned_by_gang_id']) {
        diefun('You can\'t claim a protection racket that is already owned by a gang.');
    }

    if ($user_rank->gangwars != 1) {
        diefun('You don\'t have permission to claim a protection racket');
    }

    $db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id = " . $user_class->gang);
    $db->execute();
    $ownedGangTerritoryZones = $db->fetch_row();

    if (count($ownedGangTerritoryZones) >= 4) {
        diefun('Gangs are limited to owning 4 territories. You must loose a territory before you can claim another.');
    }

    $shieldTime = time() + 7200;
    $db->query("UPDATE gang_territory_zone SET owned_by_gang_id = " . $user_class->gang . ", shield_time = " . $shieldTime . " WHERE id = " . $gangTerritoryZone['id']);
    $db->execute();

    foreach ($gang->memberids as $memberids) {
        Send_Event($memberids['id'], 'Your gang has claimed the protection racket ' . $gangTerritoryZone['name'] . '. Keep an eye out for any potential takeover attempts from other gangs.');
    }

    $db->query("INSERT INTO gang_territory_zone_history (gang_territory_zone__id, gang_id, takeover_time) VALUES (" . $gangTerritoryZone['id'] . ", " . $user_class->gang . ", " . time() . ");");
    $db->execute();

    diefun('You have successfully claimed the protection racket: ' . $gangTerritoryZone['name'] . '. <a href="gang_territories.php">Go Back</a>');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] === 'attack' && isset($_GET['id']) && (int)$_GET['id']) {
    $attackId = (int)$_GET['id'];

    security($attackId);

    $db->query("SELECT * FROM gang_territory_zone WHERE id = " . $attackId);
    $db->execute();
    $gangTerritoryZone = $db->fetch_row(true);

    $db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id = " . $user_class->gang);
    $db->execute();
    $ownedGangTerritoryZones = $db->fetch_row();

    if (count($ownedGangTerritoryZones) >= 4) {
        diefun('Gangs are limited to owning 4 territories. You must loose a territory before you can claim another.');
    }

    $gang = $user_class->gang;

    if ($user_rank->gangwars != 1) {
        diefun('You don\'t have permission to do this');
    }

    if (!$gangTerritoryZone['owned_by_gang_id']) {
        diefun('You can only attempt a takeover on a territory that has already been claimed.');
    }

    if ($gangTerritoryZone['owned_by_gang_id'] == $user_class->gang) {
        diefun('You can\'t takeover a territory that your regiment already owns.');
    }

    if ($gangTerritoryZone['shield_time'] > time()) {
        diefun('You can\'t takeover a territory that is under shield.');
    }

    $db->query("SELECT id FROM gang_territory_zone_battle WHERE gang_territory_zone_id = " . $gangTerritoryZone['id'] . " AND (is_complete IS NULL OR is_complete = 0)");
    $db->execute();
    $activeGangTerritoryBattles = $db->fetch_row();

    if ($activeGangTerritoryBattles) {
        diefun('You can\'t takeover a Protection Racket that is already in a takeover attempt.');
    }

    if (count($attackingGangTerritoryBattles) > 0) {
        diefun('Your gang can only attempt one takeover at a time.');
    }

    $db->query("
      INSERT INTO 
        gang_territory_zone_battle (gang_territory_zone_id, attacking_gang_id, defending_gang_id, time_started)
      VALUES
        (" . $gangTerritoryZone['id'] . ", " . $user_class->gang . ", " . $gangTerritoryZone['owned_by_gang_id'] . ", " . time() . ")
    ");
    $db->execute();

    $defendingGang = new Gang($gangTerritoryZone['owned_by_gang_id']);
    foreach ($defendingGang->memberids as $memberids) {
        Send_Event($memberids['id'], 'Mobster, ready yourself for battle! A gang is attempting a takeover on one of your protection rackets. <a href="gang_territories.php">Go to Protection Rackets</a>');
    }

    foreach ($gang->memberids as $memberids) {
        Send_Event($memberids['id'], 'Mobster, ready yourself for battle! Your gang is attempting a takeover of a protection racket. <a href="gang_territories.php">Go to Protection Rackets</a>');
    }

    diefun('You have successfully initiated a takeover of the Protection Racket. All gang members will be informed to prepare for the battle. The battle will commence in 30 minutes time.');
}

$db->query("SELECT * FROM gang_territory_zone ORDER BY city_id ASC");
$db->execute();
$gangTerritoryZones = $db->fetch_row();

$db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id = " . $user_class->gang);
$db->execute();
$ownedGangTerritoryZones = $db->fetch_row();

?>
<div class='box_top'>Protection Rackets</div>
<div class='box_middle'>
    <div class='pad'>
        <p>
            Welcome to Protection Rackets, here you can see all the protection rackets for your gang and other gangs. Protection Rackets provide you with a daily payout that goes to each member at rollover.
        </p>
        <p>
            You'll need to be vigilant with your protection rackets, another gang can try to takeover your protection racket by starting a battle. Once a battle has been initiated, each gang has 30 minutes to add a
            member into the strength, defense and speed stat slot to take part in the battle on behalf of the gang. If the slots aren't filled then the stat will run as 0. You also need to make sure each member in
            the battle is in the correct city for the Protection Racket when the battle takes part otherwise they'll miss it!
        </p>
        <p>
            Winning protection racket battles will also give each member of the winning side an EXP payout.
        </p>
        <p>Daily payouts on Protection Rackets will change on occassion.</p>


        <?php if ($attackingGangTerritoryBattles): ?>
            <h2>Active Protection Racket Takeovers</h2>
            <div class="contentBox">
                <table class="cleanTable">
                    <thead>
                    <tr>
                        <th>Territory</th>
                        <th>Defending Gang</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($attackingGangTerritoryBattles as $attackingGangTerritoryBattle): ?>
                        <?php
                        $db->query("SELECT name FROM gang_territory_zone WHERE id = " . $attackingGangTerritoryBattle['gang_territory_zone_id'] . " LIMIT 1");
                        $db->execute();
                        $gName = $db->fetch_single();

                        $defendingGang = new Gang($attackingGangTerritoryBattle['defending_gang_id']);
                        ?>
                        <tr>
                            <td><?php echo $gName ?></td>
                            <td><?php echo $defendingGang->formattedname ?></td>
                            <td>
                                <?php getTimeRemainingForDisplay($attackingGangTerritoryBattle['time_started']) ?>
                            </td>
                            <td>
                                <a href="gang_territory_battle.php?id=<?php echo $attackingGangTerritoryBattle['id'] ?>" class="button">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <br />
        <h2>Your Protection Rackets</h2>
        <div class="table-container">
            <table class="new_table" id="newtables" style="width:100%;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>City</th>
                        <th>Payout</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ownedGangTerritoryZones as $gangTerritoryZone): ?>
                        <tr>
                            <td>
                                <?php echo $gangTerritoryZone['name'] ?>

                                <?php
                                if ($gangTerritoryZone['shield_time'] > time()) {
                                    $remaining = $gangTerritoryZone['shield_time'] - time();
                                    $remaining = $remaining / 60;
                                } else {
                                    $remaining = 0;
                                }
                                ?>
                                <?php if ($remaining > 0): ?>
                                    <span style="color: #FF0000"><i class="fa-solid fa-shield" title="<?php echo number_format($remaining, 0) ?> minutes shield time remaining"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo getCityNameByID($gangTerritoryZone['city_id']) ?></td>
                            <td>
                                <?php if ($gangTerritoryZone['daily_points_payout'] > 0): ?>
                                    - <?php echo number_format($gangTerritoryZone['daily_points_payout'], 0) ?> Points<br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_money_payout'] > 0): ?>
                                    - $<?php echo number_format($gangTerritoryZone['daily_money_payout'], 0) ?><br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_raid_tokens_payout'] > 0): ?>
                                    - <?php echo $gangTerritoryZone['daily_raid_tokens_payout'] ?> Raid Tokens<br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_exp_payout'] > 0): ?>
                                    - <?php echo number_format($gangTerritoryZone['daily_exp_payout'], 0) ?> EXP<br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_item_payout'] > 0): ?>
                                    - <?php echo Get_Item_name($gangTerritoryZone['daily_item_payout']); ?><br />
                                <?php endif; ?>
                            </td>
                            <td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />

        <h2>All Protection Rackets</h2>
        <div class="table-container">
            <table class="new_table" id="newtables" style="width:100%;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>City</th>
                        <th>Owned By Gang</th>
                        <th>Payout</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gangTerritoryZones as $gangTerritoryZone): ?>
                        <?php
                        $db->query("SELECT * FROM gang_territory_zone_battle WHERE (is_complete = 0 OR is_complete IS NULL) AND gang_territory_zone_id = " . $gangTerritoryZone['id']);
                        $db->execute();
                        $activegtzb = $db->fetch_row();
                        ?>
                        <tr>
                            <td>
                                <?php echo $gangTerritoryZone['name'] ?>

                                <?php
                                if ($gangTerritoryZone['shield_time'] > time()) {
                                    $remaining = $gangTerritoryZone['shield_time'] - time();
                                    $remaining = $remaining / 60;
                                } else {
                                    $remaining = 0;
                                }
                                ?>
                                <?php if ($remaining > 0): ?>
                                    <span style="color: #FF0000"><i class="fa-solid fa-shield" title="<?php echo number_format($remaining, 0) ?> minutes shield time remaining"></i></span>
                                <?php endif; ?>

                                <?php if ($activegtzb): ?>
                                    <span style="color: #FF0000"><i class="fa-solid fa-gun" title="Protection Racket is in a takeover attempt"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo getCityNameByID($gangTerritoryZone['city_id']) ?></td>
                            <td>
                                <?php if ($gangTerritoryZone['owned_by_gang_id'] > 0): ?>
                                    <?php $ownedByGang = new Gang($gangTerritoryZone['owned_by_gang_id']) ?>
                                    <?php echo $ownedByGang->formattedname ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($gangTerritoryZone['daily_points_payout'] > 0): ?>
                                    - <?php echo number_format($gangTerritoryZone['daily_points_payout'], 0) ?> Points<br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_money_payout'] > 0): ?>
                                    - $<?php echo number_format($gangTerritoryZone['daily_money_payout'], 0) ?><br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_raid_tokens_payout'] > 0): ?>
                                    - <?php echo $gangTerritoryZone['daily_raid_tokens_payout'] ?> Raid Tokens<br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_exp_payout'] > 0): ?>
                                    - <?php echo number_format($gangTerritoryZone['daily_exp_payout'], 0) ?> EXP<br />
                                <?php endif; ?>
                                <?php if ($gangTerritoryZone['daily_item_payout'] > 0): ?>
                                    - <?php echo Get_Item_name($gangTerritoryZone['daily_item_payout']); ?><br />
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($gangTerritoryZone['owned_by_gang_id'] > 0): ?>
                                    <?php if ($gangTerritoryZone['owned_by_gang_id'] == $user_class->gang): ?>
                                        <?php if (getActiveGangTerritoryZoneBattle($gangTerritoryZone)): ?>
                                            <?php $activeGangTerritoryBattle = getActiveGangTerritoryZoneBattle($gangTerritoryZone); ?>
                                            <a href="gang_territory_battle.php?id=<?php echo $activeGangTerritoryBattle['id'] ?>" class="button">Defend</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($user_rank->gangwars = 1): ?>
                                            <a href="gang_territories.php?action=attack&id=<?php echo $gangTerritoryZone['id'] ?>" class="button">Attack</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($user_rank->gangwars = 1): ?>
                                        <a href="gang_territories.php?action=claim&id=<?php echo $gangTerritoryZone['id'] ?>" class="button">Claim</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<br /><br /><hr />

<?php
include("gangheaders.php");
include 'footer.php';

