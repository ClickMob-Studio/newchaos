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

    function updateTables() {
        $.ajax({
            url: 'get_bets.php?action=update', 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var cashRows = '';
                data.cash.forEach(function(bet) {
                    cashRows += `<tr><td>${bet.userid}</td><td>$${bet.amnt}</td><td><button class="takeCashButton" value="${bet.id}">Take</button></td></tr>`;
                });
                $('#cashbettable tbody').html(cashRows);
                var pointsRows = '';
                data.points.forEach(function(bet) {
                    pointsRows += `<tr><td>${bet.userid}</td><td>${bet.amnt} points</td><td><button class="takePointsButton" value="${bet.id}">Take</button></td></tr>`;
                });
                $('#pointbettable tbody').html(pointsRows);

                var creditsRows = '';
                data.credits.forEach(function(bet) {
                    creditsRows += `<tr><td>${bet.userid}</td><td>${bet.amnt} credits</td><td><button class="takeCreditsButton" value="${bet.id}">Take</button></td></tr>`;
                });
                $('#creditbettable tbody').html(creditsRows);
            },
            error: function() {
                console.error("Failed to fetch bet data");
            }
        });
    }
    setInterval(updateTables, 2000);
});
$(document).ready(function(){
    $("#betButton").click(function(){
        var amount = $("#betAmount").val();
        $.ajax({
            url: 'ajax_50.php',
            type: 'GET',
            data: {action: 'pointbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
            },
            error: function() {
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $("#betCashButton").click(function(){
        var amount = $("#betAmount").val(); 
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'cashbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                var newRow = `<tr><<td><?= $user_class->formattedname; ?></td><td>$${amount}</td> <td></td></tr>`;
                $("#cashbettable tbody").append(newRow);
            },
            error: function() {
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $("#betPointsButton").click(function(){
        var amount = $("#betPAmount").val(); 
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'pointbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                var newRow = `<tr><<td><?= $user_class->formattedname; ?></td><td>${amount} points</td> <td></td></tr>`;
                $("#pointbettable tbody").append(newRow);
            },
            error: function() {

                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $("#betCreditsButton").click(function(){
        var amount = $("#betCAmount").val(); 
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'creditbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                var newRow = `<tr><<td><?= $user_class->formattedname; ?></td><td>${amount} credits</td> <td></td></tr>`;
                $("#creditbettable tbody").append(newRow);
            },
            error: function() {
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $(document).on('click', '.takePointsButton', function(){
        var amount = $(this).val();
        var $button = $(this);
       
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'takepointbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                $button.closest('tr').fadeOut(400, function() { 
                    $(this).remove();
                });
             },
            error: function() {
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $(document).on('click', '.takeCreditsButton', function(){
        var amount = $(this).val();
        var $button = $(this);
       
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'takepointbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                $button.closest('tr').fadeOut(400, function() { 
                    $(this).remove();
                });
             },
            error: function() {
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $(document).on('click', '.takeCashButton', function(){
        var amount = $(this).val();
        var $button = $(this);
       
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'takecashbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                $button.closest('tr').fadeOut(400, function() { 
                    $(this).remove();
                });
             },
            error: function() {
                alert("An error occurred");
            }
        });
    });
});
$(document).ready(function(){
    $(document).on('click', '.removeCashButton', function(){
        var amount = $(this).val();
        var $button = $(this);
       
        $.ajax({
            url: 'ajax_50.php', 
            type: 'GET',
            data: {action: 'removecashbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                $button.closest('tr').fadeOut(400, function() { 
                    $(this).remove();
                });
             },
            error: function() {
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
        <div class="container">
    <div class="row">
        <div class="col-md-4 col-12">
            <h1>Place Cash Bet</h1>
            <input type="number" id="betAmount" class="form-control" placeholder="Enter bet amount">
            <button id="betCashButton" class="btn btn-primary mt-2">Place Bet</button>
        </div>
        <div class="col-md-4 col-12">
            <h1>Place Points Bet</h1>
            <input type="number" id="betPAmount" class="form-control" placeholder="Enter bet amount">
            <button id="betPointsButton" class="btn btn-primary mt-2">Place Bet</button>
        </div>
        <div class="col-md-4 col-12">
            <h1>Place Credit Bet</h1>
            <input type="number" id="betCAmount" class="form-control" placeholder="Enter bet amount">
            <button id="betCreditsButton" class="btn btn-primary mt-2">Place Bet</button>
        </div>
    </div>
</div>

        </tbody>
    </table>
    <div class="col-12 alert alert-info" style="display:none;"></div>
    <div class="row">
        <div class="col-md-6 col-12 style="padding-bottom:10px;"">
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
                <td><?= formatName($cas['userid'])?></td>
                <td><?= prettynum($cas['amnt'], 1)?></td>

                <?php if($user_class->id == $cas['userid']):?>
                    <td><button class="removeCashButton" value="<?=$cas['id'];?>">Remove</button></td>
                <?php else:?>
                <td><button class="takeCashButton" value="<?=$cas['id'];?>">Take</button></td>
                <?php endif;?>
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
                <td><?= formatName($poin['userid'])?></td>
                <td><?= prettynum($poin['amnt'])?> points</td>
                <?php if($user_class->id == $poin['userid']):?>
                    <td><button class="removeCashButton" value="<?=$poin['id'];?>">Remove</button></td>
                <?php else:?>
                <td><button class="takePointsButton" value="<?=$poin['id'];?>">Take</button></td>
                <?php endif;?>
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
                <td><?= formatName($cre['userid'])?></td>
                <td><?= prettynum($cre['amnt'])?></td>
                <?php if($user_class->id == $cre['userid']):?>
                    <td><button class="removeCashButton" value="<?=$cre['id'];?>">Remove</button></td>
                <?php else:?>
                <td><button class="takeCreditsButton" value="<?=$cre['id'];?>">Take</button></td>
                <?php endif;?>
            </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php 
require_once __DIR__ . '/footer.php';
