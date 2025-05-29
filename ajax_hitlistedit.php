<?php
include "ajax_header.php";

$user_class = new User($_SESSION['id']);
if (isset($_POST['hlid'])) {
    security($_POST['hlid']);
    $_POST['edittext'] = isset($_POST['edittext']) && is_string($_POST['edittext']) ? trim($_POST['edittext']) : null;
    if (isset($_POST['edittext']) && isset($_POST['hlid'])) {
        perform_query("UPDATE gangtargetlist SET notes = ? WHERE id = ? AND gangid = ?", [$_POST['edittext'], $_POST['hlid'], $user_class->gang]);
        print $_POST['edittext'];
    }
}
?>