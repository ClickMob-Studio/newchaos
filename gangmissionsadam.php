<?php
include 'header.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);


    $checkActiveMission = mysql_query("SELECT agm.kills AS current_kills, agm.busts AS current_busts, agm.crimes AS current_crimes, agm.mugs AS current_mugs, gm.name, gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.reward, gm.time AS 'mission_time', UNIX_TIMESTAMP() AS 'current_time', agm.end_time FROM active_gang_missions agm JOIN gang_missions gm ON agm.mission_id = gm.id WHERE agm.gangid = '{$user_class->gang}' AND agm.completed = 0 LIMIT 1");

    if (!$checkActiveMission) {
        die('Invalid query: ' . mysql_error());
    }

    if ($activeMission = mysql_fetch_assoc($checkActiveMission)) {
        $remainingTime = max($activeMission['end_time'] - $activeMission['current_time'], 0);

        echo "<h2>Current Mission Progress</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Name</th>
                    <th>Kills (Current / Target)</th>
                    <th>Busts (Current / Target)</th>
                    <th>Crimes (Current / Target)</th>
                    <th>Mugs (Current / Target)</th>
                    <th>Reward</th>
                    <th>Time Remaining</th>
                </tr>
                <tr>
                    <td>{$activeMission['name']}</td>
                    <td>{$activeMission['current_kills']} / " . (isset($activeMission['target_kills']) ? $activeMission['target_kills'] : '0') . "</td>
                    <td>{$activeMission['current_busts']} / " . (isset($activeMission['target_busts']) ? $activeMission['target_busts'] : '0') . "</td>
                    <td>{$activeMission['current_crimes']} / {$activeMission['target_crimes']}</td>
                    <td>{$activeMission['current_mugs']} / " . (isset($activeMission['target_mugs']) ? $activeMission['target_mugs'] : '0') . "</td>
                    <td>{$activeMission['reward']}</td>
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
        $missionsResult = mysql_query("SELECT * FROM gang_missions WHERE crimes > 0");

        if (!$missionsResult) {
            die('Invalid query: ' . mysql_error());
        }

        if (mysql_num_rows($missionsResult) > 0) {
            echo "<table border='1'>
                  <tr>
                    <th>Name</th>
                    <th>Kills</th>
                    <th>Busts</th>
                    <th>Crimes</th>
                    <th>Mugs</th>
                    <th>Reward</th>
                    <th>Time (hours)</th>
                    <th>Action</th>
                  </tr>";

            while ($mission = mysql_fetch_assoc($missionsResult)) {
                echo "<tr>
                        <td>{$mission['name']}</td>
                        <td>0 / 0</td>
                        <td>0 / 0</td>
                        <td>{$mission['crimes']}</td>
                        <td>0 / 0</td>
                        <td>{$mission['reward']}</td>
                        <td>{$mission['time']}</td>
                        <td><a href='?acceptMission={$mission['id']}'>Accept</a></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No available missions.";
        }
    }

    if (isset($_GET['acceptMission'])) {

        $activeMissionCheckQuery = "SELECT 1 FROM active_gang_missions WHERE gangid = '{$user_class->gang}' AND completed = 0 LIMIT 1";
        $activeMissionCheckResult = mysql_query($activeMissionCheckQuery);
        if (!$activeMissionCheckResult) {
            die('Invalid query: ' . mysql_error());
        }

        if (mysql_num_rows($activeMissionCheckResult) > 0) {
          
            echo Message("Your gang already has an active mission. Please complete it before starting a new one.");
        } else {
         
            $missionId = intval($_GET['acceptMission']);
            $missionQuery = "SELECT time FROM gang_missions WHERE id = '{$missionId}' LIMIT 1";
            $missionResult = mysql_query($missionQuery);
            if (!$missionResult) {
                die('Invalid query: ' . mysql_error());
            }

            if ($mission = mysql_fetch_assoc($missionResult)) {
                $duration = $mission['time'] * 3600; 
                $endTime = time() + $duration; 

                $insertMission = "INSERT INTO active_gang_missions (gangid, mission_id, kills, busts, crimes, mugs, completed, time, end_time) VALUES ('{$user_class->gang}', '{$missionId}', 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), '{$endTime}')";
                if (!mysql_query($insertMission)) {
                    die('Failed to accept the mission. Error: ' . mysql_error());
                } else {
                    echo Message("Mission accepted successfully. Refresh to see progress.");
                }
            } else {
                echo Message("Failed to retrieve mission details.");
            }
        }
    }
} else {
    echo Message("You aren't in a gang.");
}

include("gangheaders.php");
include 'footer.php';
?>
