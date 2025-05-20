<?php

require "header.php";

// if($user_class->id == 24) {
//     exit();
// }
if ($_GET['action'] == 'ban') {
    $db->query("UPDATE `grpgusers` SET `ffban` = 1 WHERE `id` = " . $user_class->id);
    $db->execute();
    echo Message('You have banned yourself from 5050 for 1 day');
    require "footer.php";
    exit;
}
$db->query("SELECT ffban FROM grpgusers WHERE ffban > 0 AND  id = " . $user_class->id);
$db->execute();
if ($db->num_rows() > 0) {
    //echo Message('You have been banned from 5050 for 1 day');
    //require "footer.php";
    //exit;
}


$db->query("SELECT * FROM fiftyfifty WHERE currency = 'cash'");
$db->execute();
$cash = $db->fetch_row();
$db->query("SELECT * FROM fiftyfifty WHERE currency = 'points'");
$db->execute();
$points = $db->fetch_row();
$db->query("SELECT * FROM fiftyfifty WHERE currency = 'credits'");
$db->execute();
$credits = $db->fetch_row();
?>
<script type="text/javascript" src="js/5050.js?v=<?php echo time(); ?>"></script>
<h1>50/50</h1>
<p>Click <a href="?action=ban">Here</a> to ban yourself from 5050 for 1 day</a>

<div class="container">
    <table>
        <tbody>
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <h1>Place Cash Bet</h1>
                        <input type="number" id="betAmount" placeholder="Enter bet amount">
                        <button id="betCashButton">Place Bet</button>
                    </div>
                    <div class="col-md-4 col-12">
                        <h1>Place Points Bet</h1>
                        <input type="number" id="betPAmount" placeholder="Enter bet amount">
                        <button id="betPointsButton">Place Bet</button>
                    </div>
                    <div class="col-md-4 col-12">
                        <h1>Place Credit Bet</h1>
                        <input type="number" id="betCAmount" placeholder="Enter bet amount">
                        <button id="betCreditsButton">Place Bet</button>
                    </div>
                </div>
            </div>

        </tbody>
    </table>
    <div class="col-12 alert alert-info" style="display:none;"></div>
    <div class="row">
        <div class="col-md-6 col-12 style=" padding-bottom:10px;">
            <h1>Cash Bets</h1>
            <table id='cashbettable'>
                <thead>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php foreach ($cash as $cas): ?>
                        <tr>
                            <td><?= formatName($cas['userid']) ?></td>
                            <td><?= prettynum($cas['amnt'], 1) ?></td>

                            <?php if ($user_class->id == $cas['userid']): ?>
                                <td><button class="removeCashButton" value="<?= $cas['id']; ?>">Remove</button></td>
                            <?php else: ?>
                                <td><button class="takeCashButton" value="<?= $cas['id']; ?>">Take</button></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6 col-12" style="padding-bottom:10px;">
            <h1>Point Bets</h1>
            <table id="pointbettable">
                <thead>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php foreach ($points as $poin): ?>
                        <tr>
                            <td><?= formatName($poin['userid']) ?></td>
                            <td><?= prettynum($poin['amnt']) ?> points</td>
                            <?php if ($user_class->id == $poin['userid']): ?>
                                <td><button class="removeCashButton" value="<?= $poin['id']; ?>">Remove</button></td>
                            <?php else: ?>
                                <td><button class="takePointsButton" value="<?= $poin['id']; ?>">Take</button></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-3 col-none"></div>
        <div class="col-md-6 col-12">
            <h1>Credit Bets</h1>
            <table id="creditbettable">
                <thead>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php foreach ($credits as $cre): ?>
                        <tr>
                            <td><?= formatName($cre['userid']) ?></td>
                            <td><?= prettynum($cre['amnt']) ?></td>
                            <?php if ($user_class->id == $cre['userid']): ?>
                                <td><button class="removeCashButton" value="<?= $cre['id']; ?>">Remove</button></td>
                            <?php else: ?>
                                <td><button class="takeCreditButton" value="<?= $cre['id']; ?>">Take</button></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<h1>Last 10 bets</h1>
<div class='container lastbets'>


</div>
<div>
    <h1>Stats</h1>
    <?php
    $sql = "SELECT
    (SELECT COUNT(*) FROM `5050log` WHERE `better` = " . $user_class->id . " OR `userid` = " . $user_class->id . ") AS total_games,
    (SELECT COUNT(*) FROM `5050log` WHERE `winner` = " . $user_class->id . ") AS games_won";
    $result = mysql_query($sql);

    $res = mysql_fetch_assoc($result);
    $totalGames = $res['total_games'];
    $gamesWon = $res['games_won'];

    if ($totalGames > 0) {
        $winningPercentage = ($gamesWon / $totalGames) * 100;
    } else {
        $winningPercentage = 0;
    }
    echo "Your winning percentage is: " . number_format($winningPercentage, 2) . "%";
    echo "<br>";
    echo "You have played a total of " . number_format($totalGames) . " games";


    ?>

    <?php
    require_once __DIR__ . '/footer.php';
