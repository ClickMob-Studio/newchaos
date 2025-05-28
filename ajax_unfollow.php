<?php
include "header.php";

$ftid = filter_input(INPUT_GET, 'ftid', FILTER_VALIDATE_INT);
perform_query("DELETE FROM forumfollows WHERE userid = ? AND ftid = ?", [$user_class->id, $ftid]);
?>