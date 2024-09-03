<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "header.php";
?>
<!-- Chat Interface -->
<div id="chatbox" style="width: 100%; height: 300px; border: 1px solid #ccc; overflow-y: scroll; padding: 10px; margin-top: 20px;"></div>
<input type="text" id="message" placeholder="Type a message..." style="width: 80%; padding: 5px;">
<button id="send" style="width: 15%; padding: 5px;">Send</button>


<script>
$(document).ready(function () {
    // Fetch messages periodically
    function fetchMessages() {
        $.ajax({
            url: 'api/fetch_messages.php', // Ensure this file is in the correct path
            method: 'GET',
            success: function (data) {
                let messages = JSON.parse(data);
                $('#chatbox').html('');
                messages.reverse().forEach(function (message) {
                    $('#chatbox').append('<p><strong>User ' + message.playerid + ':</strong> ' + message.body + '</p>');
                });
                $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
            }
        });
    }

    // Function to send a message
    function sendMessage() {
        let message = $('#message').val().trim();
        if (message === '') return;

        $.ajax({
            url: 'api/send_message.php', // Ensure this file is in the correct path
            method: 'POST',
            data: {
                playerid: 1, // Replace with the actual player ID or dynamically retrieve it
                body: message
            },
            success: function (response) {
                let result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#message').val(''); // Clear the input field
                    fetchMessages(); // Refresh messages
                } else {
                    alert(result.message); // Show error message
                }
            }
        });
    }

    // Set an interval to fetch messages every 2 seconds
    setInterval(fetchMessages, 2000);

    // Send message on button click
    $('#send').on('click', function () {
        sendMessage();
    });

    // Send message on Enter key press
    $('#message').on('keypress', function (e) {
        if (e.which === 13) {
            sendMessage();
            e.preventDefault(); // Prevent the default form submission
        }
    });

    // Initial fetch of messages
    fetchMessages();
});
</script>
