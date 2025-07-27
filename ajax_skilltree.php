<?php

include_once "ajax_header.php";

$id = $_SESSION['id'];
if (!$id) {
    echo json_encode(['error' => 'Invalid session ID']);
    exit;
}

include_once "SlimUser.php";

$user_class = new SlimUser($id);
if (empty($user_class->id)) {
    echo json_encode(['error' => 'User not found']);
    exit;
}


require_once "includes/functions.php";

$action = isset($_GET['action']) ? $_GET['action'] : null;
if (!isset($action) || empty($action)) {
    echo json_encode(['error' => 'No action specified']);
    exit;
}

switch ($action) {
    /* Failure output:
    {
        "error": "Message pertaining to the error"
    }

    Success output:
    {
        "success": "Message pertaining to the success",
        "skill_id": 123,
        "remaining_points": 5
    }
    */
    case 'claim_skill':
        $skill_id = filter_input(INPUT_GET, 'skill', FILTER_VALIDATE_INT);
        if (!$skill_id) {
            echo json_encode(['error' => 'Invalid skill ID']);
            exit;
        }

        if ($user_class->skill_points <= 0) {
            echo json_encode(['error' => 'You do not have enough skill points to claim this skill.']);
            exit;
        }

        if (in_array($skill_id, $user_class->skill_ids)) {
            echo json_encode(['error' => 'You already have this skill.']);
            exit;
        }

        $error = claim_skill($user_class->id, $skill_id);
        if (empty($error)) {
            $user_class->skill_ids[] = $skill_id;
            $user_class->skill_points--;
            echo json_encode(['success' => 'Skill claimed successfully.', 'skill_id' => $skill_id, 'remaining_points' => $user_class->skill_points]);
        } else {
            echo json_encode(['error' => 'Failed to claim the skill, ' . $error]);
        }

        break;
}
?>