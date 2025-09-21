<?php

header('Content-Type: application/json');

require_once __DIR__ . '/classes.php';
require_once __DIR__ . '/database/pdo_class.php';

$id = null;
if (isset($_GET['user_id'])) {
    $id = $_GET['user_id'];
}
if (isset($_POST['user_id'])) {
    $id = $_POST['user_id'];
}

if (!isset($id)) {
    echo json_encode(array('success' => false));
    exit;
}

$user_class = new User($id);
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

        if (isset($questSeasonMission['requirements'][$field]) && $field && $value) {
            updateQuestSeasonMissionUserProgress($questSeasonMissionUser, $field, $value);

            echo json_encode(array('success' => true));
            exit;
        }
    }

    echo json_encode(array('success' => false));
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'server_error']);
    exit;
}