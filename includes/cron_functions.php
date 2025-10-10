<?php

require_once dirname(__DIR__) . '/dbcon.php';
require_once dirname(__DIR__) . '/classes.php';
require_once dirname(__DIR__) . '/database/pdo_class.php';
require_once dirname(__DIR__) . '/includes/functions.php';

function getCronErrors(?string $script = null, int $limit = 50): array
{
    global $db;

    if ($script) {
        $db->query("SELECT * FROM cron_logs WHERE script = ? ORDER BY id DESC LIMIT $limit");
        $rows = $db->fetch_row(false, [$script]);
    } else {
        $db->query("SELECT * FROM cron_logs ORDER BY id DESC LIMIT $limit");
        $rows = $db->fetch_row();
    }

    foreach ($rows as &$row) {
        $row['datetime'] = date('Y-m-d H:i:s', (int) $row['timestamp']);
    }

    return $rows ?: [];
}

function todayYmd(): string
{
    return (new DateTimeImmutable('today'))->format('Y-m-d');
}

function cutoffTs(int $days): int
{
    return time() - $days * 24 * 60 * 60;
}

function step_otd_awards(): array
{
    global $db;
    $today = todayYmd();
    $awarded = [];

    $checkAwardSql = "SELECT 1 FROM otdwinners WHERE `type` = ? AND DATE(FROM_UNIXTIME(`timestamp`)) = ? LIMIT 1";

    $otds = [
        ['baotd', 'Back Alley OTD', 250],
        ['botd', 'Buster OTD', 10000],
        ['motd', 'Mugger OTD', 10000],
        ['kotd', 'Killer OTD', 10000],
    ];

    foreach ($otds as [$col, $type, $pts]) {
        $db->query("SELECT userid, $col AS score FROM ofthes WHERE $col > 0 ORDER BY $col DESC LIMIT 1");
        $row = $db->fetch_row(true);
        if ($row && !empty($row['userid'])) {
            $db->query($checkAwardSql);
            $already = $db->fetch_row(true, [$type, $today]);
            if (!$already) {
                $db->startTrans();
                perform_query("UPDATE grpgusers SET points = points + ? WHERE id = ?", [$pts, (int) $row['userid']]);
                perform_query(
                    "INSERT INTO otdwinners (`userid`, `type`, `howmany`, `timestamp`) VALUES (?, ?, ?, ?)",
                    [(int) $row['userid'], $type, (int) $row['score'], time()]
                );
                $db->endTrans();
                Send_Event((int) $row['userid'], "You won $type! [+{$pts} Points]");
                $awarded[] = $type;
            }
        }
    }

    // Leveller OTD
    $db->query("SELECT id, todaysexp FROM grpgusers WHERE todaysexp > 0 ORDER BY todaysexp DESC LIMIT 1");
    if ($row = $db->fetch_row(true)) {
        $db->query($checkAwardSql);
        $already = $db->fetch_row(true, ['Leveller OTD', $today]);
        if (!$already) {
            $db->startTrans();
            perform_query("UPDATE grpgusers SET points = points + 10000 WHERE id = ?", [(int) $row['id']]);
            perform_query(
                "INSERT INTO otdwinners (`userid`, `type`, `howmany`, `timestamp`) VALUES (?, ?, ?, ?)",
                [(int) $row['id'], 'Leveller OTD', (int) $row['todaysexp'], time()]
            );
            $db->endTrans();
            Send_Event((int) $row['id'], "You won Leveller Of The Day [+10000 Points]");
            $awarded[] = 'Leveller OTD';
        }
    }

    // Most Money Mugged Today
    $db->query("SELECT id, tamt FROM grpgusers WHERE tamt > 0 ORDER BY tamt DESC LIMIT 1");
    if ($row = $db->fetch_row(true)) {
        $db->query($checkAwardSql);
        $already = $db->fetch_row(true, ['Most Mugged Today', $today]);
        if (!$already) {
            $db->startTrans();
            perform_query("UPDATE grpgusers SET points = points + 10000 WHERE id = ?", [(int) $row['id']]);
            perform_query(
                "INSERT INTO otdwinners (`userid`, `type`, `howmany`, `timestamp`) VALUES (?, ?, ?, ?)",
                [(int) $row['id'], 'Most Mugged Today', (int) $row['tamt'], time()]
            );
            $db->endTrans();
            Send_Event((int) $row['id'], "You won Most Money Mugged Today [+10000 Points]");
            $awarded[] = 'Most Mugged Today';
        }
    }

    // reset ofthes counters
    perform_query("UPDATE ofthes SET baotd = 0, botd = 0, motd = 0, kotd = 0");

    return ['awarded' => $awarded];
}

function step_user_daily_resets(): array
{
    perform_query("
        UPDATE `grpgusers` SET
            `tamt` = 0,
            `todayskills` = 0,
            `todaysexp` = 0,
            `boxes_opened` = 1,
            `crimeauto` = 0,
            `csmuggling` = 6,
            `psmuggling` = 6,
            `psmuggling2` = 5,
            `rtsmuggling` = 7,
            `prayer` = 1,
            `searchdowntown` = 100,
            `dailytrains` = 0,
            `dailymugs` = 0,
            `spins` = 20,
            `gameevents` = 0,
            `voted1` = 0,
            `dailyClockins` = 0,
            `doors` = 5,
            `slots_left1` = 100,
            `roulette` = 1,
            `luckydip` = 1,
            `luckydip2` = 1,
            `chase` = 1,
            `ffban` = 0,
            `rmdays` = GREATEST(`rmdays` - 1, 0)
    ");
    perform_query("UPDATE grpgusers SET apban = GREATEST(apban - 1, 0) WHERE apban > 0");
    perform_query("UPDATE grpgusers SET relationshipdays = relationshipdays + 1 WHERE relationship > 0");
    perform_query("UPDATE grpgusers SET gndays = GREATEST(gndays - 1, 0) WHERE gndays > 0");
    perform_query("UPDATE grpgusers SET blocked = GREATEST(blocked - 1, 0) WHERE blocked > 0");
    perform_query("DELETE FROM votes");
    perform_query("DELETE FROM dond");
    perform_query("DELETE FROM rating");

    return ['ok' => true];
}

function step_jobinfo_resets(): array
{
    perform_query("UPDATE jobInfo SET addedPercent = 0 WHERE dailyClockins < 5");
    perform_query("UPDATE jobInfo SET addedPercent = LEAST(addedPercent + 5, 50) WHERE dailyClockins >= 5");
    perform_query("UPDATE jobInfo SET dailyClockins = 0");
    return ['ok' => true];
}

function step_research_and_grotto(): array
{
    global $db;
    $db->query("UPDATE user_research_type SET duration_in_days = duration_in_days - 1 WHERE duration_in_days > 0");
    $db->execute();
    $db->query("UPDATE `user_santas_grotto` SET `todays_gifts_found` = 0");
    $db->execute();
    return ['ok' => true];
}

function step_bank_interest(): array
{
    global $db;
    $paid = 0;

    $db->query("SELECT id, bank, bankboost, rmdays, donations, gndays FROM grpgusers");
    $users = $db->fetch_row();

    foreach ($users as $u) {
        $id = (int) $u['id'];
        $bank = (float) $u['bank'];
        $bankboost = (float) $u['bankboost'];
        $rmdays = (int) $u['rmdays'];
        $donations = (int) $u['donations'];
        $gndays = (int) $u['gndays'];

        $rate = ($rmdays >= 1) ? 0.04 : 0.02;
        $addmul = 0.0;
        $ptsadd = 0;
        if ($donations >= 200) {
            $addmul = 0.05;
            $ptsadd = 150;
        } elseif ($donations >= 100) {
            $addmul = 0.03;
            $ptsadd = 120;
        } elseif ($donations >= 50) {
            $addmul = 0.02;
            $ptsadd = 75;
        }
        $rate += $addmul;

        $base = min($bank, 15000000.0);
        $interest = (int) ceil($base * $rate);
        if ($bankboost > 0) {
            $interest += (int) floor($interest * ($bankboost / 10.0));
        }
        if ($interest > 0) {
            perform_query("UPDATE grpgusers SET bank = bank + ?, points = points + ? WHERE id = ?", [$interest, $ptsadd, $id]);
            Send_Event($id, "You have earned " . prettynum($interest, 1) . " in bank interest");
            $paid++;
        }

        if ($rmdays < 1 || $gndays < 1) {
            if (function_exists('invalidateFormattedName')) {
                invalidateFormattedName($id);
            }
        }
    }

    // rmupgrade decrement
    $db->query("SELECT id, rmupgrade FROM grpgusers WHERE rmupgrade >= 1");
    foreach ($db->fetch_row() as $line) {
        $newrm = max(0, (int) $line['rmupgrade'] - 1);
        perform_query("UPDATE grpgusers SET rmupgrade = ? WHERE id = ?", [$newrm, (int) $line['id']]);
    }

    return ['paid_interest_to' => $paid];
}

function step_lotteries(): array
{
    global $db;

    // Cash lottery
    $tickCost = 250000;
    $db->query("SELECT SUM(tickets) AS s FROM cashlottery");
    $amountCash = (int) ($db->fetch_row(true)['s'] ?? 0) * $tickCost;

    $db->query("SELECT * FROM cashlottery");
    $cashRows = $db->fetch_row();
    $cashWinnerId = null;

    if (!empty($cashRows)) {
        $winner = $cashRows[array_rand($cashRows)];
        $u = new User((int) $winner['userid']);
        perform_query("UPDATE grpgusers SET bank = bank + ? WHERE id = ?", [$amountCash, $u->id]);
        perform_query("INSERT INTO mlottowinners VALUES ('', ?, ?)", [$u->id, $amountCash]);
        Send_Event($u->id, "Congratulations! You won " . prettynum($amountCash, 1) . " in the lottery!");
        perform_query("DELETE FROM cashlottery");
        perform_query(
            "UPDATE gameevents SET cashlottery = CONCAT('<li>There were ', ?, ' lottery tickets bought yesterday.</li>',
                                     '<li>The jackpot was ', ?, '.</li>',
                                     '<li>The winner was [-_USER_-].</li>'),
                                  cashlotteryid = ?",
            [prettynum(count($cashRows)), prettynum($amountCash, 1), $u->id]
        );
        $cashWinnerId = $u->id;
    } else {
        perform_query("UPDATE gameevents SET cashlottery = '<li>There were 0 lottery tickets bought yesterday.</li>'");
    }

    // Points lottery
    $tickCost = 50;
    $db->query("SELECT SUM(tickets) AS s FROM ptslottery");
    $amountPts = (int) round((float) ($db->fetch_row(true)['s'] ?? 0) * $tickCost);

    $db->query("SELECT * FROM ptslottery");
    $ptRows = $db->fetch_row();
    $ptWinnerId = null;

    if (!empty($ptRows)) {
        $winner = $ptRows[array_rand($ptRows)];
        $u = new User((int) $winner['userid']);
        perform_query("UPDATE grpgusers SET points = points + ? WHERE id = ?", [$amountPts, $u->id]);
        perform_query("INSERT INTO plottowinners VALUES ('', ?, ?)", [$u->id, $amountPts]);
        Send_Event($u->id, "Congratulations! You won " . prettynum($amountPts) . " points in the lottery!");
        perform_query("DELETE FROM ptslottery");
        perform_query(
            "UPDATE gameevents SET ptslottery = CONCAT('<li>There were ', ?, ' lottery tickets bought yesterday.</li>',
                                     '<li>The jackpot was ', ?, ' points.</li>',
                                     '<li>The winner was [-_USER_-].</li>'),
                                  ptslotteryid = ?",
            [prettynum(count($ptRows)), prettynum($amountPts), $u->id]
        );
        $ptWinnerId = $u->id;
    } else {
        perform_query("UPDATE gameevents SET ptslottery = '<li>There were 0 lottery tickets bought yesterday.</li>'");
    }

    return ['cash_winner' => $cashWinnerId, 'points_winner' => $ptWinnerId];
}

function step_properties_rollover(): array
{
    global $db;
    $db->startTrans();
    perform_query("UPDATE rentedproperties SET days = days - 1");

    $db->query("SELECT owner, houseid FROM rentedproperties WHERE days = 0");
    foreach ($db->fetch_row() as $row) {
        perform_query("INSERT INTO ownedproperties VALUES ('', ?, ?)", [(int) $row['owner'], (int) $row['houseid']]);
    }
    perform_query("DELETE FROM rentedproperties WHERE days <= 0");
    $db->endTrans();
    return ['ok' => true];
}

function step_gangs_otd(): array
{
    global $db;
    $ignoreGangs = "11,31";

    $pickTop = function (string $col) use ($db, $ignoreGangs): ?int {
        $db->query("SELECT id FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `$col` DESC LIMIT 1");
        $row = $db->fetch_row(true);
        return $row ? (int) $row['id'] : null;
    };

    $topKills = $pickTop('dailyKills');
    $topCrimes = $pickTop('dailyCrimes');
    $topBusts = $pickTop('dailyBusts');
    $topMugs = $pickTop('dailyMugs');

    foreach (array_filter([$topKills, $topCrimes, $topBusts, $topMugs]) as $gid) {
        perform_query("UPDATE gangs SET respect = respect + 100 WHERE id = ?", [$gid]);
    }
    if ($topKills)
        Gang_Event($topKills, "Respect Gang Of The Day - Kills +100 Respect", 0);
    if ($topCrimes)
        Gang_Event($topCrimes, "Respect Gang Of The Day - Crimes +100 Respect", 0);
    if ($topMugs)
        Gang_Event($topMugs, "Respect Gang Of The Day - Mugs +100 Respect", 0);
    if ($topBusts)
        Gang_Event($topBusts, "Respect Gang Of The Day - Busts +100 Respect", 0);

    perform_query("UPDATE gangs SET dailyCrimes = 0, dailyKills = 0, dailyBusts = 0, dailyMugs = 0");
    return ['winners' => compact('topKills', 'topCrimes', 'topBusts', 'topMugs')];
}

function step_gang_competition(): array
{
    global $db;
    $db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC, `gang_id` ASC LIMIT 2");
    $rows = $db->fetch_row();
    $rank = 1;

    foreach ($rows as $row) {
        $gangId = (int) $row['gang_id'];
        // Simpler (no binds in wrapper fetch)
        $db->query("SELECT id FROM grpgusers WHERE gang = $gangId");
        $members = $db->fetch_row();

        foreach ($members as $m) {
            $uid = (int) $m['id'];
            if ($rank === 1) {
                perform_query("UPDATE grpgusers SET points = points + 25000 WHERE id = ?", [$uid]);
                Give_Item(163, $uid, 1); // Police Badge
                Give_Item(42, $uid, 1); // Mystery Box
                Send_Event($uid, "Your gang won 1st place in the daily contest. You have been awarded 25,000 points, 1 Police Badge & 1 Mystery Box.");
            } else {
                Give_Item(42, $uid, 1);
                Send_Event($uid, "Your gang won 2nd place in the daily contest. You have been awarded 1 Mystery Box.");
            }
        }
        $rank++;
    }
    perform_query("UPDATE `gang_comp_leaderboard` SET `daily_missions_complete` = 0");
    return ['ok' => true];
}

function step_cleanup(): array
{
    $cutoff = cutoffTs(14);
    perform_query("DELETE FROM `attacklog` WHERE `timestamp` < ?", [$cutoff]);
    perform_query("DELETE FROM `attlog` WHERE `timestamp` < ?", [$cutoff]);
    perform_query("DELETE FROM `deflog` WHERE `timestamp` < ?", [$cutoff]);
    perform_query("DELETE FROM `events` WHERE `timesent` < ?", [$cutoff]);
    perform_query("DELETE FROM `muglog` WHERE `timestamp` < ?", [$cutoff]);
    perform_query("DELETE FROM `user_logs` WHERE `timestamp` < ?", [$cutoff]);

    perform_query("DELETE FROM `active_raids`       WHERE `summoned_at`  < (NOW() - INTERVAL 14 DAY)");
    perform_query("DELETE FROM `raid_participants`  WHERE `joined_at`    < (NOW() - INTERVAL 14 DAY)");
    perform_query("DELETE FROM `raid_battle_logs`   WHERE `timestamp`    < (NOW() - INTERVAL 14 DAY)");

    $extra = 0;
    if (function_exists('cleanOldDBEntries')) {
        $extra = (int) cleanOldDBEntries();
    }
    return ['extra_deleted' => $extra];
}
