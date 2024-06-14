<?php
if (! $delayed)
    die();

$nxtdeal = nextdealer($dealer);

if ($nxtdeal == '')
    die();

$msg = get_name($nxtdeal) . ' ' . GAME_MSG_DEALER_BUTTON;
poker_log( get_name($nxtdeal), GAME_MSG_DEALER_BUTTON, $gameID );

$result_sql = $addons->get_hooks(
    array(
        'content' => "UPDATE " . DB_POKER . " SET msg = '{$msg}', lastmove = $timenow, dealer = {$nxtdeal}, move = {$nxtdeal}, bet = 0, lastbet = 0, pot = 0, p1bet = '0', p2bet = '0', p3bet = '0', p4bet = '0', p5bet = '0', p6bet = '0', p7bet = '0', p8bet = '0', p9bet = '0', p10bet = '0', hand = 1  WHERE gameID = {$gameID}"
    ),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'hand0_sql'
));
$result     = $pdo->query($result_sql);
hand_hook();
