<?php
include 'header.php';

if ($user_class->id != 174) {
    header('location: index.php');
}

file_put_contents('test.json', json_encode(array(5, 10, 15)));