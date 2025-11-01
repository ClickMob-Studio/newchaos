<?php
include 'header.php';


function microtime_float()
{
    $time = microtime();
    return (float) substr($time, 11) + (float) substr($time, 0, 8);
}

function updateMoney($money)
{
    echo '<script>$(document).ready(function(){	$("#money_container").html( "' . number_format($money) . ' ");});</script>';
}



$user_class = new User($_SESSION['id']);
$mysql = new MySQL();

if ($user_class->sessionkey != $_SESSION['iid']) {
    session_destroy();
    header('Location: index.php');
}
// Assuming the header includes the database connection and user session info

// ... (rest of the PHP functions and game logic)

?>

<h1>Blackjack - Place your bet</h1>
<p>Max bet: $100,000</p>
<p>Max games per day: 50</p>
<p>Games left for today: 50</p> <!-- This value should be dynamic based on user's activity -->

<form method="post" action="">
    <label for="betAmount">Bet Amount:</label>
    <input type="number" name="betAmount" id="betAmount" min="100" max="100000" required>
    <input type="submit" name="placeBet" value="Place Bet">
</form>

<h2>The Table</h2>
<table>
    <tr>
        <th>Your Cards</th>
        <th>Dealer's Cards</th>
    </tr>
    <tr>
        <td>
            <img src="/images_cards/back.png" alt="Card" name="card" width="72" height="96" id="card">&nbsp;<img
                src="images_cards/back.png" alt="Card" name="card" width="72" height="96" id="card"><br>
        </td>
        <td>
            <img src="/images_cards/back.png" alt="Card" name="card" width="72" height="96" id="card">&nbsp;<img
                src="images_cards/back.png" alt="Card" name="card" width="72" height="96" id="card"><br>
        </td>
    </tr>
</table>

<tr>
    <th>Player</th>
    <th>Rank</th>
    <th>Times Won</th>
</tr>


<form method="post" action="">
    <input type="submit" name="hit" value="Hit">
    <input type="submit" name="stand" value="Stand">
</form>