<?php
include 'header.php';

if ($user_class->gang == 0) {
    diefun('Your not in a gang');
}

$db->query("SELECT * FROM gang_territory_zone");
$db->execute();
$gangTerritoryZones = $db->fetch_row();

$db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id = " . $user_class->gang);
$db->execute();
$ownedGangTerritoryZones = $db->fetch_row();

?>
<div class='box_top'>Protection Rackets</div>
<div class='box_middle'>
    <div class='pad'>
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
                    <?php foreach ($gangTerritoryZones as $gangTerritoryZone): ?>
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
        <br /><hr />

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
                                <!-- TODO: Permissions -->
                                <?php if ($gangTerritoryZone['owned_by_gang_id'] > 0): ?>
                                    <?php if ($gangTerritoryZone['owned_by_gang_id'] == $user_class->gang): ?>
                                        <?php if (getActiveGangTerritoryZoneBattle($gangTerritoryZone['id'])): ?>
                                            <?php $activeGangTerritoryBattle = getActiveGangTerritoryZoneBattle($gangTerritoryZone['id']); ?>
                                            <a href="gang_territory_battle.php?id=<?php echo $activeGangTerritoryBattle['id'] ?>" class="button">Defend</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="gang_territories.php?action=attack&id=<?php echo $gangTerritoryZone['id'] ?>" class="button">Attack</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="gang_territories.php?action=claim&id=<?php echo $gangTerritoryZone['id'] ?>" class="button">Claim</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<br /><hr />

<?php
include("gangheaders.php");
include 'footer.php';

