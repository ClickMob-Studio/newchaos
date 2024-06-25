<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start();

function shorthandNumber($number) {
    if ($number >= 1000000000) {
        return round($number / 1000000000, 2) . 'B';
    } elseif ($number >= 1000000) {
        return round($number / 1000000, 2) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'k';
    }
    return number_format($number);
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id'])) {
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['id'] = $data['user_id'];
}

include "classes.php";
include "database/pdo_class.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = null;
if (isset($_SESSION['id'])) {
    $user_class = new User($_SESSION['id']);
}

session_write_close();

if (!$user_class) {
    echo json_encode(array('error' => 'refresh'));
    exit();
}

$db = database::getInstance();

try {
    $db->startTrans();

    $db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = ?");
    $db->execute(array($user_class->id));

    if ($user_class->jail || $user_class->hospital) {
        echo json_encode(array('error' => 'refresh', 'text' => "You are not able to do crimes at the moment."));
        $db->rollBack();
        exit();
    }

    $id = isset($data['id']) ? $data['id'] : null;
    $cm = isset($data['cm']) ? (int)$data['cm'] : 1;

    if ($id) {
        $crime_key = 'crimes.' . $id;
        if (!$row = $m->get($crime_key)) {
            $db->query("SELECT `id`, `nerve`, `name` FROM crimes WHERE id = ? LIMIT 1");
            $db->execute(array($id));
            $row = $db->fetch_row(true);
            $m->set($crime_key, $row, false, 120);
        }

        if (empty($row)) {
            echo json_encode(array('error' => 'refresh'));
            $db->rollBack();
            exit();
        }

        $nerve = $row['nerve'] * $cm;

        if ($user_class->nerve < $nerve) {
            echo json_encode(array('error' => 'refresh', 'text' => "You don't have enough nerve for that crime."));
            $db->rollBack();
            exit();
        }

        // Deduct nerve and perform the crime logic here
        $user_class->nerve -= $nerve;
        $db->query("UPDATE grpgusers SET nerve = ? WHERE id = ?");
        $db->execute(array($user_class->nerve, $user_class->id));

        // Additional crime logic and response preparation

        echo json_encode(array(
            'text' => "Crime successful!",
            'stats' => array(
                'points' => number_format($user_class->points),
                'money' => number_format($user_class->money),
                'level' => number_format($user_class->level),
                'mission' => "Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}"
            ),
            'bars' => array(
                'energy' => array(
                    'percent' => $user_class->energypercent,
                    'title' => $user_class->formattedenergy
                ),
                'nerve' => array(
                    'percent' => $user_class->nervepercent,
                    'title' => $user_class->formattednerve
                ),
                'awake' => array(
                    'percent' => $user_class->awakepercent,
                    'title' => $user_class->awakepercent
                ),
                'exp' => array(
                    'percent' => $user_class->exppercent,
                    'title' => $user_class->exppercent
                ),
            )
        ));
    }

    $db->endTrans();
} catch (Exception $e) {
    $db->rollBack();
    die("Error: " . $e->getMessage());
}

$db = null;
?>
