<?php

include "classes.php";
include "database/pdo_class.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$user_class = new User($_SESSION['id']);
if (isset($_GET['user_id'])) {
    $user_class = new User($_GET['user_id']);
}
if (isset($_POST['user_id'])) {
    $user_class = new User($_POST['user_id']);
}
session_write_close();

if (!$user_class) {
    echo json_encode(array('success' => false));
    exit;
}

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
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

        echo json_encode(array('success' => true));
        exit;
    }
}

echo json_encode(array('success' => false));
