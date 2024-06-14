<?php
$logic = ($autoplayer == getplayerid($plyrname)) ? true : false;
$logic = $addons->get_hooks(
    array(
        'state' => $logic,
        'content' => $logic,
    ),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'hand15_logic'
));
if ($logic)
{
    $cardq = $pdo->prepare("SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID);

    $cardq->execute();
    $cardr      = $cardq->fetch(PDO::FETCH_ASSOC);
    $tablecards = array(
        decrypt_card($cardr['card1']),
        decrypt_card($cardr['card2']),
        decrypt_card($cardr['card3']),
        decrypt_card($cardr['card4']),
        decrypt_card($cardr['card5'])
    );

    $multiwin   = find_winners($game_style);
    $i          = 0;

    while ($multiwin[$i] != '')
    {
        $usr    = get_name($multiwin[$i]);
        $result = $pdo->query("UPDATE " . DB_STATS . " SET handswon = handswon+1 WHERE player = '{$usr}' ");
        $i++;
    }

    distpot($game_style);
    $result = $pdo->query("UPDATE " . DB_POKER . " SET hand = 0, pot = 0, lastmove = $timenow WHERE gameID = {$gameID}");
    hand_hook();
}
