<?php

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

include "classes.php";
include "database/pdo_class.php";


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

error_log("REACHED UNO!");
try {
    $currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
    error_log("REACHED DOS!");
    if (isset($currentQuestSeason['id'])) {
        error_log("REACHED TRES!");
        $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
        error_log("REACHED QUATTRO!");
        $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
        error_log("REACHED SINGO!");
        $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
        error_log("REACHED SES!");

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

        error_log("REACHED OCHO!");
        if (isset($questSeasonMission['requirements']->$field) && $field && $value) {
            updateQuestSeasonMissionUserProgress($questSeasonMissionUser, $field, $value);
            error_log("REACHED NUEVE!");

            echo json_encode(array('success' => true));
            exit;
        }
    }

    error_log("REACHED DIEZ!");
    echo json_encode(array('success' => false));
    exit;
} catch (Throwable $e) {
    error_log("REACHED ONCE!");

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'server_error']);
    exit;
}
