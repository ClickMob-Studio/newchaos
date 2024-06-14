<?php 
require('sec_inc.php'); 

header('Content-Type: text/javascript');

// if ip check is on
if (IPCHECK == 1)
{
	// ip address
	$ip = $_SERVER['REMOTE_ADDR'];  
	$ipq = $pdo->query("select ipaddress from " . DB_PLAYERS . " where ipaddress = '$ip' and gID = " . $gameID); 

	// if user with the same ip exists, die
	if ($ipq->rowCount() > 0)
		die();
} 
	
$time = time(); 
$action = addslashes($_GET['action']); 

// if it is a proper action
if ($action > 0 && $action < 11)
{
	$zq = $pdo->query("SELECT gameID FROM " . DB_POKER . " WHERE gameID = $gameID AND (p1name = '$plyrname' OR p2name = '$plyrname' OR p3name = '$plyrname' OR p4name = '$plyrname' OR p5name = '$plyrname' OR p6name = '$plyrname' OR p7name = '$plyrname' OR p8name = '$plyrname' OR p9name = '$plyrname' OR p10name = '$plyrname')");
	
	// if player is already joined, die
	if ($zq->rowCount() == 1)
		die();
	
	$cq = $pdo->query("SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID . " AND p".$action."name = ''");
	
	// if player hasn't joined yet
	if ($cq->rowCount() == 1)
	{
		$cr = $cq->fetch(PDO::FETCH_ASSOC);
		$gamestyle  = $cr['gamestyle'];
		$bankorpoints = ($gamestyle=="p") ? 'points' : 'bank';

		//$statsq = $pdo->query("SELECT winpot FROM " . DB_STATS . " WHERE player = '{$plyrname}'");
		$statsq = $pdo->query("SELECT $bankorpoints FROM grpgusers WHERE username = '{$plyrname}'");


		$statsr = $statsq->fetch(PDO::FETCH_ASSOC);
		 
		$winnings = $statsr[$bankorpoints];
		
		$tablelow = $cr['tablelow'];
		
		$tablename  = $cr['tablename'];
		$tablelimit = $cr['tablelimit'];
		$tabletype  = $cr['tabletype'];
		
		$hand = $cr['hand'];

		$proceed = ($tabletype == 't' && $hand >= 0) ? false : true;
		$proceed = $addons->get_hooks(
			array(
				'content' => $proceed
			),
			array(
        	'page'     => 'includes/join.php',
        	'location'  => 'proceed_bool'
        ));

		if ($proceed)
		{
			$chips = ($winnings > $tablelimit) ? $tablelimit : $winnings;
			$chips = $addons->get_hooks(
				array(
					'content' => $chips
				),
				array(
	        		'page'     => 'includes/join.php',
	        		'location'  => 'chips_variable'
	        	)
			);
			
			$cost  = $chips;
			$cost = $addons->get_hooks(
				array(
					'content' => $cost
				),
				array(
	        		'page'     => 'includes/join.php',
	        		'location'  => 'cost_variable'
	        	)
			);

			if ($tabletype == 't')
				$tablelow = $tablelimit;

			$tablelow = $addons->get_hooks(
				array(
					'content' => $tablelow
				),
				array(
	        		'page'     => 'includes/join.php',
	        		'location'  => 'tablelow_variable'
	        	)
			);
			
			if ($chips >= $tablelow && $chips > 0)
			{
				$pdo->query("update " . DB_POKER . " set p".$action."name = '$plyrname', p".$action."bet = 'F', p".$action."pot = '$chips' where gameID = " . $gameID);
				$bank = $winnings - $cost;

				$sitelog  = "{$plyrname} joined table {$tablename} on seat {$action} with " . money($chips, $gamestyle) . ' pot';

				if ($cost !== $chips)
					$sitelog .= ' which cost him ' . money($cost, $gamestyle);

				$sitelog .= ", he has " . money($bank, $gamestyle) . " left in his bank";

				OPS_sitelog($plyrname, $sitelog);
				$bankorpoints = ($gamestyle=="p") ? 'points' : 'bank';
				$pdo->query("update grpgusers set $bankorpoints=$bank where username  = '$plyrname'");
				if ($tabletype == 't')
				{
					//$pdo->query("update " . DB_STATS . " set tournamentsplayed = tournamentsplayed + 1, winpot = $bank where player  = '$plyrname'");
					$pdo->query("update " . DB_STATS . " set tournamentsplayed = tournamentsplayed + 1 where player  = '$plyrname'");
				}
				else
				{
					//$pdo->query("update " . DB_STATS . " set gamesplayed = gamesplayed + 1, winpot = $bank  where player  = '$plyrname'");
					$pdo->query("update " . DB_STATS . " set gamesplayed = gamesplayed + 1  where player  = '$plyrname'"); 
				} 
				
				$pdo->query("update " . DB_PLAYERS . " set gID = $gameID, lastmove = " . ($time+3) . ", timetag = " . ($time+3) . "  where username = '$plyrname'");
				
				$addons->get_hooks(array(), array(
	            	'page'     => 'includes/join.php',
	            	'location'  => 'player_joined'
	            ));
				
				poker_log($plyrname, GAME_PLAYER_BUYS_IN . ' ' . money($chips, $gamestyle), $gameID);
?>
document.getElementById('player-<?php echo $action; ?>-image').innerHTML = '<img src="themes/<?php echo THEME; ?>/images/13.gif">';
<?php }else{ ?>
<?php if($tabletype == 't'){ ?>
alert('<?php echo INSUFFICIENT_BANKROLL_TOURNAMENT;?>');
<?php }else{ ?>
alert('<?php echo INSUFFICIENT_BANKROLL_SITNGO;?>');
<?php } ?>
<?php } }else{ ?>
alert('You cannot join this table');
<?php } } } $result = $pdo->query("update ".DB_POKER." set lastmove = ".($time+2)."  where gameID = ".$gameID); ?>