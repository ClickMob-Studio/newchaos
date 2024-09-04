<?php

//header('Content-type: application/json');
session_start();

include "classes.php";
include "database/pdo_class.php";

function error($msg)
{
    $response = array();
    $response['success'] = false;
    $response['error'] = $msg;

    return $response;
}

function success($msg)
{
    $response = array();
    $response['success'] = true;
    $response['message'] = $msg;

    return $response;
}

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = new User($_SESSION['id']);
session_write_close();

$response = array();

if (!isset($_GET['alv'])) {
    echo json_encode(error('Something went wrong.'));
    exit;
}
if ($_GET['alv'] !== 'yes') {
    echo json_encode(error('Something went wrong.'));
    exit;
}

$levelLimit = (int)$_GET['level_limit'];
if (!$levelLimit) {
    echo json_encode(error('Something went wrong.'));
    exit;
}

$time = time() - 900;
$protime = time();

$sql = "level <= " . $levelLimit ." AND lastactive < '{$time}' AND city = " . $user_class->city . " AND hospital = 0 AND jail = 0 AND aprotection < {$protime} AND (gang <> $user_class->gang || gang = 0)  AND admin < 1 AND hp > (50*level)/4 AND id <> $user_class->id ";

if (isset($_GET['v2']) && $_GET['v2'] == 'yes') {
    $db->query("SELECT `id` FROM `grpgusers` WHERE " . $sql . " ORDER BY rand() DESC LIMIT 10");
    $db->execute();
    $attack_id = $db->fetch_row();
} else {
    $db->query("SELECT `id` FROM `grpgusers` WHERE " . $sql . " ORDER BY rand() DESC LIMIT 1");
    $db->execute();
    $attack_id = $db->fetch_single();
}

if ($user_class->id == 830) {
    Send_Event(2, 'SA: ' . $user_class->city);
    Send_Event(2, 'SA: ' . count($attack_id));
}


if ($attack_id > 0) {
    echo json_encode(array(
        'success' => true,
        'attack_id' => $attack_id
    ));
} else {
    echo json_encode(array(
        'success' => false
    ));
}
exit;

