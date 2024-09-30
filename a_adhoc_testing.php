<?php

include 'header.php';


$db->query("SELECT id FROM grpgusers WHERE is_auto_user = 1 ORDER BY RAND() LIMIT 2");
$db->execute();
$rows = $db->fetch_row();

var_dump($rows); exit;