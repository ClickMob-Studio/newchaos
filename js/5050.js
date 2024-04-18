$(document).ready(function(){

    function updateTables() {
        $.ajax({
            url: '/ajax_50.php?action=update', 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var cashRows = '';
                data.cash.forEach(function(bet) {
                    cashRows += `<tr><td>${bet.formatted_userid}</td><td>$${bet.amnt}</td><td>${bet.button}</td></tr>`;
                });
                $('#cashbettable tbody').html(cashRows);
                var pointsRows = '';
                data.points.forEach(function(bet) {
                    pointsRows += `<tr><td>${bet.formatted_userid}</td><td>${bet.amnt} points</td><td>${bet.button}</td></tr>`;
                });
                $('#pointbettable tbody').html(pointsRows);

                var creditsRows = '';
                data.credits.forEach(function(bet) {
                    creditsRows += `<tr><td>${bet.formatted_userid}</td><td>${bet.amnt} credits</td><td>${bet.button}</td></tr>`;
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
            url: '/ajax_50.php',
            type: 'POST',
            
            data: {action: 'pointbet', amount: amount},
            dataType: "json",
            success: function(response) {
                console.log(response);
                $(".col-12.alert.alert-info").html(response.text).show();
                $('.money').html(response.stats.money)
                $(".points").html(response.stats.points)
                document.getElementById('betAmount').value = '';
            }
            ,
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
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'cashbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".money").html(response.money);
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
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'pointbet', amount: amount},
            
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".points").html(response.stats.points);
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
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'creditbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".credits").html(response.credits);
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
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'takepointbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".points").html(response.points);
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
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'takecashbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".money").html(response.money);
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
    $(document).on('click', '.takeCreditButton', function(){
        var amount = $(this).val();
        var $button = $(this);
       
        $.ajax({
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'takecreditbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".credits").html(response.credits);
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
            url: '/ajax_50.php', 
            type: 'POST',
            dataType: 'json',
            data: {action: 'removecashbet', id: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response.text).show();
                $(".points").html(response.points);
                $(".credits").html(response.credits);
                $(".money").html(response.money);
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
$(document).ready(function() {
    let fiftyRefreshes = 0;
    function fetchLatestBets() {
        fiftyRefreshes = fiftyRefreshes + 1;
        if (fiftyRefreshes == 10) {
            confirm('Have you left the casino?');
        }
        $.ajax({
            url: '/ajax_50.php?action=fecthLatest',  
            type: 'GET',                 
            success: function(data) {
            
                $('.lastbets').html(data);
            },
            error: function() {
                console.log('Error fetching data.');
            }
        });
    }

    setInterval(fetchLatestBets, 3000);
});