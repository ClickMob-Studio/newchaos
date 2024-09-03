<?php
require "header.php";
?>
<style>
    /* Chatbox animations */
#chatbox {
    transition: height 0.3s ease, opacity 0.3s ease;
}

/* Smooth transitions for the chat container */
#chat-container.open {
    bottom: 0px; /* Fully open position */
    opacity: 1;
    transition: bottom 0.5s ease-in-out, opacity 0.5s ease-in-out;
}

#chat-container.closed {
    bottom: -400px; /* Hidden position */
    opacity: 0;
}

/* Emoji Picker animations */
#emoji-picker {
    display: none; /* Hide by default */
    transition: max-height 0.3s ease, opacity 0.3s ease;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
}

#emoji-picker.open {
    max-height: 200px; /* Maximum height when open */
    opacity: 1;
}

</style>
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

<script>$(document).ready(function () {
    // Check if user is an admin
    let isAdmin = <?php echo $user_class->admin > 0 ? 'true' : 'false'; ?>;

    // Toggle chat visibility when clicking the header
    $('#chat-header, #toggle-chat').click(function () {
        const chatContainer = $('#chat-container');
        const chatbox = $('#chatbox');
        const inputContainer = $('#chat-input-container');

        // Toggle classes for animation
        chatContainer.toggleClass('open closed');

        // Toggle visibility of chatbox and input with slide animation
        chatbox.slideToggle(300);
        inputContainer.slideToggle(300, function () {
            if (chatbox.is(':visible')) {
                scrollToBottom();
            }
        });
    });

    function fetchMessages() {
        const chatbox = $('#chatbox');
        const previousScrollTop = chatbox.scrollTop();
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

                    chatbox.off('scroll');
                    $('#chatbox').html('');
                    messages.reverse().forEach(function (message) {
                        let deleteButton = isAdmin ? `<button class="btn btn-sm delete-message" data-id="${message.id}" style="background: none; border: none; color: #ff4d4d; cursor: pointer;"><i class="fa fa-trash" aria-hidden="true"></i></button>` : '';
                        $('#chatbox').append(`<p class="mb-1 d-flex justify-content-between align-items-center" style="padding: 0 5px;"><span class="text-white" style="font-size: 70%;">${message.formatted_name}:</span> <span>${message.body}</span> ${deleteButton}</p>`);
                    });

                    limitImageSize('#chatbox img', 100, 100);

                    if (isScrolledToBottom) {
                        scrollToBottom();
                    } else {
                        chatbox.scrollTop(previousScrollTop);
                    }

                    chatbox.on('scroll', function () {});
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

    function limitImageSize(selector, maxWidth, maxHeight) {
        $(selector).each(function () {
            const img = $(this);
            const imgWidth = img.width();
            const imgHeight = img.height();
            const widthScale = maxWidth / imgWidth;
            const heightScale = maxHeight / imgHeight;
            const scale = Math.min(widthScale, heightScale, 1);
            img.css({
                width: imgWidth * scale,
                height: imgHeight * scale,
                maxWidth: maxWidth + 'px',
                maxHeight: maxHeight + 'px',
            });
        });
    }

    function scrollToBottom() {
        $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
    }

    $('#send').on('click', function () {
        sendMessage();
    });

    $('#message').on('keypress', function (e) {
        if (e.which === 13) {
            sendMessage();
            e.preventDefault();
        }
    });

    function sendMessage() {
        let message = $('#message').val().trim();
        if (message === '') return;
        $.ajax({
            url: 'api/send_message.php',
            method: 'POST',
            data: {
                playerid: 1,
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

    setInterval(fetchMessages, 2000);
    fetchMessages();

    $('#emoji-btn').on('click', function (e) {
        e.stopPropagation();
        $('#emoji-picker').toggleClass('open');
    });

    $(document).on('click', function () {
        $('#emoji-picker').removeClass('open');
    });

    $(document).on('click', '#emoji-picker img', function () {
        let emojiCode = $(this).attr('onclick').match(/'([^']+)'/)[1].trim();
        let currentText = $('#message').val();
        $('#message').val(currentText + emojiCode);
        $('#message').focus();
    });

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
