<?php
include 'header.php';
?>
<div class='box_top'>Gang Forum</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->gang == 0) {
            echo Message("You aren't in a gang.");
            include 'footer.php';
            die();
        }

        $db->query("SELECT COUNT(*) FROM gftopics WHERE sectionid = ?");
        $db->execute([$user_class->gang]);
        $numrows = $db->fetch_single();
        $rowsperpage = 40;
        $totalpages = ceil($numrows / $rowsperpage);
        $totalpages = ($totalpages <= 0) ? 1 : ceil($numrows / $rowsperpage);
        $currentpage = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
        if ($currentpage > $totalpages)
            $currentpage = $totalpages;
        if ($currentpage < 1)
            $currentpage = 1;
        $offset = ($currentpage - 1) * $rowsperpage;
        if (isset($_POST['newtopic']))
            if (!empty($_POST['topic']))
                if (!empty($_POST['body']))
                    if (strlen($_POST['topic']) < 41) {
                        $subject = addslashes(nl2br(strip_tags(str_replace('"', "", $_POST['topic']))));
                        $body = addslashes(nl2br(strip_tags(str_replace('"', '', $_POST['body']))));

                        $res = $db->query("INSERT INTO gftopics (sectionid, playerid, lastreply, timesent, subject, body) VALUES (?, ?, unix_timestamp(), unix_timestamp(), ?, ?)");
                        $db->execute(array(
                            $user_class->gang,
                            $user_class->id,
                            $subject,
                            $body
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

                            $db->query("INSERT INTO gang_polls (title, options, votes, finish, topic_id) VALUES (?, ?, ?, ?, ?)");
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

                        echo Message("Your new topic has been submitted.");
                    } else
                        echo Message("Your subject can only be 40 characters in length!");
                else
                    echo Message("You didn't enter a topic body!");
            else
                echo Message("You didn't enter a topic subject!");
        ?>
        <table id="newtables" style="width:100%;word-wrap:break-word;">
            <tr>
                <th>Topic</th>
                <th>Starter</th>
                <th>Replies</th>
                <th>Views</th>
            </tr>
            <?php

            $db->query("SELECT * FROM gftopics WHERE sectionid = ? ORDER BY sticky DESC, lastreply DESC LIMIT $offset, $rowsperpage");
            $db->execute([$user_class->gang]);
            $result = $db->fetch_row();
            foreach ($result as $line) {
                $db->query("SELECT * FROM gfreplies WHERE sectionid = ? AND topicid = ?");
                $db->execute([$user_class->gang, $line['forumid']]);
                $replies = $db->fetch_row(true);
                $forum_class = new User($line['playerid']);
                echo "
    <tr>
        <td><a href='viewgangpost.php?id={$line['forumid']}'>{$line['subject']}</a></td>
        <td align='center'>$forum_class->formattedname</td>
        <td align='center'>" . prettynum($replies) . "</td>
        <td align='center'>" . prettynum($line['views']) . "</td>
    </tr>
    ";
            }
            ?>
        </table>
        <br /><br />
        <?php
        $range = 2;
        if ($currentpage > 1)
            echo " <a href='?id={$_GET['id']}&page=1'><<</a> ";
        for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++)
            if (($x > 0) && ($x <= $totalpages))
                echo ($x == $currentpage) ? " [<b>$x</b>] " : " <a href='?id={$_GET['id']}&page=$x'>$x</a> ";
        if ($currentpage != $totalpages)
            echo " <a href='?id={$_GET['id']}&page=$totalpages'>>></a> ";
        ?>
        <hr />
        <table width="100%">
            <form method="post">
                <tr>
                    <td width="12%"><b>Topic:</b></td>
                    <td width="80%"><input type="text" name="topic" size="87" /></td>
                </tr>
                <tr>
                    <td width="12%"><b>Message:</b><br />[<a href="bbcode.php">BBCode</a>]</td>
                    <td width="80%"><textarea name="body" cols="66" rows="5"></textarea></td>
                </tr>

                <?php echo "<style>
            .ic {
                margin: 5px 0 5px 0;
            }
        </style>";
                ?>

                <tr>
                    <td colspan="2" align="center"><input type="submit" name="newtopic" value="Add New Topic" /></td>
                </tr>
            </form>
        </table>
        <?php
        include("gangheaders.php");
        include("footer.php");
        ?>