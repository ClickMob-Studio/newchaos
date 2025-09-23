<?php
include "ajax_header.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$validReasons = ['invalid_click', 'click_not_trusted', 'click_count', 'aux_click', 'dev_tools_is_open'];
$now = time();

$items = [];

if (isset($input['batch']) && is_array($input['batch'])) {
    foreach ($input['batch'] as $it) {
        $reason = $it['reason'] ?? null;
        if (!in_array($reason, $validReasons, true))
            continue;
        $items[] = [
            'reason' => $reason,
            'page_hint' => $it['page_hint'] ?? null,
            'count' => max(1, (int) ($it['count'] ?? 1)),
            'last_meta' => isset($it['last']) ? json_encode($it['last']) : null,
        ];
    }
} else {
    $reason = $input['reason'] ?? null;
    if (!in_array($reason, $validReasons, true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid reason']);
        exit;
    }

    $items[] = [
        'reason' => $reason,
        'page_hint' => $input['page_hint'] ?? null,
        'count' => max(1, (int) ($input['count'] ?? 1)),
        'last_meta' => null,
    ];
}

if (empty($items)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No valid items']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$uri = $_SERVER['REQUEST_URI'] ?? '';
$ref = $_SERVER['HTTP_REFERER'] ?? '';

foreach ($items as $it) {
    $db->query("INSERT INTO autoclick_detection 
        (userid, reason, `page`, ip, user_agent, request_uri, referer, count, last_meta) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ok = $db->execute([$userId, $it['reason'], $it['page_hint'], $ip, $ua, $uri, $ref, $it['count'], $it['last_meta']]);

    if (!$ok) {
        error_log('autoclick_detection insert failed');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'DB insert failed']);
        exit;
    }

    if ($it['reason'] === 'click_not_trusted' || $it['reason'] === 'dev_tools_is_open') {
        $_SESSION['force_captcha'] = true;
    }
}

echo json_encode(['success' => true, 'message' => 'Logged']);
exit;
