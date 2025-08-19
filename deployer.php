<?php
// Turn off display but keep error_log useful
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Polyfill for getallheaders if needed
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }
}

$secret = 'zJE2vuHYVLhK5Uk2HlUdg4ZLCop2hzp3';
$payload = file_get_contents('php://input');

$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$sig256 = $headers['x-hub-signature-256'] ?? '';
$sig1 = $headers['x-hub-signature'] ?? '';

// Build expected signatures
$exp256 = 'sha256=' . hash_hmac('sha256', $payload, $secret);
$exp1 = 'sha1=' . hash_hmac('sha1', $payload, $secret);

// Validate (prefer sha256)
$valid =
    ($sig256 && hash_equals($exp256, $sig256)) ||
    ($sig1 && hash_equals($exp1, $sig1));

if (!$valid) {
    // Minimal diagnostics to your error log (not to the client)
    error_log('GitHub webhook signature mismatch. '
        . 'X-Hub-Signature-256=' . ($sig256 ?: 'MISSING')
        . ' expected=' . $exp256
        . ' | X-Hub-Signature=' . ($sig1 ?: 'MISSING')
        . ' expected=' . $exp1
        . ' | delivery=' . ($headers['x-github-delivery'] ?? 'n/a'));
    http_response_code(403);
    exit('File not found.');
}

/**
 * GIT DEPLOYMENT SCRIPT
 */
$commands = array(
    'echo $PWD',
    'whoami',
    'git fetch',
    'git reset --hard origin/php8',
    'git status',
    'git submodule sync',
    'git submodule update',
    'git submodule status',
    'php ./vendor/bin/doctrine-migrations migrate --no-interaction',
);

// Run the commands for output
$output = '';
foreach ($commands as $command) {
    $tmp = shell_exec($command);
    $output .= "<span style=\"color: #6BE234;\">$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";
}

$outp = shell_exec('composer update');
echo $outp;
?>
<!DOCTYPE HTML>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <title>GIT DEPLOYMENT SCRIPT</title>
</head>

<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
    <pre>
 ____________________________
|                            |
| Git Deployment Script v0.1 |
|      github.com/riodw 2019 |
|____________________________|

<?php echo $output; ?>
</pre>
</body>

</html>