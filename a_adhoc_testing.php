<?php

include 'header.php';


$newPassword = 'SexyShine123!';
$password = sha1($newPassword);

$db->query("UPDATE grpgusers SET password = ? WHERE id = ?");
$db->execute(array(
    $password,
    665
));
