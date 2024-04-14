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
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("betButton").addEventListener("click", function() {
        var amount = document.getElementById("betAmount").value;
        if (!amount || amount <= 0) {
            alert("Please enter a valid amount greater than zero.");
            return;
        }

        fetch('/ajax_50.php', {
            method: 'POST', // Recommended to use POST for actions that modify data
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // For sending data as form data
            },
            body: 'action=pointbet&amount=' + encodeURIComponent(amount)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json(); // Assuming the server response is JSON
        })
        .then(data => {
            console.log("Success:", data);
            document.querySelector(".col-12.alert.alert-info").style.display = 'block';
            document.querySelector(".col-12.alert.alert-info").innerHTML = data.message;
            document.querySelector(".points").textContent = `${data.newPoints} points`;
            document.getElementById('betAmount').value = ''; // Clear the input field
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert("An error occurred: " + error.message);
        });
    });
});

$(document).ready(function(){
    $("#betCashButton").click(function(){
        var amount = $("#betAmount").val(); 
        $.ajax({
            url: '/ajax_50.php', 
            type: 'GET',
            data: {action: 'cashbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                var newRow = `<tr><<td></td><td>$${amount}</td> <td></td></tr>`;
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
            url: '/ajax_50.php', 
            type: 'GET',
            data: {action: 'pointbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                var newRow = `<tr><<td></td><td>${amount} points</td> <td></td></tr>`;
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
            url: '/ajax_50.php', 
            type: 'GET',
            data: {action: 'creditbet', amount: amount},
            success: function(response) {
                $(".col-12.alert.alert-info").html(response).show();
                var newRow = `<tr><<td></td><td>${amount} credits</td> <td></td></tr>`;
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
            url: '/ajax_50.php', 
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
            url: '/ajax_50.php', 
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
    $(document).on('click', '.takeCreditButton', function(){
        var amount = $(this).val();
        var $button = $(this);
       
        $.ajax({
            url: '/ajax_50.php', 
            type: 'GET',
            data: {action: 'takecreditbet', id: amount},
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
            url: '/ajax_50.php', 
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
$(document).ready(function() {
    function fetchLatestBets() {
        $.ajax({
            url: '/ajax_50.php?action=fecthLatest',  
            type: 'GET',                 
            success: function(data) {
                console.log(data);
                $('.lastbets').html(data);
            },
            error: function() {
                console.log('Error fetching data.');
            }
        });
    }

    setInterval(fetchLatestBets, 1000);
});