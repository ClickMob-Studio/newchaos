<?php
include 'header.php';


function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

$db->query("SELECT `id`, `name` FROM `cities` ORDER BY `name` ASC");
$db->execute();
$cities = $db->fetch_row();

$cityLookup = [];
if (is_array($cities)) {
    foreach ($cities as $c) {
        $cityLookup[(int) $c['id']] = $c['name'];
    }
}

$allowedMetrics = [
    'level' => ['label' => 'Level', 'column' => 'level'],
    'strength' => ['label' => 'Strength', 'column' => 'strength'],
    'defense' => ['label' => 'Defense', 'column' => 'defense'],
    'speed' => ['label' => 'Speed', 'column' => 'speed'],
    'agility' => ['label' => 'Agility', 'column' => 'agility'],
    'money' => ['label' => 'Money', 'column' => 'money'],
    'points' => ['label' => 'Points', 'column' => 'points'],
    'total' => ['label' => 'Total', 'column' => 'total'],
    'rmdays' => ['label' => 'RM Days', 'column' => 'rmdays'],
    'bank' => ['label' => 'Bank', 'column' => 'bank'],
    'crimes' => ['label' => 'Crimes Done', 'column' => 'crimes'],          // will map to crimesucceeded
    'battlewon' => ['label' => 'Kills', 'column' => 'battlewon'],
    'battlelost' => ['label' => 'Deaths', 'column' => 'battlelost'],
    'muggedmoney' => ['label' => 'Highest Muggers', 'column' => 'muggedmoney'],
    'posts' => ['label' => 'Forum Posts', 'column' => 'posts'],
    'backalleywins' => ['label' => 'Back Alley Wins', 'column' => 'backalleywins'],
    'relationshipdays' => ['label' => 'Time Married', 'column' => 'relationshipdays'],
];

$view = isset($_GET['view']) ? (string) $_GET['view'] : 'level';
if (!array_key_exists($view, $allowedMetrics)) {
    $view = 'level';
}

$orderByColumn = ($view === 'crimes') ? 'crimesucceeded' : $allowedMetrics[$view]['column'];

$secondarySort = ($view === 'level') ? ", `exp` DESC" : "";

$selectedCityId = isset($_GET['cityid']) ? (int) $_GET['cityid'] : 0;
$selectedCityId = array_key_exists($selectedCityId, $cityLookup) ? $selectedCityId : 0;

$sql = "SELECT *
        FROM `grpgusers` gu
        WHERE (SELECT COUNT(*) FROM bans b WHERE b.id = gu.id AND b.type IN ('perm','freeze')) = 0
          AND gu.`admin` = '0'
          AND gu.`ban/freeze` = '0'";

if ($selectedCityId > 0) {
    $sql .= " AND gu.`cityid` = :cityid";
}

$sql .= " ORDER BY `{$orderByColumn}` DESC{$secondarySort} LIMIT 50";
$db->query($sql);
if ($selectedCityId > 0) {
    $db->bind(':cityid', $selectedCityId);
}
$db->execute();
$rows = $db->fetch_row(); // array of users

?>
<div class='box_top'>Hall Of Fame</div>
<div class='box_middle'>
    <div class='pad'>

        <div class="contenthead floaty">
            <form method="get" class="hof-filters" style="margin-bottom:12px;">
                <label for="cityid" style="margin-right:8px;"><strong>City:</strong></label>
                <select name="cityid" id="cityid" onchange="this.form.submit()" style="min-width:200px;">
                    <option value="0" <?php echo $selectedCityId === 0 ? ' selected' : ''; ?>>All cities</option>
                    <?php if (is_array($cities)): ?>
                        <?php foreach ($cities as $row): ?>
                            <option value="<?php echo (int) $row['id']; ?>" <?php echo ($selectedCityId === (int) $row['id']) ? ' selected' : ''; ?>>
                                <?php echo h($row['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <label for="view" style="margin:0 8px 0 0;"><strong>Metric:</strong></label>
                <select name="view" id="view" onchange="this.form.submit()" style="min-width:200px;">
                    <?php foreach ($allowedMetrics as $key => $cfg): ?>
                        <option value="<?php echo h($key); ?>" <?php echo ($view === $key) ? ' selected' : ''; ?>>
                            <?php echo h($cfg['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <noscript>
                    <button type="submit" style="margin-left:12px;">Apply</button>
                </noscript>
            </form>

            <table width='100%' border="0" bordercolor="#444444" cellpadding="4" cellspacing="0" align="center"
                class="myTable">
                <tr>
                    <td><b>Rank</b></td>
                    <td><b>Mobster</b></td>
                    <td><b>Level</b></td>
                    <td><b>Money</b></td>
                    <td><b>Gang</b></td>
                    <td><b>Online</b></td>
                </tr>

                <?php
                $rank = 0;

                if (is_array($rows) && count($rows) > 0) {
                    foreach ($rows as $line) {
                        $rank++;
                        $user_hall = new User($line['id']);

                        echo "<tr><td>";
                        if ($rank === 1) {
                            echo "<span style='color:#FFD700; font-weight:bold;'>1st</span>";
                        } elseif ($rank === 2) {
                            echo "<span style='color:#C0C0C0; font-weight:bold;'>2nd</span>";
                        } elseif ($rank === 3) {
                            echo "<span style='color:#CD7F32; font-weight:bold;'>3rd</span>";
                        } else {
                            echo h(ordinal($rank));
                        }
                        echo "</td>";

                        echo "<td>{$user_hall->formattedname}</td>";
                        echo "<td>" . h($user_hall->level) . "</td>";
                        echo "<td>" . h(prettynum($user_hall->money, 1)) . "</td>";
                        echo "<td>{$user_hall->formattedgang}</td>";
                        echo "<td>{$user_hall->formattedonline}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; opacity:0.8;'>No results found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

<style>
    .hof-filters select {
        padding: 4px 6px;
    }

    .myTable tr:nth-child(even) {
        background: #111;
    }

    .myTable tr:nth-child(odd) {
        background: #0c0c0c;
    }

    .myTable td {
        border-bottom: 1px solid #222;
    }
</style>

<?php
include 'footer.php';
