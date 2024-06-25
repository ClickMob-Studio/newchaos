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
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);
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
function replaceUserIdWithUsername($db, $text, $userId) {
    global $m;
    $name = "";

    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array($userId));
    $row = $db->fetch_row(true);

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

    $usernameElement = "<span style='color: $whichfont; display: inline-block;'>{$row['username']}</span>";

    $name .= "<div class='d-flex align-items-center flex-wrap' style='gap: 5px;'>";

    if ($row['gang'] != 0) {
        $name .= "<span class='text-gray' style='display: inline-block;'>[<b>{$row['tag']}</b>]</span>";
    }

    if ($bdays) {
        $name .= $usernameElement;
    } elseif (!empty($row['image_name'])) {
        $name .= "<img src='{$row['image_name']}' class='img-fluid' style='max-width:84px; max-height:50px; display: inline-block; vertical-align: middle;' title='{$row['username']}' />";
    } elseif ($row['gndays']) {
        $name .= "<span style='color: $whichfont; display: inline-block;'>" . nameGen($row['gndays'], $row['rmdays'], $row['uninfo'], $row['username']) . "</span>";
    } elseif (!empty($row['colours']) && $row['gradient'] == 2 && $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $colours = explode("~", $row['colours']);
        $gradient = text_gradient_function($colours[0], $colours[1], 1, $row['username']);
        $name .= "<span style='color: $whichfont; display: inline-block;'><b><i>{$gradient}</i></b></span>";
    } elseif (!empty($row['colours']) && $row['gradient'] == 3 && $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $gn = explode("~", $row['colours']);
        $username = $row['username'];
        $half = (int) ((strlen($username) / 2));
        $left = substr($username, 0, $half);
        $right = substr($username, $half);
        $gradient = text_gradient_function($gn[0], $gn[1], 1, $left);
        $gradient .= text_gradient_function($gn[1], $gn[2], 1, $right);
        if ($userId == 146) $gradient = "<span style='text-shadow: 0 0 2px #404200;letter-spacing:-1px;font-weight:900;font-size:16px;'>$gradient</span>";
        $name .= "<span style='color: $whichfont; display: inline-block;'><b><i>{$gradient}</i></b></span>";
    } elseif ($userId == 146) {
        $name .= $usernameElement;
    } elseif ($row['admin'] == 1 || $row['gm'] == 1) {
        $name .= "<span style='color: $whichfont; display: inline-block;'><i><b>{$row['username']}</b></i></span>";
    } elseif ($row['rmdays'] > 0) {
        $name .= "<span style='color: $whichfont; display: inline-block;'><b>{$row['username']}</b></span>";
    } else {
        $name .= $usernameElement;
    }

    if ($row['prestige'] > 0) {
        if ($row['prestige'] >= 10) {
            $db->query("SELECT skull FROM prestige_skull WHERE `user_id` = ?");
            $db->execute(array($userId));
            $skull = $db->fetch_single();
            if ($skull !== false) {
                $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $skull . ".png' class='img-fluid' style='display: inline-block; vertical-align: middle;' title='Prestige ({$row['prestige']})' />";
            } else {
                $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $row['prestige'] . ".png' class='img-fluid' style='display: inline-block; vertical-align: middle;' title='Prestige ({$row['prestige']})' />";
            }
        } else {
            $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $row['prestige'] . ".png' class='img-fluid' style='display: inline-block; vertical-align: middle;' title='Prestige ({$row['prestige']})' />";
        }
    }

    $name .= "</div>";

    error_log("Formatted username: " . $name);  // Debug log for checking the formatted username
    $m->set('formatName.' . $userId, $name, false, 60);
    return str_replace('[-_USERID_-]', $name, $text);
}

function text_gradient_function($startcol, $endcol, $fontsize, $user)
{
    $letters = str_split($user, 1);
    $graduations = count($letters);
    $graduations--;
    $startcoln['r'] = hexdec(substr($startcol, 0, 2));
    $startcoln['g'] = hexdec(substr($startcol, 2, 2));
    $startcoln['b'] = hexdec(substr($startcol, 4, 2));
    $GSize['r'] = (hexdec(substr($endcol, 0, 2)) - $startcoln['r']) / $graduations;
    $GSize['g'] = (hexdec(substr($endcol, 2, 2)) - $startcoln['g']) / $graduations;
    $GSize['b'] = (hexdec(substr($endcol, 4, 2)) - $startcoln['b']) / $graduations;
    for ($i = 0; $i <= $graduations; $i++) {
        $HexR = dechex(intval($startcoln['r'] + ($GSize['r'] * $i)));
        $HexG = dechex(intval($startcoln['g'] + ($GSize['g'] * $i)));
        $HexB = dechex(intval($startcoln['b'] + ($GSize['b'] * $i)));
        if (strlen($HexR) == 1)
            $HexR = "0$HexR";
        if (strlen($HexG) == 1)
            $HexG = "0$HexG";
        if (strlen($HexB) == 1)
            $HexB = "0$HexB";
        $HexCol[] = "$HexR$HexG$HexB";
    }
    $i = 0;
    $user = "";
    while ($i < count($letters)) {
        $user .= "<span style=\"color:#$HexCol[$i]\">{$letters[$i]}</span>";
        $i++;
    }
    return $user;
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
        $query = "SELECT pms.*, grpgusers.username as from_username, u2.username as to_username
                  FROM pms 
                  JOIN grpgusers ON pms.from = grpgusers.id
                  JOIN grpgusers u2 ON pms.to = u2.id
                  WHERE pms.`to` = :userId 
                  ORDER BY timesent DESC 
                  LIMIT :limit OFFSET :offset";
        $db->query($query);
        $db->bind(':userId', $userId);
        $db->bind(':limit', $limit);
        $db->bind(':offset', $offset);
        $db->execute();
        $messages = $db->fetch_row();

        foreach ($messages as &$message) {
            $message['from_username'] = replaceUserIdWithUsername($db, $message['from_username'], $message['from']);
        }

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
        $query = "SELECT pms.*, grpgusers.username as from_username, u2.username as to_username
                  FROM pms 
                  JOIN grpgusers ON pms.from = grpgusers.id
                  JOIN grpgusers u2 ON pms.to = u2.id
                  WHERE pms.`from` = :userId 
                  ORDER BY timesent DESC 
                  LIMIT :limit OFFSET :offset";
        $db->query($query);
        $db->bind(':userId', $userId);
        $db->bind(':limit', $limit);
        $db->bind(':offset', $offset);
        $db->execute();
        $messages = $db->fetch_row();

        foreach ($messages as &$message) {
            $message['to_username'] = replaceUserIdWithUsername($db, $message['to_username'], $message['to']);
        }

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
        $query = "SELECT pms.*, grpgusers.username as from_username, u2.username as to_username
                  FROM pms 
                  JOIN grpgusers ON pms.from = grpgusers.id
                  JOIN grpgusers u2 ON pms.to = u2.id
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
