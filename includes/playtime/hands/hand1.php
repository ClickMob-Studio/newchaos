<?php
if (! $delayed)
    die();

$i   = 1;
$z   = 0;
$err = true;

while ($i < 11)
{
    $pl    = $tpr["p{$i}name"];
    $chips = $tpr["p{$i}pot"];

    if ($pl != '')
    {
        if ($chips > $z)
        {
            $z          = $chips;
            $chipleader = $pl;
            $err        = false;
        }
        elseif ($chips == $z)
        {
            $err = true;
        }

        $pdo->query("UPDATE " . DB_STATS . " SET handsplayed = handsplayed + 1 WHERE player = '{$pl}'");
    }

    $i++;
}

if ($err == true)
    poker_log('', GAME_MSG_LETS_GO, $gameID);
else
    poker_log($chipleader, GAME_MSG_CHIP_LEADER, $gameID);

$msg = ($err == true) ? GAME_MSG_LETS_GO : GAME_MSG_CHIP_LEADER . ' ' . $chipleader;
$pdo->query("UPDATE " . DB_POKER . " SET msg = '{$msg}', move = {$nextup}, hand = 2, lastmove = $timenow WHERE gameID = $gameID");
hand_hook();
