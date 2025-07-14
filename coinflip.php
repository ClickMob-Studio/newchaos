<?php
include 'header.php';
$user_id = $user_class->id;
$user_balance = $user_class->money;

$notification = "";

if (isset($_POST['bet_amount']) && isset($_POST['bet_side'])) {
    $bet_amount = (float) $_POST['bet_amount']; // assuming bet is in decimal
    $user_bet = $_POST['bet_side']; // 'heads' or 'tails'

    // Check if the user has enough money
    if ($user_balance < $bet_amount) {
        $notification = "You don't have enough money to place this bet.";
    } else {
        $house_edge = 0.48;  // 48% chance for the user to win
        $random_number = mt_rand(1, 100) / 100;
        $coin_result = $random_number <= $house_edge ? "heads" : "tails";  // Determine coin result

        if ($coin_result == $user_bet) {
            // User wins
            $result = "win";
            $notification = "The coin landed on $coin_result. You win your bet!";
            // Add winnings to user's balance
            $user_balance += $bet_amount;
        } else {
            // User loses
            $result = "lose";
            $notification = "The coin landed on $coin_result. You lose your bet.";
            // Subtract bet amount from user's balance
            $user_balance -= $bet_amount;
        }

        // Update user's balance
        perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_balance, $user_id]);

        // Insert the bet into the user's history
        perform_query("INSERT INTO user_bets (user_id, bet_amount, bet_side, result) VALUES (?, ?, ?, ?)", [$user_id, $bet_amount, $user_bet, $result]);

        // Insert the bet into the global history
        perform_query("INSERT INTO global_bets (user_id, bet_amount, bet_side, result) VALUES (?, ?, ?, ?)", [$user_id, $bet_amount, $user_bet, $result]);
    }
}

?>

<form method="POST" action="">
    Bet Amount: <input type="text" name="bet_amount"><br>
    Bet Side:
    <input type="radio" name="bet_side" value="heads"> Heads
    <input type="radio" name="bet_side" value="tails"> Tails
    <br>
    <input type="submit" value="Place Bet">
</form>

<?php
if ($notification != "") {
    echo "<p><strong>$notification</strong></p>";
}
?>

<h3>Your Last 10 Bets:</h3>
<?php

$db->query("SELECT * FROM user_bets WHERE user_id = ? ORDER BY timestamp DESC LIMIT 10");
$db->execute([$user_id]);
$result = $db->fetch_row();

foreach ($result as $row) {
    echo "You bet " . $row['bet_amount'] . " on " . $row['bet_side'] . " and you " . $row['result'] . "<br>";
}
?>

<h3>Last 10 Global Bets:</h3>
<?php
$db->query("SELECT * FROM global_bets ORDER BY timestamp DESC LIMIT 10");
$db->execute();
$result = $db->fetch_row();

foreach ($result as $row) {
    echo formatName($row['user_id']) . " bet " . $row['bet_amount'] . " on " . $row['bet_side'] . " and " . $row['result'] . "<br>";
}
?>

<?php include 'footer.php'; ?>