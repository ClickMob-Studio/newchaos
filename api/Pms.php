<?php
include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
include_once "includes/functions.php";
error_reporting(0);
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function getUserId() {
    $headers = apache_request_headers();
    if (isset($headers['UserId'])) {
        return intval($headers['UserId']);
    } else {
        respond(['error' => 'User ID is required'], 400);
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (isset($_GET['action'])) {
            $userId = getUserId();
            switch ($_GET['action']) {
                case 'inbox':
                    getInbox($userId);
                    break;
                case 'outbox':
                    getOutbox($userId);
                    break;
                case 'view':
                    if (isset($_GET['id'])) {
                        viewMessage($userId, $_GET['id']);
                    } else {
                        respond(['error' => 'Message ID is required'], 400);
                    }
                    break;
                case 'send':
                    sendMessage($userId);
                    break;
                case 'delete':
                    if (isset($_GET['id'])) {
                        deleteMessage($userId, $_GET['id']);
                    } else {
                        respond(['error' => 'Message ID is required'], 400);
                    }
                    break;
                case 'report':
                    if (isset($_GET['id'])) {
                        reportMessage($userId, $_GET['id']);
                    } else {
                        respond(['error' => 'Message ID is required'], 400);
                    }
                    break;
                case 'star':
                    if (isset($_GET['id'])) {
                        starMessage($userId, $_GET['id']);
                    } else {
                        respond(['error' => 'Message ID is required'], 400);
                    }
                    break;
                default:
                    respond(['error' => 'Invalid action'], 400);
            }
        } else {
            respond(['error' => 'Action is required'], 400);
        }
        break;

    default:
        respond(['error' => 'Method not allowed'], 405);
}

function getInbox($userId) {
    global $db;

    $db->query("SELECT * FROM pms WHERE `to` = ? ORDER BY timesent DESC");
    $db->execute([$userId]);
    $messages = $db->fetch_row();
    respond(['inbox' => $messages]);
}

function getOutbox($userId) {
    global $db;

    $db->query("SELECT * FROM pms WHERE `from` = ? ORDER BY timesent DESC");
    $db->execute([$userId]);
    $messages = $db->fetch_row();
    respond(['outbox' => $messages]);
}

function viewMessage($userId, $id) {
    global $db;

    $db->query("SELECT * FROM pms WHERE id = ? AND (`to` = ? OR `from` = ?)");
    $db->execute([$id, $userId, $userId]);
    $message = $db->fetch_row(true);
    if ($message) {
        respond(['message' => $message]);
    } else {
        respond(['error' => 'Message not found'], 404);
    }
}

function sendMessage($userId) {
    global $db;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['to']) || !isset($data['subject']) || !isset($data['msgtext'])) {
        respond(['error' => 'Missing required fields'], 400);
    }

    $to = $data['to'];
    $subject = strip_tags($data['subject']);
    $msgtext = strip_tags(nl2br($data['msgtext']));
    $bomb = isset($data['bomb']) ? $data['bomb'] : 0;

    $to_user = new User($to);
    $parent = 0; // Customize as needed

    $db->query("INSERT INTO pms (parent, `to`, `from`, timesent, subject, msgtext, bomb) VALUES (?, ?, ?, unix_timestamp(), ?, ?, ?)");
    $db->execute([$parent, $to, $userId, $subject, $msgtext, $bomb]);

    $db->query("INSERT INTO maillog (`to`, `from`, timesent, subject, msgtext) VALUES (?, ?, unix_timestamp(), ?, ?)");
    $db->execute([$to, $userId, $subject, $msgtext]);

    respond(['message' => 'Message sent successfully']);
}

function deleteMessage($userId, $id) {
    global $db;

    $db->query("DELETE FROM pms WHERE id = ? AND `to` = ? AND starred = 0");
    $db->execute([$id, $userId]);
    respond(['message' => 'Message deleted successfully']);
}

function reportMessage($userId, $id) {
    global $db;

    $db->query("UPDATE maillog SET reported = 1 WHERE id = ? AND `to` = ?");
    $db->execute([$id, $userId]);
    respond(['message' => 'Message reported successfully']);
}

function starMessage($userId, $id) {
    global $db;

    $db->query("SELECT starred FROM pms WHERE id = ? AND `to` = ?");
    $db->execute([$id, $userId]);
    $star = $db->fetch_single();
    $newStar = $star ? 0 : 1;

    $db->query("UPDATE pms SET starred = ? WHERE id = ? AND `to` = ?");
    $db->execute([$newStar, $id, $userId]);
    $message = $newStar ? 'Message starred successfully' : 'Message unstarred successfully';
    respond(['message' => $message]);
}
?>
