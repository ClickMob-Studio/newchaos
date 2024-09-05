<?php
if ($valid === false) {
    header('Location: login.php');
    exit;
}

if (empty($gameID)) {
    header('Location: lobby.php');
    exit;
}

$time = time();
$sql = $addons->get_hooks(
    array(
        'content' => "SELECT * FROM " . DB_POKER . " WHERE gameID = :gameID"
    ),
    array(
        'page' => 'includes/inc_poker.php',
        'location' => 'tq_sql',
    )
);

$tq = $pdo->prepare($sql);
$tq->execute(['gameID' => $gameID]);
$tr = $tq->fetch(PDO::FETCH_ASSOC);

if (!$tr) {
    // Handle case where the game is not found
    header('Location: lobby.php');
    exit;
}

$tablehand  = $tr['hand'];
$tableid    = $tr['gameID'];
$tablename  = $tr['tablename'];
$tabletype  = $tr['tabletype'];
$tablelimit = $tr['tablelimit'];
$tablestyle = $tr['tablestyle'];
$sbamount   = $tr['sbamount'];
$bbamount   = $tr['bbamount'];
$gamestyle  = $tr['gamestyle'];

$isPlaying = false;
$mySeat = 0;

for ($pi = 1; $pi < 11; $pi++) {
    if ($tr["p{$pi}name"] === $plyrname) {
        $mySeat = $pi;
        $isPlaying = true;
        break;
    }
}

// Additional hooks if needed

$tomove = $tr['move'];
$min = $tr['tablelow'];
$sq = $pdo->prepare("SELECT style_name, style_lic FROM styles WHERE style_name = :styleName");
$sq->execute(['styleName' => $tablestyle]);

$officialstylepack = 'normal';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Additional hooks for actions

$leave = ($action === 'leave' && (!$isPlaying || $tablehand < 3));
$leave = $addons->get_hooks(
    array('content' => $leave),
    array(
        'page' => 'includes/inc_poker.php',
        'location' => 'leave_bool',
    )
);  
if ($leave)
{
    $tpq       = $pdo->query("SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID);
    $tpr       = $tpq->fetch(PDO::FETCH_ASSOC);
    $i         = 1;
    $player    = '';
    $playernum = 0;

    while ($i < 11)
    {
        if (strlen($tpr['p' . $i . 'name']) > 0)
            $playernum++;

        if ($tpr['p' . $i . 'name'] == $plyrname)
        {
            $player = $i;
            $pot    = $tpr['p' . $i . 'pot'];
        }
        $i++;
    }

    $exitTable = ($player != '') ? true : false;
    $exitTable = $addons->get_hooks(
        array(
            'content' => $exitTable
        ),
        array(
            'page'     => 'includes/inc_poker.php',
            'location' => 'exit_table_bool',
        )
    );

    if ($exitTable)
    {
        poker_log($plyrname, 'leaves the table', $gameID);

        $winpot = $pot;
        $winpot = $addons->get_hooks(
            array(
                'content' => $winpot,
            ),
            array(
                'page'     => 'includes/inc_poker.php',
                'location' => 'winpot_var',
            )
        );

        $addToBank = ($tpr['tabletype'] !== 't' || $playernum === 1) ? true : false;
        $addToBank = $addons->get_hooks(
            array(
                'content' => $addToBank
            ),
            array(
                'page'     => 'includes/inc_poker.php',
                'location' => 'add_to_bank_bool',
            )
        );
        
        if ($addToBank)
        {
            //$statsq   = $pdo->query("select winpot from " . DB_STATS . " where player = '{$plyrname}'");
            $bankorpoints = ($gamestyle=="p") ? 'points' : 'bank';
            $statsq   = $pdo->query("select $bankorpoints from grpgusers where username = '{$plyrname}'");
            
            $statsr   = $statsq->fetch(PDO::FETCH_ASSOC);
            $winnings = $statsr[$bankorpoints];
            $winpot  += $winnings;
            $result   = $pdo->query("update grpgusers set $bankorpoints = {$winpot} where username  = '{$plyrname}'");

            OPS_sitelog($plyrname, "{$plyrname} left the table with " . money($pot, $gamestyle) . ". New bank amount: " . money($winpot, $gamestyle) . ".");
        }
        
        if ($tomove == $player)
        {
            $nxtp   = nextplayer($player) || '';
            $result = $pdo->query("update " . DB_POKER . " set p" . $player . "name = '', p" . $player . "bet = '', p" . $player . "pot = '', move = {$nxtp}, lastmove = " . $time . "  where gameID = " . $gameID);
        }
        else
        {
            $result = $pdo->query("update " . DB_POKER . " set p" . $player . "name = '', p" . $player . "bet = '', p" . $player . "pot = '', lastmove = " . $time . " where gameID = " . $gameID);
        }

        $addons->get_hooks(
            array(),
            array(
                'page'      => 'includes/inc_poker.php',
                'location'  => 'if_player_exists',
            )
        );

        $wait    = (int) WAITIMER;
        $setwait = $time + $wait;

        $pdo->query("UPDATE " . DB_PLAYERS . " SET waitimer = {$setwait} WHERE username = '{$plyrname}'");
        $pdo->query("UPDATE " . DB_PLAYERS . " SET gID = 0, vID = 0 WHERE username = '{$plyrname}'");

        header("Location: sitout.php");
        die();
    }

    header("Location: lobby.php");
    die();
}
?>