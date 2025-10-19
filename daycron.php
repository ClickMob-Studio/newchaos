<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (!isset($_GET['key']) || $_GET['key'] !== 'cron69') {
    http_response_code(403);
    die('Forbidden');
}

require_once __DIR__ . '/dbcon.php';
require_once __DIR__ . '/classes.php';
require_once __DIR__ . '/database/pdo_class.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cron_functions.php';

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

$results = [];
foreach ($steps as [$label, $fn]) {
    if (!is_callable($fn)) {
        $results[] = ['name' => $label, 'ok' => false, 'ms' => 0, 'error' => "Missing function: $fn"];
        if (function_exists('logCronError')) {
            logCronError($label, new RuntimeException("Missing function: $fn"));
        }
        continue;
    }

    try {
        $results[] = runStep($label, $fn);
    } catch (Throwable $e) {
        // Log and record the failure but continue with the next step
        error_log(sprintf("[%s] Step '%s' failed: %s in %s on line %d", date('c'), $label, $e->getMessage(), $e->getFile(), $e->getLine()));
        if (function_exists('logCronError')) {
            logCronError($label, $e);
        }
        $results[] = [
            'name' => $label,
            'ok' => false,
            'ms' => 0,
            'error' => $e->getMessage(),
        ];
    }
}

foreach ([1059, 1034] as $aid) {
    if (function_exists('Send_Event')) {
        Send_Event($aid, 'Daily cron finished (all steps).');
    }
}

header('Content-Type: text/plain; charset=utf-8');
foreach ($results as $r) {
    echo ($r['ok'] ? 'OK   ' : 'FAIL ') . $r['name'] . ' in ' . $r['ms'] . "ms\n";
    if (!$r['ok'] && isset($r['error'])) {
        echo "  -> " . $r['error'] . "\n";
    }
}

Send_Event(1059, 'Daily cron finished (all steps).');