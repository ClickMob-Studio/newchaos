<?php
include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
include_once "includes/functions.php";

// Set error reporting level and use default error log
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display of errors to the user
ini_set('log_errors', 1);

$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

header('Access-Control-Allow-Origin: *'); // Allows all origins, replace '*' with specific domain for production
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allowed HTTP methods
header('Access-Control-Allow-Headers: Content-Type, Authorization, UserId'); // Allowed headers
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function getallheaders() {
    if (!is_array($_SERVER)) {
        return [];
    }

    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

function getUserId() {
    $headers = getallheaders();
    if (isset($headers['Userid'])) {
        return intval($headers['Userid']);
    } else {
        respond(['error' => 'User ID is required'], 400);
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (isset($_GET['action'])) {
            try {
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
            } catch (Exception $e) {
                respond(['error' => 'An error occurred: ' . $e->getMessage()], 500);
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
    try {
        $db->query("SELECT * FROM pms WHERE `to` = ? ORDER BY timesent DESC");
        $db->execute([$userId]);
        $messages = $db->fetch_row();
        respond(['inbox' => $messages]);
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while fetching inbox'], 500);
    }
}

function getOutbox($userId) {
    global $db;
    try {
        $db->query("SELECT * FROM pms WHERE `from` = ? ORDER BY timesent DESC");
        $db->execute([$userId]);
        $messages = $db->fetch_row();
        respond(['outbox' => $messages]);
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while fetching outbox'], 500);
    }
}

function viewMessage($userId, $id) {
    global $db;
    try {
        $db->query("SELECT * FROM pms WHERE id = ? AND (`to` = ? OR `from` = ?)");
        $db->execute([$id, $userId, $userId]);
        $message = $db->fetch_row(true);
        if ($message) {
            respond(['message' => $message]);
        } else {
            respond(['error' => 'Message not found'], 404);
        }
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while viewing message'], 500);
    }
}

function sendMessage($userId) {
    global $db;
    try {
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
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while sending message'], 500);
    }
}

function deleteMessage($userId, $id) {
    global $db;
    try {
        $db->query("DELETE FROM pms WHERE id = ? AND `to` = ? AND starred = 0");
        $db->execute([$id, $userId]);
        respond(['message' => 'Message deleted successfully']);
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while deleting message'], 500);
    }
}

function reportMessage($userId, $id) {
    global $db;
    try {
        $db->query("UPDATE maillog SET reported = 1 WHERE id = ? AND `to` = ?");
        $db->execute([$id, $userId]);
        respond(['message' => 'Message reported successfully']);
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while reporting message'], 500);
    }
}

function starMessage($userId, $id) {
    global $db;
    try {
        $db->query("SELECT starred FROM pms WHERE id = ? AND `to` = ?");
        $db->execute([$id, $userId]);
        $star = $db->fetch_single();
        $newStar = $star ? 0 : 1;

        $db->query("UPDATE pms SET starred = ? WHERE id = ? AND `to` = ?");
        $db->execute([$newStar, $id, $userId]);
        $message = $newStar ? 'Message starred successfully' : 'Message unstarred successfully';
        respond(['message' => $message]);
    } catch (Exception $e) {
        respond(['error' => 'An error occurred while starring message'], 500);
    }
}
?>
