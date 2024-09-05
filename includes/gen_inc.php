<?php

define('DB_USERCHAT', 'poker_userchat');
define('DB_LIVECHAT', 'poker_livechat');
define('DB_PLAYERS', 'poker_players');
define('DB_STATS', 'poker_stats');
define('DB_POKER', 'poker_poker');
define('DB_SITELOG', 'poker_sitelog');
define('DB_SETTINGS', 'poker_settings');
define('DB_STYLES', 'poker_styles');

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once '../dbcon.php';
require_once '../includes/tables.php';
require_once '../includes/settings.php';

/* THEME */
$themeCFN = 'includes/Theme.class.php';
$themeCF  = $themeCFN;
if (!file_exists($themeCF)) $themeCF = 'includes/' . $themeCF;
if (!file_exists($themeCF)) $themeCF = '../' . $themeCF;
if (!file_exists($themeCF)) die("Theme not found");
require_once $themeCF;

/* THEME */
$addonClassFileName = 'Addon.class.php';
$addonClassFile     = $addonClassFileName;
if (!file_exists($addonClassFile)) $addonClassFile = 'includes/' . $addonClassFile;
if (!file_exists($addonClassFile)) $addonClassFile = '../' . $addonClassFile;
if (!file_exists($addonClassFile)) die("Addon class not found");

require_once $addonClassFile;

$addonDir      = str_replace($addonClassFileName, '', $addonClassFile) . 'addons';
$addonSettings = array();
$addons        = new OPSAddon();
require_once $addonDir . '/autoloader.php';

echo $addons->get_hooks(array(), array(
    'page'     => '../includes/gen_inc.php',
    'location' => 'start'
));

require_once 'poker_inc.php';
require_once 'language.php';

// Retrieve player name and session ID
$plyrname = isset($_SESSION['username']) ? addslashes($_SESSION['username']) : '';
$SGUID    = isset($_SESSION['id']) ? addslashes($_SESSION['id']) : '';
$_SESSION['playername'] = $plyrname;
$_SESSION['SGUID'] = $SGUID;

$valid = true;
$ADMIN = isset($user_class) && $user_class->admin > 0;
$gID   = '';
$opsTheme->addVariable('is_admin', 0);
$opsTheme->addVariable('is_logged', 0);

if ($plyrname != '' && $SGUID != '') {
    $idq = $pdo->prepare("SELECT GUID, gID, ID, vID FROM " . DB_PLAYERS . " WHERE GUID = :sguid");
    $idq->execute(array(':sguid' => $SGUID));
    $idr = $idq->fetch(PDO::FETCH_ASSOC);

    if ($idq->rowCount() == 0) {
        $pdo->query("INSERT INTO " . DB_PLAYERS . " SET username= '{$plyrname}', GUID = '{$SGUID}'");
        $pdo->query("INSERT INTO " . DB_STATS . " SET player = '{$plyrname}'");
    }

    $gID    = isset($idr['gID']) ? $idr['gID'] : '';
    $pID    = isset($idr['ID']) ? $idr['ID'] : '';
    $gameID = isset($idr['vID']) ? $idr['vID'] : '';
    $sitecurrency = MONEY_PREFIX;

    if ($plyrname != '') {
        $time     = time();
        $i        = 0;

        $getstats = $pdo->prepare("SELECT a.*, b.bank FROM ".DB_STATS." a, grpgusers b WHERE a.player = :plyrname AND a.player = b.username");
        $getstats->execute(array(':plyrname' => $plyrname));
        $usestats = $getstats->fetch(PDO::FETCH_ASSOC);

        $current_chipcount = isset($usestats['bank']) ? $usestats['bank'] : 0;
        $current_money     = money($current_chipcount);

        $opsTheme->addVariable('current_chipcount', $current_chipcount);
        $opsTheme->addVariable('current_money', $current_money);
        $opsTheme->addVariable('username', $plyrname);
        $opsTheme->addVariable('is_logged', 1);
        $opsTheme->addVariable('sitecurrency', $sitecurrency);
    }
}

if ($ADMIN) {
    $opsTheme->addVariable('is_admin', 1);

    /*
    Code for admin update checks, commented out for now.
    */
}

$time     = time();
$tq       = $pdo->prepare("SELECT waitimer FROM " . DB_PLAYERS . " WHERE username = :plyrname");
$tq->execute(array(':plyrname' => $plyrname));
$tr       = $tq->fetch(PDO::FETCH_ASSOC);
$waitimer = isset($tr['waitimer']) ? $tr['waitimer'] : 0;

/* Redirect to sitout if wait timer is greater than current time
if ($waitimer > $time) {
    header('Location: sitout.php');
}
*/

// Fetch and display table types using hooks
$tableTypes = $addons->get_hooks(
    array(
        'content' => array(
            array('value' => 's', 'label' => SITNGO),
            array('value' => 't', 'label' => TOURNAMENT)
        )
    ),
    array(
        'page'     => 'general',
        'location' => 'table_types'
    )
);

// Fetch and display tournament types using hooks
$tournamentTypes = $addons->get_hooks(
    array(
        'content' => array(
            array('value' => 'r', 'label' => 'Regular')
        )
    ),
    array(
        'page'     => 'general',
        'location' => 'tournament_types'
    )
);

?>
