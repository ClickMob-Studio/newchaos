<?php

require "header.php";
?>


<h1>50/50 Game</h1>
<p>Here you can bet against each other and try and win the other person things!</p>

<div class="row">
    <div class="col-4 col-sm-12 ">
    <h1>CasH Bets</h1>
    <form action="5050.php" method="post">
        <input type="number" name="amnt" size="5" maxlength="20" min="10000" />
        <input type="submit" name="bcash" value="Bet" />
    </form>

    <h1>Points Bets</h1>
    <form action="5050.php" method="post">
        <input type="number" name="amnt" size="5" maxlength="20" min="10000" />
        <input type="submit" name="bcash" value="Bet" />
    </form>

    <h1>Credit Bets</h1>
    <form action="5050.php" method="post">
        <input type="number" name="amnt" size="5" maxlength="20" min="10000" />
        <input type="submit" name="bcash" value="Bet" />
    </form>

    
    </div>
</div>