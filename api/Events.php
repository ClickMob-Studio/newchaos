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
            getEvents($db);
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }
}

function getEvents($db)
{
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = isset($input['userId']) ? $input['userId'] : null;
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

function replaceUserIdWithUsername($db, $text, $userId)
{
    return generateFormattedName($userId);
}

function generateFormattedName($id, $nogang = 0)
{
    global $db, $m;
    $name = "";

    if ($nogang == 0 && $id != 864 && !empty($rtn = $m->get('generateFormattedName.' . $id))) {
        return $rtn;
    }

    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, pdimgname, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array($id));
    $row = $db->fetch_row(true);

    // Gang logic
    if ($row['gang'] != 0 && $nogang != 1) {
        $name .= "[{$row['tag']}] ";
    }

    // Determine title and font color based on user status
    $db->query("SELECT days FROM bans WHERE id = ? AND type IN ('perm','freeze')");
    $db->execute(array($id));
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

    // User name with image and prestige image
    $name .= $row['username'];

    if ($row['prestige'] > 0) {
        $name .= " <img src='images/skullpres_" . $row['prestige'] . ".png' title='Prestige ({$row['prestige']})' />";
    }

    if ($nogang == 0) {
        $m->set('generateFormattedName.' . $id, $name, false, 60);
    }

    return $name;
}




// Routing logic
$method = $_SERVER['REQUEST_METHOD'];
$path_info = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];
handleEventsRequest($method, $path_info);
?>
