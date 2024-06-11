<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
include "../database/pdo_class.php";

function handleEventsRequest($method, $path_info)
{
    global $db;

    switch ($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['action'])) {
                $action = $input['action'];
                if ($action == 'get') {
                    getEvents($db, $input);
                } elseif ($action == 'deleteAll') {
                    deleteAllEvents($db, $input);
                } elseif ($action == 'deleteByType') {
                    deleteEventsByType($db, $input);
                } elseif ($action == 'deleteById') {
                    deleteEventById($db, $input);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid Request']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Action not specified']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }
}

function getEvents($db, $input)
{
    $user_id = $input['user_id'];
    $search = isset($input['search']) ? '%' . $input['search'] . '%' : null;
    $page = isset($input['page']) ? (int)$input['page'] : 1;
    $rowsperpage = 30;
    $offset = ($page - 1) * $rowsperpage;

    if ($search) {
        $db->query("SELECT * FROM events WHERE `to` = :user_id AND `text` LIKE :search ORDER BY `timesent` DESC LIMIT :offset, :rowsperpage");
        $db->bind(':search', $search);
    } else {
        $db->query("SELECT * FROM events WHERE `to` = :user_id ORDER BY `timesent` DESC LIMIT :offset, :rowsperpage");
    }

    $db->bind(':user_id', $user_id, PDO::PARAM_INT);
    $db->bind(':offset', $offset, PDO::PARAM_INT);
    $db->bind(':rowsperpage', $rowsperpage, PDO::PARAM_INT);
    $db->execute();

    $events = $db->fetch_row();

    echo json_encode($events);
}

function deleteAllEvents($db, $input)
{
    $user_id = $input['user_id'];
    $db->query("DELETE FROM events WHERE `to` = :user_id");
    $db->bind(':user_id', $user_id, PDO::PARAM_INT);
    $db->execute();
    echo json_encode(['message' => 'All events deleted']);
}

function deleteEventsByType($db, $input)
{
    $user_id = $input['user_id'];
    $type1 = $input['type1'];
    $type2 = isset($input['type2']) ? $input['type2'] : null;
    
    $query = "DELETE FROM events WHERE `to` = :user_id AND (`text` LIKE :type1";
    if ($type2) {
        $query .= " OR `text` LIKE :type2";
    }
    $query .= ")";
    
    $db->query($query);
    $type1 = '%' . $type1 . '%';
    $db->bind(':user_id', $user_id, PDO::PARAM_INT);
    $db->bind(':type1', $type1);
    if ($type2) {
        $type2 = '%' . $type2 . '%';
        $db->bind(':type2', $type2);
    }
    $db->execute();
    echo json_encode(['message' => 'Events deleted']);
}

function deleteEventById($db, $input)
{
    $user_id = $input['user_id'];
    $id = $input['id'];
    $db->query("DELETE FROM events WHERE id = :id AND `to` = :user_id");
    $db->bind(':id', $id, PDO::PARAM_INT);
    $db->bind(':user_id', $user_id, PDO::PARAM_INT);
    $db->execute();
    echo json_encode(['message' => 'Event deleted']);
}

// Routing logic
$method = $_SERVER['REQUEST_METHOD'];
$path_info = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];
handleEventsRequest($method, $path_info);
?>
