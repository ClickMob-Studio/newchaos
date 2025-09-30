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
} elseif (isset($_GET['lastID'])) {
    $db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = ?");
    $db->execute(array(
        $_SESSION['id']
    ));
    $db->query("SELECT * from globalchat WHERE id > ? ORDER BY timesent");
    $db->execute([$_GET['lastID']]);
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
        echo renderChatMessage($row);
    }
}
?>