<?php
// Assuming `$conn` is your database connection
include 'header.php';

$tournamentId = isset($_GET['tournament_id']) ? intval($_GET['tournament_id']) : 0;

// Fetch the participants and their current status in the tournament
$db->query("
    SELECT tp.*, u.username, IFNULL(opponent.username, 'Bye') AS opponent_username 
    FROM tournament_participants tp 
    JOIN grpgusers u ON tp.user_id = u.id 
    LEFT JOIN grpgusers opponent ON tp.opponent_id = opponent.id 
    WHERE tp.tournament_id = ? 
    ORDER BY tp.current_round, tp.id
");
$db->execute([$tournamentId]);
$rows = $db->fetch_row();
if (empty($rows)) {
    die("No participants found for this tournament.");
}

$participants = [];
foreach ($rows as $row) {
    $participants[$row['current_round']][] = $row;
}

// Calculate the total number of rounds
$totalRounds = count($participants);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Tournament</title>
    <style>
        .tournament-bracket {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: flex-start;
            margin: 20px;
            flex-wrap: nowrap;
        }

        .round {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 20px;
        }

        .match {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .participant {
            padding: 5px 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            text-align: center;
            width: 150px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        }

        .result {
            font-weight: bold;
            color: #5cb85c;
        }

        .result:empty:before {
            content: 'Pending';
            color: #f0ad4e;
        }

        .bye {
            opacity: 0.5;
            font-style: italic;
        }

        .winner {
            color: #d9534f;
            font-size: 1.2em;
        }

        .loser {
            color: #bbb;
            text-decoration: line-through;
        }

        .match:not(:last-child):after {
            content: '';
            display: block;
            width: 2px;
            height: 20px;
            background: #ccc;
            margin: 5px auto;
        }
    </style>
</head>

<body>
    <h1>Tournament Bracket</h1>
    <div class="tournament-bracket">
        <?php for ($round = 1; $round <= $totalRounds; $round++): ?>
            <div class="round">
                <h2>Round <?= $round ?></h2>
                <?php foreach ($participants[$round] as $match): ?>
                    <div class="match">
                        <span class="participant <?= $match['result'] == 'Lose' ? 'loser' : '' ?>">
                            <?= htmlspecialchars($match['username']) ?>
                        </span>
                        <?php if (isset($match['opponent_id'])): ?>
                            <span class="participant <?= $match['result'] == 'Win' ? 'winner' : 'loser' ?>">
                                <?= htmlspecialchars($match['opponent_username']) ?>
                            </span>
                        <?php else: ?>
                            <span class="participant bye">Bye</span>
                        <?php endif; ?>
                        <span class="result"><?= $match['result'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
</body>

</html>