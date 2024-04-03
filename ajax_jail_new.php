<?php

include "ajax_header.php";

$user_class = new User($_SESSION['id']);

//
// FETH JAIL USERS
//

if (isset($_GET['action'])  && $_GET['action'] == 'fetch_users') {
    $ignore = array($user_class->id);
    $ignore = implode(',', $ignore);

    $db->query("SELECT id, jail FROM grpgusers WHERE jail > 0 AND id NOT IN ($ignore) ORDER BY jail ASC");
    $db->execute();
    $rows = $db->fetch_row();

    foreach ($rows as $key => $row) {
        $rows[$key]['username'] = str_replace('</a>', '', preg_replace('/<a[^>]*>/', '', formatName($row['id'])));

        $time = floor($row['jail'] / 60) . 'm';
        $rows[$key]['time'] = $time;
    }

    echo json_encode($rows);
}