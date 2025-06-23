<?php
include 'header.php';
if ($user_class->polled1active == 1) {
    if ($user_class->polled1 == 0) {
        if (isset($_POST['submit'])) {
            if ($_POST['poll1'] != "") {
                perform_query("UPDATE `grpgusers` SET `polled1` = '1', `points` = points + 20 WHERE `id` = ?", [$user_class->id]);
                perform_query("UPDATE `poll1` SET `votes` = votes + 1 WHERE `optionid` = ?", [$_POST['poll1']]);
                echo Message("You have successfully voted for the poll. Please enjoy this 20 points gift!<br /><br /><a href='index.php'>Home</a>");
                include 'footer.php';
                die();
            } else {
                echo Message("You didn't choose an answer.");
            }
        }
        ?>
        <tr valign="top">
            <td class="contentspacer"></td>
        </tr>
        <tr valign="top">
            <div class="contenthead">Poll</div>
        </tr>
        <tr valign="top">
            <div class="contentcontent">
                Welcome to the poll. We want to know what you want here at Chaos City, and to do this we open a poll! You will
                be granted 20 points if you answer the poll truthfully.<br /><br />
                <u><b>Question:&nbsp;</b><i>What level do you believe you should be able to prestige at?</i></u><br /><br />
                <form method="post">
                    <?php
                    $db->query("SELECT * FROM `poll1` ORDER BY `optionid` ASC");
                    $db->execute();
                    $rows = $db->fetch_row();
                    foreach ($rows as $line) {
                        echo '<input type="radio" name="poll1" value="' . $line['optionid'] . '" />&nbsp;' . $line['optionname'] . '<br />';
                    }
                    ?>
                    <br />
                    <input type="submit" name="submit" value="Submit Poll" />
                </form>
            </div>
            <td>
                <?php
    } else {
        echo Message("You have already voted on this poll.<br /><br /><a href='index.php'>Home</a>");
    }
} else {
    echo Message("There is currently no active poll running.<br /><br /><a href='index.php'>Home</a>");
}
include("footer.php");
?>