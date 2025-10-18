<?php

include_once 'header.php';
require_once 'includes/functions.php';
require_once 'includes/cron_functions.php';

if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}

$cron_errors = getCronErrors();
$steps = [
    ['A: OTD awards', 'step_otd_awards'],
    ['B: User daily resets', 'step_user_daily_resets'],
    ['C: Jobinfo resets', 'step_jobinfo_resets'],
    ['D: Research & Grotto', 'step_research_and_grotto'],
    ['E: Bank interest', 'step_bank_interest'],
    ['F: Lotteries', 'step_lotteries'],
    ['G: Properties rollover', 'step_properties_rollover'],
    ['H: Gangs OTD', 'step_gangs_otd'],
    ['I: Gang competition', 'step_gang_competition'],
    ['J: Cleanup', 'step_cleanup'],
    ['K: Decrement ban days', 'step_ban_expiry'],
    ['L: Protection Racket Payouts', 'step_gang_territory_payouts'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_reset'])) {
    $fn = trim($_POST['do_reset']);

    echo '<div style="margin:10px 0; padding:10px; border:1px solid #000000; background:#2f2f2f;">';

    if (function_exists($fn)) {
        $res = runStep("[MANUAL ADMIN] {$fn}", $fn);

        if ($res['ok']) {
            echo "<strong>✅ Step executed successfully:</strong> {$fn}<br>";
            if (isset($res['result']) && is_array($res['result'])) {
                echo "<pre style='background:#3f3f3f;padding:5px;border-radius:3px;max-width:600px;white-space:pre-wrap;'>";
                print_r($res['result']);
                echo "</pre>";
            }
        } else {
            echo "<strong style='color:red;'>❌ Error executing:</strong> {$fn}<br>";
            echo htmlspecialchars($res['error'] ?? 'Unknown error');
            // Already logged to cron_logs by runStep()
        }
    } else {
        echo "<strong style='color:red;'>Unknown step:</strong> " . htmlspecialchars($fn);
    }

    echo '</div>';
}
?>

<h1>Resets</h1>

<ul style="list-style:none; padding:0;">
    <?php foreach ($steps as [$label, $fn]): ?>
        <li style="margin-bottom:8px;">
            <form method="post" style="display:inline;">
                <input type="hidden" name="do_reset" value="<?= htmlspecialchars($fn) ?>">
                <button type="submit"
                    style="padding:6px 12px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                    <?= htmlspecialchars($label) ?>
                </button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<br />

<h2>Cron Failures</h2>

<?php
if (!$cron_errors) {
    echo "<p>No logged cron errors 🎉</p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Script</th><th>Error</th><th>Time</th></tr>";
    foreach ($cron_errors as $err) {
        echo "<tr>";
        echo "<td>{$err['id']}</td>";
        echo "<td>{$err['script']}</td>";
        echo "<td><pre style='max-width:600px;white-space:pre-wrap;'>{$err['error']}</pre></td>";
        echo "<td>{$err['datetime']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>

<?php
include 'footer.php';
?>