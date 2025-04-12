<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
if ($user_class->permban > 0)
    die();
$db->query("SELECT * FROM gcusers WHERE userid = ?");
$db->execute(array(
    $user_class->id
));
$r = $db->fetch_row(true);
$typing = isset($r['typing']) ? $r['typing'] : 0;
if ($user_class->id != 682) {
    $db->query("REPLACE INTO gcusers (userid, typing, lastseen) VALUES (?, ?, unix_timestamp())");
    $db->execute(array(
        $user_class->id,
        $typing
    ));
}
$db->query("DELETE FROM gcusers WHERE lastseen < unix_timestamp()");
$db->execute();
if (isset($_POST['msg'])) {
    $avatar = $user_class->avatar;
    $msg = nl2br($_POST['msg']);
    $db->query("UPDATE grpgusers SET globalchat = 1 WHERE id <> ?");
    $db->execute(array(
        $user_class->id
    ));

    set_last_active($user_class->id);

    $db->query("UPDATE gcusers SET typing = 0 WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
    $db->query("INSERT INTO globalchat (playerid, timesent, body) VALUES (?, unix_timestamp(), ?)");
    $db->execute(array(
        $user_class->id,
        $msg
    ));
    $newid = $db->insert_id();
    print gcTalking() . "|-|-|" . $newid . "|-|-|";
    ?>
    <table width="100%" style="word-wrap:break-word;">
        <tr>
            <td width="20%" style='background:rgba(0,0,0,.25);border:thin solid #000;' align="center">
                <?php echo "Now!"; ?><br /><br /><img src="<?php echo $avatar; ?>" height="150" width="150"
                    style="border:1px solid #666666" /><br /><?php echo $user_class->formattedname; ?></td>
            <td width="80%" style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;' valign="top"
                id="chatdiv"><?php echo BBCodeParse($msg); ?></td>
        </tr>
    </table>
    <?php
} elseif (isset($_GET['lastID'])) {
    $db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));

    $ignoredPlayerIds = array();
    $db->query("SELECT blocked FROM ignorelist WHERE blocker = $user_class->id");
    $db->execute();
    $ignored = $db->fetch_row();

    foreach ($ignored as $ignore) {
        $ignoredPlayerIds[] = $ignore['blocked'];
    }

    if (count($ignoredPlayerIds)) {
        $db->query("SELECT * FROM globalchat WHERE id > ? AND playerid <> ? AND playerid NOT IN (" . implode(',', $ignoredPlayerIds) . ") ORDER BY timesent DESC LIMIT 80");
    } else {
        $db->query("SELECT * FROM globalchat WHERE id > ? AND playerid <> ? ORDER BY timesent");
    }
    $db->execute(array(
        $_GET['lastID'],
        $user_class->id
    ));
    $rows = $db->fetch_row();
    $db->query("SELECT id FROM globalchat ORDER BY id DESC");
    $db->execute();
    $lastid = $db->fetch_single();
    if ($lastid['id'] == $_GET['lastID']) {
        print gcTalking();
        die();
    }
    print gcTalking() . "|-|-|" . $lastid['id'] . "|-|-|";
    foreach ($rows as $row) {
        $reply_class = new User($row['playerid']);
        $avatar = ($reply_class->avatar != "") ? $reply_class->avatar : "/images/no-avatar.png";
        ?>
        <table width="100%" style="word-wrap:break-word;">
            <tr>
                <td width="20%" style='background:rgba(0,0,0,.25);border:thin solid #000;' align="center">
                    <?php echo "Now!"; ?><br /><br /><img src="<?php echo $avatar; ?>" height="150" width="150"
                        style="border:1px solid #666666" /><br /><?php echo $reply_class->formattedname; ?></td>
                <td width="80%" style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;' valign="top"
                    id="chatdiv"><?php echo BBCodeParse($row['body']); ?></td>
            </tr>
        </table>
        <?php
    }
}
?>