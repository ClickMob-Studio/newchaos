<?php

include "classes.php";
include "database/pdo_class.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = new User($_SESSION['id']);
session_write_close();

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

    $field = null;
    if (isset($_GET['field'])) {
        $field = $_GET['field'];
    }

    $value = null;
    if (isset($_GET['value'])) {
        $value = $_GET['value'];
    }
ß

    if (isset($questSeasonMission['requirements']->$field) && $field && $value) {
        updateQuestSeasonMissionUserProgress($questSeasonMissionUser, $field, $value);

        return array('success' => true);
    }
}

return array('success' => false);
