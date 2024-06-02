<?php
include 'header.php';
include 'includes/pagination.class.php';

function updateUserForumNotifications($db, $user_class) {
    $db->query("UPDATE grpgusers SET forumnoti = 0 WHERE id = ?");
    $db->execute([$user_class->id]);
    if ($user_class->news > 0) {
        $db->query("UPDATE grpgusers SET news = 0, forumnoti = 0 WHERE id = ?");
        $db->execute([$user_class->id]);
    }
}

function addNewTopic($db, $user_class, $i) {
    if (!empty($_POST['msgtext']) && !empty($_POST['topic'])) {
        $db->query("UPDATE grpgusers SET forumnoti = forumnoti + 1");
        $db->execute();
        $m->set('lastpost.' . $user_class->id, 1, 10);
        $res = $db->query("INSERT INTO ftopics (sectionid, playerid, lastreply, timesent, subject, body, lastposter, lastupdated) VALUES (?, ?, unix_timestamp(), unix_timestamp(), ?, ?, ?, unix_timestamp())");
        $db->execute([$i, $user_class->id, $_POST['topic'], $_POST['msgtext'], $user_class->id]);

        $topic_id = $db->insert_id();

        if (!empty($_POST['poll_title'])) {
            $str = file_get_contents('php://input');
            parse_str($str, $output);

            $choices = serialize(array_filter($output['poll_choice']));
            $title = $_POST['poll_title'];
            $finish = strtotime($_POST['poll_finish']);
            $votes = serialize(array_fill(0, count($output['poll_choice']), 0));

            $db->query("INSERT INTO polls (title, options, votes, finish, topic_id) VALUES (?, ?, ?, ?, ?)");
            $db->execute([$title, $choices, $votes, $finish, $topic_id]);
        }

        $db->query("UPDATE grpgusers SET posts = posts + 1 WHERE id = ?");
        $db->execute([$user_class->id]);
        if ($i == 1) {
            $db->query("UPDATE grpgusers SET news = news + 1");
            $db->execute();
        }
        echo Message("Your new topic has been posted");
    }
}

function displayForums($db, $user_class, $fnames) {
    $forums = [];
    for ($i = 1; $i <= 11; $i++) {
        $db->query("SELECT * FROM ftopics WHERE sectionid = ? ORDER BY timesent DESC LIMIT 1");
        $db->execute([$i]);
        $cnt = $db->fetch_row(true);
        $db->query("SELECT count(*) FROM ftopics WHERE sectionid = ?");
        $db->execute([$i]);
        $forums[$i]['topics'] = $db->fetch_single();
        $db->query("SELECT timesent FROM freplies WHERE sectionid = ? ORDER BY timesent DESC");
        $db->execute([$i]);
        $forums[$i]['lastreply'] = $db->fetch_single();
        $db->query("SELECT COUNT(postid) FROM freplies WHERE sectionid = ?");
        $db->execute([$i]);
        $forums[$i]['replies'] = $db->fetch_single();
        $forums[$i]['lastreply'] = ($forums[$i]['lastreply'] > $cnt['timesent']) ? $forums[$i]['lastreply'] : $cnt['timesent'];
        $forums[$i]['lastpost'] = $forums[$i]['lastreply'] ? date('d F Y, g:ia', $forums[$i]['lastreply']) : 'None';
        $forums[$i]['name'] = $fnames[$i - 1][0];
        $forums[$i]['sub'] = $fnames[$i - 1][1];
    }
    return $forums;
}

function displayRecentTopics($db) {
    $db->query("SELECT DISTINCT(topicid) as one FROM freplies ORDER BY timesent DESC LIMIT 5");
    $db->execute();
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        $id[] = $row['one'];
    }
    $db->query("SELECT * FROM ftopics ORDER BY lastupdated DESC LIMIT 5");
    $db->execute();
    $rows = $db->fetch_row();
    echo "
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th colspan='3'>Recent Topics</th>
                </tr>
                <tr>
                    <th>Topic</th>
                    <th>Poster</th>
                    <th>How long ago?</th>
                </tr>
            </thead>
            <tbody>";
    foreach ($rows as $row) {
        echo "
                <tr>
                    <td><a href='forum.php?topic={$row['forumid']}'>{$row['subject']}</a></td>
                    <td>" . formatName($row['lastposter']) . "</td>
                    <td>" . howlongago($row['lastupdated']) . "</td>
                </tr>";
    }
    echo "
            </tbody>
        </table>
    </div>";
}

function displayForumSections($forums, $user_class) {
    $maxSections = ($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? 11 : 10;
    echo "
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th width='50%'>Forum</th>
                    <th width='15%'>Topics</th>
                    <th width='12%'>Replies</th>
                    <th width='20%'>Last Post</th>
                </tr>
            </thead>
            <tbody>";
    for ($i = 1; $i <= $maxSections; $i++) {
        $db->query("SELECT avatar FROM grpgusers WHERE id = ?");
        $db->execute([$forums[$i]['topicinfo']['playerid']]);
        $avatar = $db->fetch_single();
        $forums[$i]['topicinfo']['body'] = substr($forums[$i]['topicinfo']['body'], 0, 50);
        echo "
                <tr>
                    <td><a href='forum.php?id=$i'>{$forums[$i]['name']}</a><br /><span class='small'>{$forums[$i]['sub']}</span></td>
                    <td class='text-center'>{$forums[$i]['topics']}</td>
                    <td class='text-center'>{$forums[$i]['replies']}</td>
                    <td class='text-center'>{$forums[$i]['lastpost']}</td>
                </tr>";
    }
    echo "
            </tbody>
        </table>
    </div>";
}

function displayTopForumUsers($db) {
    echo "
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th width='20%'>Rank</th>
                    <th width='60%'>Mobster</th>
                    <th width='20%'>Posts</th>
                </tr>
            </thead>
            <tbody>";
    $db->query("SELECT id, posts FROM grpgusers WHERE `ban/freeze` = 0 ORDER BY posts DESC LIMIT 5");
    $db->execute();
    if (!$db->num_rows())
        echo "<tr><td colspan='3' class='text-center'>No-one has posted</td></tr>";
    else {
        $rank = 0;
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            ++$rank;
            $user = formatName($row['id']);
            echo "
                <tr>
                    <td>{$rank}</td>
                    <td>{$user}</td>
                    <td>{$row['posts']}</td>
                </tr>";
        }
    }
    echo "
            </tbody>
        </table>
    </div>";
}

function displaySingleTopic($db, $user_class, $topic) {
    $db->query("UPDATE ftopics SET views = views + 1 WHERE forumid = ?");
    $db->execute([$_GET['topic']]);
    $db->query("SELECT * FROM ftopics WHERE forumid = ?");
    $db->execute([$_GET['topic']]);
    if (!$db->num_rows())
        diefun("That topic doesn't exist");
    $topic = $db->fetch_row(true);
    if ($topic['sectionid'] == 11 && !($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo))
        diefun("You don't have access");

    // Handle posting a response
    if (isset($_POST['submit'])) {
        handlePostResponse($db, $user_class, $topic);
    }

    // Display the topic and responses
    displayTopicDetails($db, $user_class, $topic);
}

function handlePostResponse($db, $user_class, $topic) {
    if (!empty($m->get('lastpost.' . $user_class->id)))
        diefun("No spamming the forums.");
    $_POST['msgtext'] = isset($_POST['msgtext']) && is_string($_POST['msgtext']) ? trim($_POST['msgtext']) : null;
    if (empty($_POST['msgtext']))
        diefun("You didn't enter a valid response");
    $db->query("UPDATE grpgusers SET forumnoti = forumnoti + 1");
    $db->execute();
    $m->set('lastpost.' . $user_class->id, 1, 10);
    preg_match_all("/\[tag\]([0-9]*)\[\/tag\]/", $_POST['msgtext'], $tags);
    $count = count($tags[1]);
    if ($count >= 1) {
        for ($x = 0; $x < $count; $x++) {
            Send_event($tags[1][$x], formatName($user_class->id) . " mentioned you in <a href='forum.php?topic={$_GET['topic']}' style='color:orange;'>{$topic['subject']}</a>", $c);
        }
    }
    $db->query("INSERT INTO freplies (sectionid, topicid, playerid, timesent, body) VALUES ({$topic['sectionid']}, {$_GET['topic']}, $user_class->id, unix_timestamp(), '{$_POST['msgtext']}')");
    $db->execute([$topic['sectionid'], $_GET['topic'], $user_class->id, $_POST['msgtext']]);
    $db->query("UPDATE ftopics SET lastposter = ?, lastupdated = unix_timestamp() WHERE forumid = ?");
    $db->execute([$user_class->id, $_GET['topic']]);
    $db->query("UPDATE grpgusers SET posts = posts + 1 WHERE id = ?");
    $db->execute([$user_class->id]);
    echo Message("Your response has been posted");
    $db->query("SELECT * FROM forumfollows WHERE ftid = ? AND userid <> ?");
    $db->execute([$_GET['topic'], $user_class->id]);
    $rows = $db->fetch_row();
    foreach ($rows as $a) {
        Send_event($a['userid'], "<a href='forum.php?topic={$_GET['topic']}' style='color:orange;'>{$topic['subject']}</a> has a new reply to it!", $c);
    }
}

function displayTopicDetails($db, $user_class, $topic) {
    $db->query("SELECT COUNT(postid) FROM freplies WHERE topicid = ?");
    $db->execute([$_GET['topic']]);
    $pages = new pagination();
    $pages->items_total = $db->fetch_single();
    $pages->items_per_page = 25;
    $pages->max_pages = 10;
    $db->query("SELECT * FROM freplies WHERE topicid = ? ORDER BY timesent DESC " . $pages->limit());
    $db->execute([$_GET['topic']]);
    $rows = $db->fetch_row();
    $op = new User($topic['playerid']);
    $edit = ($topic['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? "<div class='edit'><textarea class='form-control' rows='10' id='edittext'>" . str_replace("<br>", "\n", $topic['body']) . "</textarea><button class='btn btn-primary' onclick='editTopic({$_GET['topic']});'>Edit Post</button></div>" : "";
    $edi = ($topic['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? " onclick='gotoedit();'" : "";

    // Admin/Moderator actions
    handleAdminActions($db, $topic);

    echo '
    <script type="text/javascript">
        function rate(arrow, fpid, rate) {
            $(this).load("ajax_forumrate" + arrow + ".php?fpid=" + fpid);
            $("#" + arrow + "rate" + fpid).html(rate);
        }
        function ratepost(arrow, postid, rate) {
            $(this).load("ajax_forumpostrate" + arrow + ".php?postid=" + postid);
            $("#" + arrow + "ratepost" + postid).html(rate);
        }
        function gotoedit() {
            if ($(".noedit").length) {
                $(".edit").css("display", "block");
                $(".noedit").css("display", "none");
            }
        }
        function gotoeditreply(fpid) {
            if ($(".noedit" + fpid).length) {
                $(".edit" + fpid).css("display", "block");
                $(".noedit" + fpid).css("display", "none");
            }
        }
        function editTopic(fpid) {
            if ($("#edittext").val() != "") {
                $.post("ajax_forumedit.php", {"topic": fpid, "edittext": $("#edittext").val()}, function (d) {
                    $(".noedit").html(d);
                    $(".noedit").css("display", "block");
                    $(".edit").css("display", "none");
                });
            }
            return false;
        }
        function editReply(fpid) {
            if ($("#edittext" + fpid).val() != "") {
                $.post("ajax_forumedit.php", {"reply": fpid, "edittext": $("#edittext" + fpid).val()}, function (d) {
                    $(".noedit" + fpid).html(d);
                    $(".noedit" + fpid).css("display", "block");
                    $(".edit" + fpid).css("display", "none");
                });
            }
            return false;
        }
        function follow() {
            $(this).load("ajax_' . $js[0] . '.php?ftid=' . $_GET['topic'] . '");
            $("#follow").html("' . $js[1] . '");
        }
    </script>
    <style>
        #rate:hover, #follow:hover {
            background: rgba(0,0,0,.25);
        }
        .edit {
            display: none;
            text-align: center;
        }
    </style>';

    displayTopicHeader($topic, $edi);

    foreach ($rows as $row) {
        displayPost($db, $user_class, $row);
    }

    if ($rtn = $pages->displayPages()) {
        echo '<span class="floaty">' . $rtn . '</span>';
    }

    if (!count($rows)) {
        echo '<div class="floaty">There are no responses yet.</div>';
    }

    if (!in_array($topic['status'], [2, 3])) {
        displayPostForm($user_class);
    }
}

function handleAdminActions($db, $topic) {
    if ($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) {
        if (isset($_POST['lock'])) {
            if ($topic['status'] == 3) {
                $db->query("UPDATE ftopics SET status = 1 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been unlocked.");
            }
            if ($topic['status'] == 2) {
                $db->query("UPDATE ftopics SET status = 0 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been unlocked.");
            }
            if ($topic['status'] == 1) {
                $db->query("UPDATE ftopics SET status = 3 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been locked.");
            }
            if ($topic['status'] == 0) {
                $db->query("UPDATE ftopics SET status = 2 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been locked.");
            }
        } elseif (isset($_POST['sticky'])) {
            if ($topic['status'] == 3) {
                $db->query("UPDATE ftopics SET status = 2 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been unstickied.");
            }
            if ($topic['status'] == 2) {
                $db->query("UPDATE ftopics SET status = 3 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been stickied.");
            }
            if ($topic['status'] == 1) {
                $db->query("UPDATE ftopics SET status = 0 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been unstickied.");
            }
            if ($topic['status'] == 0) {
                $db->query("UPDATE ftopics SET status = 1 WHERE forumid = ?");
                $db->execute([$_GET['topic']]);
                echo Message("Topic has been stickied.");
            }
        } elseif (isset($_POST['delete']) && !isset($_POST['confirm'])) {
            $prompt = "Are you sure you wish to delete this topic?<br />
            <form method='post' class='text-center'>
            <input type='hidden' name='confirm' value='yes' />
            <input type='submit' class='btn btn-danger' name='delete' value='Yes, Delete Topic' />
            </form>";
            echo $prompt;
        } elseif (isset($_POST['delete']) && isset($_POST['confirm'])) {
            $db->query("DELETE FROM ftopics WHERE forumid = ?");
            $db->execute([$_GET['topic']]);
            echo Message("Topic has been deleted.");
        } else {
            echo "
            <form method='post' class='text-center'>
                <input type='submit' class='btn btn-warning' name='lock' value='Lock Topic' />
                <input type='submit' class='btn btn-info' name='sticky' value='Sticky Topic' />
                <input type='submit' class='btn btn-danger' name='delete' value='Delete Topic' />
            </form>";
        }
    }
}

function displayTopicHeader($topic, $edi) {
    echo '<br /><br />
    <span class="floaty fw-bold">
        <a href="forum.php">Forum</a> &rarr; 
        <a href="forum.php?id=' . $topic['sectionid'] . '">' . $names[$topic['sectionid']] . '</a> &rarr; 
        ' . $topic['subject'] . '
    </span>
    <br /><br />
    <div class="floaty">
        <div class="d-flex justify-content-between">
            <div>' . date('d M Y - g:i a', $topic['timesent']) . '</div>
            <div title="' . implode('&#13;', $ups) . '" rel="tipsy" class="forumhover" onclick="rate(\'up\',' . $topic['forumid'] . ', ' . ($topic['rateup'] + 1) . ');">
                <img src="images/up.jpg" /> Rate Up (<span id="uprate' . $topic['forumid'] . '">' . $topic['rateup'] . '</span>)
            </div>
            <div title="' . implode('&#13;', $downs) . '" rel="tipsy" class="forumhover" onclick="rate(\'down\',' . $topic['forumid'] . ', ' . ($topic['ratedown'] + 1) . ');">
                <img src="images/down.jpg" /> Rate Down (<span id="downrate' . $topic['forumid'] . '">' . $topic['ratedown'] . '</span>)
            </div>
            <div class="forumhover" onclick="follow();" id="follow">
                ' . $js[2] . ' Topic
            </div>';
    if ($edi) {
        echo '<div class="forumhover" onclick="gotoedit();">Edit Post</div>';
    }
    echo '</div>
    <hr />
    <div class="d-flex">
        <div class="me-3 text-center">
            <br />
            ' . $op->formattedname . '<br />
            <br />
            <img src="' . $op->avatar . '" class="img-thumbnail" width="150" height="150" />
            <br />
            <br />
        </div>
        <div class="flex-grow-1">
            <br />
            <div class="noedit">' . BBCodeParse($topic['body']) . '</div>' . $edit;
    displayPoll($db, $topic);
    echo '</div>
    </div>
    </div>';
}

function displayPoll($db, $topic) {
    $db->query("SELECT * FROM polls WHERE topic_id = '" . $topic['forumid'] . "'");
    $db->execute();
    $poll = $db->fetch_row(true);
    if ($poll) {
        $title = $poll['title'];
        $choices = unserialize($poll['options']);
        $votes = unserialize($poll['votes']);
        $end = $poll['finish'];
        $pollId = $poll['id'];

        $db->query("SELECT * FROM voters WHERE `user_id` = ? AND `poll_id` = ?");
        $db->execute([$user_class->id, $pollId]);
        $voted = $db->fetch_row(true);
        echo '<div class="poll-box">
        <h3>' . $title . '</h3>';
        if (!$voted && (time() < $end)) {
            echo '
            <form class="poll" method="POST">
                <input type="hidden" name="pollid" value="' . $pollId . '">
                <div class="form-check">';
                foreach ($choices as $key => $value) {
                    echo '<div class="form-check">
                        <input class="form-check-input" type="radio" value="' . $key . '" name="radioq" id="' . $key . '">
                        <label class="form-check-label" for="' . $key . '">' . $value . '</label>
                    </div>';
                }

            echo '
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>';
        } else {
            $db->query("SELECT *, u.username FROM voters INNER JOIN grpgusers u ON `user_id` = u.id WHERE poll_id = ?");
            $db->execute([$poll['id']]);
            $voters = $db->fetch_row();

            echo '<div class="results mt-3">
            <h4>Results</h4>';
            for ($i = 0; $i < count($choices); $i++) {
                $votePercent = round(($votes[$i] / $poll['voters']) * 100);
                $votePercent = !empty($votePercent) ? $votePercent . '%' : '0%';
                echo '<h5>' . $choices[$i] . '</h5>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: ' . $votePercent . ';" aria-valuenow="' . $votePercent . '" aria-valuemin="0" aria-valuemax="100">' . $votePercent . '</div>
                </div>';

                if ($topic['sectionid'] != 1) {
                    echo '<div class="mt-2">';
                    foreach ($voters as $voter) {
                        if ($voter['choice'] == $i) {
                            echo $voter['username'] . ' ';
                        }
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        echo '</div>';
    }
}

function displayPost($db, $user_class, $row) {
    $db->query("SELECT username, rate FROM forumreplyrates f JOIN grpgusers g ON f.userid = g.id WHERE postid = ?");
    $db->execute([$row['postid']]);
    $subrows = $db->fetch_row();
    $ups = $downs = [];
    foreach ($subrows as $subrow) {
        switch ($subrow['rate']) {
            case 'up':
                $ups[] = $subrow['username'];
                break;
            case 'down':
                $downs[] = $subrow['username'];
                break;
        }
    }
    $co = ($co != 2) ? 2 : 1;
    $poster = new User($row['playerid']);
    $edit = ($row['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? "<div class='edit{$row['postid']}' style='display:none;text-align:center;'><textarea class='form-control' rows='10' id='edittext{$row['postid']}'>{$row['body']}</textarea><button class='btn btn-primary' onclick='editReply({$row['postid']});'>Edit Post</button></div>" : "";
    $edi = ($row['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? 1 : 0;
    echo '<div class="floaty">
        <div class="d-flex justify-content-between">
            <div>' . date('d M Y - g:i a', $row['timesent']) . '</div>
            <div title="' . implode('&#13;', $ups) . '" rel="tipsy" class="forumhover" onclick="ratepost(\'up\',' . $row['postid'] . ', ' . ($row['rateup'] + 1) . ');">
                <img src="images/up.jpg" /> Rate Up (<span id="upratepost' . $row['postid'] . '">' . $row['rateup'] . '</span>)
            </div>
            <div title="' . implode('&#13;', $downs) . '" class="forumhover" onclick="ratepost(\'down\',' . $row['postid'] . ', ' . ($row['ratedown'] + 1) . ');">
                <img src="images/down.jpg" /> Rate Down (<span id="downratepost' . $row['postid'] . '">' . $row['ratedown'] . '</span>)
            </div>';
    if ($edi) {
        echo '<div class="forumhover" onclick="gotoeditreply(' . $row['postid'] . ');">Edit Post</div>';
    }
    echo '</div>
    <hr />
    <div class="d-flex">
        <div class="me-3 text-center">
            <br />
            ' . $poster->formattedname . '<br />
            <br />
            <img src="' . $poster->avatar . '" class="img-thumbnail" width="125" height="125" />
        </div>
        <div class="flex-grow-1">
            <div class="noedit' . $row['postid'] . '">' . BBCodeParse($row['body']) . '</div>' . $edit . '
        </div>
    </div>
</div>';
}

function displayPostForm($user_class) {
    echo '<div class="floaty mb-3">
    <form name="message" method="post">
        <div class="d-flex justify-content-between mb-2">
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[b][/b]\', 4); return false;">[b]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[u][/u]\', 4); return false;">[u]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[i][/i]\', 4); return false;">[i]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[s][/s]\', 4); return false;">[s]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[url][/url]\', 6); return false;">[url]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[img][/img]\', 6); return false;">[img]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[tag][/tag]\', 6); return false;">[tag]</button>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="insertAtCursor(\'[youtube][/youtube]\', 10); return false;">[youtube]</button>
            <button type="button" class="btn btn-outline-secondary" id="semojis" onclick="return showemojis();" style="display:' . ($user_class->hideemojis ? 'block' : 'none') . ';">Show Emojis</button>
            <button type="button" class="btn btn-outline-secondary" id="hemojis" onclick="return hideemojis();" style="display:' . ($user_class->hideemojis ? 'none' : 'block') . ';">Hide Emojis</button>
        </div>
        <hr />
        <textarea name="msgtext" id="reply" class="form-control mb-2" rows="5"></textarea>
        <input type="submit" class="btn btn-primary" name="submit" value="Respond" />
    </form>
    <div id="emojis" style="display:' . ($user_class->hideemojis ? 'none' : 'block') . ';">
        ' . emotes() . '
    </div>
</div>';
}

$db->query("UPDATE grpgusers SET threadtime = unix_timestamp() WHERE id = ?");
$db->execute([$user_class->id]);
$db->query("SELECT days FROM bans WHERE id = ? AND type = 'forum'");
$db->execute([$user_class->id]);
if ($db->num_rows()) {
    diefun("You've been banned from the forum. Time remaining: " . howlongtil($db->fetch_single() * 86400));
}

$fnames = [
    ['News', 'Stay up to date and comment on game news.'],
    ['General Chat', 'Talk about anything general to Chaos City here.'],
    ['Gang Chat', 'Talk about and advertise your gang here.'],
    ['Marketplace', 'Post buying and selling needs here.'],
    ['Competitions', 'Hold any competitions you want here.'],
    ['Off Topic', 'Talk about anything not related to Chaos City here.'],
    ['Suggestions', 'Post anything new or improved you would like to see on Chaos City.'],
    ['Help Forum', 'Ask for help here from other members or staff.'],
    ['Bugs, Errors etc...', 'Report bugs and errors here.'],
    ['Graphics', 'Forum section for all your graphical needs.'],
    ['Staff Forum', 'Anything you need to talk about in private to another staff member.']
];

if (empty($_GET['id']) && empty($_GET['topic'])) {
    $forums = displayForums($db, $user_class, $fnames);
    displayRecentTopics($db);
    genHead("Forum");
    displayForumSections($forums, $user_class);
    displayTopForumUsers($db);
} elseif (!empty($_GET['id'])) {
    // Code for handling individual forum sections...
    if (isset($_POST['topic'])) {
        addNewTopic($db, $user_class, $_GET['id']);
    }
    // Pagination and forum topics display...
    include 'footer.php';
} elseif (!empty($_GET['topic'])) {
    displaySingleTopic($db, $user_class, $topic);
    include 'footer.php';
}
?>
