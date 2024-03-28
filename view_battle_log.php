<?php
include 'header.php';
?>
<div class='box_top'>Raid Stats</div>
						<div class='box_middle'>
							<div class='pad'>
<?php

echo "<style>

.mainContainer {
  max-width: 1200px;
  margin: auto;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0px 0px 8px #555;
}
.layoutContainer {
  display: flex;
  justify-content: space-around;
  align-items: center;
}
.teamStats, .bossStats {
  width: 30%;
}
.raidLog {
  white-space: pre-line;
  padding: 10px;
  border-radius: 4px;
  margin-top: 15px;
}
.bossName {
  text-align: center;
  font-size: 40px;
  color: red;
  text-shadow: 4px 4px 8px #000;
  font-weight: bold;
  -webkit-text-stroke-width: 1px;
  -webkit-text-stroke-color: black;
  transform: perspective(1px) translateZ(0);
  margin-top: 15px;
}
</style>";

echo "<div class='mainContainer'>";

if (isset($_GET['raid_id'])) {
  $raid_id = intval($_GET['raid_id']);
  
  $active_raid_query = "SELECT boss_id FROM active_raids WHERE id = " . $raid_id;
  $active_raid_result = mysql_query($active_raid_query);
  if (!$active_raid_result || mysql_num_rows($active_raid_result) == 0) {
    die('Invalid raid query or raid not found: ' . mysql_error());
  }
  $active_raid_info = mysql_fetch_assoc($active_raid_result);
  $boss_id = $active_raid_info['boss_id'];

  $boss_query = "SELECT * FROM bosses WHERE id = " . $boss_id;
  $boss_result = mysql_query($boss_query);
  if (!$boss_result) {
    die('Invalid boss query: ' . mysql_error());
  }
  $boss_info = mysql_fetch_assoc($boss_result);

  $query = "SELECT battle_log FROM raid_battle_logs WHERE raid_id = " . $raid_id;
  $result = mysql_query($query);
  if (!$result) {
    die('Invalid query: ' . mysql_error());
  }

  $team_stats_query = "SELECT SUM(total) AS total_stats FROM grpgusers WHERE id IN (SELECT user_id FROM raid_participants WHERE raid_id = " . $raid_id . ")";
  $team_stats_result = mysql_query($team_stats_query);
  $team_stats_info = mysql_fetch_assoc($team_stats_result);
  $total_stats = $team_stats_info['total_stats'];

  $participants_query = "SELECT user_id FROM raid_participants WHERE raid_id = " . $raid_id;
  $participants_result = mysql_query($participants_query);
  $participants = [];
  while ($row = mysql_fetch_assoc($participants_result)) {
    $participants[] = $row['user_id'];
  }

  echo "<div class='layoutContainer'>";

  // Team Stats
  echo "<div class='teamStats'>";
  echo "<h2>Team Stats</h2>";
  echo "Total Stats: " . number_format($total_stats);
  echo "<h3>Participants:</h3>";

  // Convert participant IDs to formatted names
  $formatted_participants = array_map('formatName', $participants);
  echo implode(', ', $formatted_participants);

  echo "</div>";	
  // Boss Avatar and Boss Name
  echo "<div class='bossAvatar'>";
  $image_link = $boss_info['image_link'];
  echo "<img src='{$image_link}' alt='Boss Avatar' style='width: 180px; height: 180px;'>";
  
  // Boss Name in dark red 3D design
  $boss_name = $boss_info['name'];
  echo "<div class='bossName'>{$boss_name}</div>";

  echo "</div>";

  // Boss Stats
  echo "<div class='bossStats'>";
  echo "<h2>Boss Stats</h2>";
  echo "Boss Name: " . $boss_info['name'];
  echo "</div>";

  echo "</div>";  // End of layoutContainer

  // Display battle log
  if (mysql_num_rows($result) > 0) {
    $row = mysql_fetch_assoc($result);
    echo "<div class='raidLog'>" . nl2br($row['battle_log']) . "</div>";
  } else {
    echo "<h1>No battle log available for this raid.</h1>";
  }
} else {
  echo "Invalid raid ID.";
}

echo "</div>";  // End of mainContainer
?>