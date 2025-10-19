<?php
// /discord/interactions

$publicKey = '6273820a3e40d76c4b5ce69277f58308f52813ec5090f1fe278fd1ad3afb8a5f';

$signature = $_SERVER['HTTP_X_SIGNATURE_ED25519'] ?? '';
$timestamp = $_SERVER['HTTP_X_SIGNATURE_TIMESTAMP'] ?? '';
$body = file_get_contents('php://input');

if (
    !\sodium_crypto_sign_verify_detached(
        sodium_hex2bin($signature),
        $timestamp . $body,
        sodium_hex2bin($publicKey)
    )
) {
    http_response_code(401);
    exit('invalid request signature');
}

$payload = json_decode($body, true);

// Ping → Pong
if (($payload['type'] ?? null) === 1) {
    header('Content-Type: application/json');
    echo json_encode(['type' => 1]);
    exit;
}