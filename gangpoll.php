<?php
include 'header.php';

if ($user_class->gang == 0) {
    echo Message("You aren't in a gang.");
}

$gang_class = new Gang($user_class->gang);
include("gangheaders.php");
$result = mysql_query("SELECT * from `gangs` WHERE `id` = '" . $user_class->gang . "'");
$worked = mysql_fetch_array($result);



if (isset($_POST['submit'])) {
    if ($_POST['poll1'] != "") {
        perform_query("UPDATE `grpgusers` SET `gangpoll` = '1' WHERE `id` = ?", [$user_class->id]);
        perform_query("UPDATE `gangpolls` SET `votes` = votes+1 AND `yes` = yes+1 WHERE `gangid`=?", [$gang_class->id]);
        echo Message("You have successfully voted for your gangs poll!<br /><br /><a href='index.php'>Home</a>");
        include 'footer.php';
        die();
    } else {
        echo Message("You didn't choose an answer.");
    }
    if ($_POST['poll2'] != "") {
        perform_query("UPDATE `grpgusers` SET `gangpoll` = '1' WHERE `id` = ?", [$user_class->id]);
        perform_query("UPDATE `gangpolls` SET `votes` = votes+1 AND `no` = no+1 WHERE `gangid`=?", [$gang_class->id]);
        echo Message("You have successfully voted for your gangs poll!<br /><br /><a href='index.php'>Home</a>");
        include 'footer.php';
        die();
    } else {
        echo Message("You didn't choose an answer.");
    }
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Gang Polls</td>
</tr>
<tr>
    <td class="contentcontent">
        <table width="100%">
            <form method="post">
                <?php
                $result = mysql_query("SELECT * FROM `gangpolls` WHERE `gangid`='" . $gang_class->id . "'");
                while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
                    echo '
<tr><td>
<u><b>Question:&nbsp;</b><i>' . $line['question'] . '</i></u><br /><br />
<input type="radio" name="poll1" value="' . $line['yanswer'] . '" />&nbsp;' . $line['yanswer'] . '<br />
<input type="radio" name="poll2" value="' . $line['nanswer'] . '" />&nbsp;' . $line['nanswer'] . '<br /></td>
</tr>';
                    ?>
                    <br />
                    <tr>
                        <td>
                            <input type="submit" name="submit" value="Submit Poll" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </form>
        </table>
</tr>
</td>
<?php
include 'footer.php';
?>