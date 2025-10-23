<?php

include 'header.php';
require_once 'includes/functions.php';

if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

if (isset($_POST['flush_cache'])) {
    if ($cache->flushAll()) {
        echo '<div class="dcPanel p-2 mb-4 d-flex align-items-center justify-content-center"><p>Cache flushed successfully.</p></div>';
    } else {
        echo '<div class="dcPanel p-2 mb-4 d-flex align-items-center justify-content-center"><p>Error flushing cache.</p></div>';
    }
}

if (isset($_POST['flush_db'])) {
    $total = cleanOldDBEntries();
    if ($total >= 0) {
        echo '<div class="dcPanel p-2 mb-4 d-flex align-items-center justify-content-center"><p>Removed ' . number_format($total) . ' old database entries.</p></div>';
    } else {
        echo '<div class="dcPanel p-2 mb-4 d-flex align-items-center justify-content-center"><p>Error cleaning old database entries.</p></div>';
    }
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
    <li><a href="admin_item_check.php">Item Check</a></li>
    <li><a href="admin_mass_pm.php">Mass PM Users</a></li>
    <li><a href="admin_resets.php">Daily Reset Management</a></li>
    <li><a href="admin_discord.php">Manage Discord Commands</a></li>
</ul>

<br />

<h2>Cache Management</h2>
<form method="post" id="flush-cache">
    <input type="submit" name="flush_cache" value="Flush Cache">
</form>

<br />

<h2>Database Management</h2>
<form method="post" id="flush-db">
    <input type="submit" name="flush_db" value="Clean Old Database Entries">
</form>

<script>
    document.querySelector('#flush-cache').addEventListener('submit', function (event) {
        if (!confirm('Are you sure you want to flush the cache? This action cannot be undone.')) {
            event.preventDefault();
        }
    });

    document.querySelector('#flush-db').addEventListener('submit', function (event) {
        if (!confirm('Are you sure you want to clean old database entries? This action cannot be undone.')) {
            event.preventDefault();
        }
    });
</script>

<?php
include 'footer.php';
?>