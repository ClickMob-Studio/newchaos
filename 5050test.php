<?php

require "header.php";
?>
<h1>50/50 Game</h1>
<p>Here you can bet against each other and try and win the other person things!</p>

<div class="row" style="justify-content: center;">
    <!-- Cash Bets -->
    <div class="col-sm-12 col-md-4 col-lg-4">
        <h1>Cash Bets</h1>
        <form id="cashBetForm">
            <input type="number" name="amnt" size="5" maxlength="20" min="10000" />
            <input type="button" value="Bet" class="betButton" data-currency="cash" />
        </form>
    </div>
    <!-- Points Bets -->
    <div class="col-4 col-md-4 col-lg-4">
        <h1>Points Bets</h1>
        <form id="pointsBetForm">
            <input type="number" name="amnt" size="5" maxlength="20" min="100" />
            <input type="button" value="Bet" class="betButton" data-currency="points" />
        </form>
    </div>
    <!-- Credit Bets -->
    <div class="col-4 col-md-4 col-lg-4">
        <h1>Credit Bets</h1>
        <form id="creditBetForm">
            <input type="number" name="amnt" size="5" maxlength="20" min="10" />
            <input type="button" value="Bet" class="betButton" data-currency="credits" />
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.betButton').click(function() {
        var form = $(this).closest('form');
        var currency = $(this).data('currency');
        var amount = form.find('input[name="amnt"]').val();
        
        // AJAX request to server
        $.ajax({
            url: 'ajax50.php', // The file where you handle the AJAX request
            type: 'POST',
            data: {
                amnt: amount,
                curr: currency,
                action: 'placeBet'
            },
            success: function(response) {
                // Handle success (e.g., display a message)
                alert("Bet placed successfully!");
            },
            error: function() {
                // Handle error
                alert("Error placing bet. Please try again.");
            }
        });
    });
});
</script>
