<?php
//$link = mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt');
//$db_selected = mysql_select_db('ml2', $link);

$query = sprintf("SELECT username FROM grpgusers WHERE username LIKE '%%%s%%' LIMIT 5",
                 mysql_real_escape_string($_POST['query']));
$result = mysql_query($query);

$resultsArray = array();
while ($row = mysql_fetch_assoc($result)) {
    $resultsArray[] = $row['username'];
}

echo json_encode($resultsArray);
?>