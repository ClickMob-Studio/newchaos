<?php
include 'header.php';
?>

<style>
    .avatar {
        width: 100px; /* fixed width */
        height: 100px; /* fixed height */
        object-fit: cover; /* maintain aspect ratio */
        border-radius: 5px; /* optional: rounded corners */
    }
    .text-center {
        text-align: center;
    }
    .card-body {
        text-align: center;
    }
    .username {
        display: block; /* ensures it appears on a new line under the avatar */
        font-size: 1.3rem; /* sets the text size to 1.3rem as requested */
        color: #333; /* dark gray for better readability */
        overflow: hidden; /* prevents overflow */
        white-space: nowrap; /* prevents wrapping */
        text-overflow: ellipsis; /* adds ellipsis if text is too long */
        max-width: 100px; /* matches avatar width */
        margin: auto; /* centers the username horizontally */
    }
    .text-muted{
        color:#fff;
    }
</style>

<div class="container mt-3">
    <h1>Global Chat</h1>
   
    <div id="gccontainer" style="margin:0; padding:10px;">
        <?php gcTalking(); ?>
   </div>

    <?php
    if ($user_class->fbitime > 0) {
        diefun("You can't communicate if you're in FBI Jail!");
    }

    echo "
    <script>
    function addBB(text) {
        var textarea = document.getElementById('reply');
        textarea.value += text;
    }
    </script>
    <script type='module'>
    import { EmojiButton } from 'https://unpkg.com/@joeattardi/emoji-button@4.3.0/dist/index.js';

    const picker = new EmojiButton({
        theme: 'dark',
        emojiSize: '20px',
        emojisPerRow: 18,
        rows: 4,
        showVariants: false,
        position: 'bottom'
    });
    const trigger = document.querySelector('#trigger');
    const textarea = document.querySelector('#reply');

    function addasmiley(text, textarea) {
        textarea.focus();
        textarea.setRangeText(
          text,
          textarea.selectionStart,
          textarea.selectionEnd,
          'end'
        );
    }

    picker.on('emoji', selection => {
        addasmiley(selection.emoji, textarea);
    });

    trigger.addEventListener('click', () => picker.togglePicker(trigger));
    </script>";

    if ($user_class->level < 2 && $user_class->prestige == 0)
        diefun("You must be level 2 to use this feature.");

    $db->query("SELECT * FROM globalchat ORDER BY timesent DESC LIMIT 80");
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        $chat_user = new User($row['playerid']);
        $avatar = (!empty($chat_user->avatar)) ? $chat_user->avatar : "/images/no-avatar.png";
        $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
        ?>

        <div class="card mb-3" style="background-color: #8e8e8e21;">
            <div class="card-body">
                <div class="row g-0">
                    <div class="col-md-2 text-center">
                        <img src="<?= $avatar ?>" class="avatar" alt="Avatar">
                        <span class="username"><?= htmlspecialchars($chat_user->formattedname); ?></span>
                    </div>
                    <div class="col-md-10 text-center">
                        <?= BBCodeParse(stripslashes($row['body'])) ?>
                        <br>
                        <small class="text-muted"><?= howlongago($row['timesent']) ?> ago</small>
                        <div>
                            <?php if (($user_class->admin || $user_class->gm || $user_class->cm) && (!$chat_user->admin && !$chat_user->gm)): ?>
                                <a href="?gcban=<?= $chat_user->id ?>&conf=<?= $_SESSION['security'] ?>" class="btn btn-warning btn-sm">Ban User</a>
                            <?php endif; ?>
                            <?php if ($user_class->admin or $user_class->gm or $user_class->cm): ?>
                                <a href="?delgc=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete Post</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <hr>
    <form name="message" class="form-inline">
        <div class="form-group mx-sm-3 mb-2">
            <textarea name="msgtext" id="reply" class="form-control" style="width: 90%;" oninput="typing();"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Post</button>
    </form>

    <?php
    include("footer.php");
    ?>
</div>
