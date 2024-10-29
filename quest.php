<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
$questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);

var_dump($questSeasonUser);