<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "header.php";
?>
<!-- Chat Interface -->
<div class="fixed-bottom p-2" id="chat-container" style="max-width: 350px; right: 10px; bottom: 0; z-index: 1030; background-color: rgba(142, 142, 142, 0.13); color: #fff; border-top: 1px solid rgba(78, 77, 72, 0.8); cursor: pointer;">
    <div class="d-flex align-items-center justify-content-between" id="chat-header">
        <h6 class="mb-0 text-white">Global Chat</h6>
        <button class="btn btn-sm btn-secondary text-white" id="toggle-chat" style="background-color: rgba(78, 77, 72, 0.8); border-color: rgba(78, 77, 72, 0.8); font-size: 0.75rem;">-</button>
    </div>
    <div id="chatbox" class="border rounded mt-2" style="height: 200px; overflow-y: auto; background-color: #2e2e2a; display: none; color: #fff;"></div>
    <div class="input-group mt-2" id="chat-input-container" style="display: none;">
        <button class="btn btn-secondary" id="emoji-picker" style="background-color: rgba(78, 77, 72, 0.8); border-color: rgba(78, 77, 72, 0.8);">😀</button> <!-- Emoji Picker Button -->
        <input type="text" id="message" class="form-control" placeholder="Type a message..." style="background-color: #3a3935; color: #fff; border: 1px solid rgba(78, 77, 72, 0.8);">
        <button class="btn text-white" id="send" style="background-color: rgba(78, 77, 72, 0.8); border-color: rgba(78, 77, 72, 0.8);">Send</button>
    </div>
</div>

<!-- Emoji Picker Modal -->
<div id="emoji-modal" style="display: none; position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); width: 300px; max-height: 300px; overflow-y: auto; background-color: #2e2e2a; border: 1px solid #4e4d48; padding: 10px; z-index: 1050;">
    <button id="close-emoji-modal" style="float: right; background-color: #4e4d48; border: none; color: white;">Close</button>
    <div id="emoji-list"><?php emotes(); ?></div> <!-- Populate emojis using the emotes() function -->
</div>

<script>
$(document).ready(function () {
    // Toggle chat visibility when clicking the header
    $('#chat-header, #toggle-chat').click(function() {
        $('#chatbox, #chat-input-container').toggle();
        let isExpanded = $('#chatbox').is(':visible');
        $('#chat-container').css('background-color', isExpanded ? '#21201C' : 'rgba(142, 142, 142, 0.13)');
        $('#toggle-chat').text(isExpanded ? '-' : '+');
        if (isExpanded) {
            scrollToBottom();
        }
    });

    // Fetch messages periodically
    function fetchMessages() {
        const chatbox = $('#chatbox');
        const isScrolledToBottom = chatbox[0].scrollHeight - chatbox.scrollTop() <= chatbox.outerHeight() + 1;
        $.ajax({
            url: 'api/fetch_messages.php',
            method: 'GET',
            success: function (data) {
                try {
                    let messages = JSON.parse(data);
                    if (!Array.isArray(messages)) {
                        console.error('Unexpected data format:', messages);
                        $('#chatbox').html('<p>Error: Unexpected data format received.</p>');
                        return;
                    }
                    $('#chatbox').html('');
                    messages.reverse().forEach(function (message) {
                        $('#chatbox').append(`<p class="mb-1"><span style="font-size: 70%;" class="text-white">${message.formatted_name}:</span> ${message.body}</p>`);
                    });
                    if (isScrolledToBottom) {
                        scrollToBottom();
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error, data);
                    $('#chatbox').html('<p>Error parsing the server response.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#chatbox').html('<p>Failed to fetch messages from the server.</p>');
            }
        });
    }

    // Scroll to the bottom of the chatbox
    function scrollToBottom() {
        $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
    }

    // Show the emoji picker modal when the button is clicked
    $('#emoji-picker').on('click', function () {
        $('#emoji-modal').show();
    });

    // Close the emoji picker modal
    $('#close-emoji-modal').on('click', function () {
        $('#emoji-modal').hide();
    });

    // Function to add an emoji to the message input
    window.addsmiley = function(code) {
        let message = $('#message').val();
        $('#message').val(message + ' ' + code);
        $('#message').focus();
        $('#emoji-modal').hide();
    };

    // Send message on button click
    $('#send').on('click', function () {
        sendMessage();
    });

    // Send message on Enter key press
    $('#message').on('keypress', function (e) {
        if (e.which === 13) {
            sendMessage();
            e.preventDefault();
        }
    });

    // Function to send a message
    function sendMessage() {
        let message = $('#message').val().trim();
        if (message === '') return;
        $.ajax({
            url: 'api/send_message.php',
            method: 'POST',
            data: {
                playerid: 1, // Replace with the actual player ID or dynamically retrieve it
                body: message
            },
            success: function (response) {
                let result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#message').val('');
                    fetchMessages();
                } else {
                    alert(result.message);
                }
            }
        });
    }

    // Set an interval to fetch messages every 2 seconds
    setInterval(fetchMessages, 2000);

    // Initial fetch of messages
    fetchMessages();
});
</script>


