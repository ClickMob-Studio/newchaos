<?php

include_once 'header.php';
require_once 'includes/functions.php';

define('DISCORD_GUILD_ID', '1222776672408043651');
define('DISCORD_APP_ID', '1429601793544945775');
define('DISCORD_BOT_TOKEN', 'Bot MTQyOTYwMTc5MzU0NDk0NTc3NQ.GTH4CZ.V4YjWVpSJT20rbIr3g2Z9VsunPla8-uGZWWluA');


$db->query("SELECT * FROM discord_commands");
$db->execute();
$commands = $db->fetch_row();

?>

<h1>Discord Slash Commands</h1>

<div class="table-container">
    <table class="new_table" id="commands_table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Command</th>
                <th>Description</th>
                <th>Response</th>
                <th>Handler</th>
                <th>Active</th>
                <th>Ephemeral</th>
                <th>Last Synced</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($commands)): ?>
                <tr>
                    <td colspan="9" style="text-align:center;">No commands found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($commands as $cmd): ?>
                    <tr>
                        <td><?= htmlspecialchars($cmd['id']) ?></td>
                        <td><strong>/<?= htmlspecialchars($cmd['command_name']) ?></strong></td>
                        <td><?= htmlspecialchars($cmd['description']) ?></td>
                        <td>
                            <div style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                title="<?= htmlspecialchars($cmd['response'] ?? '') ?>">
                                <?= htmlspecialchars($cmd['response'] ?? '') ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($cmd['handler'] ?? '') ?></td>
                        <td style="text-align:center;">
                            <?= $cmd['is_active'] ? '✅' : '❌' ?>
                        </td>
                        <td style="text-align:center;">
                            <?= $cmd['is_ephemeral'] ? '👀 Only user' : '🌐 Public' ?>
                        </td>
                        <td><?= htmlspecialchars($cmd['last_synced_at'] ?? '-') ?></td>
                        <td style="text-align:center;">
                            <a href="edit_command.php?id=<?= urlencode($cmd['id']) ?>" class="btn btn-small">Edit</a>
                            <a href="sync_command.php?id=<?= urlencode($cmd['id']) ?>" class="btn btn-small">Sync</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .new_table th,
    .new_table td {
        border: 1px solid #444;
        padding: 6px 10px;
        text-align: left;
    }

    .new_table th {
        background-color: #222;
        color: #fff;
    }

    .btn {
        background-color: #5865F2;
        color: white;
        padding: 3px 6px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.8rem;
    }

    .btn:hover {
        background-color: #4752C4;
    }

    .btn-small {
        margin: 0 2px;
    }
</style>

<?php include 'footer.php'; ?>