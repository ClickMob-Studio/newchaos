<?php

function jsonResponse(array $data)
{
    die(json_encode($data));
}

$content = array_key_exists('content', $_POST) && is_string($_POST['content']) && $_POST['content'] !== '' ? strip_tags(trim($_POST['content'])) : null;

if ($content !== null) {
    $e = new Emotions();
    $bbcode = new \BBCode();
    $parsed = $e->getEmotion($content);
    $parsed = $bbcode->parse_bbcode($parsed);
    $ret = [
        'type' => 'success',
        'content' => $parsed,
    ];
} else {
    $ret = [
        'type' => 'info',
        'content' => 'No content to parse',
    ];
}

return jsonResponse($ret);
