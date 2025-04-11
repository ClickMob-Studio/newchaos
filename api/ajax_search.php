<?php
include "../database/pdo_class.php";
include "../classes.php";
include "../codeparser.php";
include_once "includes/functions.php";
error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

function formatUName($id, $nogang = 0)
{
    global $db;
    $name = "";

    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, pdimgname, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array($id));
    $row = $db->fetch_row(true);

    if ($row['gang'] != 0 && $nogang != 1) {
        if ($row['formattedTag'] == "Yes") {
            $name .= ($row['leader'] == $id)
                ? "<span style='color: grey;'>[<b>" . gradientTag($row['gang']) . "</b>]</span> "
                : "<span style='color: grey;'>[" . gradientTag($row['gang']) . "]</span> ";
        } else {
            $name .= ($row['leader'] == $id)
                ? "<span style='color: blue;'>[<b>{$row['tag']}</b>]</span> "
                : "<span style='color: white;'>[{$row['tag']}]</span> ";
        }
    }

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

    if ($bdays) {
        $name .= "<span style='color: $whichfont;'>{$row['username']}</span>";
    } elseif (!empty($row['image_name']) && $row['pdimgname'] > 0) {
        $name .= "<img src='{$row['image_name']}' style='max-width:84px; max-height:50px;' title='" . $row['username'] . "' />";
    } elseif ($row['gndays']) {
        $name .= "<span style='color: $whichfont;'>" . nameGen($row['gndays'], $row['rmdays'], $row['uninfo'], $row['username']) . "</span>";
    } elseif (!empty($row['colours']) && $row['gradient'] == 2 && $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $colours = explode("~", $row['colours']);
        $gradient = text_gradient($colours[0], $colours[1], 1, $row['username']);
        $name .= "<span style='color: $whichfont;'><b><i>{$gradient}</i></b></span>";
    } elseif (!empty($row['colours']) && $row['gradient'] == 3 && $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $gn = explode("~", $row['colours']);
        $username = $row['username'];
        $half = (int) ((strlen($username) / 2));
        $left = substr($username, 0, $half);
        $right = substr($username, $half);
        $gradient = text_gradient($gn[0], $gn[1], 1, $left);
        $gradient .= text_gradient($gn[1], $gn[2], 1, $right);
        if ($id == 146)
            $gradient = "<span style='text-shadow: 0 0 2px #404200;letter-spacing:-1px;font-weight:900;font-size:16px;'>$gradient</span>";
        $name .= "<span style='color: $whichfont;'><b><i>{$gradient}</i></b></span>";
    } elseif ($id == 146) {
        $name .= "<span style='color: $whichfont;'>{$row['username']}</span>";
    } elseif ($row['admin'] == 1 || $row['gm'] == 1) {
        $name .= "<span style='color: $whichfont;'><i><b>{$row['username']}</b></i></span>";
    } elseif ($row['rmdays'] > 0) {
        $name .= "<span style='color: $whichfont;'><b>{$row['username']}</b></span>";
    } else {
        $name .= "<span style='color: $whichfont;'>{$row['username']}</span>";
    }

    if ($row['prestige'] > 0) {
        if ($row['prestige'] >= 10) {
            $db->query("SELECT skull FROM prestige_skull WHERE `user_id` = ?");
            $db->execute(array($id));
            $skull = $db->fetch_single();

            if ($skull !== false) {
                $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $skull . ".png' title='Prestige ({$row['prestige']})' />";
            } else {
                $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $row['prestige'] . ".png' title='Prestige ({$row['prestige']})' />";
            }
        } else {
            $name .= " <img src='https://chaoscity.co.uk/images/skullpres_" . $row['prestige'] . ".png' title='Prestige ({$row['prestige']})' />";
        }
    }

    return $name;
}

$response = [
    'status' => 'error',
    'data' => null
];

try {
    $userId = $_POST['user_id'];
    $user_class = new User($userId);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $search = [];

        if (isset($_POST['id'])) {
            $search['id'] = abs((int) $_POST['id']);
        }
        if (isset($_POST['name'])) {
            $search['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        }
        if (isset($_POST['money'])) {
            $search['money'] = abs((int) $_POST['money']);
        }
        if (isset($_POST['level'])) {
            $search['level'] = abs((int) $_POST['level']);
        }
        if (isset($_POST['level2'])) {
            $search['level2'] = abs((int) $_POST['level2']);
        }
        if (isset($_POST['lastactive'])) {
            $search['lastactive'] = abs((int) $_POST['lastactive']);
        }
        if (isset($_POST['lastactive2'])) {
            $search['lastactive2'] = abs((int) $_POST['lastactive2']);
        }
        if (isset($_POST['attack'])) {
            $search['attack'] = abs((int) $_POST['attack']);
        }
        if (isset($_POST['location'])) {
            $search['location'] = abs((int) $_POST['location']);
        }
        if (isset($_POST['gang'])) {
            $search['gang'] = abs((int) $_POST['gang']);
        }
        if (isset($_POST['online'])) {
            $search['online'] = abs((int) $_POST['online']);
        }

        $sql = "id != 0";
        $bindParams = [];

        if (!empty($search['id']) && $search['id'] != 0) {
            $sql .= " AND id = :id";
            $bindParams[':id'] = $search['id'];
        }
        if (!empty($search['name'])) {
            $sql .= " AND username LIKE :name";
            $bindParams[':name'] = "%" . $search['name'] . "%";
        }
        if ($search['level'] > 0 || $search['level2'] > 0) {
            $sql .= " AND (`level` >= :level AND `level` <= :level2)";
            $bindParams[':level'] = $search['level'];
            $bindParams[':level2'] = $search['level2'];
        }
        if (!empty($search['lastactive']) && !empty($search['lastactive2'])) {
            $la = $search['lastactive'] * 86400;
            $la2 = $search['lastactive2'] * 86400;
            $sql .= " AND (`lastactive` >= :lastactive AND `lastactive` <= :lastactive2)";
            $bindParams[':lastactive'] = $la;
            $bindParams[':lastactive2'] = $la2;
        }
        if (!empty($search['location']) && $search['location'] != 0) {
            $sql .= " AND city = :location AND eqarmor <> 43";
            $bindParams[':location'] = $search['location'];
        }
        if (!empty($search['gang']) && $search['gang'] != 0 && $search['gang'] != 999999) {
            $sql .= " AND gang = :gang";
            $bindParams[':gang'] = $search['gang'];
        } elseif (!empty($search['gang']) && $search['gang'] == 999999) {
            $sql .= " AND gang = 0";
        }
        if (!empty($search['money'])) {
            $sql .= " AND money > :money";
            $bindParams[':money'] = $search['money'];
        }
        if ($search['attack'] == 1) {
            $protime = time();
            $sql .= " AND hospital = 0 AND jail = 0 AND aprotection < :protime AND (gang <> :userGang OR gang = 0) AND admin < 1 AND hp > (50*level)/4 AND id <> :userId";
            $bindParams[':protime'] = $protime;
            $bindParams[':userGang'] = $user_class->gang;
            $bindParams[':userId'] = $user_class->id;
        } elseif ($search['attack'] == 2) {
            $sql .= " AND hospital > 0";
        }
        $time = time() - 900;
        if ($search['online'] == 1) {
            $sql .= " AND lastactive > :time";
            $bindParams[':time'] = $time;
        } elseif ($search['online'] == 2) {
            $sql .= " AND lastactive < :time";
            $bindParams[':time'] = $time;
        }

        $limit = ($user_class->rmdays > 0) ? 20 : 10;
        $sql .= " ORDER BY rand() DESC LIMIT " . (int) $limit;

        $db->query("SELECT id, username, level, money, city, gang, lastactive, hp FROM `grpgusers` WHERE $sql");
        $db->execute($bindParams);
        $results = $db->fetch_row();

        if ($results) {
            foreach ($results as &$result) {
                $result['username'] = formatUName($result['id']);
                $result['money'] = '$' . number_format($result['money']);
                $result['lastactive'] = howlongago($result['lastactive']);
            }

            $response['status'] = 'success';
            $response['data'] = $results;
        } else {
            $response['status'] = 'no_results';
        }
    } else {
        $response['status'] = 'invalid_method';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    //$response['data'] = $e->getMessage();
}

echo json_encode($response);
?>