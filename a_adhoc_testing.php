<?php
include 'header.php';

$db->query("SELECT * FROM missions WHERE timestamp > 1713135600 AND completed = 'successful'");
$db->execute();
$rows = $db->fetch_row();

foreach ($rows as $row) {
    $gUser = new User($row['userid']);

    addToGangCompLeaderboard($gUser->gang, 'missions_complete', 1);
}
echo 'done';

include 'footer.php';
?>