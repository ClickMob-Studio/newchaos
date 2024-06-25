<?php
include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
include_once "includes/functions.php";

error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display of errors to the user
ini_set('log_errors', 1);
ini_set('error_log', 'api.log');

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
                error_log('Error in switch case: ' . $e->getMessage());
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
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

    error_log("Fetching inbox for userId: $userId with limit: $limit and offset: $offset");

    try {
        $query = "SELECT pms.*, users.username as from_username, u2.username as to_username
                  FROM pms 
                  JOIN users ON pms.from = users.id
                  JOIN users u2 ON pms.to = u2.id
                  WHERE pms.`to` = :userId 
                  ORDER BY timesent DESC 
                  LIMIT :limit OFFSET :offset";
        $db->query($query);
        $db->bind(':userId', $userId);
        $db->bind(':limit', $limit);
        $db->bind(':offset', $offset);
        $db->execute();
        $messages = $db->fetch_row();

        error_log("Fetched messages: " . print_r($messages, true));

        $hasMore = count($messages) === $limit;
        respond(['inbox' => $messages, 'hasMore' => $hasMore]);
    } catch (Exception $e) {
        error_log('Error in getInbox: ' . $e->getMessage());
        respond(['error' => 'An error occurred while fetching inbox'], 500);
    }
}

function getOutbox($userId) {
    global $db;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    error_log("Fetching outbox for userId: $userId with limit: $limit and offset: $offset");

    try {
        $query = "SELECT pms.*, users.username as from_username, u2.username as to_username
                  FROM pms 
                  JOIN users ON pms.from = users.id
                  JOIN users u2 ON pms.to = u2.id
                  WHERE pms.`from` = :userId 
                  ORDER BY timesent DESC 
                  LIMIT :limit OFFSET :offset";
        $db->query($query);
        $db->bind(':userId', $userId);
        $db->bind(':limit', $limit);
        $db->bind(':offset', $offset);
        $db->execute();
        $messages = $db->fetch_row();

        error_log("Fetched messages: " . print_r($messages, true));

        $hasMore = count($messages) === $limit;
        respond(['outbox' => $messages, 'hasMore' => $hasMore]);
    } catch (Exception $e) {
        error_log('Error in getOutbox: ' . $e->getMessage());
        respond(['error' => 'An error occurred while fetching outbox'], 500);
    }
}

function viewMessage($userId, $id) {
    global $db;
    try {
        $query = "SELECT pms.*, users.username as from_username, u2.username as to_username
                  FROM pms 
                  JOIN users ON pms.from = users.id
                  JOIN users u2 ON pms.to = u2.id
                  WHERE pms.id = ? AND (pms.`to` = ? OR pms.`from` = ?)";
        $db->query($query);
        $db->execute([$id, $userId, $userId]);
        $message = $db->fetch_row(true);
        if ($message) {
            respond(['message' => $message]);
        } else {
            respond(['error' => 'Message not found'], 404);
        }
    } catch (Exception $e) {
        error_log('Error in viewMessage: ' . $e->getMessage());
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

        $query = "INSERT INTO pms (parent, `to`, `from`, timesent, subject, msgtext, bomb) VALUES (?, ?, ?, unix_timestamp(), ?, ?, ?)";
        $db->query($query);
        $db->execute([0, $to, $userId, $subject, $msgtext, $bomb]);

        $query = "INSERT INTO maillog (`to`, `from`, timesent, subject, msgtext) VALUES (?, ?, unix_timestamp(), ?, ?)";
        $db->query($query);
        $db->execute([$to, $userId, $subject, $msgtext]);

        respond(['message' => 'Message sent successfully']);
    } catch (Exception $e) {
        error_log('Error in sendMessage: ' . $e->getMessage());
        respond(['error' => 'An error occurred while sending message'], 500);
    }
}

function deleteMessage($userId, $id) {
    global $db;
    try {
        $query = "DELETE FROM pms WHERE id = ? AND `to` = ? AND starred = 0";
        $db->query($query);
        $db->execute([$id, $userId]);
        respond(['message' => 'Message deleted successfully']);
    } catch (Exception $e) {
        error_log('Error in deleteMessage: ' . $e->getMessage());
        respond(['error' => 'An error occurred while deleting message'], 500);
    }
}

function reportMessage($userId, $id) {
    global $db;
    try {
        $query = "UPDATE maillog SET reported = 1 WHERE id = ? AND `to` = ?";
        $db->query($query);
        $db->execute([$id, $userId]);
        respond(['message' => 'Message reported successfully']);
    } catch (Exception $e) {
        error_log('Error in reportMessage: ' . $e->getMessage());
        respond(['error' => 'An error occurred while reporting message'], 500);
    }
}

function starMessage($userId, $id) {
    global $db;
    try {
        $query = "SELECT starred FROM pms WHERE id = ? AND `to` = ?";
        $db->query($query);
        $db->execute([$id, $userId]);
        $star = $db->fetch_single();
        $newStar = $star ? 0 : 1;

        $query = "UPDATE pms SET starred = ? WHERE id = ? AND `to` = ?";
        $db->query($query);
        $db->execute([$newStar, $id, $userId]);
        $message = $newStar ? 'Message starred successfully' : 'Message unstarred successfully';
        respond(['message' => $message]);
    } catch (Exception $e) {
        error_log('Error in starMessage: ' . $e->getMessage());
        respond(['error' => 'An error occurred while starring message'], 500);
    }
}
?>
