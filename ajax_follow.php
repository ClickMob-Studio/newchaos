<?php
include "header.php";

$ftid = filter_input(INPUT_GET, 'ftid', FILTER_VALIDATE_INT);
$db->query("SELECT * FROM forumfollows WHERE userid = ? AND ftid = ?");
$db->execute([$user_class->id, $ftid]);
if ($db->num_rows() == 0) {
    perform_query("INSERT INTO forumfollows (userid, ftid) VALUES (?, ?)", [$user_class->id, $ftid]);
}

?>