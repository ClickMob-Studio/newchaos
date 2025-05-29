<?php
include 'header.php';
?>
<div class='box_top'>Gang Forum</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);



        if ($user_class->gang == 0) {
            echo Message("You aren't in a gang.");
            include 'footer.php';
            die();
        }

        $user_rank = new GangRank($user_class->grank);

        //Check Forum Banned
        $type = "forum";
        $result = mysql_query("SELECT * FROM `bans` WHERE `type`='$type'");
        $worked = mysql_fetch_array($result);
        if ($worked['id'] == $user_class->id) {
            echo Message('&nbsp;You have been forum banned for ' . prettynum($worked['days']) . ' days.');
            include 'footer.php';
            die();
        }
        //End Check
//Edit Topic
        if (isset($_POST['submit'])) {

            $_POST['subject'] = str_replace('"', '', $_POST['subject']);
            $subject = strip_tags($_POST['subject']);
            $subject = nl2br($subject);
            $subject = addslashes($subject);
            $_POST['body'] = str_replace('"', '', $_POST['body']);
            $body = strip_tags($_POST['body']);
            $body = nl2br($body);
            $body = addslashes($body);

            $result = mysql_query("UPDATE `gftopics` SET `subject` = '$subject', `body` = '$body' WHERE `forumid` = '" . $_GET['id'] . "'");
            echo Message("You have successfully edited this topic.");
        }

        //Reply
        if (isset($_POST['reply'])) {
            $testbody = str_replace(" ", "", $_POST['body']);
            if ($testbody != "") {
                $result = mysql_query("SELECT * from `gftopics` WHERE `forumid` = " . $_GET['id'] . "");
                $worked = mysql_fetch_array($result);

                $sectionid = $worked['sectionid'];
                $topicid = $_GET['id'];
                $playerid = $user_class->id;
                $_POST['body'] = str_replace('"', '', $_POST['body']);
                $body = strip_tags($_POST['body']);
                $body = nl2br($body);
                $body = addslashes($body);
                $timesent = time();

                perform_query("INSERT INTO `gfreplies` (`sectionid`, `playerid`, `timesent`, `topicid`, `body`)" . "VALUES (?, ?, ?, ?, ?)", [$sectionid, $playerid, $timesent, $topicid, $body]);
                perform_query("UPDATE `gftopics` SET `lastreply` = '" . time() . "' WHERE `forumid` = ?", [$_GET['id']]);
                echo Message("You have added a reply.");
            } else {
                echo Message("You didn't enter anything.");
            }
        }





        //Delete Post
        if ($user_rank->gforum == 1) {
            if (isset($_POST['delete'])) {
                perform_query("DELETE FROM `gfreplies` WHERE `postid` = ?", [$_POST['postid']]);
                echo Message("The post you requested was deleted.");
            }

            //Delete Topic
            if (isset($_POST['deletetopic'])) {
                $rsection = mysql_query("SELECT * FROM `gftopics` WHERE `forumid` = '" . $_GET['id'] . "'");
                $section = mysql_fetch_array($rsection);
                echo Message("The topic you requested was deleted.<br /><br /><a href='gangforum.php?id=" . $section['sectionid'] . "'>Go Back</a>");
                perform_query("DELETE FROM `gftopics` WHERE `forumid` = ?", [$_GET['id']]);
                perform_query("DELETE FROM `gfreplies` WHERE `topicid` = ?", [$_GET['id']]);
                include("footer.php");
                die();
            }

            //Lock Topic
            if (isset($_POST['lock'])) {
                perform_query("UPDATE `gftopics` SET `locked` = '1' WHERE `forumid` = ?", [$_GET['id']]);
                echo Message("You have locked this topic.");
            }

            //Sticky Topic
            if (isset($_POST['sticky'])) {
                perform_query("UPDATE `gftopics` SET `sticky` = '1' WHERE `forumid` = ?", [$_GET['id']]);
                echo Message("You have stickied this topic.");
            }

            //Unlock Topic
            if (isset($_POST['unlock'])) {
                perform_query("UPDATE `gftopics` SET `locked` = '0' WHERE `forumid` = ?", [$_GET['id']]);
                echo Message("You have unlocked this topic.");
            }

            //Unsticky Topic
            if (isset($_POST['unsticky'])) {
                perform_query("UPDATE `gftopics` SET `sticky` = '0' WHERE `forumid` = ?", [$_GET['id']]);
                echo Message("You have unstickied this topic.");
            }
        }

        $result = mysql_query("SELECT * from `gftopics` WHERE `forumid` = " . $_GET['id'] . "");
        $worked = mysql_fetch_array($result);

        $ticket_class = new User($worked['playerid']);

        if ($ticket_class->avatar != "") {
            $avatar = $ticket_class->avatar;
        } else {
            $avatar = "/images/no-avatar.png";
        }

        if ($_GET['id'] != $worked['forumid']) {
            echo Message("This topic could not be found in our database, sorry.");
            include("footer.php");
            die();
        }

        //Add Views
        $result = mysql_query("SELECT * from `gftopics` WHERE `forumid` = '" . $_GET['id'] . "'");
        $worked = mysql_fetch_array($result);
        $views = $worked['views'] + 1;
        perform_query("UPDATE `gftopics` SET `views` = ? WHERE `forumid` = ?", [$views, $_GET['id']]);
        //End Views
        


        if ($user_class->gang != $worked['sectionid']) {

            echo Message('You are not allowed to be here.');
            include("footer.php");
            die();
        }
        ?>

        <tr>
            <td class="contentspacer"></td>
        </tr>
        <tr>
            <td class="contenthead"><a href="gangforum.php">Gang Forum</a> > <?php echo $worked['subject']; ?></td>
        </tr>
        <tr>
            <td class="contentcontent">

                <table width="100%" cellpadding="5" cellspacing="0"
                    style="border:1px solid #222222; table-layout:fixed; width:100%; word-wrap:break-word;">

                    <tr>
                        <td width="20%" bgcolor="#030303" align="center" valign="top">
                            <?php echo date(d . " " . F . " " . Y . ", " . g . ":" . ia, $worked['timesent']) ?><br /><br /><a
                                href='profiles.php?id=<?php echo $ticket_class->id; ?>'><img
                                    src="<?php echo $avatar; ?>" height="100" width="100"
                                    style="border:1px solid #222222" /></a><br /><?php echo $ticket_class->formattedname; ?><br /><br />Posts:&nbsp;<?php echo prettynum($ticket_class->posts); ?>
                        </td>
                        <td width="80%" bgcolor="#090909" valign="top">
                            <?php echo BBCodeParse(strip_tags($worked['body'])); ?>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
        <?php if ($user_rank->gforum == 1) { ?>
            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <td class="contenthead">Edit Topic</td>
            </tr>
            <tr>
                <td class="contentcontent">
                    <table width="100%">
                        <form method="post">
                            <tr>
                                <td width="12%"><b>Subject:</b></td>
                                <td width="80%"><input type="text" name="subject" size="50"
                                        value="<?php echo $worked['subject']; ?>" /></td>
                            </tr>
                            <tr>
                                <td width="12%"><b>Message:</b></td>
                                <td width="80%"><textarea name="body" cols="66"
                                        rows="5"><?php echo strip_tags($worked['body']); ?></textarea></td>
                            </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td align="center"><input type="submit" name="submit" value="Edit Topic" /></td>
                            <?php if ($worked['locked'] == 0) { ?>
                                <td align="center"><input type="submit" name="lock" value="Lock Topic" /></td><?php } else { ?>
                                <td align="center"><input type="submit" name="unlock" value="Unlock Topic" /></td><?php } ?>
                            <?php if ($worked['sticky'] == 0) { ?>
                                <td align="center"><input type="submit" name="sticky" value="Sticky Topic" /></td>
                            <?php } else { ?>
                                <td align="center"><input type="submit" name="unsticky" value="Unsticky Topic" /></td><?php } ?>
                            <td align="center"><input type="submit" name="deletetopic" value="Delete Topic" /></td>
                        </tr>
                        </form>
                    </table>
                </td>
            </tr>
        <?php }
        ?>


        <?php
        //Pages Stuff
// find out how many rows are in the table
        $result = mysql_query("SELECT COUNT(*) FROM `gfreplies` WHERE `topicid` = '" . $_GET['id'] . "'");
        $r = mysql_fetch_row($result);
        $numrows = $r[0];

        // number of rows to show per page
        $rowsperpage = 10;
        // find out total pages
        $totalpages = ceil($numrows / $rowsperpage);
        if ($totalpages <= 0) {
            $totalpages = 1;
        } else {
            $totalpages = ceil($numrows / $rowsperpage);
        }

        // get the current page or set a default
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            // cast var as int
            $currentpage = (int) $_GET['page'];
        } else {
            // default page num
            $currentpage = 1;
        } // end if
// if current page is greater than total pages...
        if ($currentpage > $totalpages) {
            // set current page to last page
            $currentpage = $totalpages;
        } // end if
// if current page is less than first page...
        if ($currentpage < 1) {
            // set current page to first page
            $currentpage = 1;
        } // end if
// the offset of the list, based on current page
        $offset = ($currentpage - 1) * $rowsperpage;

        $resultrows = mysql_query("SELECT * from `gfreplies` WHERE `topicid` = '" . $_GET['id'] . "'");
        $workedrows = mysql_num_rows($resultrows);
        if ($workedrows > 0) {
            ?>
            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <td class="contenthead">Replies</td>
            </tr>
            <tr>
                <td class="contentcontent">
                    <?php
                    $result123 = mysql_query("SELECT * from `gfreplies` WHERE `topicid` = '" . $_GET['id'] . "' ORDER BY `timesent` ASC LIMIT $offset, $rowsperpage");
                    while ($row = mysql_fetch_array($result123)) {





                        $reply_class = new User($row['playerid']);

                        if ($reply_class->avatar != "") {
                            $avatar = $reply_class->avatar;
                        } else {
                            $avatar = "/images/no-avatar.png";
                        }
                        ?>

                        <table width="100%" cellpadding="5" cellspacing="0"
                            style="border:1px solid #222222; table-layout:fixed; width:100%; overflow:hidden; word-wrap:break-word;">
                            <tr>
                                <td width="20%" bgcolor="#030303" align="center" valign="top">
                                    <?php echo date(d . " " . F . " " . Y . ", " . g . ":" . ia, $row['timesent']) ?><br /><br /><a
                                        href='profiles.php?id=<?php echo $reply_class->id; ?>'><img src="<?php echo $avatar; ?>"
                                            height="100" width="100"
                                            style="border:1px solid #222222" /></a><br /><?php echo $reply_class->formattedname; ?><br /><br />Posts:&nbsp;<?php echo prettynum($reply_class->posts); ?><br /><?php if ($user_rank->gforum == 1) { ?>
                                        <form method="post"><input type="hidden" name="postid"
                                                value="<?php echo $row['postid']; ?>" /><input type="submit" name="delete"
                                                value="Delete Post" /></form><?php } ?>
                                </td>
                                <td width="80%" bgcolor="#090909" valign="top">
                                    <?php echo BBCodeParse(strip_tags($row['body'])); ?>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                        <?php
                    }
                    echo "<br />";
                    /*             * ****  build the pagination links ***** */
                    // range of num links to show
                    $range = 2;

                    // if not on page 1, don't show back links
                    if ($currentpage > 1) {
                        // show << link to go back to page 1
                        echo " <a href='{$_SERVER['PHP_SELF']}?id={$_GET['id']}&page=1'><<</a> ";
                    } // end if
// loop to show links to range of pages around current page
                    for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                        // if it's a valid page number...
                        if (($x > 0) && ($x <= $totalpages)) {
                            // if we're on current page...
                            if ($x == $currentpage) {
                                // 'highlight' it but don't make a link
                                echo " [<b>$x</b>] ";
                                // if not current page...
                            } else {
                                // make it a link
                                echo " <a href='{$_SERVER['PHP_SELF']}?id={$_GET['id']}&page=$x'>$x</a> ";
                            } // end else
                        } // end if
                    } // end for
// if not on last page, show forward and last page links
                    if ($currentpage < $totalpages) {
                        // echo forward link for lastpage
                        echo " <a href='{$_SERVER['PHP_SELF']}?id={$_GET['id']}&page=$totalpages'>>></a> ";
                    } // end if
                    /*             * **** end build pagination links ***** */
                    ?>
                </td>
            </tr>
            <?php
        }
        $result22 = mysql_query("SELECT * from `gftopics` WHERE `forumid` = '" . $_GET['id'] . "'");
        $worked22 = mysql_fetch_array($result22);
        if ($worked22['locked'] != 1) {
            ?>
            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <td class="contenthead">Add Reply</td>
            </tr>
            <tr>
                <td class="contentcontent">
                    <table width="100%">
                        <form method="post">
                            <tr>
                                <td width="12%"><b>Message:</b><br />[<a href="bbcode.php">BBCode</a>]</td>
                                <td width="80%"><textarea name="body" cols="66" rows="5"></textarea></td>
                            </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td align="center"><input type="submit" name="reply" value="Add Reply" /></td>
                        </tr>
                        </form>
                    </table>
                    <?php
        } else {
            ?>

            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <td class="contenthead">Add Reply</td>
            </tr>
            <tr>
                <td class="contentcontent">
                    <table width="100%">
                        <form method="post">
                            <tr>
                                <td align="center">This topic has been locked.</td>
                            </tr>
                        </form>
                    </table>

                    <?php
        }
        include("gangheaders.php");
        include("footer.php");
        ?>