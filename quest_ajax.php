<?php

header('Content-Type: application/json');

include "classes.php";
include "database/pdo_class.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (!isset($_SESSION['id']) && !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_class = new User($_SESSION['id']);
if (isset($_GET['user_id'])) {
    $user_class = new User($_GET['user_id']);
}
if (isset($_POST['user_id'])) {
    $user_class = new User($_POST['user_id']);
}

if (!$user_class) {
    echo json_encode(array('success' => false));
    exit;
}

try {
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
    exit;
} catch (Throwable $e) {
    // Last-resort JSON error so the response is never empty
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'server_error']);
    exit;
}
