<?php
include "ajax_header.php";

$userId = $_SESSION['id'];
if (isset($_GET['page'])) {
    $page = $_GET['page'];

    $validPageChoices = array(
        'jail',
        'backalley',
        'captcha'
    );
    if (!in_array($page, $validPageChoices)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid page'));
        exit;
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'No page'));
    exit;
}

if (isset($_GET['reason'])) {
    $reason = $_GET['reason'];

    $validReasonChoices = array(
        'invalid_click',
        'click_not_trusted',
        'dev_tools_is_open',
        'click_count',
    );
    if (!in_array($reason, $validReasonChoices)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid reason'));
        exit;
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'No reason'));
    exit;
}


$db->query("INSERT INTO autoclick_detection (userid, page, reason, timestamp) VALUES (?, ?, ?, ?)");
$db->execute(array(
    $userId,
    $page,
    $reason,
    time()
));

echo json_encode(array('success' => true, 'message' => 'Added'));
exit;