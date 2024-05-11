<?php
include 'header.php';
?>

<div class="container mt-3">
    <div class="alert alert-info">Global Chat</div>

    <?php
    if ($user_class->fbitime > 0) {
        diefun("You can't communicate if you're in FBI Jail!");
    }

    // JavaScript for handling text formatting and emoji picker
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
        $avatar = (!empty($array['avatar'])) ? $array['avatar'] : "/images/no-avatar.png";
        $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
        ?>

        <div class="card mb-3" style="background-color: #8e8e8e21;">
            <div class="card-body">
                <div class="row g-0">
                    <div class="col-md-2 text-center">
                        <img src="<?= $avatar ?>" class="img-fluid rounded-circle" alt="Avatar">
                        <br>
                        <?= htmlspecialchars($array['name']) ?>
                    </div>
                    <div class="col-md-10">
                        <?= BBCodeParse(stripslashes($row['body'])) ?>
                        <br>
                        <small class="text-muted"><?= howlongago($row['timesent']) ?> ago</small>
                        <div>
                            <?php if (($user_class->admin || $user_class->gm || $user_class->cm) && (!$array['admin'] && !$array['gm'])): ?>
                                <a href="?gcban=<?= $row['playerid'] ?>&conf=<?= $_SESSION['security'] ?>" class="btn btn-warning btn-sm">Ban User</a>
                            <?php endif; ?>
                            <?php if ($user_class->admin || $user_class->gm || $user_class->cm): ?>
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
