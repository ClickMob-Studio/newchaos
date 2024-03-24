<?php
include 'header.php';

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    $bet = $_POST['bet']; // The amount of money the user bets
    $bullets = $_POST['bullets']; // The number of bullets

    // Security checks for bet and bullets
    if (!is_numeric($bet) || $bet <= 0) {
        echo "Invalid bet amount.";
        exit;
    }
    if (!is_numeric($bullets) || $bullets < 1 || $bullets > 5) {
        echo "Invalid number of bullets. Choose between 1 and 5.";
        exit;
    }

    // Check if the user has enough money for the bet
    // This should be replaced with a query to your database
    $userMoney = 1000; // Replace this with the actual user money
    if ($userMoney < $bet) {
        echo "You do not have enough money to make this bet.";
        exit;
    }

    // Determine if the user gets shot
    $chamber = rand(1, 6);
    if ($chamber <= $bullets) {
        echo "Bang! You've been shot! You lose your bet of $bet.";
        // Deduct the bet from the user's money
        // Add database operation here
    } else {
        // Calculate payout
        $payoutMultiplier = 1.15 + (($bullets - 1) * 0.15);
        $winnings = $bet * $payoutMultiplier;
        echo "Click! The gun did not fire. You win! You get $winnings.";

        // Add the winnings to the user's money
        // Add database operation here
    }
}

?>

<form action="russianroulette.php" method="post">
    Bet Amount: <input type="number" name="bet" min="1" required><br>
    Number of Bullets (1-5): <input type="number" name="bullets" min="1" max="5" required><br>
    <input type="submit" name="submit" value="Play">
</form>
