<?php

include "classes.php";
include "database/pdo_class.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = new User($_SESSION['id']);
if (isset($_GET['user_id'])) {
    $user_class = new User($_GET['user_id']);
}
if (isset($_POST['user_id'])) {
    $user_class = new User($_POST['user_id']);
}
session_write_close();

if (!$user_class) {
    return json_encode(array('success' => false));
}

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

    $field = null;
    if (isset($_POST['field'])) {
        $field = $_POST['field'];
    }
    if (isset($_GET['field'])) {
        $field = $_GET['field'];
    }

    $value = null;
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    if (isset($_GET['value'])) {
        $value = $_GET['value'];
    }

    if (isset($questSeasonMission['requirements']->$field) && $field && $value) {
        updateQuestSeasonMissionUserProgress($questSeasonMissionUser, $field, $value);

        return json_encode(array('success' => true));
    }
}

return json_encode(array('success' => false));
