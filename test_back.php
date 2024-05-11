<?php
include 'header.php';
function gcTalk($which = 0, $gang = 0) {
    global $db;
    if ($which == 0) {
        $db->query("SELECT * FROM gcusers");
        $db->execute();
    } else {
        $db->query("SELECT * FROM gmusers WHERE gang = ?");
        $db->execute(array($gang));
    }
    $rows = $db->fetch_row();
    $ret = '<div class="flexcont" style="margin: 2px; display: flex; flex-wrap: nowrap; justify-content: space-around; align-items: center;">';
    $count = count($rows);
    $leftover = 4 - ($count % 4);
    if ($count < 4)
        $leftover = 0;
    foreach ($rows as $row) {
        if ($row['userid'] == 150) continue;  // Skip specific user by ID
        $ret .= '<div class="flexele" style="margin: 2px; flex: 1 0 22%; box-sizing: border-box;">';
        $ret .= '<div class="floaty" style="height: 20px; line-height: 20px; background-color: ';
        $ret .= ($row['typing']) ? 'rgba(0, 255, 0, 0.125);' : 'transparent;';
        $ret .= ' cursor: pointer;" onclick="addsmiley(\' [tag]' . $row['userid'] . '[/tag] \');">';
        $ret .= formatName($row['userid']);
        $ret .= '</div></div>';
    }
    // Fill the remaining space with empty flex elements, if necessary
    for ($i = 0; $i < $leftover; $i++) {
        $ret .= '<div class="flexele" style="margin: 2px; flex: 1 0 22%;"></div>';
    }
    $ret .= '</div>';
    return $ret;
}

?>

<style>
    .quote {
        margin-left: 20px;
        border-left: 3px solid #ccc;
        padding-left: 10px;
        color: #666;
        font-style: italic;
    }
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
        color:#fff !important;
    }
</style>

<div class="container mt-3">
    <h1>Global Chat</h1>
   
    <div id="gccontainer" class="dcPanel dcAvatarPanel" style="margin: 0; margin-bottom:10px;padding: 10px; width: 100%; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <?php echo gcTalk(); ?>
</div>
<!-- BBCode Toolbar using Bootstrap's Button Groups -->
<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
        <div class="btn-group mr-2" role="group" aria-label="First group">
            <button type="button" class="btn btn-secondary" onclick="addBB('[b][/b]', 4);">[b]</button>
            <button type="button" class="btn btn-secondary" onclick="addBB('[u][/u]', 4);">[u]</button>
            <button type="button" class="btn btn-secondary" onclick="addBB('[i][/i]', 4);">[i]</button>
            <button type="button" class="btn btn-secondary" onclick="addBB('[s][/s]', 4);">[s]</button>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Second group">
            <button type="button" class="btn btn-secondary" onclick="addBB('[url][/url]', 6);">[url]</button>
            <button type="button" class="btn btn-secondary" onclick="addBB('[img][/img]', 6);">[img]</button>
            <button type="button" class="btn btn-secondary" onclick="addBB('[tag][/tag]', 6);">[tag]</button>
            <button type="button" class="btn btn-secondary" onclick="addBB('[youtube][/youtube]', 10);">[youtube]</button>
        </div>
        <button type="button" class="btn btn-secondary" id="showEmojis" style="display: <?php echo ($user_class->hideemojis) ? 'block' : 'none'; ?>;">Show Emojis</button>
        <button type="button" class="btn btn-secondary" id="hideEmojis" style="display: <?php echo ($user_class->hideemojis) ? 'none' : 'block'; ?>;">Hide Emojis</button>
    </div>
    <!-- Message Form -->
    <hr style="border:0; border-top:thin solid #333;">
    <table>
        <form name="message">
            <tr>
                <td>
                    <textarea autofocus name="msgtext" id="reply" oninput="typing();" style="width:90%; height:125px;"></textarea><br />
                </td>
                <td>
                    <input type="submit" name="submit" onclick="return sendGmail();" value="Post" />
                </td>
            </tr>
        </form>
    </table>




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
        function formatQuotes($text) {
            $pattern = '/\[quote\](.*?)\[\/quote\]/s';
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[0] as $match) {
                    $quotedText = preg_replace($pattern, '$1', $match);
                    $formattedText = "<div class='quote'>" . formatQuotes($quotedText) . "</div>"; // Recursive for nested quotes
                    $text = str_replace($match, $formattedText, $text);
                }
            }
            return $text;
        }
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
                        <?= formatQuotes(BBCodeParse(stripslashes($row['body']))) ?>
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

    

    <?php
    include("footer.php");
    ?>
</div>
