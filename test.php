<?php

require "header.php";
?>
<!-- Chat Interface -->
<div class="fixed-bottom bg-light border-top shadow-sm p-2" id="chat-container" style="background-color:#21201c;max-width: 350px; right: 10px; bottom: 0; z-index: 1030;">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Global Chat</h6>
        <button class="btn btn-sm btn-secondary" id="toggle-chat" style="font-size: 0.75rem;">-</button>
    </div>
    <div id="chatbox" class="border rounded bg-white mt-2" style="height: 200px; overflow-y: auto; display: none;"></div>
    <div class="input-group mt-2" id="chat-input-container" style="display: none;">
        <input type="text" id="message" class="form-control" placeholder="Type a message...">
        <button class="btn btn-primary" id="send">Send</button>
    </div>
</div>

<script>
$(document).ready(function () {
    // Toggle chat visibility
    $('#toggle-chat').click(function() {
        $('#chatbox, #chat-input-container').toggle();
        $(this).text($(this).text() === '-' ? '+' : '-');
    });

    // Fetch messages periodically
    function fetchMessages() {
        $.ajax({
            url: 'api/fetch_messages.php', // Ensure this file is in the correct path
            method: 'GET',
            success: function (data) {
                let messages = JSON.parse(data);
                $('#chatbox').html('');
                messages.reverse().forEach(function (message) {
                    $('#chatbox').append('<p class="mb-1"><strong>User ' + message.playerid + ':</strong> ' + message.body + '</p>');
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
                playerid: <?php echo $user_class->id; ?>, // Replace with the actual player ID or dynamically retrieve it
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
