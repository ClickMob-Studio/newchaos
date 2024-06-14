<?php
require('gen_inc.php');

header('Content-Type: text/javascript');

// get list of lobbies live

$elementId = 'gamelist';

if (isset($_GET['l']) && strlen($_GET['l']) > 0)
	$elementId = preg_replace('/[^A-Za-z0-9_-]/i', '', $_GET['l']);

if ($valid == false)
	die();

$sql = $addons->get_hooks(
    array(
        'content' => "SELECT * FROM " . DB_POKER . " ORDER BY gamestyle desc, tabletype desc, sbamount ASC"
    ),
    array(
        'page'     => 'includes/live_games.php',
        'location'  => 'tableq_sql'
    )
);
$tableq = $pdo->query($sql);
$table_html = '';
$rows = '';

$games = array();
if (! function_exists('add_game_table_column'))
{
	function add_game_table_column($label, $value)
	{
		global $games, $gamID;
		$games[$gamID][$label] = $value;
	}
}

while ($tabler = $tableq->fetch(PDO::FETCH_ASSOC))
{
	$i = 1;
	$x = 0;
	$time = time();
	$ktimer = DISCONNECT;
	$timekick = $time - $ktimer;
	$gamID = $tabler['gameID'];

	$addons->get_hooks(

	    array(),

	    array(
	        'page'     => 'includes/live_games.php',
	        'location'  => 'after_gameid_var'
	    )
	);

	while ($i < 11)
	{
		if (strlen($tabler['p' . $i . 'name']) != '')
		{
			$usr     = $tabler['p' . $i . 'name'];
			$pot     = $tabler['p' . $i . 'pot'];
			$ttq_sql = $addons->get_hooks(
                array(
                    'content' => "SELECT gID, timetag FROM " . DB_PLAYERS . " WHERE username = '$usr'"
                ),
                array(
                    'page'     => 'includes/live_games.php',
                    'location'  => 'ttq_sql'
                )
            );
			$ttq = $pdo->query($ttq_sql);
			$ttr = $ttq->fetch(PDO::FETCH_ASSOC);
			$tpr = $ttr;

			$tkick = ($ttr['timetag'] < $timekick) ? true : false;
			$tkick = $addons->get_hooks(
                array(
                    'state' => $tkick,
                    'content' => $tkick,
                ),
                array(
                    'page'     => 'includes/live_games.php',
                    'location'  => 'player_tkick'
                )
            );

			if ($tkick || ($ttr['gID'] != $gamID))
			{
				$result = $pdo->exec("update " . DB_POKER . " set p" . $i . "name = '', p" . $i . "bet = '', p" . $i . "pot = '' , lastmove = " . ($time + 1) . " where gameID = " . $gamID);
				//$result = $pdo->exec("update " . DB_STATS . " set winpot = winpot + " . $pot . " where player  = '" . $usr . "'  ");
				$bankorpoints = ($tabler['gamestyle']=="p") ? 'points' : 'bank';
				$result = $pdo->exec("update grpgusers set $bankorpoints = $bankorpoints + " . $pot . " where username  = '" . $usr . "'  ");
				
				$result3_sql = $addons->get_hooks(
                    array(
                        'content' => "UPDATE " . DB_PLAYERS . " SET gID = 0 WHERE username = '$usr'"
                    ),
                    array(
                        'page'     => 'includes/live_games.php',
                        'location'  => 'result3_sql'
                    )
                );
				$result3     = $pdo->exec($result3_sql);
			}

			$x++;
		}

		$i++;
	}

	$tablename       = $tabler['tablename'];
	$gamestyle		 = ($tabler['gamestyle'] == 't') ? GAME_TEXAS : GAME_TEXASPOINTS;
	$min             = money_small($tabler['tablelow'], $tabler['gamestyle']);
	$tablelimit      = $tabler['tablelimit'];
	$max             = money_small($tablelimit, $tabler['gamestyle']);
	$gID             = $tabler['gameID'];
	$sbamount		 = $tabler['sbamount'];
	$bbamount		 = $tabler['bbamount'];	
	$tablemultiplier = 1;

	if ($tablelimit == 25000) $tablemultiplier = 2;
	if ($tablelimit == 50000) $tablemultiplier = 4;
	if ($tablelimit == 100000) $tablemultiplier = 8;
	if ($tablelimit == 250000) $tablemultiplier = 20;
	if ($tablelimit == 500000) $tablemultiplier = 40;
	if ($tablelimit == 1000000) $tablemultiplier = 80;

	if($sbamount != 0) {
	$SB = money_small($tabler['sbamount'], $tabler['gamestyle']);
	$BB = money_small($tabler['bbamount'], $tabler['gamestyle']);
	} else {
	if ($tabler['tabletype'] == 't')
		{
		$BB = money_small(50 * $tablemultiplier, $tabler['gamestyle']) . '-' . money_small(50 * $tablemultiplier * 9, $tabler['gamestyle']);
		$SB = money_small(25 * $tablemultiplier, $tabler['gamestyle']) . '-' . money_small(25 * $tablemultiplier * 9, $tabler['gamestyle']);
		}
	  else
		{
		$BB = money_small(200 * $tablemultiplier, $tabler['gamestyle']);
		$SB = money_small(100 * $tablemultiplier, $tabler['gamestyle']);
		}
	}

	$NEW_GAME     = addslashes(NEW_GAME);
	$PLAYING      = addslashes(PLAYING);
	$tablestatus  = (($tabler['hand'] == '') ? $NEW_GAME : $PLAYING);
	$TOURNAMENT   = addslashes(TOURNAMENT);
	$SITNGO       = addslashes(SITNGO);
	$tabletype    = ($tabler['tabletype'] == 't') ? $TOURNAMENT : $SITNGO;
	$buyin        = ($tabler['tabletype'] == 't') ? $max : $min . ' / ' . $max;


    
    $tablename = $addons->get_hooks(array('content' => $tablename), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_name'
	));
	$games[$gamID][TABLE_HEADING_NAME] = $tablename;

	$tableplayers = $x . '/10';

	$games[$gamID][TABLE_HEADING_PLAYERS] = $tableplayers;

	$games[$gamID][TABLE_HEADING_TYPE] = $tabletype;

	$games[$gamID][ADMIN_TABLES_GAME] = $gamestyle;

	$games[$gamID][TABLE_HEADING_BUYIN] = $buyin;

	$games[$gamID]["Blinds"] = $SB ." / ".$BB;

//	$games[$gamID][TABLE_HEADING_SMALL_BLINDS] = $SB;

//	$games[$gamID][TABLE_HEADING_BIG_BLINDS] = $BB;

	//$games[$gamID][TABLE_HEADING_STATUS] = $tablestatus;

	$addons->get_hooks(array(), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'each_game_column'
	));
}

$opsTheme->addVariable('rows', $rows);

/* --- Table Header */
$gameHead  = '';

foreach (array_keys($games[$gamID]) as $label)
{
	$opsTheme->addVariable('text', $label);
	$gameHead .= $opsTheme->viewPart('gametable-head-col');
}
/* Table Header --- */

/* --- Table Rows */
$gameRows = '';

foreach ($games as $rowId => $row)
{
	$columns = '';

	foreach ($row as $col)
	{
		$opsTheme->addVariable('text', $col);
		$columns .= $opsTheme->viewPart('gametable-each-col');
	}

	$rowArray = $addons->get_hooks(
		array(
			'content' => array(
				'id'      => $rowId,
				'onclick' => "open_game({$rowId});",
				'columns' => $columns
			)
		),
		array(
			'page'     => 'includes/live_games.php',
			'location'  => 'each_game_row_info'
		)
	);
	$opsTheme->addVariable('row', $rowArray);

	$gameRows .= $opsTheme->viewPart('gametable-each-row');
}
/* Table Rows --- */

$opsTheme->addVariable('game', array(
	'head' => $gameHead,
	'rows' => $gameRows
));
?>

gameslist = '<?php echo $opsTheme->viewPage('live-games'); ?>';
document.getElementById('<?= $elementId; ?>').innerHTML = gameslist;