<?php
error_reporting(-1); // reports all errors
ini_set("display_errors", "1"); // shows all errors
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

include "ajax_header.php";

if (empty($_SESSION['id']))
    echo 'empty';
else
    echo $_SESSION['id'];

$id = security($_POST['id']);
if ($id > 0) {

    echo $id;

    $sql = 
    $db->query('UPDATE `ads` SET `flagcount` = `flagcount` + 1, `flaggedby` = CONCAT(COALESCE(`flaggedby`,","), "'.$_SESSION['id'].',") WHERE `id`= '.$id.' AND (`flaggedby` IS NULL OR `flaggedby` NOT LIKE "%,'.$_SESSION['id'].',%")');
    $db->execute();

    echo '<div class="floaty" style="margin:0;background:rgba(0,128,0,.25);">';
        echo 'Report successful';
    echo '</div>';
}
?>