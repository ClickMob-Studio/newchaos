<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            Global Chat
        </div>
        <div class="card-body">
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
                )
            }

            picker.on('emoji', selection => {
                addasmiley(selection.emoji, textarea);
                textarea.focus()
            });

            trigger.addEventListener('click', () => picker.togglePicker(trigger));
            </script>";

            if(isset($_GET['gcban']) && $_GET['conf'] == $_SESSION['security']){
                if($user_class->admin || $user_class->gm || $user_class->cm){
                    $db->query("SELECT id FROM grpgusers WHERE id = ? AND admin = 0 AND gm = 0");
                    $db->execute(array(
                        $_GET['gcban']
                    ));
                    $id = $db->fetch_single();
                    if(empty($id) || $id <= 0)
                        diefun("Invalid id number. Ban failed.");
                    $db->query("INSERT INTO bans VALUES('', ?, ?, ?, ?)");
                    $db->execute(array(
                        $id,
                        $user_class->id,
                        'gc',
                        60
                    ));
                    diefun(formatName($id). " has been banned for 60 minutes. If the user needs a more severe punishment, use a mail ban.");
                }
            }
            if(isset($_GET['delgc'])){
                if($user_class->admin || $user_class->gm || $user_class->cm){
                    $db->query("DELETE FROM globalchat WHERE id = ?");
                    $db->execute(array(
                        $_GET['delgc']
                    ));
                }
            }

            $db->query("SELECT days FROM bans WHERE id = ? AND type = 'gc'");
            $db->execute(array(
                $user_class->id
            ));
            if($mins = $db->fetch_single())
                diefun("You are banned from global chat for $mins minutes.");
            $_SESSION['security'] = rand(1000000000,2000000000);
            $db->query("SELECT id FROM globalchat ORDER BY id DESC");
            $db->execute();
            $lastid = $db->fetch_row(true);
            $db->query("SELECT * FROM bans WHERE `type` = 'mail' AND id = $user_class->id");
            $db->execute();
            if ($db->num_rows()) {
                $r = $db->fetch_row(true);
                diefun("&nbsp;You have been mail banned for " . prettynum($r['days']) . " days.");
            }
            if ($user_class->level < 2 && $user_class->prestige == 0)
                diefun("You must be level 2 to use this feature.");
            $lastid = (!empty($lastid['id'])) ? $lastid['id'] : 0;

            echo '<script>
                    var lastGmailID = ' . $lastid . ';
                    syncGmail();
                  </script>';

            echo "<script>
                    document.addEventListener('DOMContentLoaded', function () {
                        function handleRating(event) {
                            var button = event.target;
                            var action = button.getAttribute('data-action');
                            var postId = button.getAttribute('data-id');

                            console.log('Rating button clicked: Action - ' + action + ', Post ID - ' + postId);

                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'ajax_gc_new.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function () {
                                if (this.status === 200) {
                                    console.log(this.responseText);
                                }
                            };
                            xhr.send('action=' + action + '&post_id=' + postId);
                        }

                        var ratingButtons = document.querySelectorAll('.rating-btn');
                        ratingButtons.forEach(function(button) {
                            button.addEventListener('click', handleRating);
                        });
                    });
                  </script>";

            echo '<div id="gccontainer">' . gcTalking() . '</div>';
            ?>
            <div class="d-flex justify-content-center my-3">
                <button id="trigger" class="btn btn-secondary me-2">Emoji</button>
                <button id="semojis" class="btn btn-secondary <?php echo ($user_class->hideemojis) ? 'd-block' : 'd-none'; ?>">Show Emojis</button>
                <button id="hemojis" class="btn btn-secondary <?php echo ($user_class->hideemojis) ? 'd-none' : 'd-block'; ?>">Hide Emojis</button>
            </div>
            <form name="message">
                <div class="mb-3">
                    <textarea class="form-control" name="msgtext" id="reply" rows="5" oninput="typing();" autofocus></textarea>
                </div>
                <button type="submit" name="submit" class="btn btn-primary" onclick="return sendGmail();">Post</button>
            </form>
            <hr />
            <div id="emojis" class="<?php echo ($user_class->hideemojis) ? 'd-none' : 'd-block'; ?>">
                <?php emotes(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = $user_class->id");
$db->execute();
$db->query("SELECT * from globalchat ORDER BY timesent DESC LIMIT 80");
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = ? AND rating_action = 'like'");
    $db->execute(array($row['id']));
    $likes = $db->fetch_single();

    $db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = ? AND rating_action = 'dislike'");
    $db->execute(array($row['id']));
    $dislikes = $db->fetch_single();

    $db->query("SELECT * FROM chat_rating WHERE user_id = $user_class->id AND post_id = ? AND rating_action='like'");
    $db->execute(array($row['id']));
    $userLiked = $db->fetch_single();

    $db->query("SELECT * FROM chat_rating WHERE user_id = $user_class->id AND post_id = ? AND rating_action='dislike'");
    $db->execute(array($row['id']));
    $userDisliked = $db->fetch_single();

    if (!$m->get('tavcache.'.$row['playerid'])) {
        $reply_class = new User($row['playerid']);
        $array = array(
            'name' => $reply_class->formattedname,
            'avatar' => $reply_class->avatar,
            'admin' => $reply_class->admin,
            'gm' => $reply_class->gm
        );
        $m->set('tavcache.'.$row['playerid'], $array, 60);
    } else {
        $array = $m->get('tavcache.'.$row['playerid']);
    }
    $avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
    $quotetext = str_replace(array('\'','"'),array('\\\'','&quot;'),$row['body']);

    echo '<div class="card my-3">';
    echo '<div class="card-body d-flex">';
    echo '<div class="flex-shrink-0 me-3 text-center">';
    echo '<img src="' . $avatar . '" class="img-fluid rounded-circle" style="max-width: 150px;" />';
    echo '<p class="mt-2 mb-0">' . (($row['playerid'] > 0) ? $array['name'] : '<span class="text-danger">System</span>') . '</p>';
    echo '</div>';
    echo '<div>';
    echo '<p>' . BBCodeParse(stripslashes($row['body'])) . '</p>';
    echo '<small>' . howlongago($row['timesent']) . ' ago</small>';
    echo '</div>';
    echo '</div>';
    echo '<div class="card-footer">';
    echo ($user_class->admin || $user_class->gm || $user_class->cm) && (!$array['admin'] && !$array['gm']) ? '<a href="?gcban=' . $row['playerid'] . '&conf=' . $_SESSION['security'] . '" class="btn btn-danger btn-sm">Ban User</a> ' : '';
    echo ($user_class->admin || $user_class->gm || $user_class->cm) ? '<a href="?delgc=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete Post</a>' : '';
    echo '</div>';
    echo '</div>';
}
include("footer.php");
?>
