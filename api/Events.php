<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "../database/pdo_class.php";

function handleEventsRequest($method, $path_info)
{
    global $db;

    switch ($method) {
        case 'GET':
            getEvents($db);
            break;
        case 'DELETE':
            if (isset($path_info[1])) {
                $action = $path_info[1];
                if ($action == 'all') {
                    deleteAllEvents($db);
                } elseif ($action == 'attacks') {
                    deleteEventsByType($db, 'attacked you');
                } elseif ($action == 'mugs') {
                    deleteEventsByType($db, 'mugged', 'mug you');
                } elseif ($action == 'busts') {
                    deleteEventsByType($db, 'busted out');
                } else {
                    deleteEventById($db, $action);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid Request']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }
}

function getEvents($db)
{
    $user_id = $_GET['user_id'];
    $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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

function deleteAllEvents($db)
{
    $user_id = $_GET['user_id'];
    $db->query("DELETE FROM events WHERE `to` = :user_id");
    $db->bind(':user_id', $user_id, PDO::PARAM_INT);
    $db->execute();
    echo json_encode(['message' => 'All events deleted']);
}

function deleteEventsByType($db, $type1, $type2 = null)
{
    $user_id = $_GET['user_id'];
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

function deleteEventById($db, $id)
{
    $user_id = $_GET['user_id'];
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
