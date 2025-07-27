<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR | E_PARSE);

// Polyfill for getallheaders for non-Apache or CGI environments
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
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
$signature = isset($headers['x-hub-signature']) ? $headers['x-hub-signature'] : '';

$hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    exit('File not found.');
}

// Parse event type and branch (no null coalescing)
$event = isset($headers['x-github-event']) ? $headers['x-github-event'] : '';
$data = json_decode($payload, true);
$branch = isset($data['ref']) ? $data['ref'] : '';
if ($event !== 'push' || $branch !== 'refs/heads/main') {
    exit('File not found.');
}

/**
 * GIT DEPLOYMENT SCRIPT
 */
// The commands
$commands = array(
    'echo $PWD',
    'whoami',
    'git fetch',
    'git reset --hard HEAD',
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