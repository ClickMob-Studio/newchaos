<?php
include "ajax_header.php";

mysql_select_db('chaoscit_game', mysql_connect('localhost', 'chaoscit_user', '3lrKBlrfMGl2ic14'));
$user_class = new User($_SESSION['id']);
$gangid = $user_class->gang;
$q = mysql_query("SELECT * FROM gmusers WHERE userid = {$_SESSION['id']}");
$r = mysql_fetch_array($q);
$typing = isset($r['typing']) ? $r['typing'] : 0;

perform_query("REPLACE INTO gmusers (userid, typing, lastseen, gang) VALUES (?, ?, unix_timestamp(), ?)", [$_SESSION['id'], $typing, $gangid]);
perform_query("DELETE FROM gmusers WHERE lastseen < unix_timestamp()");
if (isset($_POST['msg'])) {
    $avatar = $user_class->avatar;
    $msg = $_POST['msg'];
    $msg = strip_tags($msg);
    $msg = nl2br($msg);
    $msg = addslashes($msg);

    perform_query("UPDATE grpgusers SET gangmail = 1 WHERE gang = ?", [$gangid]);

    $result = mysql_query("INSERT INTO `gangmail` (`gangid`, `playerid`, `timesent`, `subject`, `body`) VALUES ('$gangid' ,'{$_SESSION['id']}', unix_timestamp(), 'whocares', '$msg')");
    $newid = mysql_insert_id();
    print gcTalking(1, $gangid) . "|-|-|" . $newid . "|-|-|";
    $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $msg);
    echo '<div class="floaty">';
    ?>
    <table width="100%" style="word-wrap:break-word;">
        <tr>
            <td width="20%" style='background:rgba(0,0,0,.25);border:thin solid #000;' align="center">
                <?php echo "Now!"; ?><br /><br /><img src="<?php echo $avatar; ?>" height="150" width="150"
                    style="border:1px solid #666666" /><br /><?php echo $user_class->formattedname; ?>
            </td>
            <td width="80%" style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;' valign="top"
                id="chatdiv"><?php echo BBCodeParse($msg); ?></td>
        </tr>
    </table>
    <?php
    echo '</div>';
} elseif (isset($_GET['lastID'])) {
    perform_query("UPDATE `grpgusers` SET `gangmail` = '0' WHERE `id` = ?", [$_SESSION['id']]);
    $result = mysql_query("SELECT * from `gangmail` WHERE `gangid` = '$user_class->gang' AND gmailid>{$_GET['lastID']} ORDER BY `timesent`");
    $lastid = mysql_fetch_array(mysql_query("SELECT gmailid FROM gangmail WHERE gangid = $user_class->gang ORDER BY gmailid DESC"));
    if ($lastid['gmailid'] == $_GET['lastID'])
        die(gcTalking(1, $gangid));
    print gcTalking(1, $gangid) . "|-|-|" . $lastid['gmailid'] . "|-|-|";
    while ($row = mysql_fetch_array($result)) {
        $reply_class = new User($row['playerid']);
        $avatar = ($reply_class->avatar != "") ? $reply_class->avatar : "/images/no-avatar.png";
        $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
        ?>
        <table width="100%" style="word-wrap:break-word;">
            <tr>
                <td width="20%" style='background:rgba(0,0,0,.25);border:thin solid #000;' align="center">
                    <?php echo "Now!"; ?><br /><br /><img src="<?php echo $avatar; ?>" height="150" width="150"
                        style="border:1px solid #666666" /><br /><?php echo $reply_class->formattedname; ?>
                </td>
                <td width="80%" style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;' valign="top"
                    id="chatdiv"><?php echo BBCodeParse($row['body']); ?></td>
            </tr>
        </table>
        <?php
    }
}
?>