<?php
include 'header.php';

/**
 * CONFIG
 */
$rankColours = ['FFD700', 'C0C0C0', 'CD7F32']; // gold, silver, bronze
$nor = 3; // number of ranks to be shown per category
$categories = [
    'level' => [20000, 10000, 5000],
    'crimes' => [20000, 10000, 5000],
    'referrals' => [20000, 10000, 5000],
    'attacks won' => [20000, 10000, 5000],
    'attacks lost' => [20000, 10000, 5000],
    'defend won' => [20000, 10000, 5000],
    'defend lost' => [20000, 10000, 5000],
    'busts' => [20000, 10000, 5000],
    'mugs' => [20000, 10000, 5000],
];
$donatePrizes = [30, 20, 10];

/**
 * HELPERS
 */

function formatNameFast(int $id, array $usersMap, bool $viewerIsAdmin, int $viewerId): string
{
    if (!isset($usersMap[$id]))
        return "User#$id";
    $u = $usersMap[$id];

    $isSelf = ($viewerId === $id);
    $anonymous = (!$isSelf && !$viewerIsAdmin && !empty($u['dprivacy']));
    if ($anonymous)
        return 'Anonymous';

    return htmlspecialchars((string) $u['username'], ENT_QUOTES, 'UTF-8');
}

/**
 * DATA FETCH
 */

$db->query("SELECT endtime FROM bloodbath ORDER BY endtime DESC LIMIT 1");
$db->execute();
$bbEndTs = (int) $db->fetch_single();

$lim = (int) $nor;
$db->query("
    SELECT b.userid, b.donator, g.dprivacy
    FROM bbusers b
    JOIN grpgusers g ON g.id = b.userid
    WHERE b.donator <> 0
      AND g.lastactive > UNIX_TIMESTAMP() - (86400 * 7)
    ORDER BY b.donator DESC
    LIMIT $lim
");
$db->execute();
$donators = $db->fetch_row() ?: [];

$topsByCategory = [];
$allUserIds = [];

foreach ($donators as $r)
    $allUserIds[] = (int) $r['userid'];

foreach ($categories as $label => $prizes) {
    $col = str_replace(' ', '', $label);

    $lim = (int) $nor;
    $sql = "
  SELECT b.userid, b.`$col` AS metric
  FROM bbusers b
  JOIN grpgusers g ON g.id = b.userid
  WHERE b.`$col` <> 0
    AND g.lastactive > UNIX_TIMESTAMP() - (86400 * 7)
    AND g.admin = 0
  ORDER BY b.`$col` DESC
  LIMIT $lim
";
    $db->query($sql);
    $db->execute();
    $rows = $db->fetch_row() ?: [];
    $topsByCategory[$label] = $rows;

    foreach ($rows as $r)
        $allUserIds[] = (int) $r['userid'];
}

$allUserIds = array_values(array_unique(array_filter($allUserIds)));
$usersMap = [];
if ($allUserIds) {
    $place = implode(',', array_fill(0, count($allUserIds), '?'));
    $db->query("SELECT id, username, dprivacy FROM grpgusers WHERE id IN ($place)");
    $db->execute($allUserIds);
    foreach ($db->fetch_row() as $u) {
        $usersMap[(int) $u['id']] = [
            'username' => $u['username'],
            'dprivacy' => (int) $u['dprivacy'],
        ];
    }
}

$viewerIsAdmin = !empty($user_class->admin);
$viewerId = (int) $user_class->id;

/**
 * RENDER
 */
?>

<style>
    .bb-wrap {
        margin: 10px 0;
    }

    .bb-intro {
        color: #d33;
        text-align: center;
        margin: 6px 0 12px;
    }

    .bb-table {
        width: 100%;
        table-layout: fixed;
        margin-top: 20px;
    }

    .bb-table th,
    .bb-table td {
        padding: 8px;
    }

    .bb-rank {
        width: 10%;
        white-space: nowrap;
    }

    .bb-name {
        width: 40%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bb-metric,
    .bb-reward {
        width: 25%;
    }
</style>

<div class="box_top">Bloodbath</div>
<div class="box_middle">
    <div class="pad bb-wrap">

        <div class="bb-intro">
            <strong>Welcome to Bloodbath</strong> — a chance to gain extra points for your hard work!
        </div>

        <div style="text-align:center; margin-bottom:10px; font-size:1.05em;">
            Bloodbath will end in
            <span id="bb-countdown" data-end="<?= htmlspecialchars((string) $bbEndTs, ENT_QUOTES, 'UTF-8'); ?>"></span>
        </div>

        <table class="bb-table">
            <tr>
                <th colspan="3" style="font-size:1.1em;">Donations</th>
            </tr>
            <tr>
                <th class="bb-rank"><b>Rank</b></th>
                <th class="bb-name"><b>Username</b></th>
                <th class="bb-reward"><b>Reward<br>(% of total donation)</b></th>
            </tr>
            <?php
            $rank = 0;
            foreach ($donators as $row):
                $rank++;
                $colour = $rankColours[$rank - 1] ?? null;

                $displayName = formatNameFast((int) $row['userid'], $usersMap, $viewerIsAdmin, $viewerId);
                if ($viewerIsAdmin && !empty($row['dprivacy'])) {
                    $realName = formatNameFast((int) $row['userid'], $usersMap, true, $viewerId);
                    $displayName .= ' (' . $realName . ')';
                }
                ?>
                <tr>
                    <td class="bb-rank">
                        <?php
                        $rankStr = ordinal($rank);
                        echo $colour ? "<span style='font-weight:bold;color:#{$colour}'>{$rankStr}</span>" : $rankStr;
                        ?>
                    </td>
                    <td class="bb-name"><?= $displayName; ?></td>
                    <td class="bb-reward">
                        <?php
                        $reward = $donatePrizes[$rank - 1] ?? null;
                        echo $reward !== null
                            ? ($colour ? "<span style='font-weight:bold;color:#{$colour}'>" : "")
                            . number_format($reward) . "%"
                            . ($colour ? "</span>" : "")
                            : "-";
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php
        foreach ($categories as $label => $prizes):
            $col = str_replace(' ', '', $label);
            $rows = $topsByCategory[$label] ?? [];
            ?>
            <table class="bb-table">
                <tr>
                    <th colspan="4" style="font-size:1.1em;">
                        <?php
                        // Rename header for "crimes" per your original
                        $header = ($label === 'crimes')
                            ? 'Nerve used on Crimes divided by Level (Nerve/Level)'
                            : ucfirst($label);
                        echo htmlspecialchars($header, ENT_QUOTES, 'UTF-8');
                        ?>
                    </th>
                </tr>
                <tr>
                    <th class="bb-rank"><b>Rank</b></th>
                    <th class="bb-name"><b>Username</b></th>
                    <th class="bb-metric"><b><?= htmlspecialchars(ucfirst($label), ENT_QUOTES, 'UTF-8'); ?></b></th>
                    <th class="bb-reward"><b>Reward</b></th>
                </tr>

                <?php
                $rank = 0;
                $topIds = [];
                foreach ($rows as $r):
                    $rank++;
                    $topIds[] = (int) $r['userid'];
                    $colour = $rankColours[$rank - 1] ?? null;
                    $rankStr = ordinal($rank);

                    $displayName = formatNameFast((int) $r['userid'], $usersMap, $viewerIsAdmin, $viewerId);

                    $metric = $r['metric'];
                    $metricLabel = ($label === 'crimes') ? 'points' : $label;
                    ?>
                    <tr>
                        <td class="bb-rank">
                            <?= $colour ? "<span style='font-weight:bold;color:#{$colour}'>{$rankStr}</span>" : $rankStr; ?>
                        </td>
                        <td class="bb-name"><?= $displayName; ?></td>
                        <td class="bb-metric">
                            <?= prettynum($metric) . ' ' . htmlspecialchars(ucfirst($metricLabel), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="bb-reward">
                            <?php
                            $p = $prizes[$rank - 1] ?? null;
                            echo $p !== null
                                ? ($colour ? "<span style='font-weight:bold;color:#{$colour}'>" : "")
                                . number_format($p) . " Points"
                                . ($colour ? "</span>" : "")
                                : "-";
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php
                // Optional: show viewer's rank (originally done for id==682; now generic & efficient)
                // Only if viewer is NOT already in top list and has a non-zero metric in the last week.
                if ($viewerId && !in_array($viewerId, $topIds, true)) {
                    // 1) Get viewer's metric
                    $db->query("
                SELECT b.`$col` AS metric
                FROM bbusers b
                JOIN grpgusers g ON g.id = b.userid
                WHERE b.userid = ?
                  AND g.lastactive > UNIX_TIMESTAMP() - (86400 * 7)
            ");
                    $db->execute([$viewerId]);
                    $myMetric = (int) ($db->fetch_single() ?? 0);

                    if ($myMetric > 0) {
                        // 2) Dense rank = 1 + count of distinct values greater than my value
                        $db->query("
                    SELECT 1 + COUNT(DISTINCT b.`$col`) AS rnk
                    FROM bbusers b
                    JOIN grpgusers g ON g.id = b.userid
                    WHERE b.`$col` > ?
                      AND g.lastactive > UNIX_TIMESTAMP() - (86400 * 7)
                      AND g.admin = 0
                ");
                        $db->execute([$myMetric]);
                        $myRank = (int) ($db->fetch_single() ?? 0);

                        if ($myRank > 0) {
                            echo "<tr>
                            <td class='bb-rank'>" . htmlspecialchars(ordinal($myRank), ENT_QUOTES, 'UTF-8') . "</td>
                            <td class='bb-name'>" . htmlspecialchars(formatNameFast($viewerId, $usersMap + [$viewerId => ['username' => $user_class->username ?? "User#$viewerId", 'dprivacy' => 0]], $viewerIsAdmin, $viewerId), ENT_QUOTES, 'UTF-8') . "</td>
                            <td class='bb-metric'>" . prettynum($myMetric) . ' ' . htmlspecialchars(ucfirst($metricLabel), ENT_QUOTES, 'UTF-8') . "</td>
                            <td class='bb-reward'>-</td>
                          </tr>";
                        }
                    }
                }
                ?>
            </table>
        <?php endforeach; ?>

    </div>
</div>

<script>
    (function () {
        // Client-side countdown (no server polling)
        const el = document.getElementById('bb-countdown');
        if (!el) return;
        const end = parseInt(el.dataset.end, 10) * 1000;

        function fmt(ms) {
            if (ms <= 0) return '0s';
            const s = Math.floor(ms / 1000);
            const d = Math.floor(s / 86400);
            const h = Math.floor((s % 86400) / 3600);
            const m = Math.floor((s % 3600) / 60);
            const ss = s % 60;
            return (d ? d + 'd ' : '') + (h ? h + 'h ' : '') + (m ? m + 'm ' : '') + ss + 's';
        }

        function tick() {
            el.textContent = fmt(end - Date.now());
        }
        tick();
        setInterval(tick, 1000);
    })();
</script>

<?php include 'footer.php'; ?>