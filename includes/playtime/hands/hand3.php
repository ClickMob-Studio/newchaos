<?php
if (! $delayed)
    die();

require 'playtime/multiplier.php';

if ($autopot > $BB)
{
    $msg   = $autoname . ' ' . GAME_MSG_BIG_BLIND . ' ' . money_small($BB, $gamestyle);
    $npot  = $autopot - $BB;
    $nbet  = $BB;
    $lbet  = $autoplayer . '|' . $BB;
    $ntpot = $tablepot + $nbet;
    poker_log($autoname, GAME_MSG_BIG_BLIND . ' ' . money_small($BB, $gamestyle), $gameID);
}
else
{
    $msg   = $autoname . ' ' . GAME_PLAYER_GOES_ALLIN;
    $npot  = 0;
    $nbet  = $BB;
    $lbet  = '';
    $ntpot = $tablepot + $nbet;
    poker_log($autoname, GAME_PLAYER_GOES_ALLIN, $gameID);
}

$result = $pdo->query("UPDATE " . DB_POKER . " SET pot = {$ntpot}, bet = {$nbet}, msg = '{$msg}', p{$autoplayer}pot = '{$npot}', p{$autoplayer}bet = '{$nbet}', hand = 4, lastmove = $timenow WHERE gameID = {$gameID}");
hand_hook();
