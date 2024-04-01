<?php

include 'header.php';

if (isset($_GET['key']) && $_GET['key'] === 'wetesters') {
    $result = mysql_query("SELECT `id` FROM `grpgusers` WHERE `is_jail_bot` = 1");
    while ($line = mysql_fetch_array($result)) {
        echo  $line['id'];
        mysql_query("UPDATE `grpgusers` SET `jail` = 300 WHERE `id` = " . $line['id']);

        Send_Event(2, "Jail Bots Ran");
    }
}