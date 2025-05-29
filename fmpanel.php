<?php
ini_set('session.save_path', '/home/mafiqzrl/tmp/sessions');
ini_set('session.gc_probability', 1);
include 'fmheader.php';
//Delete Post
if (isset($_POST['deletepost'])) {
    perform_query("DELETE FROM `freplies` WHERE `postid` = ?", [$_POST['postid']]);
    echo Message("The reported post was deleted.");
}
//Un-report Post
if (isset($_POST['unreport'])) {
    perform_query("UPDATE `freplies` SET `reported` = '0', `reporter` = '0' WHERE `postid` = ?", [$_POST['postid']]);
    echo Message("The post was un-reported.");
}
//Delete Thread
if (isset($_POST['deletetopic'])) {
    perform_query("DELETE FROM `ftopics` WHERE `forumid` = ?", [$_POST['postid']]);
    perform_query("DELETE FROM `freplies` WHERE `topicid` = ?", [$_POST['postid']]);
    echo Message("The reported topic was deleted.");
}
//Un-report Thread
if (isset($_POST['unreporttopic'])) {
    perform_query("UPDATE `ftopics` SET `reported` = '0', `reporter` = '0' WHERE `forumid` = ?", [$_POST['postid']]);
    echo Message("The topic was un-reported.");
}
if ($_GET['page'] == "bans") {
    ?>
    <tr>
        <td class="contentspacer"></td>
    </tr>
    <tr>
        <td class="contenthead">Forum Banned List</td>
    </tr>
    <tr>
        <td class="contentcontent">
            <table width="100%">
                <tr>
                    <td><b>Player</b></td>
                    <td><b>Days</b></td>
                    <td><b>Banned By</b></td>
                </tr>
                <?php
                $result = mysql_query("SELECT * FROM `bans` WHERE `type` = 'forum'");
                if (mysql_num_rows($result) > 0) {
                    while ($line = mysql_fetch_array($result)) {
                        $banned_user = new User($line['id']);
                        $banned_by = new User($line['bannedby']);
                        echo '<tr><td width="40%">' . $banned_user->formattedname . '</td><td width="20%">' . prettynum($line['days']) . '</td><td width="40%">' . $banned_by->formattedname . '</td></tr>';
                    }
                    echo "</table>";
                } else {
                    echo "</table>";
                    echo "<br />Noone is currently forum banned.";
                }
                ?>
        </td>
    </tr>
    <?php
    include 'footer.php';
    die();
}
if ($_GET['page'] == "reportpost") {
    ?>
    <tr>
        <td class="contentspacer"></td>
    </tr>
    <tr>
        <td class="contenthead">Reported Posts</td>
    </tr>
    <tr>
        <td class="contentcontent">
            <table width="100%" style="table-layout:fixed; width:100%; overflow:hidden; word-wrap:break-word;">
                <tr>
                    <td><b>Post</b></td>
                    <td><b>Reported By</b></td>
                    <td><b>Delete Post</b></td>
                    <td><b>Un-Report</b></td>
                </tr>
                <?php
                $result = mysql_query("SELECT * FROM `freplies` WHERE `reported` = '1'");
                if (mysql_num_rows($result) > 0) {
                    while ($line = mysql_fetch_array($result)) {
                        $output = BBCodeParse(strip_tags($line['body']));
                        $reported_by = new User($line['reporter']);
                        echo "<tr><td width='45%'>" . $output . "</td><td width='25%'>" . $reported_by->formattedname . "</td><td width='15%'><form method='post'><input type='hidden' name='postid' value='" . $line['postid'] . "' /><input type='submit' name='deletepost' value='Delete' /></form></td><td width='15%'><form method='post'><input type='hidden' name='postid' value='" . $line['postid'] . "' /><input type='submit' name='unreport' value='Un-Report' /></form></td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "</table>";
                    echo "<br />There are currently no reported posts.";
                }
                ?>
        </td>
    </tr>
    <?php
    include 'footer.php';
    die();
}
if ($_GET['page'] == "reportthread") {
    ?>
    <tr>
        <td class="contentspacer"></td>
    </tr>
    <tr>
        <td class="contenthead">Reported Threads</td>
    </tr>
    <tr>
        <td class="contentcontent">
            <table width="100%" style="table-layout:fixed; width:100%; overflow:hidden; word-wrap:break-word;">
                <tr>
                    <td><b>Thread</b></td>
                    <td><b>Reported By</b></td>
                    <td><b>Delete Post</b></td>
                    <td><b>Un-Report</b></td>
                </tr>
                <?php
                $result = mysql_query("SELECT * FROM `ftopics` WHERE `reported` = '1'");
                if (mysql_num_rows($result) > 0) {
                    while ($line = mysql_fetch_array($result)) {
                        $reported_by = new User($line['reporter']);
                        echo "<tr><td width='45%'><a href='viewpost.php?id=" . $line['forumid'] . "' target='_blank'>" . $line['subject'] . "</a></td><td width='25%'>" . $reported_by->formattedname . "</td><td width='15%'><form method='post'><input type='hidden' name='postid' value='" . $line['forumid'] . "' /><input type='submit' name='deletetopic' value='Delete' /></form></td><td width='15%'><form method='post'><input type='hidden' name='postid' value='" . $line['forumid'] . "' /><input type='submit' name='unreporttopic' value='Un-Report' /></form></td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "</table>";
                    echo "<br />There are currently no reported threads.";
                }
                ?>
        </td>
    </tr>
    <?php
    include 'footer.php';
    die();
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">FM Guide</td>
</tr>
<tr>
    <td class="contentcontent">
        <center>
            <i>This guide was written by NATAS. If you have any questions or wish to add something to it, don't hesitate
                to mail me.</i>
        </center>
        <br /><br />
        <hr width="95%" /><br />
        <b>
            <font color="#EE0000">Offense System</font>
        </b>
        <br /><br />
        <i>The following part of the guide states which punishment should be placed for each offense in the forums. If
            you are unsure of the one that should be placed please message an Admin with a detailed
            explanation.</i><br /><br />
        <i>All bannings and warnings should be noted in the Player Notes section of their profile. This should include
            the punishment, the date and your username.</i>
        <br /><br />
        <b><u>Hidden Links</u></b>
        <br />
        Hidden links are known as url's that are hidden with a misleading title. For example if i was to show this: <a
            href="http://mafiatown.org/profiles.php?id=1&rate=up" target="_blank">Free Stuff!</a> then thats classed as
        a hidden link. This is because i said you can get free stuff, however the link rates me up. For this offense the
        following actions should apply. For a first offense, a formal warning should be placed. For a second offense, a
        3 day forum ban should be placed. For a third offense, a 14 day forum ban should be placed. And for a fourth
        offense, a permanent forum ban should be placed (just set the days to 999,999).
        <br /><br />
        <b><u>Advertising</u></b>
        <br />
        Advertising is known as a player posting a link to their website or game. This could be done in the form of a
        link, an image or even just plain text. For this offense the following actions should be made. For a first
        offense, a formal warning should be placed. For a second offense, a 14 day forum ban should be placed. For a
        third offense, a permanent forum ban should be placed (just set the days to 999,999).
        <br /><br />
        <b><u>Spamming</u></b>
        <br />
        Spamming is known as a player purposely repeating the same post or thread. For example if i was to create five
        consecutive threads about the same thing it would be classed as spamming. For this offense the following actions
        should be placed. For a first offense, a 1 day forum ban should be placed, for a second offense, a 14 day forum
        ban should be placed. And for a third offense, a permanent forum ban should be placed (just set the days to
        999,999).
        <br /><br />
        <b><u>Verbal Abuse</u></b>
        <br />
        Verbal abuse is known as a player saying rude or inappripriate statements to another. This is usually in the
        form of extreme language. Depending on the seriousness of the abuse being caused the following actions should
        take place. For a small abusive statement, a formal warning should be placed and an appropriate apology to be
        written to the target. For an extreme abusive statement, the player should be forum banned for 3 days. For a
        second offense, the player should be forum banned for 14 days. And for a third offense a permanent forum ban
        should be placed (just set the days to 999,999).
    </td>
</tr>
<?php
include 'footer.php';
?>