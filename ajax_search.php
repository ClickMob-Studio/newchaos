<?php
include "ajax_header.php";

if (isset($_GET['term'])) {
    $term = htmlspecialchars($_GET['term'] ?? '', ENT_QUOTES, 'UTF-8');

    if (is_numeric($term)) {
        $db->query("SELECT id, username FROM grpgusers WHERE id = ?");
        $db->execute([$term]);
    } else {
        $db->query("SELECT id, username FROM grpgusers WHERE username LIKE ?");
        $db->execute(['%' . $term . '%']);
    }

    $users = $db->fetch_row();

    $userData = array();
    foreach ($users as $user) {
        $userData[] = array(
            "id" => $user['id'],
            "label" => $user['username'],
            "value" => $user['username']
        );
    }

    echo json_encode($userData);
}

?>