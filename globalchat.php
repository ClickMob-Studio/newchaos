<?php
include 'header.php';
?>

<style>
    .emoji-picker {
        --emoji-size: 36px;
        --emoji-gap: 2px;
        --emoji-radius: 10px;
        --emoji-bg: #262626;
        --emoji-bg-alt: #f6f7f9;
        --emoji-hover: #373737;
        --emoji-border: #393939;
        --emoji-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        display: block;
        margin: 6px 0;
    }

    .emoji-summary {
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: 6px 10px;
        background: var(--emoji-bg);
        border: 1px solid #393939;
        border-radius: 999px;
        box-shadow: var(--emoji-shadow);
        user-select: none;
    }

    .emoji-panel {
        margin-top: 8px;
        padding: 10px;
        background: var(--emoji-bg);
        border: 1px solid var(--emoji-border);
        border-radius: 12px;
        box-shadow: var(--emoji-shadow);
    }

    .emoji-toolbar {
        display: flex;
        margin-bottom: 8px;
    }

    .emoji-search {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid var(--emoji-border);
        border-radius: 8px;
        font: inherit;
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, 44px);
        gap: var(--emoji-gap);
    }

    .emoji-btn {
        display: grid;
        place-items: center;
        width: var(--emoji-size);
        height: var(--emoji-size);
        border: 1px solid var(--emoji-border);
        border-radius: var(--emoji-radius);
        background: #353535;
        padding: 0;
        cursor: pointer;
        transition: transform 80ms ease, background 80ms ease;
    }

    .emoji-btn:hover {
        background: var(--emoji-hover);
    }

    .emoji-btn:active {
        transform: scale(0.98);
    }

    .emoji-btn:focus {
        outline: 2px solid #7aa2ff;
        outline-offset: 2px;
    }

    .emoji-imgwrap {
        width: 70%;
        height: 70%;
        display: block;
    }

    .emoji-btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        image-rendering: -webkit-optimize-contrast;
    }

    .emoji-picker {
        background: transparent;
        border: none;
        width: 100%;
    }



    .chat-input {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        width: 100%;
        padding: 0.5rem;
        background: #111;
        border-top: 1px solid #333;
    }

    .chat-input textarea {
        flex: 1;
        min-height: 3rem;
        max-height: 12rem;
        resize: vertical;
        padding: 0.75rem 1rem;

        font-size: 16px;
        line-height: 1.5;
        font-family: system-ui, sans-serif;

        border-radius: 8px;
        border: 2px solid #ff6218;
        background: #000;
        color: #fff;
        outline: none;

        overflow: hidden;
    }

    .chat-input textarea:focus {
        border-color: #ff8445;
        box-shadow: 0 0 0 2px rgba(255, 98, 24, 0.4);
    }

    .chat-input button {
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #ff6218, #ff8445);
        color: #fff;
        transition: background 0.2s, transform 0.1s;
    }

    .chat-input button:hover {
        background: linear-gradient(135deg, #ff8445, #ffa063);
    }

    .chat-input button:active {
        transform: scale(0.97);
    }
</style>

<div class='box_top'>Global chat</div>
<div class='box_middle'>
    <div class='pad'>
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
const textarea = document.querySelector('#reply')

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

        if (isset($_GET['gcban']) && $_GET['conf'] == $_SESSION['security']) {
            if ($user_class->admin || $user_class->gm || $user_class->cm) {
                $db->query("SELECT id FROM grpgusers WHERE id = ? AND admin = 0 AND gm = 0");
                $db->execute(array(
                    $_GET['gcban']
                ));
                $id = $db->fetch_single();
                if (empty($id) || $id <= 0)
                    diefun("Invalid id number. Ban failed.");
                $db->query("INSERT INTO bans (id, bannedby, type, days) VALUES(?, ?, ?, ?)");
                $db->execute(array(
                    $id,
                    $user_class->id,
                    'gc',
                    60
                ));
                diefun(formatName($id) . " has been banned for 60 minutes. If the user needs a more severe punishment, use a mail ban.");
            }
        }
        if (isset($_GET['delgc'])) {
            if ($user_class->admin || $user_class->gm || $user_class->cm) {
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
        if ($mins = $db->fetch_single())
            diefun("You are banned from global chat for $mins minutes.");
        $_SESSION['security'] = rand(1000000000, 2000000000);
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
        print <<<TEXT
<script>
var lastGmailID = $lastid;
syncGmail();
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to handle click events on like and dislike buttons
    function handleRating(event) {
        var button = event.target;
        var action = button.getAttribute('data-action');
        var postId = button.getAttribute('data-id');


    // Test log
    console.log("Rating button clicked: Action - " + action + ", Post ID - " + postId);



        // AJAX request to server
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajax_gc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.status === 200) {
                // Handle response here
                console.log(this.responseText);
            }
        };
        xhr.send('action=' + action + '&post_id=' + postId);
    }

    // Add event listeners to all like and dislike buttons
    var ratingButtons = document.querySelectorAll('.rating-btn');
    ratingButtons.forEach(function(button) {
        button.addEventListener('click', handleRating);
    });
});
</script>

TEXT;
        echo '
    <div id="gccontainer" style="margin:0; padding:10px;">
        ' . gcTalking() . '
   </div>';
        ?>
        <style>
            .flexele {
                flex: 1;
                text-align: center;
                padding: 10px;
            }

            .flexcont {
                text-align: center;
            }
        </style>

        <table style="margin-bottom: 10px;">
            <tr>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[b][/b]', 4);return false;">[b]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[u][/u]', 4);return false;">[u]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[i][/i]', 4);return false;">[i]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[s][/s]', 4);return false;">[s]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[url][/url]', 6);return false;">[url]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[img][/img]', 6);return false;">[img]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover" onclick="addBB('[tag][/tag]', 6);return false;">[tag]</span>
                </td>
                <td class="flexcont">
                    <span class="flexele forumhover"
                        onclick="addBB('[youtube][/youtube]', 10);return false;">[youtube]</span>
                </td>
            </tr>
        </table>

        <form name="message" id="chat-form" onsubmit="return sendGmail();">
            <div class="chat-input">
                <textarea id="reply" name="msgtext" placeholder="Type your message…" oninput="typing();"
                    autofocus></textarea>
                <button type="submit">Post</button>
            </div>
        </form>

        <?php
        // echo '<div id="emojis" style="display:', ($user_class->hideemojis) ? 'none' : 'block', ';">';
        // emotes();
        // echo '</div>';
        

        // NEW:
        echo '<details id="emoji-picker" class="emoji-picker"', ($user_class->hideemojis ? '' : ' open'), '>';
        echo '  <summary class="emoji-summary" aria-controls="emoji-panel" aria-expanded="false">😊 Emojis</summary>';
        echo '  <div id="emoji-panel" class="emoji-panel">';
        echo '    <div class="emoji-toolbar">';
        echo '      <input type="search" id="emoji-search" class="emoji-search" placeholder="Search…" aria-label="Search emojis">';
        echo '    </div>';
        echo '    <div class="emoji-grid" role="listbox" aria-label="Emoji list">';

        $innarr = [];
        foreach ($smiarr as $index => $img) {
            if (empty($img[1]))
                $img[1] = $img[2] = 19;
            if (isset($innarr[$img[0]]))
                continue;

            // basic escaping for safety
            $file = htmlspecialchars($img[0], ENT_QUOTES, 'UTF-8');
            $code = htmlspecialchars($index, ENT_QUOTES, 'UTF-8');
            $w = (int) $img[1];
            $h = (int) $img[2];

            // button + img ensures accessible, focusable controls
            echo '<button type="button" class="emoji-btn" data-emoji="', $code, '" title="', $code, '" aria-label="', $code, '" role="option">';
            echo '  <span class="emoji-imgwrap" aria-hidden="true">';
            echo '    <img loading="lazy" src="smileys/', $file, '" width="', $w, '" height="', $h, '" alt="">';
            echo '  </span>';
            echo '</button>';

            $innarr[$img[0]] = 1;
        }
        echo '    </div>';
        echo '  </div>';
        echo '</details>';
        // END NEW
        
        echo '</table>';

        echo '<style>';
        echo '#chatdiv img{';
        echo 'height: auto;';
        echo 'max-width: 500px;';
        echo '}';
        echo '
    span.likes, span.dislikes {
        margin-right: 10px;
        padding: 0px 8px;
    }
    i.fa-thumbs-down  {
        transform: rotateY(180deg);
    }
    ';
        echo '</style>';
        echo '<div id="chat_block">';

        $ignoredPlayerIds = array();
        $db->query("SELECT blocked FROM ignorelist WHERE blocker = $user_class->id");
        $db->execute();
        $ignored = $db->fetch_row();

        foreach ($ignored as $ignore) {
            $ignoredPlayerIds[] = $ignore['blocked'];
        }

        $db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = $user_class->id");
        $db->execute();
        if (count($ignoredPlayerIds)) {
            $db->query("SELECT * FROM globalchat WHERE playerid NOT IN (" . implode(',', $ignoredPlayerIds) . ") ORDER BY timesent DESC LIMIT 80");
        } else {
            $db->query("SELECT * FROM globalchat ORDER BY timesent DESC LIMIT 80");
        }
        //$db->query("SELECT * from globalchat ORDER BY timesent DESC LIMIT 80");
        $rows = $db->fetch_row();
        foreach ($rows as $row) {

            $db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = ? AND rating_action = 'like'");
            $db->execute(
                array(
                    $row['id']
                )
            );
            $likes = $db->fetch_single();

            $db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = ? AND rating_action = 'dislike'");
            $db->execute(
                array(
                    $row['id']
                )
            );
            $dislikes = $db->fetch_single();

            $db->query("SELECT * FROM chat_rating WHERE user_id = $user_class->id AND post_id = ? AND rating_action='like'");
            $db->execute(
                array(
                    $row['id']
                )
            );
            $userLiked = $db->fetch_single();

            $db->query("SELECT * FROM chat_rating WHERE user_id = $user_class->id AND post_id = ? AND rating_action='dislike'");
            $db->execute(
                array(
                    $row['id']
                )
            );
            $userDisliked = $db->fetch_single();

            $reply_class = new User($row['playerid']);
            $array = array(
                'name' => $reply_class->formattedname,
                'avatar' => $reply_class->avatar,
                'admin' => $reply_class->admin,
                'gm' => $reply_class->gm
            );

            $avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
            $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
            echo '<div class="floaty">';
            echo '</div>';
            echo '<table class="flexcont" style="width:100%;">';
            echo '<tr>';

            // Left cell for avatar and username
            echo '<td class="flexele" style="border-right:thin solid #333;text-align:center;width:200px;">';
            echo '<img src="' . $avatar . '" height="150" width="150" style="border:1px solid #666666;margin-bottom: 6px;" />';
            echo '<br />';
            if ($row['playerid'] > 0) {
                echo $array['name'];
            } else {
                echo '<span style="color:red">System</span>';
            }
            echo '</td>';

            // Right cell for the body content
            echo '<td class="flexele" style="padding:10px;">';
            echo BBCodeParse(stripslashes($row['body']));
            echo '<br><br>';
            echo howlongago($row['timesent']) . ' ago <br><br>';
            echo (($user_class->admin || $user_class->gm || $user_class->cm) && (!$array['admin'] && !$array['gm'])) ? '<a href="?gcban=' . $row['playerid'] . '&conf=' . $_SESSION['security'] . '">Ban User</a>' : '';
            echo ($user_class->admin || $user_class->gm || $user_class->cm) ? '<a href="?delgc=' . $row['id'] . '">Delete Post</a>' : '';
            echo '<br><div class="flexele forumhover" onClick="addsmiley(\'[quote=' . $row['playerid'] . ']' . str_replace(array("\n", "\r"), array('', '\n'), $quotetext) . '[/quote]\\n\\n\');">';
            echo 'Quote';
            echo '</div>';
            echo '</td>';

            echo '</tr>';
            echo '</table>';
        }


        print "</div>";



        include("footer.php");
        ?>

        <script>
            const reply = document.getElementById("reply");
            reply.addEventListener("input", () => {
                reply.style.height = "auto";        // reset
                reply.style.height = reply.scrollHeight + "px"; // grow to fit
            });

            (() => {
                // Resolve your target textarea: keep compatibility with your existing form
                function getMessageField() {
                    // Prefer #reply if it exists, fallback to document.message.msgtext
                    const reply = document.getElementById('reply');
                    if (reply) return reply;
                    if (document.message && document.message.msgtext) return document.message.msgtext;
                    return null;
                }

                // Insert text at caret position (works for inputs & textareas)
                function insertAtCaret(el, text) {
                    if (!el) return;
                    el.focus();
                    const start = el.selectionStart ?? el.value.length;
                    const end = el.selectionEnd ?? el.value.length;
                    const before = el.value.slice(0, start);
                    const after = el.value.slice(end);
                    el.value = before + text + after;
                    // place caret after inserted text
                    const pos = start + text.length;
                    el.setSelectionRange?.(pos, pos);
                    // optional: dispatch an input event if your app listens for it
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // Click handler for emojis (event delegation)
                document.addEventListener('click', (e) => {
                    const btn = e.target.closest('.emoji-btn[data-emoji]');
                    if (!btn) return;

                    const code = btn.getAttribute('data-emoji') || '';
                    const field = getMessageField();
                    if (!field) return;

                    // space-padding like your original (" ' code ' ")
                    const toInsert = (field.value && !/\s$/.test(field.value)) ? ' ' + code + ' ' : code + ' ';
                    insertAtCaret(field, toInsert);
                });

                // Search filter
                const search = document.getElementById('emoji-search');
                const grid = document.querySelector('.emoji-grid');
                if (search && grid) {
                    search.addEventListener('input', () => {
                        const q = search.value.trim().toLowerCase();
                        grid.querySelectorAll('.emoji-btn').forEach(btn => {
                            const code = (btn.getAttribute('data-emoji') || '').toLowerCase();
                            // simple contains match over the code (e.g., ":smile:")
                            btn.style.display = code.includes(q) ? '' : 'none';
                        });
                    });
                }

                // Keep summary aria-expanded in sync with <details> state
                const picker = document.getElementById('emoji-picker');
                const summary = picker?.querySelector('.emoji-summary');
                if (picker && summary) {
                    const sync = () => summary.setAttribute('aria-expanded', picker.open ? 'true' : 'false');
                    picker.addEventListener('toggle', sync);
                    sync();
                }
            })();
        </script>

        <style>
            #newtables th {

                padding: 10px;
                text-align: left;
                font-weight: normal;
                border-bottom: 1px solid #555;
            }

            #newtables td {
                padding: 10px;
                border-bottom: 1px solid #444;
            }
        </style>