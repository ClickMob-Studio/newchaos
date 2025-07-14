<?php

include "ajax_header.php";

$result = $db->query("SELECT username FROM grpgusers WHERE username LIKE ? LIMIT 5");
$db->execute(['%' . $_POST['query'] . '%']);
$rows = $db->fetch_row();

$resultsArray = array();
foreach ($rows as $row) {
    $resultsArray[] = $row['username'];
}

echo json_encode($resultsArray);
?>