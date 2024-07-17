<?php
require "header.php";
$two_weeks_ago = time() - (14 * 24 * 60 * 60);

// Prepare the SQL query to delete records older than two weeks
$sql = "DELETE FROM events WHERE timesent < $two_weeks_ago";
mysql_query($sql);