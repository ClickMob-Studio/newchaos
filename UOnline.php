<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

header('Content-Type: application/json');

try {
    $db->query("SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
    $rows = $db->fetch_row();

    if ($rows === false) {
        throw new Exception('Error fetching rows from the database.');
    }
    $onlineNow = count($rows);

    $store = array();
    foreach ($rows as $row) {
        $user_online = new User($row['id']);
        $store[] = array(
            'avatar' => $user_online->avatar,
            'formattedname' => generateFormattedName($user_online->id),
            'level' => $user_online->level,
            'money' => $user_online->money,
            'id' => $user_online->id,
            'formattedgang' => $user_online->formattedgang,
            'type' => $user_online->type,
            'cityname' => $user_online->cityname,
            'cityid' => $user_online->city,
            'hospital' => $user_online->hospital,
            'jail' => $user_online->jail,
            'lastactive' => howlongago($user_online->lastactive)
        );
    }

    echo json_encode(array('users_online' => $store, 'onlineNow' => $onlineNow));
} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage()
    ));
}

function generateFormattedName($id, $nogang = 0)
{
    global $db, $m;
    $name = "";

    if ($nogang == 0 && $id != 864 and !empty($rtn = $m->get('generateFormattedName.' . $id)))
        return $rtn;

    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, pdimgname, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array($id));
    $row = $db->fetch_row(true);

    // Gang logic
    if ($row['gang'] != 0 && $nogang != 1) {
        if ($row['formattedTag'] == "Yes") {
            $name .= "[" . ($row['leader'] == $id ? "[" . gradientTag($row['gang']) . "]" : gradientTag($row['gang'])) . "] ";
        } else {
            $name .= "[" . ($row['leader'] == $id ? "<b>{$row['tag']}</b>" : "{$row['tag']}") . "] ";
        }
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
        $name .= " <img src='images/skullpres_" . $row['prestige'] . ".png' alt='Prestige ({$row['prestige']})' />";
    }

    if ($nogang == 0) {
        $m->set('generateFormattedName.' . $id, $name, false, 60);
    }

    // Strip any remaining HTML tags and return clean text
    $name = strip_tags($name);

    return $name;
}
?>
