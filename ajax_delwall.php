<?php
include "ajax_header.php";

$user_class = new User($_SESSION['id']);

security($_POST['id'], 'num');

if ($user_class->admin == 1) {
    $db->query("DELETE FROM wallcomments WHERE id = ?");
    $db->execute([$_POST['id']]);
} else {
    $db->query("DELETE FROM wallcomments WHERE id = ? AND (userid = ? OR posterid = ?)");
    $db->execute([$_POST['id'], $_SESSION['id'], $_SESSION['id']]);
}
?>