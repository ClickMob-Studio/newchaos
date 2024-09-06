<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// // Debug mode setup
// if (isset($_GET['debug'])) {
//     ini_set('display_errors', true);
//     ini_set("log_errors", 1);
//     error_reporting(E_ALL);
// } else {
//     ini_set('display_errors', false);
//     error_reporting(0);
// }

// Required configuration and settings
require_once 'config.inc.php';
require 'tables.php';
require 'settings.php';

/* THEME */
if (!isset($opsTheme)) {
    $themeCFN = 'Theme.class.php';
    $themeCF  = $themeCFN;

    // Check various paths for the Theme class
    if (!file_exists($themeCF)) $themeCF = 'includes/' . $themeCF;
    if (!file_exists($themeCF)) $themeCF = '../' . $themeCF;
    if (!file_exists($themeCF)) die("Theme class file not found.");

    require($themeCF);
}

/* THEME */

if (!isset($addons)) {
    $addonClassFileName = 'Addon.class.php';
    $addonClassFile     = $addonClassFileName;

    // Check various paths for the Addon class
    if (!file_exists($addonClassFile)) $addonClassFile = 'includes/' . $addonClassFile;
    if (!file_exists($addonClassFile)) $addonClassFile = '../' . $addonClassFile;
    if (!file_exists($addonClassFile)) die("Addon class file not found.");

    // Set the addons directory
    $addonDir = str_replace($addonClassFileName, '', $addonClassFile) . 'addons';

    require($addonClassFile);
    $addonSettings = array();
    $addons        = new OPSAddon(); // Removed the namespace slash for PHP 5.6 compatibility
    require($addonDir . '/autoloader.php');
}

// Execute addon hooks
echo $addons->get_hooks(array(), array(
    'page'     => 'includes/sec_inc.php',
    'location' => 'start'
));

require 'poker_inc.php';

// Retrieve player details from session
$plyrname = isset($_SESSION['username']) ? addslashes($_SESSION['username']) : 'Chaos';
$SGUID    = isset($_SESSION['id']) ? addslashes($_SESSION['id']) : '';

if ($plyrname == '' || $SGUID == '') {
    die("no player name or GUID set!");
}

$valid  = false;
$gameID = '';
$gID    = '';

// Prepare and execute query to validate player
$idq = $pdo->prepare("SELECT GUID, vID, gID, banned FROM " . DB_PLAYERS . " WHERE username = :plyrname AND GUID = :sguid");
$idq->execute(array(':plyrname' => $plyrname, ':sguid' => $SGUID));
$idr = $idq->fetch(PDO::FETCH_ASSOC);

// Check if user is valid and not banned
if ($idq->rowCount() == 1 && $idr['banned'] != 1) {
    $valid  = true;
    $gameID = $idr['vID'];
    $gID    = $idr['gID'];

    // Fetch user stats
    $getstats = $pdo->prepare("SELECT a.*, b.bank FROM " . DB_STATS . " a, grpgusers b WHERE a.player = :plyrname AND a.player = b.username");
    $getstats->execute(array(':plyrname' => $plyrname));
    $usestats = $getstats->fetch(PDO::FETCH_ASSOC);

    $current_chipcount = isset($usestats['bank']) ? $usestats['bank'] : 0;
    $current_money = money($current_chipcount);

    // Set theme variables
    $opsTheme->addVariable('current_chipcount', $current_chipcount);
    $opsTheme->addVariable('current_money', $current_money);
    $opsTheme->addVariable('username', $plyrname);
}

// Check if the user is invalid or game ID is missing
if ($valid == false || $gameID == '') {
    die("invalid user");
}

require 'language.php';
?>
