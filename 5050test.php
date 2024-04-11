<?php
require "header.php";
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
<script>
$(document).ready(function(){
    $("#betButton").click(function(){
        var amount = $("#betAmount").val(); // Assuming you have an input field for bet amount
        $.ajax({
            url: 'ajax_50.php', // The PHP file you created
            type: 'GET',
            data: {action: 'pointbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
            },
            error: function() {
                // Handle error
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $("#betCashButton").click(function(){
        var amount = $("#betAmount").val(); // Assuming you have an input field for bet amount
        $.ajax({
            url: 'ajax_50.php', // The PHP file you created
            type: 'GET',
            data: {action: 'cashbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
            },
            error: function() {
                // Handle error
                alert("An error occurred");
            }
        });
    });
});
</script>
<h1>50/50</h1>
<div class="container">
    <table>
        <tbody>
            <td>
                <h1>Place Cash Bet</h1>
            <input type="number" id="betAmount" placeholder="Enter bet amount">
            <button id="betCashButton">Place Bet</button>
            </td>
        </tbody>
    </table>
    <div class="col-12 alert alert-info" style="display:none;"></div>
    <div class="row">
        <div class="col-md-4 col-12">
            <h1>Cash Bets</h1>
            
            <table>
            <thead>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody>
            <?php foreach ($cash as $cas): ?>
            <tr>
                <td><?= formatName($cas['userid'])?></td>
                <td><?= prettynum($cas['amnt'], 1)?></td>
                <td>LINK</td>
            </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4 col-12">
        <h1>Point Bets</h1>
            <table>
                <thead>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody>
            <?php foreach ($points as $poin): ?>
            <tr>
                <td><?= formatName($poin['userid'])?></td>
                <td><?= prettynum($poin['amnt'])?></td>
                <td>LINK</td>
            </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4 col-12">
        <h1>Credit Bets</h1>
            <table>
            <thead>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody>
            <?php foreach ($credits as $cre): ?>
            <tr>
                <td><?= formatName($cre['userid'])?></td>
                <td><?= prettynum($cre['amnt'])?></td>
                <td>LINK</td>
            </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
