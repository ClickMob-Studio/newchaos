<?php
require "ajax_header.php";
$user_class = new User($_SESSION['id']);

$dsn = "mysql:host=127.0.0.1;dbname=aa;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, 'aa_user', 'GmUq38&SVccVSpt', $options);

if (isset($_POST['action'])) {

    if (!isset($_POST['gameid']))
        die();

    $gameid = $_POST['gameid'];

    $stmt = $pdo->prepare("SELECT * FROM rps WHERE id = ?");
    $stmt->execute([$gameid]);
    $game = $stmt->fetch();

    if ($game) {
        $player = ($game['p1'] == $user_class->id) ? 'p1' : 'p2';
        $opponent = ($player == 'p1') ? 'p2' : 'p1';

        if ($game['p1'] != $user_class->id && $game['p2'] != $user_class->id) {
            echo json_encode(
                array(
                    'status' => 'error',
                // 'debug' => $debug
                )
            );
            exit();
        }

        if ($_POST['action'] == 'update') {
            $status = (empty($game[$opponent . '_turn'])) ? 'wait' : 'done';

            echo json_encode(
                    array(
                        'status' => $status
                    )
                );
        } else if ($_POST['action'] == 'update_round') {

            $status = (!empty($game[$opponent . '_turn'])) ? 'wait' : 'done';

            echo json_encode(
                    array(
                        'status' => $status
                    )
                );
        } elseif ($_POST['action'] == 'turn') {
            $pick = $_POST['pick'];
            $allowed = array('p', 'r', 's');
            if (in_array($pick, $allowed)) {
                if (empty($game[$player . '_turn'])) {
                    $sql = "UPDATE rps SET `" . $player . "_turn` = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$pick, $gameid]);
                }
            }
        } elseif ($_POST['action'] == 'nextround') {
            if ($game['current_round'] != $game['rounds']) {
                if ($game['processed'] == 1) {
                    if (!empty($game[$player . '_turn'])) {
                        $sql = "UPDATE rps SET `" . $player . "_turn` = '' WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$gameid]);
                    }

                    $stmt = $pdo->prepare("SELECT * FROM rps WHERE id = ?");
                    $stmt->execute([$gameid]);
                    $game = $stmt->fetch();

                    if ($game['p1_turn'] == '' && $game['p2_turn'] == '') {
                        // End Round
                        $sql = "UPDATE rps SET processed = 0, `last` = null, current_round = current_round + 1 WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$gameid]);
                    }
                }
            }
        } else {
            // No Game
        }
    }
}

$pdo = null;

// $.ajax({
//     type: "POST",
//     url: "ajax_rps.php",
//     data: {'action' : 'update', 'gameid' : 1}
// })
// .done(function( result ) {
//     result = JSON.parse(result)
//     if (result.status == 'done')
//         location.reload();
// });