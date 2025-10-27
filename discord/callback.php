<?php

session_start();

require_once '../dbcon.php';
require_once '../classes.php';
require_once '../database/pdo_class.php';
require_once '../includes/functions.php';
require_once '../includes/cron_functions.php';

$clientId = '1429601793544945775';
$clientSecret = 'tXDUlVULZQZLFA_QBTvl2G16zQWhqijO';
$redirectUri = 'https://chaoscity.co.uk/discord/callback.php';

$guildId = '1222776672408043651';

if (!isset($_GET['code'], $_GET['state']) || $_GET['state'] !== ($_SESSION['discord_oauth_state'] ?? '')) {
    http_response_code(400);
    exit('Invalid state or missing code');
}
$code = $_GET['code'];

$tokenRes = http_post_form('https://discord.com/api/oauth2/token', [
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirectUri,
]);

if (empty($tokenRes['access_token'])) {
    http_response_code(400);
    exit('Token exchange failed');
}

$accessToken = $tokenRes['access_token'];
$refreshToken = $tokenRes['refresh_token'] ?? null;
$expiresIn = $tokenRes['expires_in'] ?? 0;
$expiresAt = time() + (int) $expiresIn;

$user = discord_api_get('/users/@me', $accessToken);
if (empty($user['id'])) {
    http_response_code(400);
    exit('Failed to fetch user profile');
}
$discordUserId = $user['id'];

$db->query("UPDATE grpgusers SET `discord_user_id` = ? WHERE id = ?");
$db->execute([$discordUserId, $_SESSION['id']]);

header('Location: /settings.php');

function http_post_form(string $url, array $data): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data, '', '&', PHP_QUERY_RFC3986),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 15,
    ]);
    $res = curl_exec($ch);
    if ($res === false)
        throw new RuntimeException('cURL error: ' . curl_error($ch));
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code >= 400)
        return [];
    return json_decode($res, true) ?: [];
}

function discord_api_get(string $path, string $accessToken): array
{
    $ch = curl_init('https://discord.com/api' . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $accessToken],
        CURLOPT_TIMEOUT => 15,
    ]);
    $res = curl_exec($ch);
    if ($res === false)
        throw new RuntimeException('cURL error: ' . curl_error($ch));
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code === 403)
        return [];
    return json_decode($res, true) ?: [];
}