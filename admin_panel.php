<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}
?>

<h1>Admin Panel</h1>

<ul>
    <li><a href="adamexp.php">Donations</a></li>
    <li><a href="admin_sendlogs.php">Send Logs</a></li>
    <li><a href="admin_inventory.php">Inventories</a></li>
    <li><a href="admin_mdpl.php">Daily Mission Payout Logs</a></li>
    <li><a href="admin_rbl.php">Raid Battle Logs</a></li>
    <li><a href="admin_gang_vault_logs.php">Gang Vault Logs</a></li>
    <li><a href="admin_packs.php">Pack Logs</a></li>
    <li><a href="admin_pmlogs.php">PM Logs</a></li>
    <li><a href="admin_gang_pms.php">Gang PM Logs</a></li>
    <li><a href="admin_eco.php">Economy Logs</a></li>
    <li><a href="admin_gang_territory_battles.php">Protection Racket Battles</a></li>
    <li><a href="admin_autoclick.php">Click Checks</a></li>
    <li><a href="admin_edit_boss.php">Raid Bosses</a></li>
    <li><a href="admin_attack_logs.php">Attack Logs</a></li>
</ul>

<?php
include 'footer.php';
?>
