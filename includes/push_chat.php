<?php
require('sec_inc.php');
header('Content-Type: text/javascript');

if (empty($gID) || $gID == 0) {
    die();
}

if (!isset($_POST['msg']) && !isset($_GET['msg'])) {
    die();
}

// Fetch existing chat entries
$chtq = $pdo->query("SELECT * FROM " . DB_USERCHAT . " WHERE gameID = " . (int)$gID);
$chtr = $chtq->fetch(PDO::FETCH_ASSOC);
$time = time() + 2;

// Sanitize and process previous chat messages
$c2 = isset($chtr['c2']) ? addslashes($chtr['c2']) : '';
$c3 = isset($chtr['c3']) ? addslashes($chtr['c3']) : '';
$c4 = isset($chtr['c4']) ? addslashes($chtr['c4']) : '';
$c5 = isset($chtr['c5']) ? addslashes($chtr['c5']) : '';

// Get the new message from POST or GET
$msg = isset($_POST['msg']) ? $_POST['msg'] : $_GET['msg'];
$msg = strip_tags(str_ireplace(array('"', "'"), array('&quot;', '&apos;'), $msg));
$msg = preg_replace('/([^\s]{16})(?=[^\s])/m', '$1 ', $msg);
$msg = substr($msg, 0, 100); // Limit message to 100 characters

if (!empty($msg)) {
    // Fetch player information
    $plyrQ = $pdo->prepare("SELECT ID, avatar FROM " . DB_PLAYERS . " WHERE username = :username");
    $plyrQ->execute(array(':username' => $plyrname));
    $plyrF = $plyrQ->fetch(PDO::FETCH_ASSOC);

    if ($plyrF) {
        // Create the message XML
        $msg = '<user>
            <id>' . htmlspecialchars($plyrF['ID'], ENT_QUOTES, 'UTF-8') . '</id>
            <name>' . htmlspecialchars($plyrname, ENT_QUOTES, 'UTF-8') . '</name>
            <avatar>' . htmlspecialchars($plyrF['avatar'], ENT_QUOTES, 'UTF-8') . '</avatar>
        </user>
        <message>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</message>';

        $msg = addslashes($msg);

        // Check if chat exists for the game and update or insert
        if ($chtq->rowCount() > 0) {
            $updateStmt = $pdo->prepare("UPDATE " . DB_USERCHAT . " SET updatescreen = :time, c1 = :c2, c2 = :c3, c3 = :c4, c4 = :c5, c5 = :msg WHERE gameID = :gameID");
            $updateStmt->execute(array(
                ':time' => $time,
                ':c2' => $c2,
                ':c3' => $c3,
                ':c4' => $c4,
                ':c5' => $c5,
                ':msg' => $msg,
                ':gameID' => $gID,
            ));
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO " . DB_USERCHAT . " (updatescreen, c5, gameID) VALUES (:time, :msg, :gameID)");
            $insertStmt->execute(array(
                ':time' => $time,
                ':msg' => $msg,
                ':gameID' => $gID,
            ));
        }
    }
}
?>
