<?php


// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once 'dbcon.php';
require_once 'includes/tables.php';
//require_once 'includes/settings.php';

/* THEME */
// $themeCFN = 'includes/Theme.class.php';
// $themeCF  = $themeCFN;
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
$addons        = new \OPSAddon();
require_once $addonDir . '/autoloader.php';

echo $addons->get_hooks(array(), array(
    'page'     => 'includes/gen_inc.php',
    'location' => 'start'
));

require_once 'poker_inc.php';
require_once 'language.php';

$plyrname = isset($_SESSION['username']) ? addslashes($_SESSION['username']) : '';
$SGUID    = isset($_SESSION['id']) ? addslashes($_SESSION['id']) : '';
$_SESSION['playername'] = $plyrname;
$_SESSION['SGUID'] = $SGUID;

$valid = true;
$ADMIN = ($user_class) ? $user_class->admin > 0 : false;
$gID   = '';
$opsTheme->addVariable('is_admin', 0);
$opsTheme->addVariable('is_logged', 0);

if ($plyrname != '' && $SGUID != '') {
    $idq = $pdo->prepare("SELECT GUID, gID, ID, vID FROM " . DB_PLAYERS . " WHERE GUID = :sguid");
    $idq->execute([':sguid' => $SGUID]);
    $idr = $idq->fetch(PDO::FETCH_ASSOC);

    if ($idq->rowCount() == 0) {
        $pdo->query("INSERT INTO " . DB_PLAYERS . " SET username= '{$plyrname}', GUID = '{$SGUID}'");
        $pdo->query("INSERT INTO " . DB_STATS . " SET player = '{$plyrname}'");
    }

    $gID    = $idr['gID'];
    $pID    = $idr['ID'];
    $gameID = $idr['vID'];
    $sitecurrency = MONEY_PREFIX;

    if ($plyrname != '') {
        $time     = time();
        $i        = 0;

        $getstats = $pdo->prepare("SELECT a.*, b.bank FROM ".DB_STATS." a, grpgusers b WHERE a.player = :plyrname AND a.player = b.username");
        $getstats->execute([':plyrname' => $plyrname]);
        $usestats = $getstats->fetch(PDO::FETCH_ASSOC);

        $current_chipcount = $usestats['b.bank'];
        $current_money     = money($usestats['b.bank']);

        $opsTheme->addVariable('current_chipcount', $current_chipcount);
        $opsTheme->addVariable('current_money', $current_money);
        $opsTheme->addVariable('username', $plyrname);
        $opsTheme->addVariable('is_logged', 1);
        $opsTheme->addVariable('sitecurrency', $sitecurrency);
    }
}

if ($ADMIN == true) {
    $opsTheme->addVariable('is_admin', 1);

    /*
    $now               = time();
    $updateCheckTimer  = (isset($_GET['force'])) ? (60) : (60 * 60 * 3);
    $last_update_check = LASTUPDATECH + $updateCheckTimer;

    if ($now > $last_update_check) {
        $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '$now' WHERE setting = 'lastupdatech'");
        $updateJson = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA==')));

        if (isset($updateJson->status) && $updateJson->status === "OK")
            $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '1' WHERE setting = 'updatealert'");
        else
            $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '0' WHERE setting = 'updatealert'");

        $newAddonUpdates = 0;
        foreach (glob('includes/addons/*', GLOB_ONLYDIR) as $addonDir) {
            $addonInfoFile = "{$addonDir}/info.json";

            if (! file_exists($addonInfoFile)) continue;

            $addonInfo = json_decode(file_get_contents($addonInfoFile));

            if (! isset($addonInfo->version, $addonInfo->update_url)) continue;

            $addonJson = json_decode(file_get_contents_ssl($addonInfo->update_url, array(
                'ip'      => get_user_ip_addr(),
                'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
                'license' => LICENSEKEY,
                'version' => $addonInfo->version,
            )));

            if (isset($addonJson->status) && $addonJson->status === "OK")
                $newAddonUpdates++;
        }
        $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$newAddonUpdates}' WHERE setting = 'addonupdatea'");

        $newThemeUpdates = 0;
        foreach (glob('themes/*', GLOB_ONLYDIR) as $themeDir) {
            $themeInfoFile = "{$themeDir}/info.json";

            if (! file_exists($themeInfoFile)) continue;

            $themeInfo = json_decode(file_get_contents($themeInfoFile));

            if (! isset($themeInfo->version, $themeInfo->update_url)) continue;

            $themeJson = json_decode(file_get_contents_ssl($themeInfo->update_url, array(
                'ip'      => get_user_ip_addr(),
                'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
                'license' => LICENSEKEY,
                'version' => $themeInfo->version,
            )));

            if (isset($themeJson->status) && $themeJson->status === "OK")
                $newThemeUpdates++;
        }
        $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$newThemeUpdates}' WHERE setting = 'themeupdatea'");

        header('Refresh: 0');
    }
    */
}

$time     = time();
$tq       = $pdo->prepare("SELECT waitimer FROM " . DB_PLAYERS . " WHERE username = :plyrname");
$tq->execute([':plyrname' => $plyrname]);
$tr       = $tq->fetch(PDO::FETCH_ASSOC);
$waitimer = $tr['waitimer'];

/* if ($waitimer > $time) {
    header('Location: sitout.php');
} */

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
