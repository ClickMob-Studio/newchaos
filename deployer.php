<?php
if ($_GET['accept'] !== 'true') {
    exit;
}

/**
 * GIT DEPLOYMENT SCRIPT
 *
 */
// The commands
$commands = array(
    'echo $PWD',
    'whoami',
    'git reset --hard HEAD',
    'git pull',
    'git status',
    'git submodule sync',
    'git submodule update',
    'git submodule status',
    'php ./vendor/bin/doctrine-migrations migrate --no-interaction',
);
// Run the commands for output
$output = '';
foreach ($commands as $command) {
    // Run it
    $tmp = exec($command);
    // Output
    $output .= "<span style=\"color: #6BE234;\">$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";
}
$outp = shell_exec('composer update');
echo $outp;


// Make it pretty for manual user access (and why not?)
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