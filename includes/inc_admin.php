<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($valid == false)
{
    header('Location: login.php');
}

if ($ADMIN == false)
{
    header('Location: index.php');
}

$action = (isset($_POST['action'])) ? addslashes($_POST['action']) : '';

//var_dump($addons); die();

$addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/inc_admin.php',
        'location'  => 'admin_post'
    )
);


if (isset($_POST['player']))
    $usr = addslashes($_POST['player']);


if ($action == 'createtable')
{
    $sql = 'INSERT INTO ' . DB_POKER . ' SET ';
    $updates = array();

    $skip = array('action', 'gameID', 'Submit');
    $skipnumbers = array('tablelow', 'pot', 'bet', 'lastmove');

    $addons->get_hooks(array(), array(
        'page'     => 'includes/inc_admin.php',
        'location'  => 'create_table_start'
    ));

    foreach ($_POST as $key => $value)
    {
        if (in_array( $key, $skip ))
        {
            continue;
        }

        if ($key === 'tablename')
            $value = str_replace(array('"', "'"), array('&quot;', '&apos;'), $value);

        if ($key == 'startdate')
        {
            $updates[] = $key . ' = TIMESTAMP("' . str_replace( 'T', ' ', $value ) . '")';
            continue;
        }
        
        if ( $key == 'sbamount' ) {
            $updates[] = $key . ' = ' . transfer_to($value);
            continue;
        }  
        
        if ( $key == 'bbamount' ) {
            $updates[] = $key . ' = ' . transfer_to($value);
            continue;
        }                

        if (in_array( $key, $skipnumbers))
        {
            $updates[] = $key . ' = ' . $value;
            continue;
        }

        $updates[] = $key . ' = "' . $value . '"';
    }

    $updates[] = "hand = -1";
    $sql      .= implode(',', $updates);

    $result = $pdo->query( $sql );

    OPS_sitelog($plyrname, "{$plyrname} created a new table " . str_replace(array('"', "'"), array('&quot;', '&apos;'), $_POST['tablename']));

    $addons->get_hooks(
        array(
            'content' => $pdo->lastInsertId()
        ),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'after_create_table'
        )
    );
}

$delete = (isset($_GET['delete'])) ? addslashes($_GET['delete']) : '';

if (is_numeric($delete) && isset($delete))
{
    $result = $pdo->exec("delete from  " . DB_POKER . " where gameID = " . $delete);

    $result = $pdo->exec("delete from " . DB_LIVECHAT . " where gameID = " . $delete);

    $result = $pdo->exec("update " . DB_PLAYERS . " set vID = 0, gID = 0 where vID = " . $delete);
}

if ($action == 'install')
{
	$dir  = getcwd();
    $path = $dir . "/images/tablelayout/";
    $ext  = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
    $nam  = basename( $_FILES['uploaded_file']['name'], '.png' );
    $path = $path . basename( $_FILES['uploaded_file']['name']);

    if ($ext === 'png')
    {
        if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path))
        {
            $msg = "<div class='alert alert-success'>The style " . basename( $_FILES['uploaded_file']['name'], '.png') . " has been added!</div>";
        }
        else
        {
            $msg = "<div class='alert alert-danger'>There was an error , please try again!</div>";
        }

        $lic = (isset($_POST['lic'])) ? addslashes($_POST['lic']) : '';
        $sq  = $pdo->prepare("select style_name from styles where style_name = '" . $nam . "'");
        $sq->execute();

        if ($sq->rowCount() > 0)
        {
            $msg = ADMIN_MSG_STYLE_INSTALLED;
        }
        elseif ($nam == '' || $lic == '')
        {
            $msg = ADMIN_MSG_MISSING_DATA;
        }
        else
        {
            $result = $pdo->exec("insert into " . DB_STYLES . " set style_name = '$nam', style_lic = '$lic'");
            OPS_sitelog($plyrname, "{$plyrname} added a new table style.");
            header('Location: admin.php?admin=styles&success=true');
            die();
        }
    }
}

if (!isset($usr))
{    
    $usr = (isset($_GET['delete']))  ? addslashes($_GET['delete']) : '';
}

if ($usr != '')
{
    $uq = $pdo->query("select email from " . DB_PLAYERS . " where username = '" . $usr . "'");
    $ur = $uq->fetch(PDO::FETCH_ASSOC);
    $em = $ur['email'];

    if ($action == 'ban')
    {
        if ($em != '')
            $result = $pdo->exec("update " . DB_PLAYERS . " set banned = '1' where email = '" . $em . "'");
        $result = $pdo->exec("update " . DB_PLAYERS . " set banned = '1' where username = '" . $usr . "'");

        OPS_sitelog($plyrname, "{$plyrname} banned user {$usr}.");
    }
    elseif ($action == 'unban')
    {
        if ($em != '')
            $result = $pdo->exec("update " . DB_PLAYERS . " set banned = 0 where email = '" . $em . "'");
        $result = $pdo->exec("update " . DB_PLAYERS . " set banned = 0 where username = '" . $usr . "'");

        OPS_sitelog($plyrname, "{$plyrname} unbanned user {$usr}.");
    }
    elseif ($action == 'reset')
    {
        $result = $pdo->exec("update " . DB_STATS . " set winpot = 0, rank = '', gamesplayed = 0, tournamentswon = 0, tournamentsplayed = 0, handsplayed = 0, handswon = 0, bet = 0, checked = 0, called = '0', allin = '0', fold_pf = '0', fold_f = '0', fold_t = '0', fold_r = 0 where player = '" . $usr . "'");

        OPS_sitelog($plyrname, "{$plyrname} reset the stats for user {$usr}.");
    }
    elseif ($action == 'approve')
    {
        $result = $pdo->exec("update " . DB_PLAYERS . " set approve = 0 where username = '" . $usr . "'");
        OPS_sitelog($plyrname, "{$plyrname} approved user {$usr}.");
    }
    elseif ($action == 'delete')
    {
        $result = $pdo->exec("delete from " . DB_PLAYERS . " where username = '" . $usr . "'");
        $result = $pdo->exec("delete from  " . DB_STATS . " where player = '" . $usr . "'");

        OPS_sitelog($plyrname, "{$plyrname} deleted user {$usr}.");

        if (file_exists('images/avatars/' . $usr . '.jpg'))
        {
            unlink('images/avatars/' . $usr . '.jpg');
        }
    }
}

if ($action == 'update')
{
    $title = (isset($_POST['title'])) ? addslashes($_POST['title']) : '';
    if ($title == '')
    {
        $title = 'Texas Holdem Poker';
    }

    $emailmode = (isset($_POST['emailmode'])) ? addslashes($_POST['emailmode']) : '';
    if ($emailmode != 1)
    {
        $emailmode = 0;
    }

    $ipcheck = (isset($_POST['ipcheck'])) ? addslashes($_POST['ipcheck']) : '';
    if ($ipcheck != 0)
    {
        $ipcheck = 1;
    }

    $appmode = (isset($_POST['appmode'])) ? addslashes($_POST['appmode']) : '';
    if (($appmode != 1) && ($appmode != 2))
    {
        $appmode = 0;
    }

    if ($appmode == 1)
    {
        $emailmode = 1;
    }

    $memmode   = (isset($_POST['memmode'])) ? addslashes($_POST['memmode']) : '';
    $deletearray = array(
        30,
        60,
        90,
        180,
        'never'
    );
    $delete      = (isset($_POST['delete'])) ? addslashes($_POST['delete']) : '';

    if (!in_array($delete, $deletearray))
    {
        $delete = 90;
    }

    $sess = (isset($_POST['session']))   ? addslashes($_POST['session'])   : '';

    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $title . "' WHERE setting = 'title'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $appmode . "' WHERE setting = 'appmod'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $emailmode . "' WHERE setting = 'emailmod'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $ipcheck . "' WHERE setting = 'ipcheck'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $memmode . "' WHERE setting = 'memmod'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $delete . "' WHERE setting = 'deletetimer'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $sess . "' WHERE setting = 'session'");
    
    OPS_sitelog($plyrname, "{$plyrname} updated Basic settings.");

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'settings_update_basic'
        )
    );

    header('Location: admin.php?admin=settings&ud=1');
}

if ($action == 'update2')
{
    $kickarray = array(
        3,
        5,
        7,
        10,
        15
    );
    $kick      = (isset($_POST['kick'])) ? addslashes($_POST['kick']) : '';

    if ( !in_array($kick, $kickarray) )
    {
        $kick = 5;
    }

    $movearray = array(
        10,
        15,
        20,
        27
    );
    $move      = (isset($_POST['move'])) ? addslashes($_POST['move']) : '';

    if ( !in_array($move, $movearray) )
    {
        $move = 20;
    }

    $showdownarray = array(
        3,
        4,
        5,
        7,
        10
    );
    $showdown      = (isset($_POST['showdown'])) ? addslashes($_POST['showdown']) : '';

    if ( !in_array($showdown, $showdownarray) )
    {
        $showdown = 7;
    }

    $waitarray = array(
        0,
        10,
        15,
        20,
        25
    );
    $wait      = (isset($_POST['wait'])) ? addslashes($_POST['wait']) : '';

    if (!in_array($wait, $waitarray))
    {
        $wait = 20;
    }

    $disconarray = array(
        15,
        30,
        60,
        90,
        120
    );
    $discon      = (isset($_POST['disconnect'])) ? addslashes($_POST['disconnect']) : '';

    if (!in_array($discon, $disconarray))
    {
        $discon = 60;
    }

    $sess      = (isset($_POST['session']))   ? addslashes($_POST['session'])   : '';

    $result = $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $kick . "' WHERE setting = 'kicktimer'");

    $result = $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $showdown . "' WHERE setting = 'showtimer'");

    $result = $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $move . "' WHERE setting = 'movetimer'");

    $result = $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $wait . "' WHERE setting = 'waitimer'");

    $result = $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $sess . "' WHERE setting = 'session'");

    $result = $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $discon . "' WHERE setting = 'disconnect'");

    OPS_sitelog($plyrname, "{$plyrname} updated Detailed settings.");

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'settings_update_detailed'
        )
    );

    header('Location: admin.php?admin=settings&ud=1');
}

if ($action == 'smtp')
{
    $smtp_on      = (isset($_POST['smtp_on']))      ? addslashes($_POST['smtp_on'])      : 'no';
    $smtp_host    = (isset($_POST['smtp_host']))    ? addslashes($_POST['smtp_host'])    : '';
    $smtp_port    = (isset($_POST['smtp_port']))    ? addslashes($_POST['smtp_port'])    : '';
    $smtp_encrypt = (isset($_POST['smtp_encrypt'])) ? addslashes($_POST['smtp_encrypt']) : 'none';
    $smtp_auth    = (isset($_POST['smtp_auth']))    ? addslashes($_POST['smtp_auth'])    : 'no';
    $smtp_user    = (isset($_POST['smtp_user']))    ? addslashes($_POST['smtp_user'])    : '';
    $smtp_pass    = (isset($_POST['smtp_pass']))    ? addslashes($_POST['smtp_pass'])    : '';

    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_on}' WHERE setting = 'smtp_on'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_host}' WHERE setting = 'smtp_host'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_port}' WHERE setting = 'smtp_port'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_encrypt}' WHERE setting = 'smtp_encrypt'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_auth}' WHERE setting = 'smtp_auth'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_user}' WHERE setting = 'smtp_user'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$smtp_pass}' WHERE setting = 'smtp_pass'");

    OPS_sitelog($plyrname, "{$plyrname} updated SMTP settings.");

    $addons->get_hooks(array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'settings_update_smtp'
        )
    );
    header('Location: admin.php?admin=settings&ud=1');
}

if ($action == 'currency')
{
    $ssizearray = array(
        'tiny',
        'low',
        'med',
        'high'
    );
    $ssize = (isset($_POST['stakesize'])) ? addslashes($_POST['stakesize']) : '';

    if (! in_array($ssize, $ssizearray))
        $ssize = med;

    $renewbutton = (isset($_POST['renew'])) ? addslashes($_POST['renew']) : '';
    
    if ($renewbutton != 0)
        $renewbutton = 1;

    $money_prefix = (isset($_POST['money_prefix'])) ? addslashes($_POST['money_prefix']) : '$';
    $money_decima = (isset($_POST['money_decima'])) ? addslashes($_POST['money_decima']) : '.';
    $money_thousa = (isset($_POST['money_thousa'])) ? addslashes($_POST['money_thousa']) : '.';
    $admin_users  = (isset($_POST['admin_users']))  ? addslashes($_POST['admin_users'])  : 'admin';
    $reg_winpot   = (isset($_POST['reg_winpot']))   ? addslashes($_POST['reg_winpot'])   : '1000';

    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $ssize . "' WHERE setting = 'stakesize'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '" . $renewbutton . "' WHERE setting = 'renew'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$money_prefix}' WHERE setting = 'money_prefix'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$money_decima}' WHERE setting = 'money_decima'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$money_thousa}' WHERE setting = 'money_thousa'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$admin_users}' WHERE setting = 'admin_users'");
    $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$reg_winpot}' WHERE setting = 'reg_winpot'");

    OPS_sitelog($plyrname, "{$plyrname} updated Currency settings.");

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'settings_update_currency'
        )
    );

    header('Location: admin.php?admin=settings&ud=1');
}

$adminview = (isset($_GET['admin'])) ? addslashes($_GET['admin']) : '';

if ( $action == 'edittable' )
{
    $sql = 'update ' . DB_POKER . ' SET ';
    $updates = array();

    $skip = array('action', 'gameID', 'tabletype', 'tablegame', 'Submit');
    $skipnumbers = array('tablelow', 'pot', 'bet', 'lastmove');

    $addons->get_hooks(array(), array(
        'page'     => 'includes/inc_admin.php',
        'location'  => 'edit_table_start'
    ));

    foreach ($_POST as $key => $value)
    {
        if (in_array( $key, $skip ))
        {
            continue;
        }

        if ($key === 'tablename')
            $value = str_replace(array('"', "'"), array('&quot;', '&apos;'), $value);

        if ($key == 'startdate')
        {
            $updates[] = $key . ' = TIMESTAMP("' . str_replace( 'T', ' ', $value ) . '")';
            continue;
        }
        
        if ( $key == 'sbamount' ) {
            $updates[] = $key . ' = ' . transfer_to($value);
            continue;
        }  
        
        if ( $key == 'bbamount' ) {
            $updates[] = $key . ' = ' . transfer_to($value);
            continue;
        }                

        if (in_array( $key, $skipnumbers))
        {
            $updates[] = $key . ' = ' . $value;
            continue;
        }

        $updates[] = $key . ' = "' . $value . '"';
    }

    $sql .= implode(',', $updates);
    $sql .= " where gameID = {$_POST['gameID']}";

    $result = $pdo->exec( $sql );

    $tQ = $pdo->query("SELECT tablename FROM " . DB_POKER . " WHERE gameID = {$_POST['gameID']}");
    $tF = $tQ->fetch(PDO::FETCH_ASSOC);
    OPS_sitelog($plyrname, "{$plyrname} edited table " . $tF['tablename']);

    header('Location: admin.php?admin=tables&ud=1');
}

if ( $action == 'editmember' )
{
    $sql = 'UPDATE ' . DB_PLAYERS . ' SET ';
    $updates = array();
    $skip = array('ID', 'action');
    $skipnumbers = array('datecreated', 'lastlogin', 'banned', 'approve', 'lastmove', 'waitimer', 'vID', 'gID', 'timetag');

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'editmember_before_update'
        )
    );

    foreach ($_POST as $key => $value) {
        if ( in_array( $key, $skip ) )
            continue;

        if ( $key == 'startdate' ) {
            $updates[] = $key . ' = TIMESTAMP("' . str_replace( 'T', ' ', $value ) . '")';
            continue;
        }

        if ( in_array( $key, $skipnumbers ) )
        {
            $updates[] = $key . ' = ' . $value;
            continue;
        }

        $updates[] = $key . ' = "' . $value . '"';
    }

    $sql .= implode(',', $updates);
    $sql .= " WHERE username = '" . $_POST['username'] . "'";

    $result = $pdo->exec( $sql );
    OPS_sitelog($plyrname, "{$plyrname} edited user " . $_POST['username']);

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'editmember_after_update'
        )
    );

    header('Location: admin.php?admin=members&ud=1');
}

if ( $action == 'editmemberchips' )
{
    $winpot = (int) $_POST['winpot'];
    $winpot = transfer_to($winpot);
    $player = $_POST['player'];

    $playerStatQ = $pdo->query("SELECT winpot FROM " . DB_STATS . " WHERE player = '{$player}'");
    $playerStat  = $playerStatQ->fetch(PDO::FETCH_ASSOC);
    $playerPot   = $playerStat['winpot'];

    $math    = ($winpot > $playerPot) ? 'added' : 'subtracted';
    $mathNum = ($winpot > $playerPot) ? ($winpot - $playerPot) : ($playerPot - $winpot);

    OPS_sitelog($plyrname, "{$plyrname} {$math} " . money($mathNum) . " in {$player}&apos;s bank. Old amount: " . money($playerPot) . ". New amount: " . money($winpot) . ".");

    $pdo->query("UPDATE " . DB_STATS . " SET winpot = {$winpot} WHERE player = '{$player}'");
    header('Location: admin.php?admin=members&ud=1');
}



// updates
if (isset($_GET['download_update']))
{
    $updateJson = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA=='), true));

    if (! isset($updateJson->status) || $updateJson->status !== "OK")
        return false;
    
    if (file_put_contents('update-' . $updateJson->version . '.zip', file_get_contents_ssl($updateJson->url)))
    {
        header("Content-type: application/json; charset=utf-8");
        echo json_encode(array(
            'status' => 'OK'
        ));
        exit();
    }
}

if (isset($_GET['extract_update']))
{
    $updateJson = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA==')));

    if (! isset($updateJson->status) || $updateJson->status !== "OK")
        return false;

    $version = $updateJson->version;
    $zipFile = "update-$version.zip";
    
    if ( file_exists($zipFile) )
    {
        $dir = realpath('');
        $zip = new ZipArchive;
        
        if ($zip->open($zipFile) === true)
        {
            $zip->extractTo($dir);
            $zip->close();

            $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '$version' WHERE setting = 'scriptversio' AND Xkey = 'SCRIPTVERSIO'");
            $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '0' WHERE setting = 'updatealert' AND Xkey = 'UPDATEALERT'");

            unlink($zipFile);
            OPS_sitelog($plyrname, "{$plyrname} updated the script version to {$version}");

            header("Content-type: application/json; charset=utf-8");
            echo json_encode(array(
                'status' => 'OK'
            ));
            exit();
        }
    }
}

if (isset($_GET['create_backup']))
{
    $dir = realpath('');
    $zip = new ZipArchive;

    if (! file_exists('backups')) mkdir('backups');

    if ($zip->open('backups/backup-' . time() . '.zip', ZipArchive::CREATE) === true)
    {
        foreach (rglob($dir . '/*') as $file)
        {
            $zip->addFile($file);
        }
        $zip->close();

        header('Location: admin.php?admin=updates');
    }
}


function rglob($pattern, $flags = 0)
{
    $files = array_filter(glob($pattern, $flags), 'is_file');

    foreach ( glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
        if (preg_match('/^.*?\/backups$/i', $dir)) continue;
        $files = array_merge($files, rglob($dir . '/' . basename($pattern), $flags));
    }

    return $files;
}

?>