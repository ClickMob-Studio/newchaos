<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

header('Content-Type: application/json');

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

    foreach ($events as &$event) {
        // Replace the [-_USERID_-] placeholder
        if (strpos($event['text'], '[-_USERID_-]') !== false) {
            $event['text'] = replaceUserIdWithUsername($db, $event['text'], $event['extra']);
        }

        // Extract and replace user IDs from profile links
        $event['text'] = preg_replace_callback(
            "/<a [^>]*href='profiles.php\?id=(\d+)'[^>]*>(.*?)<\/a>/",
            function ($matches) use ($db) {
                $userId = $matches[1];
                $username = replaceUserIdWithUsername($db, '[-_USERID_-]', $userId);
                return "<span style='color: inherit; text-decoration: none; display:inline;'>$username</span>";
            },
            $event['text']
        );
    }

    echo json_encode($events);
}

function replaceUserIdWithUsername($db, $text, $userId) {
    global $m;
    $name = "";
    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, pdimgname, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array($userId));
    $row = $db->fetch_row(true);
    if ($row['gang'] != 0) {
        //if ($row['formattedTag'] == "Yes") {
            //if ($row['leader'] == $userId) {
               // $name .= "<span style='color: grey; display:inline;'>[<b>" . ($row['gang']) . "</b>]</span> ";
           // } else {
             //   $name .= "<span style='color: grey; display:inline;'>[" . gradientTag($row['gang']) . "]</span> ";
            //}
        //} else {
            //if ($row['leader'] == $userId) {
              //  $name .= "<span style='color: blue; display:inline;'>[<b>{$row['tag']}</b>]</span> ";
            //} else {
               // $name .= "<span style='color: white; display:inline;'>[{$row['tag']}]</span> ";
            //}
        //}
    }
    $db->query("SELECT days FROM bans WHERE id = ? AND type IN ('perm','freeze')");
    $db->execute(array($userId));
    $bdays = $db->fetch_single();
    if ($bdays) {
        $title = "Banned";
        $whichfont = "#FFFFFF";
    } elseif ($row['admin'] == 1) {
        $title = "Admin";
        $whichfont = "#FF1111";
    } elseif ($row['gm'] == 1) {
        $title = "Chat Moderator";
        $whichfont = "#FFFFFF";
    } elseif ($row['rmdays'] >= 1) {
        $title = "VIP ({$row['rmdays']} VIP Days Left)";
        $whichfont = "#00BF03";
    } else {
        $title = "Not Respected";
        $whichfont = "#009102";
    }
    if ($bdays) {
        $name .= "<span style='color: $whichfont; display:inline;'>{$row['username']}</span>";
    } elseif (!empty($row['image_name']) && $row['pdimgname'] > 0) {
        $name .= "<img src='{$row['image_name']}' style='max-width:84px; max-height:50px; display:inline; vertical-align:middle;' title='" . $row['username'] . "' />";
    } elseif ($row['gndays']) {
        $name .= "<span style='color: $whichfont; display:inline;'>" . nameGen($row['gndays'], $row['rmdays'], $row['uninfo'], $row['username']) . "</span>";
    } elseif (!empty($row['colours']) && $row['gradient'] == 2 && $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $colours = explode("~", $row['colours']);
        $gradient = text_gradient($colours[0], $colours[1], 1, $row['username']);
        $name .= "<span style='color: $whichfont; display:inline;'><b><i>{$gradient}</i></b></span>";
    } elseif (!empty($row['colours']) && $row['gradient'] == 3 && $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $gn = explode("~", $row['colours']);
        $username = $row['username'];
        $half = (int) ((strlen($username) / 2));
        $left = substr($username, 0, $half);
        $right = substr($username, $half);
        $gradient = text_gradient($gn[0], $gn[1], 1, $left);
        $gradient .= text_gradient($gn[1], $gn[2], 1, $right);
        if ($userId == 146) $gradient = "<span style='text-shadow: 0 0 2px #404200;letter-spacing:-1px;font-weight:900;font-size:16px;'>$gradient</span>";
        $name .= "<span style='color: $whichfont; display:inline;'><b><i>{$gradient}</i></b></span>";
    } elseif ($userId == 146) {
        $name .= "<span style='color: $whichfont; display:inline;'>{$row['username']}</span>";
    } elseif ($row['admin'] == 1 || $row['gm'] == 1) {
        $name .= "<span style='color: $whichfont; display:inline;'><i><b>{$row['username']}</b></i></span>";
    } elseif ($row['rmdays'] > 0) {
        $name .= "<span style='color: $whichfont; display:inline;'><b>{$row['username']}</b></span>";
    } else {
        $name .= "<span style='color: $whichfont; display:inline;'>{$row['username']}</span>";
    }
    if ($row['prestige'] > 0) {
        if ($row['prestige'] >= 10) {
            $db->query("SELECT skull FROM prestige_skull WHERE `user_id` = ?");
            $db->execute(array($userId));
            $skull = $db->fetch_single();
            if ($skull !== false) {
                $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $skull . ".png' style='display:inline;' title='Prestige ({$row['prestige']})' />";
            } else {
                $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $row['prestige'] . ".png' style='display:inline; vertical-align:middle;' title='Prestige ({$row['prestige']})' />";
            }
        } else {
            $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $row['prestige'] . ".png' style='display:inline; vertical-align:middle;' title='Prestige ({$row['prestige']})' />";
        }
    }
    if ($nogang == 0) $m->set('formatName.' . $userId, $name, false, 60);
    return str_replace('[-_USERID_-]', $name, $text);
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
    $userId = $input['id'];
    $db->query("DELETE FROM events WHERE id = :id AND `to` = :user_id");
    $db->bind(':id', $userId, PDO::PARAM_INT);
    $db->bind(':user_id', $user_id, PDO::PARAM_INT);
    $db->execute();
    echo json_encode(['message' => 'Event deleted']);
}

// Routing logic
$method = $_SERVER['REQUEST_METHOD'];
$path_info = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];
handleEventsRequest($method, $path_info);
?>
