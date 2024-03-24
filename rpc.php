<?php
require "header.php";

$dsn = "mysql:host=127.0.0.1;dbname=aa;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, 'aa_user', 'GmUq38&SVccVSpt', $options);

if ($user_class->id != 174) {
    header('index.php');
}

echo "<style>
.container {
    display: flex;
    justify-content: space-evenly;
    flex-wrap: wrap;
}
.header, .status, .choice.next, .results, #names, #currentStatus {
    flex-basis: 100%;
}
.choice {
    display: flex;
    flex-wrap: nowrap;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}
.pick:hover {
    background-color: #2c3e50;
}
.choice > span {
    font-size: 1.25em;
}
.status {
    font-size: 14px;
    margin-bottom: 2em;
}
.winner {
    background-color: darkgreen;
}
.results, .status, .gameWinner > h2, .gameWinner > img {
    display: grid;
    margin-top: 1em;
}
#vs {
    margin: 0px 15px
}
</style>";

echo "<div class='container'>
<div class='header'>
    <h2>Rock, Paper or Scissors</h2>
</div>";

$choices = [
    'p' => [
        'name' => 'Paper',
        'image' => 'images/paper.png'
    ],
    'r' => [
        'name' => 'Rock',
        'image' => 'images/rock.png'
    ],
    's' => [
        'name' => 'Scissors',
        'image' => 'images/scissors.png'
    ],
];

if (isset($_GET['gameid'])) {

    $gameid = $_GET['gameid'];

    $stmt = $pdo->prepare("SELECT * FROM rps WHERE id = ?");
    $stmt->execute([$gameid]);
    $game = $stmt->fetch();

    if ($game) {

        $history = false;

        $player = ($game['p1'] == $user_class->id) ? 'p1' : 'p2';
        $opponent = ($player == 'p1') ? 'p2' : 'p1';

        $p = $game[$player . '_turn'];
        $o = $game[$opponent . '_turn'];

        if ($game['last']) {
            $last = unserialize($game['last']);
            if ($last['round'] <= $game['current_round'])
                if (empty($p) && empty($o))
                    $history = false;
                else
                    $history = true;
        }

        if ($game['p1'] != $user_class->id && $game['p2'] != $user_class->id) {
            diefun('You are not a player in this game');
        }

        if ($history && !empty($o) && empty($p)) {
            echo "<div class='status'>
                <span id='names'>" . formatname($user_class->id) . " <span id='vs'>VS</span> " . formatname($game[$opponent]) . "</span>
                <span>Wager: " . prettynum($game['wager'], 1) . "</span>
                <span id='currentStatus'>Waiting for opponent to proceed to next round</span>
            </div>";
            echo "<script>
                var rpsupdate = () => {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax_rps.php',
                        data: {'action' : 'update_round', 'gameid' : 1}
                    })
                    .done(function( result ) {
                        console.log(result);
                    });
                };
                setInterval(rpsupdate, 1000);
            </script>
            ";
            exit();
        }

        if (empty($p) && !$history) {
            // Current player needs to make a choice
            echo "<div class='status'>
                <span id='names'>" . formatname($user_class->id) . " <span id='vs'>VS</span> " . formatname($game[$opponent]) . "</span>
                <span>Wager: " . prettynum($game['wager'], 1) . "</span>
                <span id='currentStatus'>Round " . $game['current_round'] . " : Make your choice!</span>
            </div>";
            echo "<div class='choice pick' data-pick='r'>
                    <img width='100px' src='images/rock.png'>
                    <span>Rock</span>
                </div>
                <div class='choice pick' data-pick='p'>
                    <img width='100px' src='images/paper.png'>
                    <span>Paper</span>
                </div>
                <div class='choice pick' data-pick='s'>
                    <img width='100px' src='images/scissors.png'>
                    <span>Scissors</span>
                </div>";


                echo "<script>
                $('.pick').click(function(e) {
                    var pick = $(this).attr('data-pick');
                    console.log(pick);
                    $('.pick').remove();
                    $.ajax({
                        type: 'POST',
                        url: 'ajax_rps.php',
                        data: {'action' : 'turn', 'gameid' : 1, 'pick' : pick}
                    })
                    .done(function( result ) {
                        location.reload();
                    });
                });
            </script>";

        } else if ((empty($p) || empty($o)) && $history) {
            // Other player has moved on - need to slow last result

            echo 'HISTORY';

            $p = $last[$player];
            $o = $last[$opponent];

            $pchoice = $choices[$p];
            $ochoice = $choices[$o];

            $winner = $last['winner'];
            $pwinner = ($winner == $player) ? 'winner' : '';
            $owinner = ($winner == $opponent) ? 'winner' : '';

            // Both have chosen.. lets show the result
            echo "<div class='status'>
                <span id='currentStatus'>End of Round " . $game['current_round'] . " of 3</span>
            </div>";
            echo "<div class='choice " . $pwinner . "'>
                    <span>" . formatname($user_class->id) . "</span>
                    <img width='100px' src='" . $pchoice['image'] . "'>
                    <span>" . $pchoice['name'] . "</span>
                </div>
                <div class='choice " . $owinner . "'>
                    <span>" . formatname($game[$opponent]) . "</span>
                    <img width='100px' src='" . $ochoice['image'] . "'>
                    <span>" . $ochoice['name'] . "</span>
                </div>";

                echo "<div class='results'>
                        <h2>Round Winner: " . formatname($game[$winner]) . "</h2>
                    </div>
                ";

                // <span>Round Results</span>
                // <span>Test 1</span>
                // <span>Tester 0</span>

                echo "<div class='choice next'>
                <button id='nextround'>Next Round</button>
            </div>";
            echo "<script>
            $('#nextround').click(function(e) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax_rps.php',
                    data: {'action' : 'nextround', 'gameid' : 1}
                })
                .done(function( result ) {
                    location.reload();
                });
            });
            </script>";

            exit();
        } else {
            if (empty($o)) {
                // Waiting for opponent to make their choice
                echo "<div class='status'>
                        <span id='names'>" . formatname($user_class->id) . " <span id='vs'>VS</span> " . formatname($game[$opponent]) . "</span>
                        <span>Wager: " . prettynum($game['wager'], 1) . "</span>
                        <span id='currentStatus'>Waiting for your opponent!</span>
                    </div>";
                    echo "<script>
                var rpsupdate = () => {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax_rps.php',
                        data: {'action' : 'update', 'gameid' : 1}
                    })
                    .done(function( result ) {
                        result = JSON.parse(result)
                        if(result.status == 'done')
                            location.reload();
                    });
                };
                setInterval(rpsupdate, 2000);
            </script>";
            } else {

                $pchoice = $choices[$p];
                $ochoice = $choices[$o];

                if ($p == $o) {
                    // Tie
                    $winner = 'Tie';
                    $winnerStr = "Its a Tie!";
                } else if ($p != $o) {
                    // Player wins
                    if (($p == 'r' && $o == 's') || ($p == 's' && $o == 'p') || ($p == 'p' && $o == 'r')) {
                        $winner = $player;
                        $winnerStr = formatname($user_class->id);
                    } else {
                        $winner = $opponent;
                        $winnerStr = formatname($opponent);
                    }
                }

                if ($game['processed'] == 0) {

                    $last = [
                        'p1' => $p,
                        'p2' => $o,
                        'winner' => $winner,
                        'round' => $game['current_round']
                    ];
                    $last = serialize($last);

                    if ($winner != 'Tie') {
                        $column = $winner . '_wins';
                        $sql = "UPDATE rps SET `" . $winner . "_wins` = `" . $winner . "_wins` + 1, processed = 1, last = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$last, $gameid]);
                    } else {
                        $sql = "UPDATE rps SET processed = 1, last = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$last, $gameid]);
                    }
                }

                if ($winner != 'Tie') {
                    $pwinner = ($winner == $player) ? 'winner' : '';
                    $owinner = ($winner == $opponent) ? 'winner' : '';
                }

                // Both have chosen.. lets show the result
                echo "<div class='status'>
                <span id='names'>" . formatname($user_class->id) . " <span id='vs'>VS</span> " . formatname($game[$opponent]) . "</span>
                    <span id='currentStatus'>End of Round " . $game['current_round'] . " of 3</span>
                </div>";
                echo "<div class='choice " . $pwinner . "'>
                        <span>" . formatname($user_class->id) . "</span>
                        <img width='100px' src='" . $pchoice['image'] . "'>
                        <span>" . $pchoice['name'] . "</span>
                    </div>
                    <div class='choice " . $owinner . "'>
                        <span>" . formatname($game[$opponent]) . "</span>
                        <img width='100px' src='" . $ochoice['image'] . "'>
                        <span>" . $ochoice['name'] . "</span>
                    </div>";

                    echo "<div class='results'>
                            <h2>Round Winner: " . $winnerStr . "</h2>
                        </div>
                    ";

                    if ($game['current_round'] < $game['rounds']) {
                        echo "<div class='choice next'>
                            <button id='nextround'>Next Round</button>
                        </div>";
                        echo "<script>
                        $('#nextround').click(function(e) {
                            $.ajax({
                                type: 'POST',
                                url: 'ajax_rps.php',
                                data: {'action' : 'nextround', 'gameid' : 1}
                            })
                            .done(function( result ) {
                                location.reload();
                            });
                        });
                        </script>";
                    } else {
                        // Do winner
                        $scores = [$game['p1_wins'], $game['p2_wins']];
                        $maxs = array_keys($scores, max($scores));
                        if (count($maxs) > 1) {
                            $winner = "BOTH";
                        } else {
                            $winner = ($maxs[0] == 0) ? formatname($game[$player]) : formatname($game[$opponent]);
                            $winner_class = ($maxs[0] == 0) ? $user_class : new User($game[$opponent]);
                        }
                        echo "<div class='gameWinner'>
                        <h2>Game Winner</h2>
                        <span>$winner</span>
                        <img src='" . $winner_class->avatar . "'>
                        </div>";
                    }
            };
        }
    }
}
echo "</div>";

?>