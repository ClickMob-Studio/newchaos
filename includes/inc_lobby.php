<?php

if ($valid == false) {
    header('Location: login.php');
    exit(); // Add exit() after header to stop script execution
}

// Ensure variable declaration is done separately for PHP 5.6 compatibility
$is_omaha = false;
$is_omaha = $addons->get_hooks(
    array(
        'state' => $is_omaha,
        'content' => $is_omaha,
    ),
    array(
        'page'     => 'includes/inc_lobby.php',
        'location'  => 'omaha_logic'
    )
);

// Use isset to check both GET and POST, ensuring that gameID is set correctly
$gameID = isset($_GET['gameID']) ? addslashes($_GET['gameID']) : '';
$gameID = isset($_POST['gameID']) ? addslashes($_POST['gameID']) : $gameID;

if ($gameID != '') {
    // Use positional parameter binding for compatibility with older PHP versions
    $gq = $pdo->prepare("SELECT gamestyle FROM " . DB_POKER . " WHERE gameID = ?");
    $gq->execute(array($gameID));

    if ($gq->rowCount() == 1) {
        $tabler = $gq->fetch(PDO::FETCH_ASSOC);

        // Check gamestyle and omaha status
        if ($tabler['gamestyle'] == 'o' && $is_omaha == false) {
            echo "<script>alert('Please install Omaha hold em add-on.');</script>";
            return; // This will stop execution without closing PHP script, adjust if needed
        }

        // Hook to determine if player can enter the game
        $enterGame = $addons->get_hooks(
            array(
                'content' => true
            ),
            array(
                'page'     => 'includes/inc_lobby.php',
                'location'  => 'enter_game_logic'
            )
        );

        // Check if entering the game was successful
        if ($enterGame) {
            // Ensure all values are properly escaped or parameterized
            $result = $pdo->exec("UPDATE " . DB_PLAYERS . " SET vID = " . (int)$gameID . " WHERE username = '" . addslashes($plyrname) . "'");
            header('Location: poker.php');
            exit(); // Add exit() after header to stop script execution
        }
    }

    // Redirect back to lobby if not entering the game
    header('Location: lobby.php');
    exit();
}
?>
