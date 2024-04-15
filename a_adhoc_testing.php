<?php
include 'header.php';

$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC LIMIT 2");
$db->execute();
$dailyRows = $db->fetch_row();

$i = 1;
foreach ($dailyRows as $row) {
    Send_Event(2, $i . ' ' . $row['gang_id'], 2);

    if ($i == 1) {
        $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = 2");
        $db->execute();

        Give_Item(163, 2, 1);
        Give_Item(42, 2, 1);
    } else {
        Give_Item(42, 2, 1);
    }


    $i++;
}
echo 'done';

include 'footer.php';
?>