<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
$questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
$questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
$questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

if ($questSeasonUser) {

?>

<h1>Quest: <?php echo $currentQuestSeason['name'] ?></h1><hr />
<p><?php echo $currentQuestSeason['description'] ?></p>
<hr />

<h2>Mission: <?php echo $questSeasonMission['name'] ?></h2>
<p><?php echo $questSeasonMission['description'] ?></p>

<h2><strong>Progress:</strong></h2>
<ul>
    <?php foreach ($questSeasonMission['requirements'] as $req => $num): ?>
        <li><?php echo getDisplayForQuestReq($req, $num) ?></li>
    <?php endforeach; ?>
</ul>

    <?php

} else {

}
