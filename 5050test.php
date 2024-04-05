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
document.addEventListener('DOMContentLoaded', function() {
    // Select all bet buttons
    const betButtons = document.querySelectorAll('.betButton');

    // Add click event listener to each button
    betButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const formData = new FormData(form); 
            formData.append('currency', this.getAttribute('data-currency'));
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Handle response data
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
