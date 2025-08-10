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

      $db->query("SELECT boss_id FROM active_raids WHERE id = ?");
      $db->execute([$raid_id]);
      $boss_id = $db->fetch_single();

      if (!isset($boss_id)) {
        die('Could not find this raid.');
      }

      $db->query("SELECT * FROM bosses WHERE id = ?");
      $db->execute([$boss_id]);
      $boss = $db->fetch_row(true);
      if (empty($boss)) {
        die("Could not find boss information.");
      }

      $db->query("SELECT battle_log FROM raid_battle_logs WHERE raid_id = ?");
      $db->execute([$raid_id]);
      $battle_log = $db->fetch_row(true);
      if (empty($battle_log)) {
        die("Could not find battle log information.");
      }

      $db->query("SELECT SUM(total) AS total_stats FROM grpgusers WHERE id IN (SELECT user_id FROM raid_participants WHERE raid_id = ?)");
      $db->execute([$raid_id]);
      $total_stats = $db->fetch_single();

      $db->query("SELECT user_id FROM raid_participants WHERE raid_id = ?");
      $db->execute([$raid_id]);
      $participants = $db->fetch_row();
      $ids = array_map('intval', array_column($participants, 'user_id'));

      echo "<div class='layoutContainer'>";

      // Team Stats
      echo "<div class='teamStats'>";
      echo "<h2>Team Stats</h2>";
      echo "Total Stats: " . number_format($total_stats);
      echo "<h3>Participants:</h3>";

      // Convert participant IDs to formatted names
      $formatted_participants = array_map('formatName', $ids);
      echo implode(', ', $formatted_participants);

      echo "</div>";
      // Boss Avatar and Boss Name
      echo "<div class='bossAvatar'>";
      $image_link = $boss['image_link'];
      echo "<img src='{$image_link}' alt='Boss Avatar' style='width: 180px; height: 180px;'>";

      // Boss Name in dark red 3D design
      $boss_name = $boss['name'];
      echo "<div class='bossName'>{$boss_name}</div>";

      echo "</div>";

      // Boss Stats
      echo "<div class='bossStats'>";
      echo "<h2>Boss Stats</h2>";
      echo "Boss Name: " . $boss['name'];
      echo "</div>";

      echo "</div>";  // End of layoutContainer
    
      // Display battle log
      if (isset($battle_log)) {
        echo "<div class='raidLog'>" . nl2br($battle_log['battle_log']) . "</div>";
      } else {
        echo "<h1>No battle log available for this raid.</h1>";
      }
    } else {
      echo "Invalid raid ID.";
    }

    echo "</div>";  // End of mainContainer
    require "footer.php";
    ?>