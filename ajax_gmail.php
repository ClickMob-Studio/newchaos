<?php
include "ajax_header.php";

$user_class = new User($_SESSION['id']);
$gangid = $user_class->gang;

$db->query("SELECT * FROM gmusers WHERE userid = ? LIMIT 1");
$db->execute([$_SESSION['id']]);
$r = $db->fetch_row(true);

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

    $db->query("INSERT INTO `gangmail` (`gangid`, `playerid`, `timesent`, `subject`, `body`) VALUES (?, ?, unix_timestamp(), 'whocares', ?)");
    $db->execute([$gangid, $_SESSION['id'], $msg]);
    $newid = $db->lastInsertId();
    print gcTalking(1, $gangid) . "|-|-|" . $newid . "|-|-|";
    $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $msg);
    echo '<div class="floaty">';
    ?>
    <table width="100%" style="word-wrap:break-word;">
        <tr>
            <td width="20%" style='background:rgba(0,0,0,.25);border:thin solid #000;' align="center">
                <?php echo "Now!"; ?><br /><br /><img src="<?php echo $avatar; ?>" height="150" width="150"
                    style="border:1px solid #666666;margin-bottom: 6px;" /><br /><?php echo $user_class->formattedname; ?>
            </td>
            <td width="80%" style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;' valign="top"
                id="chatdiv"><?php echo BBCodeParse($msg); ?></td>
        </tr>
    </table>
    <?php
    echo '</div>';
} elseif (isset($_GET['lastID'])) {
    perform_query("UPDATE `grpgusers` SET `gangmail` = '0' WHERE `id` = ?", [$_SESSION['id']]);

    $db->query("SELECT * FROM gangmail WHERE gangid = ? AND gmailid > ? ORDER BY timesent");
    $db->execute([$user_class->gang, $_GET['lastID']]);
    $result = $db->fetch_row();

    $db->query("SELECT gmailid FROM gangmail WHERE gangid = ? ORDER BY gmailid DESC LIMIT 1");
    $db->execute([$user_class->gang]);
    $lastid = $db->fetch_single();
    if ($lastid == $_GET['lastID'])
        die(gcTalking(1, $gangid));

    print gcTalking(1, $gangid) . "|-|-|" . $lastid['gmailid'] . "|-|-|";

    foreach ($result as $row) {
        $reply_class = new User($row['playerid']);
        $avatar = ($reply_class->avatar != "") ? $reply_class->avatar : "/images/no-avatar.png";
        $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
        ?>
        <table width="100%" style="word-wrap:break-word;">
            <tr>
                <td width="20%" style='background:rgba(0,0,0,.25);border:thin solid #000;' align="center">
                    <?php echo "Now!"; ?><br /><br /><img src="<?php echo $avatar; ?>" height="150" width="150"
                        style="border:1px solid #666666;margin-bottom: 6px;" /><br /><?php echo $reply_class->formattedname; ?>
                </td>
                <td width="80%" style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;' valign="top"
                    id="chatdiv"><?php echo BBCodeParse($row['body']); ?></td>
            </tr>
        </table>
        <?php
    }
}
?>