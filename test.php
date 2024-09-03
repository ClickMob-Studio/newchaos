<?php
require "header.php";
?>

<div class="p-2" id="chat-container" style="position: fixed; max-width: 350px; right: 10px; bottom: 10px; z-index: 1030; background-color: rgba(142, 142, 142, 0.13); color: #fff; border-top: 1px solid rgba(78, 77, 72, 0.8); cursor: pointer;">
    <div class="d-flex align-items-center justify-content-between" id="chat-header">
        <h6 class="mb-0 text-white">Global Chat</h6>
        <span class="btn btn-sm btn-secondary text-white" style="background-color: rgba(78, 77, 72, 0.8); border-color: rgba(78, 77, 72, 0.8); font-size: 0.75rem;">-</span>
    </div>
    <div id="chatbox" class="border rounded mt-2" style="height: 400px; overflow-y: auto; background-color: #2e2e2a; display: none; color: #fff;"></div>
    <div class="input-group mt-2" id="chat-input-container" style="display: none;">
        <input type="text" id="message" class="form-control" placeholder="Type a message..." style="background-color: #3a3935; color: #fff; border: 1px solid rgba(78, 77, 72, 0.8);">
        <button class="btn text-white" id="emoji-btn" style="background-color: rgba(78, 77, 72, 0.8); border-color: rgba(78, 77, 72, 0.8);">😊</button>
        <button class="btn text-white" id="send" style="background-color: rgba(78, 77, 72, 0.8); border-color: rgba(78, 77, 72, 0.8);">Send</button>
    </div>
    <div id="emoji-picker" style="display: none; position: absolute; bottom: 50px; right: 20px; background: #2e2e2a; border: 1px solid rgba(78, 77, 72, 0.8); padding: 5px; border-radius: 5px;">
        <?php emotes(); ?>
    </div>
</div>

<script>
$(document).ready(function () {
    // Check if user is an admin
    let isAdmin = <?php echo $user_class->admin > 0 ? 'true' : 'false'; ?>;

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
                        // Append messages and check for images
                        let deleteButton = isAdmin ? `<button class="btn btn-sm btn-danger delete-message" data-id="${message.id}" style="margin-left: 10px;">Delete</button>` : '';
                        $('#chatbox').append(`<p class="mb-1"><span style="font-size: 70%;" class="text-white">${message.formatted_name}:</span> ${message.body} ${deleteButton}</p>`);
                    });

                    // Check for images and limit their size
                    limitImageSize('#chatbox img', 100, 100); // Example limit: 100x100 pixels

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

    // Function to limit the size of images
    function limitImageSize(selector, maxWidth, maxHeight) {
        $(selector).each(function () {
            const img = $(this);
            const imgWidth = img.width();
            const imgHeight = img.height();

            // Calculate the scaling factor to maintain the aspect ratio
            const widthScale = maxWidth / imgWidth;
            const heightScale = maxHeight / imgHeight;
            const scale = Math.min(widthScale, heightScale, 1); // Never upscale

            // Apply new dimensions
            img.css({
                width: imgWidth * scale,
                height: imgHeight * scale,
                maxWidth: maxWidth + 'px',
                maxHeight: maxHeight + 'px',
            });
        });
    }

    // Scroll to the bottom of the chatbox
    function scrollToBottom() {
        $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
    }

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

    // Toggle emoji picker visibility
    $('#emoji-btn').on('click', function (e) {
        e.stopPropagation();
        $('#emoji-picker').toggle();
    });

    // Hide emoji picker when clicking outside
    $(document).on('click', function () {
        $('#emoji-picker').hide();
    });

    // Insert emoji into the input field when clicked
    $(document).on('click', '#emoji-picker img', function () {
        let emojiCode = $(this).attr('onclick').match(/'([^']+)'/)[1].trim(); // Extracts the emoji code
        let currentText = $('#message').val();
        $('#message').val(currentText + emojiCode);
        $('#message').focus();
    });

    /// Delete message on button click
$(document).on('click', '.delete-message', function () {
    let messageId = $(this).data('id');
    if (confirm('Are you sure you want to delete this message?')) {
        $.ajax({
            url: 'api/delete_message.php',
            method: 'POST',
            data: {
                message_id: messageId
            },
            success: function (response) {
                let result = JSON.parse(response);
                if (result.status === 'success') {
                    fetchMessages();
                } else {
                    alert(result.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Failed to delete the message.');
            }
        });
    }
});

});
</script>
