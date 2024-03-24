<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
$play = ($user_class->admin != 1 && $user_class->gm != 1 && $user_class->eo != 1) ? " AND playerid = $user_class->id" : "";
if (isset($_POST['topic'])) {
    $_POST['topic'] = isset($_POST['topic']) && is_string($_POST['topic']) ? trim($_POST['topic']) : null;
    $_POST['edittext'] = isset($_POST['edittext']) && is_string($_POST['edittext']) ? trim($_POST['edittext']) : null;
    if (isset($_POST['edittext']) && isset($_POST['topic'])) {
        $db->query("UPDATE ftopics SET body = ? WHERE forumid = ?{$play}");
        $db->execute(array(
            $_POST['edittext'],
            $_POST['topic']
        ));
        print BBCodeParse(strip_tags(stripslashes($_POST['edittext'])));
    }
}
if (isset($_POST['reply'])) {
    $_POST['reply'] = isset($_POST['reply']) && is_string($_POST['reply']) ? trim($_POST['reply']) : null;
    $_POST['edittext'] = isset($_POST['edittext']) && is_string($_POST['edittext']) ? trim($_POST['edittext']) : null;
    if (isset($_POST['edittext']) && isset($_POST['reply'])) {
        $db->query("UPDATE freplies SET body = ? WHERE postid = ?{$play}");
        $db->execute(array(
            $_POST['edittext'],
            $_POST['reply']
        ));
        print BBCodeParse(strip_tags(stripslashes($_POST['edittext'])));
    }
}
?>