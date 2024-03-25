<?php
include 'header.php';
include 'includes/pagination.class.php';
?>
<div class='box_top'>Forums</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
$db->query("UPDATE grpgusers SET forumnoti = 0 WHERE id = ?");
$db->execute(array(
    $user_class->id
));
if ($user_class->news > 0) {
    $db->query("UPDATE grpgusers SET news = 0, forumnoti = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
}
addBrowser($user_class->id, $user_class->username);
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
                $db->execute(array(
                    $i
                ));
                $names[$i] = $db->fetch_single();
                $db->query("SELECT ff_auth FROM forum_forums WHERE ff_id = ?");
                $db->execute(array(
                    $i
                ));
                $auth = $db->fetch_single();
                if ($auth == 'gang') {
                    if (empty($user_class->gang))
                        diefun("That board doesn't exist");
                    $db->query("SELECT forumid FROM gangs WHERE id = ?");
                    $db->execute(array(
                        $user_class->gang
                    ));
                    $fid = $db->fetch_single();
                    if ($i != $fid)
                        diefun("That board doesn't exist");
                }
            }
            if (isset($_POST['topic'])) {
                if (!empty($m->get('lastpost.' . $user_class->id)))
                    diefun("No spaming the forums.");
                $_POST['topic'] = isset($_POST['topic']) && is_string($_POST['topic']) ? trim($_POST['topic']) : null;
                $_POST['msgtext'] = isset($_POST['msgtext']) && is_string($_POST['msgtext']) ? trim($_POST['msgtext']) : null;

                if (!empty($_POST['msgtext']) && !empty($_POST['topic'])) {

                    $db->query("UPDATE grpgusers SET forumnoti = forumnoti + 1");
                    $db->execute();
                    $m->set('lastpost.' . $user_class->id, 1, 10);
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
                        foreach($choices as $key => $value)
                            if(empty($value))
                                unset($choices[$key]);

                        $title = $_POST['poll_title'];
                        $finish = strtotime($_POST['poll_finish']);
                        $votes = array_fill(0, count($output['poll_choice']), 0);
                        $votes = serialize($votes);

                        $db->query("INSERT INTO polls (title, options, votes, finish, topic_id) VALUES (?, ?, ?, ?, ?)");
                        $db->execute(
                            array(
                                $title,
                                $choices,
                                $votes,
                                $finish,
                                $topic_id
                            )
                        );
                    }

                    $db->query("UPDATE grpgusers SET posts = posts + 1 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
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
            ?><tr><td class="contentspacer"></td></tr>
            <tr><td class="contenthead"><a href="forum.php">Forum</a> &rarr; <?php echo $names[$i]; ?></td></tr>
            <tr><td class="contentcontent">
                    <div class='paginate'><?php echo $pages->displayPages(); ?></div>
                    <table id='newtables' class='altcolors' style='width:100%;'>
                        <tr>
                            <th width="46%">Topic</th>
                            <th width="26%">Starter</th>
                            <th width="10%">Rating</th>
                            <th width="10%">Replies</th>
                            <th width="10%">Views</th>
                        </tr><?php
                        $db->query("SELECT * FROM ftopics WHERE sectionid = ? ORDER BY FIELD( status, 3,1,0,2 ), case when (SELECT MAX(timesent) FROM freplies WHERE forumid = topicid) > timesent then (SELECT MAX(timesent) FROM freplies WHERE forumid = topicid) else timesent end DESC, lastreply DESC" . $pages->limit());
                        $db->execute(array(
                            $i
                        ));
                        if (!$db->num_rows())
                            echo "<tr><td colspan='4' class='center'>There are no topics</td></tr>";
                        else {
                            $rows = $db->fetch_row();
                            foreach ($rows as $row) {
                                $rating = $row['rateup'] - $row['ratedown'];
                                $rating = ($rating == 0) ? '-' : $rating;
                                if ($rating != 0) {
                                    $rating = ($rating > 0) ? '<span style="color:green">+' . $rating . '<span>' : '<span style="color:red">' . $rating . '<span>';
                                }
                                $co = ($co != 1) ? 1 : 2;
                                $db->query("SELECT COUNT(postid) FROM freplies WHERE sectionid = ? AND topicid = ?");
                                $db->execute(array(
                                    $i,
                                    $row['forumid']
                                ));
                                $replies = $db->fetch_single();
                                ?><tr class='colour<?php echo $co ?>'>
                                    <td><a style='color:#c1c1c1;' href='forum.php?topic=<?php echo $row['forumid']; ?>'><?php echo $row['subject'] . ' '; ?></a><?php
                                        echo $row['status'] == 1 ? "[Sticky]" : '';
                                        echo $row['status'] == 2 ? "[Lock]" : '';
                                        echo $row['status'] == 3 ? "[Sticky,Lock]" : '';
                                        ?></td>
                                    <td><?php echo formatName($row['playerid']); ?></td>
                                    <td><?php echo $rating; ?></td>
                                    <td><?php echo $replies; ?></td>
                                    <td><?php echo $row['views']; ?></td>
                                </tr><?php
                            }
                        }
                        ?></table>
                    <div class='paginate'><?php echo $pages->displayPages(); ?></div>
			<?php
			if (!($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) && $i == 1) continue;
				echo'<div class="floaty" style="margin-bottom:-10px;text-align:center;">';
					echo'<span style="color:red;font-weight:bold;">New Topic</span>';
					echo'<hr style="border:0;border-top:thin solid #333;" />';
					echo'<div class="flexcont">';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[b][/b]\', 4);return false;">';
							echo'[b]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[u][/u]\', 4);return false;">';
							echo'[u]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[i][/i]\', 4);return false;">';
							echo'[i]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[s][/s]\', 4);return false;">';
							echo'[s]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[url][/url]\', 6);return false;">';
							echo'[url]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[img][/img]\', 6);return false;">';
							echo'[img]';
						echo'</div>';
echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[tag][/tag]\', 6);return false;">';
							echo'[tag]';
						echo'</div>';

						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[youtube][/youtube]\', 10);return false;">';
							echo'[youtube]';
						echo'</div>';
						echo'<div id="semojis" class="flexele forumhover" onclick="return showemojis();" style="display:' , ($user_class->hideemojis) ? 'block' : 'none' , ';flex:2;">';
							echo'Show Emojis';
						echo'</div>';
						echo'<div id="hemojis" class="flexele forumhover" onclick="return hideemojis();" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';flex:2;">';
							echo'Hide Emojis';
						echo'</div>';
					echo'</div>';
					echo'<hr style="border:0;border-top:thin solid #333;" />';
					echo'<form name="message" method="post">';
						echo'<table style="width:100%;">';
							echo'<tr>';
								echo'<td style="width:10%;">Topic:</td>';
								echo'<td><input type="text" required name="topic" /></td>';
							echo'</tr>';
							echo'<tr>';
								echo'<td>Message:</td>';
								echo'<td><textarea name="msgtext" required id="reply" style="width:95%;height:125px;"></textarea></td>';
                            echo'</tr>';

                            echo "<style>
                                .ic {
                                    margin: 5px 0 5px 0;
                                }
                            </style>";

                            /*
                                Polls
                            */
                                //<button>Add Poll</button>
                            // if ($user_class->id == 150 || $user_class->id == 1) {
                                echo '<tr>';
                                echo '<td>Poll</td>';
                                echo '<td>

                                <button id="addpoll">Add Poll</button>
                                <div id="poll" style="display: none">
                                    Title: <input type="text" id="poll_title" name="poll_title">
                                    Choices:<br>
                                    <div class="choices" style="display:inherit">
                                        <input class="ic" type="text" name="poll_choice[]"/>
                                        <input class="ic" type="text" name="poll_choice[]"/>
                                    </div>
                                    <button id="addchoice">Add Another</button>
                                    <br>
                                    Finish:
                                    <input type="date" id="poll_finish" name="poll_finish" required value="' . date('Y-m-d', strtotime("+2 day")) . '";
                                </div>
                                </td>';
                                echo '</tr>';
                            //}
                            echo'<tr>';
								echo'<td colspan="2" style="text-align:center;"><input type="submit" id="createTopic" name="submit" value="Create Topic" /></td>';
							echo'</tr>';
						echo'</table>';
					echo'</form>';
					echo'<div id="emojis" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';">';
						emotes();
					echo'</div>';
				echo'</div>';
            include 'footer.php';
            exit;
        }
    }
} else if (empty($_GET['id']) && empty($_GET['topic'])) {
    $db->query("UPDATE grpgusers SET threadtime = unix_timestamp() WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    $db->query("SELECT days FROM bans WHERE id = ? AND type = 'forum'");
    $db->execute(array(
        $user_class->id
    ));
    if ($db->num_rows())
        diefun("You've been banned from the forum. Time remaining: " . howlongtil($db->fetch_single() * 86400));
    $forums = array();
    $fnames = array(array('News', 'Stay up to date and comment on game news.'),
        array('General Chat', 'Talk about anything general to MafiaLords here.'),
        array('Gang Chat', 'Talk about and advertise your gang here.'),
        array('Marketplace', 'Post buying and selling needs here.'),
        array('Competitions', 'Hold any competitions you want here.'),
        array('Off Topic', 'Talk about anything not related to MafiaLords here.'),
        array('Suggestions', 'Post anything new or improved you would like to see on MafiaLords.'),
        array('Help Forum', 'Ask for help here from other members or staff.'),
        array('Bugs, Errors etc...', 'Report bugs and errors here.'),
        array('Graphics', 'Forum section for all your graphical needs.'),
        array('Staff Forum', 'Anything you need to talk about in private to another staff member.'));
    for ($i = 1; $i <= 11; $i++) {
        $db->query("SELECT * FROM ftopics WHERE sectionid = ? ORDER BY timesent DESC LIMIT 1");
        $db->execute(array(
            $i
        ));
        $cnt = $db->fetch_row(true);
        $db->query("SELECT count(*) FROM ftopics WHERE sectionid = ?");
        $db->execute(array(
            $i
        ));
        $forums[$i]['topics'] = $db->fetch_single();
        $db->query("SELECT timesent FROM freplies WHERE sectionid = ? ORDER BY timesent DESC");
        $db->execute(array(
            $i
        ));
        $forums[$i]['lastreply'] = $db->fetch_single();
        $db->query("SELECT COUNT(postid) FROM freplies WHERE sectionid = ?");
        $db->execute(array(
            $i
        ));
        $forums[$i]['replies'] = $db->fetch_single();
        $forums[$i]['lastreply'] = ($forums[$i]['lastreply'] > $cnt['timesent']) ? $forums[$i]['lastreply'] : $cnt['timesent'];
        $forums[$i]['lastpost'] = $forums[$i]['lastreply'] ? date('d F Y, g:ia', $forums[$i]['lastreply']) : 'None';
        $forums[$i]['name'] = $fnames[$i - 1][0];
        $forums[$i]['sub'] = $fnames[$i - 1][1];
    }
    $db->query("SELECT DISTINCT(topicid) as one FROM freplies ORDER BY timesent DESC LIMIT 5");
    $db->execute();
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        $id[] = $row['one'];
    }
    $db->query("SELECT * FROM ftopics ORDER BY lastupdated DESC LIMIT 5");
    $db->execute();
    $rows = $db->fetch_row();
    print"<table id='newtables' class='altcolors' style='width:100%;'>
        <tr>
            <th colspan='3'>Recent Topics</th>
        </tr>
        <tr>
            <th>Topic</th>
            <th>Poster</th>
            <th>How long ago?</th>";
    foreach ($rows as $row) {
        $db->query("");
        print"
            <tr>
                <td><a href='forum.php?topic={$row['forumid']}'>{$row['subject']}</a></td>
                <td>" . formatName($row['lastposter']) . "</td>
                <td>" . howlongago($row['lastupdated']) . "</td>
            </tr>";
    }
    print"</table>";
    genHead("Forum");
    print"
            <table id='newtables' class='altcolors' style='width:100%;'>
                <tr>
                    <th width='50%'>Forum</th>
                    <th width='15%'>Topics</th>
                    <th width='12%'>Replies</th>
                    <th width='20%'>Last Post</td>
                </tr>";
    if ($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo)
        $j = 11;

        else$j = 10;
    for ($i = 1; $i <= $j; $i++) {
        $db->query("SELECT avatar FROM grpgusers WHERE id = ?");
        $db->execute(array(
            $forums[$i]['topicinfo']['playerid']
        ));
        $avatar = $db->fetch_single();
        $forums[$i]['topicinfo']['body'] = substr($forums[$i]['topicinfo']['body'], 0, 50);
        print"
                    <tr>
                        <td><a style='color:#b5b4b4;' href='forum.php?id=$i'>{$forums[$i]['name']}</a><br /><span style='font-size:.75em;'>{$forums[$i]['sub']}</span></td>
                        <td align='center'>{$forums[$i]['topics']}</td>
                        <td align='center'>{$forums[$i]['replies']}</td>
                        <td align='center'>{$forums[$i]['lastpost']}</td>
                    </tr>";
    }
    ?>
    </table>
    </td></tr>
    <tr><td class="contentspacer"></td></tr>
    <tr><td class="contenthead">Top 5 Forum Whores</td></tr>
    <tr><td class="contentcontent">
            <table class='altcolors' id='newtables' style='width:100%;'>
                <tr>
                    <th width='20%'>Rank</th>
                    <th width='60%'>Mobster</th>
                    <th width='20%'>Posts</th>
                </tr><?php
                $db->query("SELECT id, posts FROM grpgusers WHERE `ban/freeze` = 0 ORDER BY posts DESC LIMIT 5");
                $db->execute();
                if (!$db->num_rows())
                    echo "<tr><td colspan='3'>No-one has posted</td></tr>";
                else {
                    $rank = 0;
                    $rows = $db->fetch_row();
                    foreach ($rows as $row) {
                        ++$rank;
                        $user = formatName($row['id']);
                        ?><tr>
                            <td><?php echo $rank; ?></td>
                            <td><?php echo $user; ?></td>
                            <td><?php echo $row['posts']; ?></td>
                        </tr><?php
                    }
                }
                ?></table>
        </td></tr><?php
} else if (empty($_GET['id']) && !empty($_GET['topic'])) {
    $db->query("UPDATE ftopics SET views = views + 1 WHERE forumid = ?");
    $db->execute(array(
        $_GET['topic']
    ));
    $db->query("SELECT * FROM ftopics WHERE forumid = ?");
    $db->execute(array(
        $_GET['topic']
    ));
    if (!$db->num_rows())
        diefun("That topic doesn't exist");
    $topic = $db->fetch_row(true);
    if ($topic['sectionid'] == 11 && !($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo))
        diefun("You don't have access");
    if (isset($_POST['submit'])) {
        if (!empty($m->get('lastpost.' . $user_class->id)))
            diefun("No spaming the forums.");
        $_POST['msgtext'] = isset($_POST['msgtext']) && is_string($_POST['msgtext']) ? trim($_POST['msgtext']) : null;
        if (empty($_POST['msgtext']))
            diefun("You didn't enter a valid response");
        $db->query("UPDATE grpgusers SET forumnoti = forumnoti + 1");
        $db->execute();
        $m->set('lastpost.' . $user_class->id, 1, 10);
        preg_match_all("/\[tag\]([0-9]*)\[\/tag\]/", $_POST['msgtext'], $tags);
        $count = count($tags[1]);
        if ($count >= 1) {
            $x = 0;
            while ($x < $count) {
				Send_event($tags[1][$x], formatName($user_class->id) . " mentioned you in <a href='forum.php?topic={$_GET['topic']}' style='color:orange;'>{$topic['subject']}</a>", $c);
                $x++;
            }
        }
        $db->query("INSERT INTO freplies (sectionid, topicid, playerid, timesent, body) VALUES ({$topic['sectionid']}, {$_GET['topic']}, $user_class->id, unix_timestamp(), '{$_POST['msgtext']}')");
        $db->execute(array(
            $topic['sectionid'],
            $_GET['topic'],
            $user_class->id,
            $_POST['msgtext']
        ));
        $db->query("UPDATE ftopics SET lastposter = ?, lastupdated = unix_timestamp() WHERE forumid = ?");
        $db->execute(array(
            $user_class->id,
            $_GET['topic']
        ));
        $db->query("UPDATE grpgusers SET posts = posts + 1 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        echo Message("Your response has been posted");
        $db->query("SELECT * FROM forumfollows WHERE ftid = ? AND userid <> ?");
        $db->execute(array(
            $_GET['topic'],
            $user_class->id
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $a)
            Send_event($a['userid'], "<a href='forum.php?topic={$_GET['topic']}' style='color:orange;'>{$topic['subject']}</a> has a new reply to it!", $c);
    }
    $db->query("SELECT COUNT(postid) FROM freplies WHERE topicid = ?");
    $db->execute(array(
        $_GET['topic']
    ));
	$pages = new pagination();
	$pages->items_total = $db->fetch_single();
	$pages->items_per_page = 25;
	$pages->max_pages = 10;
    $db->query("SELECT * FROM freplies WHERE topicid = ? ORDER BY timesent DESC " . $pages->limit());
    $db->execute(array(
        $_GET['topic']
    ));
    $rows = $db->fetch_row();
    $op = new User($topic['playerid']);
    $edit = ($topic['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? "<div class='edit'><textarea style='width:100%;height:100%;' rows='10' id='edittext'>" . str_replace("<br>", "\n", $topic['body']) . "</textarea><button onclick='editTopic({$_GET['topic']});'>Edit Post</button></div>" : "";
    $edi = ($topic['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? " onclick='gotoedit();'" : "";
    if ($user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) {
        if (isset($_POST['lock'])) {
            if ($topic['status'] == 3) {
                $db->query("UPDATE ftopics SET status = 1 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been unlocked.");
            }
            if ($topic['status'] == 2) {
                $db->query("UPDATE ftopics SET status = 0 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been unlocked.");
            }
            if ($topic['status'] == 1) {
                $db->query("UPDATE ftopics SET status = 3 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been locked.");
            }
            if ($topic['status'] == 0) {
                $db->query("UPDATE ftopics SET status = 2 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been locked.");
            }
        } elseif (isset($_POST['sticky'])) {
            if ($topic['status'] == 3) {
                $db->query("UPDATE ftopics SET status = 2 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been unstickied.");
            }
            if ($topic['status'] == 2) {
                $db->query("UPDATE ftopics SET status = 3 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been stickied.");
            }
            if ($topic['status'] == 1) {
                $db->query("UPDATE ftopics SET status = 0 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been unstickied.");
            }
            if ($topic['status'] == 0) {
                $db->query("UPDATE ftopics SET status = 1 WHERE forumid = ?");
                $db->execute(array(
                    $_GET['topic']
                ));
                echo Message("Topic has been stickied.");
            }
        } elseif (isset($_POST['delete']) AND ! isset($_POST['confirm'])) {
            $prompt = "Are you sure you wish to delete this topic?<br />
			<form method='post' style='margin:0 auto;text-align:center;'>
			<input type='hidden' name='confirm' value='yes' />
			<input type='submit' name='delete' value='Yes, Delete Topic' />
			</form>";
        } elseif (isset($_POST['delete']) AND isset($_POST['confirm'])) {
            $db->query("DELETE FROM ftopics WHERE forumid = ?");
            $db->execute(array(
                $_GET['topic']
            ));
            echo Message("Topic has been deleted.");
        }
                if (isset($prompt))
                    print $prompt;
                else {
                    ?>
                    <form method='post' style='margin:0 auto;text-align:center;'>
                        <input type='submit' name='lock' value='Lock Topic' />
                        <input type='submit' name='sticky' value='Sticky Topic' />
                        <input type='submit' name='delete' value='Delete Topic' />
                    </form>
                    <?php
                }
            }
            $db->query("SELECT * FROM forumfollows WHERE userid = ? AND ftid = ?");
            $db->execute(array(
                $user_class->id,
                $_GET['topic']
            ));
			$ups = $downs = array();
			$db->query("SELECT username, rate FROM forumpostrates f JOIN grpgusers g ON f.userid = g.id WHERE fpid = ?");
			$db->execute(array(
				$_GET['topic']
			));
			$subrows = $db->fetch_row();
			$ups = $downs = array();
			foreach($subrows as $subrow){
				switch($subrow['rate']){
					case 'up':
						$ups[] = $subrow['username'];
						break;
					case 'down':
						$downs[] = $subrow['username'];
						break;
				}
			}
            $js = ($db->num_rows()) ? array('unfollow', 'Unfollowed!', 'Unfollow') : array('follow', 'Following!', 'Follow');
            echo'<script type="text/javascript">';
                echo'function rate(arrow, fpid, rate) {';
                    echo'$(this).load("ajax_forumrate" + arrow + ".php?fpid=" + fpid);';
                    echo'$("#" + arrow + "rate" + fpid).html(rate);';
                echo'}';
                echo'function ratepost(arrow, postid, rate) {';
                    echo'$(this).load("ajax_forumpostrate" + arrow + ".php?postid=" + postid);';
                    echo'$("#" + arrow + "ratepost" + postid).html(rate);';
                echo'}';
                echo'function gotoedit() {';
                    echo'if ($(".noedit").length) {';
                        echo'$(".edit").css("display", "block");';
                        echo'$(".noedit").css("display", "none");';
                    echo'}';
                echo'}';
                echo'function gotoeditreply(fpid) {';
                    echo'if ($(".noedit" + fpid).length) {';
                        echo'$(".edit" + fpid).css("display", "block");';
                        echo'$(".noedit" + fpid).css("display", "none");';
                    echo'}';
                echo'}';
                echo'function editTopic(fpid) {';
                    echo'if ($("#edittext").val() != "") {';
                        echo'$.post("ajax_forumedit.php", {"topic": fpid, "edittext": $("#edittext").val()}, function (d) {';
                            echo'$(".noedit").html(d);';
                            echo'$(".noedit").css("display", "block");';
                            echo'$(".edit").css("display", "none");';
                        echo'});';
                    echo'}';
                    echo'return false;';
                echo'}';
                echo'function editReply(fpid) {';
                    echo'if ($("#edittext" + fpid).val() != "") {';
                        echo'$.post("ajax_forumedit.php", {"reply": fpid, "edittext": $("#edittext" + fpid).val()}, function (d) {';
                            echo'$(".noedit" + fpid).html(d);';
                            echo'$(".noedit" + fpid).css("display", "block");';
                            echo'$(".edit" + fpid).css("display", "none");';
                        echo'});';
                    echo'}';
                    echo'return false;';
                echo'}';
                echo'function follow() {';
                    echo'$(this).load("ajax_' . $js[0] . '.php?ftid=' . $_GET['topic'] . '");';
                    echo'$("#follow").html("' . $js[1] . '");';
                echo'}';
            echo'</script>';
            echo'<style>';
                echo'#rate:hover,#follow:hover{';
                    echo'background:rgba(0,0,0,.25);';
                echo'}';
                echo'.edit{';
                    echo'display:none;';
                    echo'text-align:center;';
                echo'}';
            echo'</style>';
			echo'<br />';
			echo'<br />';
			echo'<span class="floaty" style="font-weight:bold;">';
				echo'<a href="forum.php">Forum</a> &rarr; ';
				echo'<a href="forum.php?id=' . $topic['sectionid'] . '">' . $names[$topic['sectionid']] . '</a> &rarr; ';
				echo $topic['subject'];
			echo'</span>';
			echo'<br />';
			echo'<br />';
			echo'<div class="floaty">';
				echo'<div class="flexcont">';
					echo'<div class="flexele">';
						echo date('d M Y - g:i a', $topic['timesent']);
					echo'</div>';
					echo'<div title="' . implode('&#13;', $ups) . '" rel="tipsy" class="flexele forumhover" onclick="rate(\'up\',' . $topic['forumid'] . ', ' . ($topic['rateup'] + 1) . ');">';
						echo'<img src="images/up.jpg" /> Rate Up (<span id="uprate' . $topic['forumid'] . '">' . $topic['rateup'] . '</span>)';
					echo'</div>';
					echo'<div title="' . implode('&#13;', $downs) . '" rel="tipsy" class="flexele forumhover" onclick="rate(\'down\',' . $topic['forumid'] . ', ' . ($topic['ratedown'] + 1) . ');">';
						echo'<img src="images/down.jpg" /> Rate Down (<span id="downrate' . $topic['forumid'] . '">' . $topic['ratedown'] . '</span>)';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="follow();" id="follow">';
						echo $js[2] . ' Topic';
					echo'</div>';
				if($edi){
					echo'<div class="flexele forumhover" onclick="gotoedit();">';
						echo 'Edit Post';
					echo'</div>';
				}
				echo'</div>';
				echo'<hr style="border:0;border-top:thin solid #333;" />';
				echo'<div class="flexcont">';
					echo'<div class="flexele" style="border-right:thin solid #333;">';
						echo'<br />';
						echo $op->formattedname . '<br />';
						echo'<br />';
						echo '<img src="' .  $op->avatar . '" width="150" height="150" />';
						echo'<br />';
						echo'<br />';
					echo'</div>';
					echo'<div class="flexele" style="flex:3;padding:10px;">';
						echo'<br />';
                        echo'<div class="noedit">' . BBCodeParse($topic['body']) . '</div>' . $edit;

                        $db->query("SELECT * FROM polls WHERE topic_id = '" . $topic['forumid'] . "'");
                        $db->execute();
                        $poll = $db->fetch_row(true);
                        if($poll) {
                            $title = $poll['title'];
                            $choices = unserialize($poll['options']);
                            $votes = unserialize($poll['votes']);
                            $end = $poll['finish'];
                            $pollId = $poll['id'];

                                $db->query("SELECT * FROM voters WHERE `user_id` = ? AND `poll_id` = ?");
                                $db->execute(
                                    array(
                                        $user_class->id,
                                        $pollId
                                    )
                                );
                                $voted = $db->fetch_row(true);
                                echo '<div class="poll-box">
                                <h3>' . $title. '</h3>';
                                if (!$voted && (time() < $end)) {
                                    echo '
                                    <form class="poll" method="POST">
                                        <input type="hidden" name="pollid" value="' . $pollId . '">
                                        <div class="radiobuttons">
                                        <ul style="text-align: left;list-style: none;padding-left: 0px;display: inline-block;">';
                                        foreach($choices as $key => $value) {
                                            echo '<li><input type="radio" value="' . $key . '" name="radioq" id="'.$key.'">' . $value . '</li>';
                                        }

                                    echo '
                                    </ul>
                                    </div>
                                    <div class="clear"></div>
                                    <button>Submit</button>
                                    <div class="clear"></div>
                                    </form>
                                    <div class="results"></div>';
                                } else {

                                    $db->query("SELECT *, u.username FROM voters INNER JOIN grpgusers u ON `user_id` = u.id WHERE poll_id = ?");
                                    $db->execute(
                                        array($poll['id'])
                                    );
                                    $voters = $db->fetch_row();

                                    echo '<div class="results">';
                                    echo '<h3>Results</h3>';
                                    for($i= 0; $i<count($choices); $i++ ) {
                                        $votePercent = round(($votes[$i]/$poll['voters'])*100);
                                        $votePercent = !empty($votePercent)?$votePercent.'%':'0%';
                                        echo '<h2>'.$choices[$i].'</h2>';
                                        echo '<div class="progress" style="margin: auto;max-width:80%">';
                                        echo '<div class="poll-progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:'.$votePercent.'">'.$votePercent.'</div></div>';

                                        if ($topic['sectionid'] != 1) {
                                            echo '<div style="max-width:90%;margin:auto;">';
                                            foreach($voters as $voter) {
                                                if ($voter['choice'] == $i) {
                                                    echo $voter['username'] . ' ';
                                                }
                                            }
                                            echo '</div>';
                                        }

                                    }
                                    echo '</div></div>';
                                }
                            }
					echo'</div>';
				echo'</div>';
			echo'</div>';
            if (!in_array($topic['status'], [2, 3])) {
				echo'<div class="floaty" style="margin-bottom:-10px;">';
					echo'<form name="message" method="post">';
					echo'<div class="flexcont">';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[b][/b]\', 4);return false;">';
							echo'[b]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[u][/u]\', 4);return false;">';
							echo'[u]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[i][/i]\', 4);return false;">';
							echo'[i]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[s][/s]\', 4);return false;">';
							echo'[s]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[url][/url]\', 6);return false;">';
							echo'[url]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[img][/img]\', 6);return false;">';
							echo'[img]';
						echo'</div>';
echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[tag][/tag]\', 6);return false;">';
							echo'[tag]';
						echo'</div>';
						echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[youtube][/youtube]\', 10);return false;">';
							echo'[youtube]';
						echo'</div>';
						echo'<div id="semojis" class="flexele forumhover" onclick="return showemojis();" style="display:' , ($user_class->hideemojis) ? 'block' : 'none' , ';flex:2;">';
							echo'Show Emojis';
						echo'</div>';
						echo'<div id="hemojis" class="flexele forumhover" onclick="return hideemojis();" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';flex:2;">';
							echo'Hide Emojis';
						echo'</div>';
					echo'</div>';
					echo'<hr style="border:0;border-top:thin solid #333;" />';
					echo'<form name="message">';
						echo'<textarea name="msgtext" id="reply" style="width:95%;height:125px;"></textarea>';
						echo'<input type="submit" name="submit" value="Respond" />';
					echo'</form>';
					echo'<div id="emojis" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';">';
						emotes();
					echo'</div>';
				echo'</div>';
            }
			if($rtn = $pages->displayPages())
				echo '<span class="floaty">' . $rtn . '</span>';
                if (!count($rows))
                    echo '<div class="floaty">There are no responses yet.</div>';
                else {
                    foreach ($rows as $row) {
						$db->query("SELECT username, rate FROM forumreplyrates f JOIN grpgusers g ON f.userid = g.id WHERE postid = ?");
						$db->execute(array(
							$row['postid']
						));
						$subrows = $db->fetch_row();
						$ups = $downs = array();
						foreach($subrows as $subrow){
							switch($subrow['rate']){
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
                        $edit = ($row['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? "<div class='edit{$row['postid']}' style='display:none;text-align:center;'><textarea style='width:100%;height:100%;' rows='10' id='edittext{$row['postid']}'>{$row['body']}</textarea><button onclick='editReply({$row['postid']});'>Edit Post</button></div>" : "";
                        $edi = ($row['playerid'] == $user_class->id || $user_class->admin || $user_class->gm || $user_class->cm || $user_class->eo) ? 1 : 0;
						echo'<div class="floaty">';
							echo'<div class="flexcont">';
								echo'<div class="flexele">';
									echo date('d M Y - g:i a', $row['timesent']);
								echo'</div>';
								echo'<div title="' . implode('&#13;', $ups) . '" rel="tipsy" class="flexele forumhover" onclick="ratepost(\'up\',' . $row['postid'] . ', ' . ($row['rateup'] + 1) . ');">';
									echo'<img src="images/up.jpg" /> Rate Up (<span id="upratepost' . $row['postid'] . '">' . $row['rateup'] . '</span>)';
								echo'</div>';
								echo'<div title="' . implode('&#13;', $downs) . '" class="flexele forumhover" onclick="ratepost(\'down\',' . $row['postid'] . ', ' . ($row['ratedown'] + 1) . ');">';
									echo'<img src="images/down.jpg" /> Rate Down (<span id="downratepost' . $row['postid'] . '">' . $row['ratedown'] . '</span>)';
								echo'</div>';
							if($edi){
								echo'<div class="flexele forumhover" onclick="gotoeditreply(' . $row['postid'] . ');">';
									echo 'Edit Post';
								echo'</div>';
							}
							echo'</div>';
							echo'<hr style="border:0;border-top:thin solid #333;" />';
							echo'<div class="flexcont">';
								echo'<div class="flexele" style="border-right:thin solid #333;">';
									echo'<br />';
									echo $poster->formattedname . '<br />';
									echo'<br />';
									echo '<img src="' .  $poster->avatar . '" width="125" height="125" />';
								echo'</div>';
								echo'<div class="flexele" style="flex:3;">';
									echo '<div class="noedit' . $row['postid'] . '">' . BBCodeParse($row['body']) . '</div>' . $edit;
								echo'</div>';
							echo'</div>';
						echo'</div>';
                    }
                }
				if($rtn)
					echo '<span class="floaty">' . $rtn . '</span>';
        }
        include 'footer.php';
