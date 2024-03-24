<?php

$link = mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt');
$db_selected = mysql_select_db('ml2', $link);
header('Content-Type: application/json');

// Ensure a connection to the database is established
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

$raidId = isset($_POST['raidId']) ? intval($_POST['raidId']) : 0;

if ($raidId) {
    // Fetch participants for the specified raid
    $query = "SELECT * FROM raid_participants WHERE raid_id = $raidId";
    
    $result = mysql_query($query);

    if ($result) {
        // Check if there are any rows returned
        if (mysql_num_rows($result) > 0) {
            $participants = [];

            while ($row = mysql_fetch_assoc($result)) {
                $participants[] = $row;
            }

            echo json_encode($participants);
        } else {
            echo json_encode(['error' => 'No participants found for the specified raid']);
        }
    } else {
        echo json_encode(['error' => 'Error executing the query: ' . mysql_error()]);
    }
} else {
    echo json_encode(['error' => 'Invalid raid ID']);
}

// Close the connection
mysql_close($link);
?>
