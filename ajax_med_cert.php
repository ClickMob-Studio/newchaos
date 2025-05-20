<?php

require_once 'includes/functions.php';

start_session_guarded();

function error($msg, $userBaStats = array())
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

include "classes.php";
include "database/pdo_class.php";

$user_class = new User($_SESSION['id']);
//if ($user_class->admin < 1 || $user_class->id < 398) {
//    echo 'exit'; exit;
//}

session_write_close();

if (!isset($_GET['alv'])) {
    echo json_encode(error('Something went wrong.'));
    exit;
}
if ($_GET['alv'] !== 'yes') {
    echo json_encode(error('Something went wrong.'));
    exit;
}

if ($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital) {
    echo json_encode(error('You already have full HP and are not in the hospital.'));
    exit;
}

$totalMedPackCount = check_items(14, $user_class->id);

if (!$totalMedPackCount) {
    echo json_encode(success('You do not have any Med Packs.'));
    exit;
}

$medPackCount = check_items(14, $user_class->id);
if ($medPackCount > 0) {
    $db->query("SELECT * FROM items WHERE id = 14");
    $db->execute();
    $row = $db->fetch_row(true);

    $hosp = floor(($user_class->hospital / 100) * $row['reduce']);
    $newhosp = $user_class->hospital - $hosp;
    $newhosp = ($newhosp < 0) ? 0 : $newhosp;
    $hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
    $hp = $user_class->purehp + $hp;
    $hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
    $db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
    $db->execute(array(
        $newhosp,
        $hp,
        $user_class->id
    ));

    Take_Item(14, $user_class->id);

    echo json_encode(array(
        'success' => true,
        'message' => 'You successfully used a ' . $row["itemname"] . '.',
        'med_pack_count' => ($totalMedPackCount - 1)
    ));
    exit;
}

echo json_encode(success('Something went wrong, if this issue persists, please message an Admin.'));
exit;

