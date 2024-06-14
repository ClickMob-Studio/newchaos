<?php
$request_uri = $_SERVER['REQUEST_URI'];
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);
// Handle specific redirections
// if ($request_uri == '/crime.php') {
//     header('Location: /Missions.php', true, 301);
//     exit();
// } elseif (preg_match('/^(admin|faq|lobby|pokerrules|sitout|poker)\.php$/', $request_uri)) {
//     // Extract the page name from the URI
//     preg_match('/^(admin|faq|lobby|pokerrules|sitout|poker)\.php$/', $request_uri, $matches);
//     $page_name = $matches[1];
    
//     // Redirect to poker_index.php with the page name as a query parameter
//     header("Location: /poker_index.php?pagename=$page_name", true, 301);
//     exit();
// }
define('DS', DIRECTORY_SEPARATOR);
define('ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
//  ini_set('display_errors', 0);
// error_reporting(0);

//if (isset($_GET['bypass'])  && $_GET['bypass'] === 'yes_please') {
//    $_SESSION['admin_bypass'] = 1;
//}
//
//if (!isset($_SESSION['admin_bypass'])) {
//    echo " The game is currently closed for updates, we will be re-opening at 3pm GMT.";
//    exit;
//}

//$allowed_ips = array('82.30.147.11', '82.17.40.235');
//if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//echo " The game is currently closed for updates, we will be re-opening at 3pm GMT.";
//exit;
//}

//require_once ABSPATH . DS . 'lib' . DS . 'php7-mysql-shim.php';
require_once ABSPATH . 'consts.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';


// Security fix. (don't trust $_SERVER['PHP_SELF']
if (PHP_SAPI != 'cli') {
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
}

/*
 * Storing language file doesn't make it visible in the app
 * you have to list the language here in appropriate format
 * 3charcode => 'LanguageName'
 */
$GLOBALS['availableLanguages'] = [
    'eng' => 'English',
    // russian is for testing purposes only
    'rus' => 'Ру�?�?кий',
];

// default language is set here
define('DEFAULT_LANGUAGE', 'eng');
$lang = DEFAULT_LANGUAGE;

if (PHP_SAPI != 'cli') {
    $cwd = getcwd();

    chdir(ABSPATH . 'lang');
    foreach (glob('*.inc.php', GLOB_NOSORT | GLOB_NOESCAPE) as $file) {
        $file = str_replace('.inc.php', '', $file);

        if (
            isset($_SESSION['language']) and
            $_SESSION['language'] == $file and
            array_key_exists($file, $GLOBALS['availableLanguages'])
        ) {
            $lang = $file;
            $_SESSION['language'] = $lang;
        }
    }
    chdir($cwd);
}

define('CURRENT_LANGUAGE', $lang);

require_once ABSPATH . 'lang/' . CURRENT_LANGUAGE . '.inc.php';

// Output buffer initialization
ob_start();

// Timezone
date_default_timezone_set('America/Menominee');

// Error handler
function ps_error_handler($errno, $estr, $errfile, $errline)
{
    $replevel = error_reporting();
    if (($errno & $replevel) != $errno) {
        return;
    }

    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    Logger::error($errno . ': ' . $estr . ' at line ' . $errline . ' in file ' . $errfile);
    include_once 'error.php';
    die();
}

// Exception handler
function ps_exception_handler($e)
{
    if (getenv('DEBUG') == 1) {
        echo $e->getMessage() . '<br>' . $e->getTraceAsString();
        exit;
    }
    if (get_class($e) == 'Exception' || get_class($e) == 'SQLException') {
        if (ob_get_level() > 0) {
            ob_end_clean();
            ob_start();
        }

        Logger::error($e);
        include_once 'error.php';
        die();
    } elseif (get_class($e) == 'SoftException') {
        echo HTML::ShowMessage($e->getMessage());
    } elseif (get_class($e) == 'CheatingException') {
        if (ob_get_level() > 0) {
            ob_end_clean();
            ob_start();
        }

        Logger::error($e);
        include_once 'error.php';
        die();
    }
}

if (PHP_SAPI != 'cli') {
    set_error_handler('ps_error_handler', E_ALL ^ E_NOTICE);
    set_exception_handler('ps_exception_handler');

    if (getenv('SENTRY_DSN')) {
        // Register sentry PHP listener for exceptions
        Sentry\init(['dsn' => getenv('SENTRY_DSN')]);
    }
}

// Temporarily allow classes to now have the correct extend support
if (PHP_MAJOR_VERSION >= 7) {
    set_error_handler(function ($errno, $errstr) {
        return strpos($errstr, 'Declaration of') === 0
            || strpos($errstr, 'Non-static method') === 0
            || strpos($errstr, 'Accessing static property') === 0
            || strpos($errstr, 'Undefined') === 0;
    }, E_WARNING | E_DEPRECATED | E_NOTICE);
}

function flushOutput()
{
    ob_end_flush();
}
register_shutdown_function('flushOutput');

$host    = 'localhost';
$ln      = 'chaoscit_user';
$pw      = '3lrKBlrfMGl2ic14';
$db      = 'chaoscit_game';
$charset = 'utf8mb4';

try {
    $doctrine = \Doctrine\DBAL\DriverManager::getConnection([
        'dbname' => $db,
        'user' => $ln,
        'password' => $pw,
        'host' => $host,
        'charset' => 'utf8mb4',
        'driver' => 'mysqli',
    ]);
    /** @var \Doctrine\DBAL\Driver\Mysqli\MysqliConnection $connection */
    $connection = $doctrine->getWrappedConnection();
    $conn = $connection->getWrappedResourceHandle();
} catch (\Exception $e) {
    Logger::log($e);
    define('ERROR_TYPE', 'Connection');
    require_once 'error.php';
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $ln, $pw, array(
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ));
} catch (PDOException $e) {
    die("Unable to connect to database");
}

class DBi {
    public static $conn;
}

DBi::$conn = $conn; //set in Doctrine wrapper above

//mysqli_result shim
function mysqli_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}
class MySQL{
    public static function GetSingle($query) {
        $res = DBi::$conn->query($query);
        if (!$res) {
            throw new Exception(' Query: ' . $query);
        }
        if (mysqli_num_rows($res) == 0) {
            return false;
        }

        $row = mysqli_fetch_row($res);

        return $row[0];
    }
    public static function GetFields($tableName)
    {
        $ret = [];
        $rs = DBi::$conn->query('DESC `' . $tableName . '`');
        while ($row = mysqli_fetch_assoc($rs)) {
            $ret[] = $row['Field'];
        }

        return $ret;
    }
}

/*
 *  Security checks
 */
// We protect input data by escaping it
Security::ProtectInput();

// Server wide variables
global $server_variables;
$server_variables = Variable::GetAllValues();
