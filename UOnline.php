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
    if ($row['gang'] != 0 and $nogang != 1) {
        if ($id == 2) {
            if ($row['gndays'] > 0) {
                $name .= "<a style='font-size:1.5em;' href='viewgang.php?id={$row['gang']}'";
            } else {
                $name .= "<a href='viewgang.php?id={$row['gang']}'";
            }
        } else {
            $name .= "<a href='viewgang.php?id={$row['gang']}'";
        }
        if ($row['formattedTag'] == "Yes")
            $name .= ($row['leader'] == $id) ? " title='Gang Leader'><font color=grey>[<b>" . gradientTag($row['gang']) . "</b>]</font></a> " : "><font color=grey>[" . gradientTag($row['gang']) . "]</font></a> ";
        else
            $name .= ($row['leader'] == $id) ? " title='Gang Leader'><font color=blue>[<b>{$row['tag']}</b>]</font></a> " : "><font color=white>[{$row['tag']}]</font></a> ";
    }

    // Determine title and font color based on user status
    $db->query("SELECT days FROM bans WHERE id = ? AND type IN ('perm','freeze')");
    $db->execute(array($id));
    $bdays = $db->fetch_single();
    if ($bdays) {
        $title = "Banned";
        $whichfont = "#FFFFFF";
    } else if ($row['admin'] == 1) {
        $title = "Admin";
        $whichfont = "#FF1111";
    } else if ($row['gm'] == 1) {
        $title = "Chat Moderator";
        $whichfont = "#FFFFFF";
    } else if ($row['rmdays'] >= 1) {
        $title = "VIP ({$row['rmdays']} VIP Days Left)";
        $whichfont = "#00BF03";
    } else {
        $title = "Not Respected";
        $whichfont = "#009102";
    }

    // User name with image
    if (!empty($row['image_name']) && $row['pdimgname'] > 0) {
        $name .= "<a title='" . $title . " [" . $row['username'] . "]' href='profiles.php?id=" . $id . "'>";
        $name .= "<img id='main-image' src='{$row['image_name']}' style='max-width:84px; max-height:50px;' title='" . $row['username'] . "' />";
        $name .= "</a>";
    } else {
        $name .= "<a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</font></a>";
    }

    // Add prestige image
    if ($row['prestige'] > 0) {
        $name .= " <img id='prestige-image' src='images/skullpres_" . $row['prestige'] . ".png' title='Prestige ({$row['prestige']})' />";
    }

    if ($nogang == 0)
        $m->set('generateFormattedName.' . $id, $name, false, 60);

    return $name;
}



