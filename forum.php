<?php
include 'header.php';
include 'includes/pagination.class.php';
?>
<style>
    .table>thead {
        vertical-align: bottom;
        color: white;
    }

    .table>:not(caption)>*>* {
        padding: .5rem .5rem;
        color: white;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }
</style>
<?php
$names = array(
    1 => 'News',
    2 => 'General Chat',
    3 => 'Gang Chat',
    4 => 'Marketplace',
    5 => 'Competitions',
    6 => 'Off-Topic',
    7 => 'Suggestions',
    8 => 'Help',
    9 => 'Bugs/Errors',
    10 => 'Graphics',
    11 => 'Staff',
    12 => 'Missing Items'
);
?>
<div class='container mt-4'>
    <div class='box_top'>
        Forums
        <?php
        // Check if a forum ID is set in the URL (through GET)
        if (isset($_GET['id']) && isset($names[$_GET['id']])) {
            // Display the name of the selected forum
            echo " - " . $names[$_GET['id']];
        }
        ?>
    </div>
    <div class='box_middle'>
        <div class='pad'>
            <?php
            $db->query("UPDATE grpgusers SET forumnoti = 0 WHERE id = ?");
            $db->execute(array($user_class->id));
            if ($user_class->news > 0) {
                $db->query("UPDATE grpgusers SET news = 0, forumnoti = 0 WHERE id = ?");
                $db->execute(array($user_class->id));
            }
            addBrowser($user_class->id, $user_class->username);

            $_GET['topic'] = isset($_GET['topic']) && ctype_digit($_GET['topic']) ? $_GET['topic'] : null;
            $_GET['id'] = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
            if (!empty($_GET['id'])) {
                if (!in_array($_GET['id'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)))
                    diefun("That board doesn't exist");
                $db->query("SELECT MAX(ff_id) FROM forum_forums");
                $db->execute();
                $max = $db->fetch_single();
                for ($i = 1; $i <= $max; ++$i) {
                    if (($i == 11 && !($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) || $i == 12))
                        continue;
                    if ($i == 1 && !($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) && isset($_POST['newtopic']))
                        unset($_POST['newtopic']);
                    if ($_GET['id'] == $i) {
                        if (!isset($names[$i])) {
                            $db->query("SELECT ff_name FROM forum_forums WHERE ff_id = ?");
                            $db->execute(array($i));
                            $names[$i] = $db->fetch_single();
                            $db->query("SELECT ff_auth FROM forum_forums WHERE ff_id = ?");
                            $db->execute(array($i));
                            $auth = $db->fetch_single();
                            if ($auth == 'gang') {
                                if (empty($user_class->gang))
                                    diefun("That board doesn't exist");
                                $db->query("SELECT forumid FROM gangs WHERE id = ?");
                                $db->execute(array($user_class->gang));
                                $fid = $db->fetch_single();
                                if ($i != $fid)
                                    diefun("That board doesn't exist");
                            }
                        }
                        if (isset($_POST['topic'])) {
                            $_POST['topic'] = isset($_POST['topic']) && is_string($_POST['topic']) ? trim($_POST['topic']) : null;
                            $_POST['msgtext'] = isset($_POST['msgtext']) && is_string($_POST['msgtext']) ? trim($_POST['msgtext']) : null;
                            if (!empty($_POST['msgtext']) && !empty($_POST['topic'])) {
                                $db->query("UPDATE grpgusers SET forumnoti = forumnoti + 1");
                                $db->execute();
                                $res = $db->query("INSERT INTO ftopics (sectionid, playerid, lastreply, timesent, subject, body, lastposter, lastupdated) VALUES (?, ?, unix_timestamp(), unix_timestamp(), ?, ?, ?, unix_timestamp())");
                                $db->execute(array(
                                    $i,
                                    $user_class->id,
                                    $_POST['topic'],
                                    $_POST['msgtext'],
                                    $user_class->id
                                ));
                                $topic_id = $db->insert_id();
                                if (!empty($_POST['poll_title'])) {
                                    $str = file_get_contents('php://input');
                                    parse_str($str, $output);
                                    $choices = serialize($output['poll_choice']);
                                    foreach ($choices as $key => $value)
                                        if (empty($value))
                                            unset($choices[$key]);
                                    $title = $_POST['poll_title'];
                                    $finish = strtotime($_POST['poll_finish']);
                                    $votes = array_fill(0, count($output['poll_choice']), 0);
                                    $votes = serialize($votes);
                                    $db->query("INSERT INTO polls (title, options, votes, finish, topic_id) VALUES (?, ?, ?, ?, ?)");
                                    $db->execute(array($title, $choices, $votes, $finish, $topic_id));
                                }
                                $db->query("UPDATE grpgusers SET posts = posts + 1 WHERE id = ?");
                                $db->execute(array($user_class->id));
                                if ($i == 1) {
                                    $db->query("UPDATE grpgusers SET news = news + 1");
                                    $db->execute();
                                }
                                echo Message("Your new topic has been posted");
                            }
                        }
                        $pages = new pagination();
                        $pages->items_per_page = 30;
                        $pages->max_pages = 10;
                        $db->query("SELECT count(*) FROM ftopics WHERE sectionid = ?");
                        $db->execute(array($i));
                        $pages->items_total = $db->fetch_single();
                        ?>
                        <!-- Link to show/hide the "New Topic" form -->
                        <a href="javascript:void(0);" onclick="toggleNewTopicForm();" class="btn btn-primary mb-3">Create New
                            Topic</a>

                        <!-- The "New Topic" form, initially hidden -->
                        <div id="newTopicForm" class="card mt-4" style="display:none;">
                            <div class="card-body">
                                <h5 class="card-title text-danger text-center">New Topic</h5>
                                <hr>
                                <div class="d-flex justify-content-around mb-2">
                                    <span class="badge bg-secondary forumhover" onclick="insertAtCursor('[b][/b]', 4);">[b]</span>
                                    <span class="badge bg-secondary forumhover" onclick="insertAtCursor('[u][/u]', 4);">[u]</span>
                                    <span class="badge bg-secondary forumhover" onclick="insertAtCursor('[i][/i]', 4);">[i]</span>
                                    <span class="badge bg-secondary forumhover" onclick="insertAtCursor('[s][/s]', 4);">[s]</span>
                                    <span class="badge bg-secondary forumhover"
                                        onclick="insertAtCursor('[url][/url]', 6);">[url]</span>
                                    <span class="badge bg-secondary forumhover"
                                        onclick="insertAtCursor('[img][/img]', 6);">[img]</span>
                                    <span class="badge bg-secondary forumhover"
                                        onclick="insertAtCursor('[tag][/tag]', 6);">[tag]</span>
                                    <span class="badge bg-secondary forumhover"
                                        onclick="insertAtCursor('[youtube][/youtube]', 10);">[youtube]</span>
                                    <span class="badge bg-secondary forumhover" id="semojis" onclick="return showemojis();"
                                        style="display:<?php echo ($user_class->hideemojis) ? 'block' : 'none'; ?>;">Show
                                        Emojis</span>
                                    <span class="badge bg-secondary forumhover" id="hemojis" onclick="return hideemojis();"
                                        style="display:<?php echo ($user_class->hideemojis) ? 'none' : 'block'; ?>;">Hide
                                        Emojis</span>
                                </div>
                                <hr>
                                <form name="message" method="post">
                                    <div class="mb-3">
                                        <label for="topic" class="form-label">Topic:</label>
                                        <input type="text" class="form-control" name="topic" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="msgtext" class="form-label">Message:</label>
                                        <textarea class="form-control" name="msgtext" id="reply" rows="5" required></textarea>
                                    </div>
                                    <div id="poll" class="mb-3" style="display: none;">
                                        <label for="poll_title" class="form-label">Poll Title:</label>
                                        <input type="text" class="form-control" id="poll_title" name="poll_title">
                                        <label for="poll_choice[]" class="form-label">Choices:</label>
                                        <div class="choices">
                                            <input class="form-control mb-2" type="text" name="poll_choice[]">
                                            <input class="form-control mb-2" type="text" name="poll_choice[]">
                                        </div>
                                        <button class="btn btn-secondary" id="addchoice">Add Another</button>
                                        <label for="poll_finish" class="form-label mt-3">Finish:</label>
                                        <input type="date" class="form-control" id="poll_finish" name="poll_finish" required
                                            value="<?php echo date('Y-m-d', strtotime("+2 day")); ?>">
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary" id="createTopic" name="submit">Create
                                            Topic</button>
                                    </div>
                                </form>
                                <div id="emojis" style="display:<?php echo ($user_class->hideemojis) ? 'none' : 'block'; ?>;">
                                    <?php emotes(); ?>
                                </div>
                            </div>
                        </div>

                        <!-- JavaScript to toggle the visibility of the "New Topic" form -->
                        <script type="text/javascript">
                            function toggleNewTopicForm() {
                                var form = document.getElementById('newTopicForm');
                                if (form.style.display === "none" || form.style.display === "") {
                                    form.style.display = "block"; // Show the form
                                } else {
                                    form.style.display = "none"; // Hide the form
                                }
                            }
                        </script>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Topic</th>
                                        <th>Starter</th>
                                        <th>Rating</th>
                                        <th>Replies</th>
                                        <th>Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $db->query("SELECT * 
                                    FROM ftopics 
                                    WHERE sectionid = ? 
                                    ORDER BY FIELD(status, 3, 1, 0, 2), 
                                             CASE 
                                                 WHEN (SELECT MAX(timesent) 
                                                       FROM freplies 
                                                       WHERE forumid = topicid) > timesent 
                                                 THEN (SELECT MAX(timesent) 
                                                       FROM freplies 
                                                       WHERE forumid = topicid) 
                                                 ELSE timesent 
                                             END DESC, 
                                             lastreply DESC" . $pages->limit());
                                    $db->execute(array($i));

                                    if (!$db->num_rows()) {
                                        echo "<tr><td colspan='5' class='text-center'>There are no topics</td></tr>";
                                    } else {
                                        $rows = $db->fetch_row();
                                        foreach ($rows as $row) {
                                            // Calculate rating
                                            $rating = $row['rateup'] - $row['ratedown'];
                                            $rating = ($rating == 0) ? '-' : $rating;
                                            if ($rating != 0) {
                                                $rating = ($rating > 0) ? '<span class="text-success">+' . $rating . '</span>' : '<span class="text-danger">' . $rating . '</span>';
                                            }

                                            // Fetch the number of replies using LEFT JOIN to count replies including 0 replies
                                            $db->query("SELECT COUNT(postid) 
                                            FROM freplies 
                                            WHERE sectionid = ? 
                                            AND topicid = ?");
                                            $db->execute(array($i, $row['forumid']));
                                            $replies = $db->fetch_single();

                                            // If no replies, set it to 0
                                            $replies = ($replies === null) ? 0 : $replies;

                                            ?>
                                            <tr>
                                                <td><a href='forum.php?topic=<?php echo $row['forumid']; ?>' class="text-decoration-none">
                                                        <?php echo $row['subject'] . ' '; ?>
                                                        <?php
                                                        echo $row['status'] == 1 ? "[Sticky]" : '';
                                                        echo $row['status'] == 2 ? "[Lock]" : '';
                                                        echo $row['status'] == 3 ? "[Sticky, Lock]" : '';
                                                        ?>
                                                    </a></td>
                                                <td><?php echo formatName($row['playerid']); ?></td>
                                                <td><?php echo $rating; ?></td>
                                                <td><?php echo $replies; ?></td>
                                                <td><?php echo $row['views']; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class='d-flex justify-content-center'><?php echo $pages->displayPages(); ?></div>

                        <?php
                        include 'footer.php';
                        exit;
                    }
                }
            } else if (empty($_GET['id']) && empty($_GET['topic'])) {
                $db->query("UPDATE grpgusers SET threadtime = unix_timestamp() WHERE id = ?");
                $db->execute(array($user_class->id));

                $db->query("SELECT days FROM bans WHERE id = ? AND type = 'forum'");
                $db->execute(array($user_class->id));
                if ($db->num_rows())
                    diefun("You've been banned from the forum. Time remaining: " . howlongtil($db->fetch_single() * 86400));

                $forums = array();
                $fnames = array(
                    array('News', 'Stay up to date and comment on game news.'),
                    array('General Chat', 'Talk about anything general to Chaos City here.'),
                    array('Gang Chat', 'Talk about and advertise your gang here.'),
                    array('Marketplace', 'Post buying and selling needs here.'),
                    array('Competitions', 'Hold any competitions you want here.'),
                    array('Off Topic', 'Talk about anything not related to Chaos City here.'),
                    array('Suggestions', 'Post anything new or improved you would like to see on Chaos City.'),
                    array('Help Forum', 'Ask for help here from other members or staff.'),
                    array('Bugs, Errors etc...', 'Report bugs and errors here.'),
                    array('Graphics', 'Forum section for all your graphical needs.'),
                    array('Staff Forum', 'Anything you need to talk about in private to another staff member.')
                );

                for ($i = 1; $i <= 11; $i++) {
                    $db->query("SELECT * FROM ftopics WHERE sectionid = ? ORDER BY timesent DESC LIMIT 1");
                    $db->execute(array($i));
                    $cnt = $db->fetch_row(true);
                    $db->query("SELECT count(*) FROM ftopics WHERE sectionid = ?");
                    $db->execute(array($i));
                    $forums[$i]['topics'] = $db->fetch_single();
                    $db->query("SELECT timesent FROM freplies WHERE sectionid = ? ORDER BY timesent DESC");
                    $db->execute(array($i));
                    $forums[$i]['lastreply'] = $db->fetch_single();
                    $db->query("SELECT COUNT(postid) FROM freplies WHERE sectionid = ?");
                    $db->execute(array($i));
                    $forums[$i]['replies'] = $db->fetch_single();
                    if (isset($cnt['timesent'])) {
                        $forums[$i]['lastreply'] = ($forums[$i]['lastreply'] > $cnt['timesent']) ? $forums[$i]['lastreply'] : $cnt['timesent'];
                    } else {
                        $forums[$i]['lastreply'] = 0;
                    }

                    $forums[$i]['lastpost'] = $forums[$i]['lastreply'] ? date('d F Y, g:ia', $forums[$i]['lastreply']) : 'None';
                    $forums[$i]['name'] = $fnames[$i - 1][0];
                    $forums[$i]['sub'] = $fnames[$i - 1][1];
                }
                $db->query("SELECT topicid FROM freplies GROUP BY topicid ORDER BY MAX(timesent) DESC LIMIT 5");
                $db->execute();
                $rows = $db->fetch_row();
                foreach ($rows as $row) {
                    $id[] = $row['topicid'];
                }
                $db->query("SELECT * FROM ftopics ORDER BY lastupdated DESC LIMIT 5");
                $db->execute();
                $rows = $db->fetch_row();
                ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
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
                            <tbody>
                                <?php
                                foreach ($rows as $row) {
                                    ?>
                                    <tr>
                                        <td><a
                                                href='forum.php?topic=<?php echo $row['forumid']; ?>'><?php echo $row['subject']; ?></a>
                                        </td>
                                        <td><?php echo formatName($row['lastposter']); ?></td>
                                        <td><?php echo howlongago($row['lastupdated']); ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    genHead("Forum");
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Forum</th>
                                    <th>Topics</th>
                                    <th>Replies</th>
                                    <th>Last Post</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo)
                                    $j = 11;
                                else
                                    $j = 10;
                                for ($i = 1; $i <= $j; $i++) {
                                    if (isset($forums[$i]['topicinfo']['playerid'])) {
                                        $db->query("SELECT avatar FROM grpgusers WHERE id = ?");
                                        $db->execute(array($forums[$i]['topicinfo']['playerid']));
                                        $avatar = $db->fetch_single();
                                        $forums[$i]['topicinfo']['body'] = substr($forums[$i]['topicinfo']['body'], 0, 50);
                                    }
                                    ?>
                                    <tr>
                                        <td><a href='forum.php?id=<?php echo $i; ?>'
                                                class="text-decoration-none"><?php echo $forums[$i]['name']; ?></a><br><span
                                                class='text-muted'><?php echo $forums[$i]['sub']; ?></span></td>
                                        <td class='text-center'><?php echo $forums[$i]['topics']; ?></td>
                                        <td class='text-center'><?php echo $forums[$i]['replies']; ?></td>
                                        <td class='text-center'><?php echo $forums[$i]['lastpost']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center">Top 5 Forum Posters</th>
                                </tr>
                                <tr>
                                    <th>Rank</th>
                                    <th>Mobster</th>
                                    <th>Posts</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db->query("SELECT id, posts FROM grpgusers WHERE `ban/freeze` = 0 ORDER BY posts DESC LIMIT 5");
                                $db->execute();
                                if (!$db->num_rows()) {
                                    echo "<tr><td colspan='3' class='text-center'>No-one has posted</td></tr>";
                                } else {
                                    $rank = 0;
                                    $rows = $db->fetch_row();
                                    foreach ($rows as $row) {
                                        ++$rank;
                                        $user = formatName($row['id']);
                                        ?>
                                        <tr>
                                            <td><?php echo $rank; ?></td>
                                            <td><?php echo $user; ?></td>
                                            <td><?php echo $row['posts']; ?></td>
                                        </tr>
                                    <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
            } else if (empty($_GET['id']) && !empty($_GET['topic'])) {
                $db->query("UPDATE ftopics SET views = views + 1 WHERE forumid = ?");
                $db->execute(array($_GET['topic']));
                $db->query("SELECT * FROM ftopics WHERE forumid = ?");
                $db->execute(array($_GET['topic']));
                if (!$db->num_rows())
                    diefun("That topic doesn't exist");
                $topic = $db->fetch_row(true);
                if ($topic['sectionid'] == 11 && !($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo))
                    diefun("You don't have access");
                if (isset($_POST['submit'])) {
                    $_POST['msgtext'] = isset($_POST['msgtext']) && is_string($_POST['msgtext']) ? trim($_POST['msgtext']) : null;
                    if (empty($_POST['msgtext']))
                        diefun("You didn't enter a valid response");
                    $db->query("UPDATE grpgusers SET forumnoti = forumnoti + 1");
                    $db->execute();
                    preg_match_all("/\[tag\]([0-9]*)\[\/tag\]/", $_POST['msgtext'], $tags);
                    $count = count($tags[1]);
                    if ($count >= 1) {
                        $x = 0;
                        while ($x < $count) {
                            Send_event($tags[1][$x], formatName($user_class->id) . " mentioned you in <a href='forum.php?topic={$_GET['topic']}' class='text-decoration-none' style='color:orange;'>{$topic['subject']}</a>", $c);
                            $x++;
                        }
                    }
                    $db->query("INSERT INTO freplies (sectionid, topicid, playerid, timesent, body) VALUES (?, ?, ?, unix_timestamp(), ?)");
                    $db->execute(array($topic['sectionid'], $_GET['topic'], $user_class->id, $_POST['msgtext']));
                    $db->query("UPDATE ftopics SET lastposter = ?, lastupdated = unix_timestamp() WHERE forumid = ?");
                    $db->execute(array($user_class->id, $_GET['topic']));
                    $db->query("UPDATE grpgusers SET posts = posts + 1 WHERE id = ?");
                    $db->execute(array($user_class->id));
                    echo Message("Your response has been posted");
                    $db->query("SELECT * FROM forumfollows WHERE ftid = ? AND userid <> ?");
                    $db->execute(array($_GET['topic'], $user_class->id));
                    $rows = $db->fetch_row();
                    foreach ($rows as $a)
                        Send_event($a['userid'], "<a href='forum.php?topic={$_GET['topic']}' class='text-decoration-none' style='color:orange;'>{$topic['subject']}</a> has a new reply to it!", $c);
                }
                $db->query("SELECT COUNT(postid) FROM freplies WHERE topicid = ?");
                $db->execute(array($_GET['topic']));
                $pages = new pagination();
                $pages->items_total = $db->fetch_single();
                $pages->items_per_page = 25;
                $pages->max_pages = 10;
                $db->query("SELECT * FROM freplies WHERE topicid = ? ORDER BY timesent DESC " . $pages->limit());
                $db->execute(array($_GET['topic']));
                $rows = $db->fetch_row();
                $op = new User($topic['playerid']);
                $edit = ($topic['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? "<div class='edit'><textarea class='form-control' rows='5' id='edittext'>" . str_replace("<br>", "\n", $topic['body']) . "</textarea><button class='btn btn-secondary mt-2' onclick='editTopic({$_GET['topic']});'>Edit Post</button></div>" : "";
                $edi = ($topic['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? " onclick='gotoedit();'" : "";
                if ($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) {
                    if (isset($_POST['lock'])) {
                        if ($topic['status'] == 3) {
                            $db->query("UPDATE ftopics SET status = 1 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been unlocked.");
                        }
                        if ($topic['status'] == 2) {
                            $db->query("UPDATE ftopics SET status = 0 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been unlocked.");
                        }
                        if ($topic['status'] == 1) {
                            $db->query("UPDATE ftopics SET status = 3 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been locked.");
                        }
                        if ($topic['status'] == 0) {
                            $db->query("UPDATE ftopics SET status = 2 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been locked.");
                        }
                    } elseif (isset($_POST['sticky'])) {
                        if ($topic['status'] == 3) {
                            $db->query("UPDATE ftopics SET status = 2 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been unstickied.");
                        }
                        if ($topic['status'] == 2) {
                            $db->query("UPDATE ftopics SET status = 3 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been stickied.");
                        }
                        if ($topic['status'] == 1) {
                            $db->query("UPDATE ftopics SET status = 0 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been unstickied.");
                        }
                        if ($topic['status'] == 0) {
                            $db->query("UPDATE ftopics SET status = 1 WHERE forumid = ?");
                            $db->execute(array($_GET['topic']));
                            echo Message("Topic has been stickied.");
                        }
                    } elseif (isset($_POST['delete']) && !isset($_POST['confirm'])) {
                        $prompt = "Are you sure you wish to delete this topic?<br />
                        <form method='post' class='text-center'>
                        <input type='hidden' name='confirm' value='yes' />
                        <button type='submit' class='btn btn-danger' name='delete'>Yes, Delete Topic</button>
                        </form>";
                    } elseif (isset($_POST['delete']) && isset($_POST['confirm'])) {
                        $db->query("DELETE FROM ftopics WHERE forumid = ?");
                        $db->execute(array($_GET['topic']));
                        echo Message("Topic has been deleted.");
                    }
                    if (isset($prompt)) {
                        echo $prompt;
                    } else {
                        ?>
                                <div class='text-center'>
                                    <form method='post'>
                                        <button type='submit' class='btn btn-warning' name='lock'>Lock Topic</button>
                                        <button type='submit' class='btn btn-info' name='sticky'>Sticky Topic</button>
                                        <button type='submit' class='btn btn-danger' name='delete'>Delete Topic</button>
                                    </form>
                                </div>
                        <?php
                    }
                }
                $db->query("SELECT * FROM forumfollows WHERE userid = ? AND ftid = ?");
                $db->execute(array($user_class->id, $_GET['topic']));
                $ups = $downs = array();
                $db->query("SELECT username, rate FROM forumpostrates f JOIN grpgusers g ON f.userid = g.id WHERE fpid = ?");
                $db->execute(array($_GET['topic']));
                $subrows = $db->fetch_row();
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
                $js = ($db->num_rows()) ? array('unfollow', 'Unfollowed!', 'Unfollow') : array('follow', 'Following!', 'Follow');
                ?>
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
                                    $.post("ajax_forumedit.php", { "topic": fpid, "edittext": $("#edittext").val() }, function (d) {
                                        $(".noedit").html(d);
                                        $(".noedit").css("display", "block");
                                        $(".edit").css("display", "none");
                                    });
                                }
                                return false;
                            }
                            function editReply(fpid) {
                                if ($("#edittext" + fpid).val() != "") {
                                    $.post("ajax_forumedit.php", { "reply": fpid, "edittext": $("#edittext" + fpid).val() }, function (d) {
                                        $(".noedit" + fpid).html(d);
                                        $(".noedit" + fpid).css("display", "block");
                                        $(".edit" + fpid).css("display", "none");
                                    });
                                }
                                return false;
                            }
                            function follow() {
                                $(this).load("ajax_<?php echo $js[0]; ?>.php?ftid=<?php echo $_GET['topic']; ?>");
                                $("#follow").html("<?php echo $js[1]; ?>");
                            }
                        </script>
                        <style>
                            #rate:hover,
                            #follow:hover {
                                background: rgba(0, 0, 0, .25);
                            }

                            .edit {
                                display: none;
                                text-align: center;
                            }
                        </style>
                        <div class="mt-4">
                            <span class="fw-bold">
                                <a href="forum.php" class="text-decoration-none">Forum</a> &rarr;
                                <a href="forum.php?id=<?php echo $topic['sectionid']; ?>"
                                    class="text-decoration-none"><?php echo $names[$topic['sectionid']]; ?></a> &rarr;
                        <?php echo $topic['subject']; ?>
                            </span>
                            <div class="d-flex justify-content-between mt-3 mb-2">
                                <span><?php echo date('d M Y - g:i a', $topic['timesent']); ?></span>
                                <span title="<?php echo implode('&#13;', $ups); ?>" class="badge bg-success forumhover"
                                    onclick="rate('up',<?php echo $topic['forumid']; ?>, <?php echo ($topic['rateup'] + 1); ?>);"><img
                                        src="images/up.jpg" /> Rate Up (<span
                                        id="uprate<?php echo $topic['forumid']; ?>"><?php echo $topic['rateup']; ?></span>)</span>
                                <span title="<?php echo implode('&#13;', $downs); ?>" class="badge bg-danger forumhover"
                                    onclick="rate('down',<?php echo $topic['forumid']; ?>, <?php echo ($topic['ratedown'] + 1); ?>);"><img
                                        src="images/down.jpg" /> Rate Down (<span
                                        id="downrate<?php echo $topic['forumid']; ?>"><?php echo $topic['ratedown']; ?></span>)</span>
                                <span class="badge bg-primary forumhover" onclick="follow();" id="follow"><?php echo $js[2]; ?>
                                    Topic</span>
                        <?php if ($edi) { ?>
                                    <span class="badge bg-secondary forumhover" onclick="gotoedit();">Edit Post</span>
                        <?php } ?>
                            </div>
                            <hr>
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="text-center">
                                        <img src="<?php echo $op->avatar; ?>" width="150" height="150" class="img-thumbnail mb-3" />
                                        <p><?php echo $op->formattedname; ?></p>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="noedit"><?php echo BBCodeParse($topic['body']); ?></div>
                            <?php echo $edit; ?>
                                <?php
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
                                    $db->execute(array($user_class->id, $pollId));
                                    $voted = $db->fetch_row(true);
                                    echo '<div class="poll-box">';
                                    echo '<h3>' . $title . '</h3>';
                                    if (!$voted && (time() < $end)) {
                                        echo '
                                    <form class="poll" method="POST">
                                        <input type="hidden" name="pollid" value="' . $pollId . '">
                                        <div class="mb-3">';
                                        foreach ($choices as $key => $value) {
                                            echo '<div class="form-check">
                                                <input class="form-check-input" type="radio" value="' . $key . '" name="radioq" id="' . $key . '">
                                                <label class="form-check-label" for="' . $key . '">' . $value . '</label>
                                              </div>';
                                        }
                                        echo '
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                    <div class="results"></div>';
                                    } else {
                                        $db->query("SELECT *, u.username FROM voters INNER JOIN grpgusers u ON `user_id` = u.id WHERE poll_id = ?");
                                        $db->execute(array($poll['id']));
                                        $voters = $db->fetch_row();
                                        echo '<div class="results mt-3">';
                                        echo '<h3>Results</h3>';
                                        foreach ($choices as $key => $value) {
                                            $votePercent = round(($votes[$key] / $poll['voters']) * 100);
                                            $votePercent = !empty($votePercent) ? $votePercent . '%' : '0%';
                                            echo '<h5>' . $value . '</h5>';
                                            echo '<div class="progress mb-2" style="height: 25px;">
                                                <div class="progress-bar" role="progressbar" style="width: ' . $votePercent . ';" aria-valuenow="' . $votePercent . '" aria-valuemin="0" aria-valuemax="100">' . $votePercent . '</div>
                                              </div>';
                                            if ($topic['sectionid'] != 1) {
                                                echo '<div>';
                                                foreach ($voters as $voter) {
                                                    if ($voter['choice'] == $key) {
                                                        echo $voter['username'] . ' ';
                                                    }
                                                }
                                                echo '</div>';
                                            }
                                        }
                                        echo '</div></div>';
                                    }
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    if (!in_array($topic['status'], [2, 3])) {
                        ?>
                            <div class="card mt-4">
                                <div class="card-body">
                                    <form name="message" method="post">
                                        <div class="d-flex justify-content-around mb-2">
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[b][/b]', 4);">[b]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[u][/u]', 4);">[u]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[i][/i]', 4);">[i]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[s][/s]', 4);">[s]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[url][/url]', 6);">[url]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[img][/img]', 6);">[img]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[tag][/tag]', 6);">[tag]</span>
                                            <span class="badge bg-secondary forumhover"
                                                onclick="insertAtCursor('[youtube][/youtube]', 10);">[youtube]</span>
                                            <span class="badge bg-secondary forumhover" id="semojis" onclick="return showemojis();"
                                                style="display:<?php echo ($user_class->hideemojis) ? 'block' : 'none'; ?>;">Show
                                                Emojis</span>
                                            <span class="badge bg-secondary forumhover" id="hemojis" onclick="return hideemojis();"
                                                style="display:<?php echo ($user_class->hideemojis) ? 'none' : 'block'; ?>;">Hide
                                                Emojis</span>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <textarea class="form-control" name="msgtext" id="reply" rows="5"></textarea>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary" name="submit">Respond</button>
                                        </div>
                                    </form>
                                    <div id="emojis" style="display:<?php echo ($user_class->hideemojis) ? 'none' : 'block'; ?>;">
                                <?php emotes(); ?>
                                    </div>
                                </div>
                            </div>
                    <?php
                    }
                    if ($rtn = $pages->displayPages()) {
                        echo '<div class="d-flex justify-content-center mt-4">' . $rtn . '</div>';
                    }
                    if (!count($rows)) {
                        echo '<div class="text-center mt-4">There are no responses yet.</div>';
                    } else {
                        foreach ($rows as $row) {
                            $db->query("SELECT username, rate FROM forumreplyrates f JOIN grpgusers g ON f.userid = g.id WHERE postid = ?");
                            $db->execute(array($row['postid']));
                            $subrows = $db->fetch_row();
                            $ups = $downs = array();
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

                            $poster = new User($row['playerid']);
                            $edit = ($row['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? "<div class='edit{$row['postid']}' style='display:none;text-align:center;'><textarea class='form-control' rows='5' id='edittext{$row['postid']}'>{$row['body']}</textarea><button class='btn btn-secondary mt-2' onclick='editReply({$row['postid']});'>Edit Post</button></div>" : "";
                            $edi = ($row['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? 1 : 0;
                            ?>
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><?php echo date('d M Y - g:i a', $row['timesent']); ?></span>
                                            <span title="<?php echo implode('&#13;', $ups); ?>" class="badge bg-success forumhover"
                                                onclick="ratepost('up',<?php echo $row['postid']; ?>, <?php echo ($row['rateup'] + 1); ?>);"><img
                                                    src="images/up.jpg" /> Rate Up (<span
                                                    id="upratepost<?php echo $row['postid']; ?>"><?php echo $row['rateup']; ?></span>)</span>
                                            <span title="<?php echo implode('&#13;', $downs); ?>" class="badge bg-danger forumhover"
                                                onclick="ratepost('down',<?php echo $row['postid']; ?>, <?php echo ($row['ratedown'] + 1); ?>);"><img
                                                    src="images/down.jpg" /> Rate Down (<span
                                                    id="downratepost<?php echo $row['postid']; ?>"><?php echo $row['ratedown']; ?></span>)</span>
                                    <?php if ($edi) { ?>
                                                <span class="badge bg-secondary forumhover"
                                                    onclick="gotoeditreply(<?php echo $row['postid']; ?>);">Edit Post</span>
                                    <?php } ?>
                                        </div>
                                        <hr>
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <div class="text-center">
                                                    <img src="<?php echo $poster->avatar; ?>" width="125" height="125"
                                                        class="img-thumbnail mb-3" />
                                                    <p><?php echo $poster->formattedname; ?></p>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="noedit<?php echo $row['postid']; ?>"><?php echo BBCodeParse($row['body']); ?>
                                                </div>
                                        <?php echo $edit; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                        }
                    }
                    if ($rtn) {
                        echo '<div class="d-flex justify-content-center mt-4">' . $rtn . '</div>';
                    }
            }
            include 'footer.php';
            ?>
        </div>
    </div>
</div>