<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
$db->query("SELECT days FROM bans WHERE id = ? AND type = 'gc'");
$db->execute(array(
    $user_class->id
));
if ($mins = $db->fetch_single())
    die();
if (isset($user_class->permban) && $user_class->permban > 0)
    die();
$db->query("SELECT * FROM gcusers WHERE userid = ?");
$db->execute(array(
    $_SESSION['id']
));
$r = $db->fetch_row(true);
$typing = isset($r['typing']) ? $r['typing'] : 0;
if ($_SESSION['id'] != 0) {
    $db->query("REPLACE INTO gcusers (userid, typing, lastseen) VALUES (?, ?, unix_timestamp())");
    $db->execute(array(
        $_SESSION['id'],
        $typing
    ));
}
$db->query("DELETE FROM gcusers WHERE lastseen < unix_timestamp()");
$db->execute();
if (isset($_POST['msg'])) {
    $avatar = $user_class->avatar;
    $msg = nl2br($_POST['msg']);
    $msg = strip_tags($msg);
    $db->query("UPDATE grpgusers SET globalchat = 1 WHERE id <> ?");
    $db->execute(array(
        $user_class->id
    ));

    set_last_active($user_class->id);

    $db->query("UPDATE gcusers SET typing = 0 WHERE userid = ?");
    $db->execute(array(
        $_SESSION['id']
    ));
    $db->query("INSERT INTO globalchat (playerid, timesent, body) VALUES (?, unix_timestamp(), ?)");
    $db->execute(array(
        $_SESSION['id'],
        $msg
    ));
    $newid = $db->insert_id();
    $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $msg);
    $banbutton = (($user_class->admin || $user_class->gm || $user_class->cm)) ? "<a href='?deltav=$newid'><button style='float:left;height:25px;'>Delete Post</button></a> " : "";
    print gcTalking() . "|-|-|" . $newid . "|-|-|";
    ?>
    <table class="flexcont" style="width:100%;">
        <tbody>
            <tr>
                <td class="flexele" style="border-right:thin solid #333;text-align:center;width:200;"><img
                        style="width:150px; height:150px; margin-bottom: 6px;"
                        src="<?php echo $avatar; ?>"><br><?php echo $user_class->formattedname; ?></td>
                <td class="flexele" style="padding:10px;">
                    <?php echo $msg; ?><br><br>NOW <br><br>
                </td>
            </tr>
        </tbody>
    </table>


    <?php
} elseif (isset($_GET['lastID'])) {
    $db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = ?");
    $db->execute(array(
        $_SESSION['id']
    ));
    $db->query("SELECT * from globalchat WHERE id > ? AND playerid <> ? ORDER BY timesent");
    $db->execute(array(
        $_GET['lastID'],
        $_SESSION['id']
    ));
    $rows = $db->fetch_row();
    $db->query("SELECT id FROM globalchat ORDER BY id DESC");
    $db->execute();
    $lastid = $db->fetch_row(true);
    if (isset($_GET['lastID']) && isset($lastid['id']) && $lastid['id'] == $_GET['lastID']) {
        print gcTalking();
        die();
    }

    if (isset($lastid['id'])) {
        print gcTalking() . "|-|-|" . $lastid['id'] . "|-|-|";
    }

    foreach ($rows as $row) {
        $reply_class = new User($row['playerid']);
        $avatar = ($reply_class->avatar != "") ? $reply_class->avatar : "/images/no-avatar.png";
        $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
        echo '<div class="floaty">';
        echo '<div class="flexcont" style="text-align:center;">';
        echo '<div class="flexele">';
        echo 'Now!';
        echo '</div>';
        echo '<div class="flexele">';
        echo (($user_class->admin || $user_class->gm || $user_class->cm) && (!$reply_class->admin && !$reply_class->gm)) ? '<a href="?tavban=' . $row['playerid'] . '&conf=' . $_SESSION['security'] . '">Ban User</a> ' : '';
        echo '</div>';
        echo '<div class="flexele">';
        echo ($user_class->admin || $user_class->gm || $user_class->cm) ? '<a href="?delgc=' . $lastid['id'] . '">Delete Post</a>' : '';
        echo '</div>';
        echo '<div class="flexele forumhover" onClick="addsmiley(\'[quote=' . $row['playerid'] . ']' . str_replace(array("\n", "\r"), array('', '\n'), $quotetext) . '[/quote]\\n\\n\');">';
        echo 'Quote';
        echo '</div>';
        echo '</div>';
        echo '<div class="flexcont">';
        echo '<div class="flexele" style="border-right:thin solid #333;text-align:center;">';
        echo '<img src="' . $avatar . '" height="150" width="150" style="border:1px solid #666666;margin-bottom: 6px;" />';
        echo '<br />';
        echo $reply_class->formattedname;
        echo '</div>';
        echo '<div class="flexele" style="flex:3;padding:10px;max-width:73%;overflow-wrap:break-word;">';
        echo BBCodeParse(stripslashes($row['body']));
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
?>