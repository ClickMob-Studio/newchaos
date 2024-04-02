<?php
include "ajax_header.php";
mysql_select_db('aa', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));
$user_class = new User($_SESSION['id']);
if (isset($_POST['hlid'])) {
    security($_POST['hlid']);
    $_POST['edittext'] = isset($_POST['edittext']) && is_string($_POST['edittext']) ? trim($_POST['edittext']) : null;
    if (isset($_POST['edittext']) && isset($_POST['hlid'])) {
        mysql_query("UPDATE gangtargetlist SET notes = '{$_POST['edittext']}' WHERE id = {$_POST['hlid']} AND gangid = $user_class->gang");
        print $_POST['edittext'];
    }
}
?>