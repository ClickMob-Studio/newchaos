<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
$questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
$questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
$questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

if ($questSeasonUser) {

?>

<h1><?php echo $currentQuestSeason['name'] ?>: <?php echo $questSeasonMission['name'] ?></h1><hr />

<p><?php echo $questSeasonMission['description'] ?></p>

<div class="row">
    <div class="col-md-12">
    </div>
</div>
<?php

} else {

}
