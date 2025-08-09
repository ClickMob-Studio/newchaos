<?php
include 'header.php';

if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
    $user_rank = new GangRank($user_class->grank);

    $db->query("SELECT agm.kills AS current_kills, agm.busts AS current_busts, agm.crimes AS current_crimes, agm.mugs AS current_mugs, agm.backalleys AS current_backalleys, gm.name, gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.backalleys AS target_backalleys, gm.reward, gm.time AS 'mission_time', UNIX_TIMESTAMP() AS 'current_time', agm.end_time FROM active_gang_missions agm JOIN gang_missions gm ON agm.mission_id = gm.id WHERE agm.gangid = ? AND agm.completed = 0 LIMIT 1");
    $db->execute([$user_class->gang]);
    $activeMission = $db->fetch_row(true);

    if (isset($activeMission)) {
        $remainingTime = max($activeMission['end_time'] - $activeMission['current_time'], 0);

        echo "<h2>Current Mission Progress</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Name</th>
                    <th>Kills</th>
                    <th>Busts</th>
                    <th>Crimes</th>
                    <th>Mugs</th>
                    <th>Backalley</th>
                    <th>Reward</th>
                    <th>Time Remaining</th>
                </tr>
                <tr>
                    <td>{$activeMission['name']}</td>
                    <td>" . (($activeMission['target_kills'] > 0) ? number_format($activeMission['current_kills'], 0) : '0') . " / " . (number_format($activeMission['target_kills'], 0) ?: '0') . "</td>
                    <td>" . (($activeMission['target_busts'] > 0) ? number_format($activeMission['current_busts'], 0) : '0') . " / " . (number_format($activeMission['target_busts'], 0) ?: '0') . "</td>
                    <td>" . (($activeMission['target_crimes'] > 0) ? number_format($activeMission['current_crimes'], 0) : '0') . " / " . (number_format($activeMission['target_crimes'], 0) ?: '0') . "</td>
                    <td>" . (($activeMission['target_mugs'] > 0) ? number_format($activeMission['current_mugs'], 0) : '0') . " / " . (number_format($activeMission['target_mugs'], 0) ?: '0') . "</td>
                    <td>" . (($activeMission['target_backalleys'] > 0) ? number_format($activeMission['current_backalleys'], 0) : '0') . " / " . (number_format($activeMission['target_backalleys'], 0) ?: '0') . "</td>
                    <td>" . number_format($activeMission['reward']) . " points</td>
                    <td><div id='countdown'>Loading...</div></td>
                </tr>
              </table>";

        echo "<script>
                function updateCountdown(time) {
                    var countdown = document.getElementById('countdown');
                    var hours = Math.floor(time / 3600);
                    var minutes = Math.floor((time % 3600) / 60);
                    var seconds = time % 60;
                    countdown.innerHTML = hours + 'h ' + minutes + 'm ' + seconds + 's ';
                    if (time <= 0) {
                        countdown.innerHTML = 'Mission Completed';
                        clearInterval(interval);
                    }
                }
                var timeLeft = $remainingTime;
                updateCountdown(timeLeft);
                var interval = setInterval(function() {
                    timeLeft--;
                    updateCountdown(timeLeft);
                }, 1000);
              </script>";
    } else {
        echo "<h2>Available Missions</h2>";
        $db->query("SELECT * FROM gang_missions");
        $db->execute();
        $missionResult = $db->fetch_row();
        if (!isset($missionResult)) {
            die('Failed to find gang missions');
        }

        if (count($missionResult) > 0) {
            echo "<table border='1'>
                  <tr>
                    <th>Name</th>
                    <th>Kills</th>
                    <th>Busts</th>
                    <th>Crimes</th>
                    <th>Mugs</th>
                    <th>Backalleys</th>
                    <th>Reward (points)</th>
                    <th>Time (hours)</th>
                    <th>Action</th>
                  </tr>";

            foreach ($missionResult as $mission) {
                $db->query("SELECT * FROM active_gang_missions WHERE gangid = " . $user_class->gang . " AND mission_id = " . $mission['id'] . " ORDER BY time DESC LIMIT 1");
                $db->execute();
                $lastMission = $db->fetch_row(true);

                if ($user_rank->crime > 0 || $user_class->gangleader == $user_class->id) {
                    $startBtn = "<a class='btn btn-primary' href='?acceptMission={$mission['id']}'>Accept</a>";
                    if ($lastMission) {
                        $nowTime = time();
                        $nextStartTime = $lastMission['time'] + (7 * 24 * 60 * 60);
                        if ($nowTime < $nextStartTime) {
                            $startBtn = 'Available in ' . daysToTime($nextStartTime - $nowTime);
                        }
                    }
                } else {
                    $startBtn = "";
                }

                echo "<tr>
                        <td>{$mission['name']}</td>
                        <td>" . number_format($mission['kills'], 0) . "</td>
                        <td>" . number_format($mission['busts'], 0) . "</td>
                        <td>" . number_format($mission['crimes'], 0) . "</td>
                        <td>" . number_format($mission['mugs'], 0) . "</td>
                        <td>" . number_format($mission['backalleys'], 0) . "</td>
                        <td>" . number_format($mission['reward'], 0) . "</td>
                        <td>{$mission['time']}</td>
                        <td>{$startBtn}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No available missions.";
        }
    }

    if (isset($_GET['acceptMission'])) {
        $db->query("SELECT 1 FROM active_gang_missions WHERE gangid = ? AND completed = 0 LIMIT 1");
        $db->execute([$user_class->gang]);
        $activeMission = $db->fetch_row(true);
        if (isset($activeMission)) {
            echo Message("Your gang already has an active mission. Please complete it before starting a new one.");
        } else {
            $missionId = intval($_GET['acceptMission']);

            $db->query("SELECT time FROM gang_missions WHERE id = ? LIMIT 1");
            $db->execute([$missionId]);
            $mission = $db->fetch_single();
            if (!isset($result)) {
                die('Failed to retrieve mission details for mission id: ' . $missionId);
            }

            $duration = $mission['time'] * 3600;
            $endTime = time() + $duration;

            perform_query("INSERT INTO active_gang_missions (gangid, mission_id, kills, busts, crimes, mugs, completed, time, end_time) VALUES (?, ?, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ?)", [$user_class->gang, $missionId, $endTime]);
            echo Message("Mission accepted successfully. Refresh to see progress.");
        }
    }
} else {
    echo Message("You aren't in a gang.");
}

?>

<br /><br />
<hr />

<?php
include("gangheaders.php");
include 'footer.php';
?>