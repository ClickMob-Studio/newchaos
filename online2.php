<?php
include 'header.php';
?>
<div class='box_top'>Users Online</div>
<div class='box_middle'>
    <div class='pad'>
        <?php


        $db->query("SELECT * FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400 ORDER BY lastactive DESC");
        $db->execute();

        $rows = $db->fetch_row();

        $now = time();
        $past24hour = 0;
        $rows = array_filter($rows, function ($row) {
            global $now, $past24hour;

            if ($row['lastactive'] < $now - 3600) {
                $past24hour += 1;
                return false;
            }

            return true;
        });

        echo '<p>There has been ' . $past24hour . ' users online in the past 24 hours.</p>';
        echo '<p>There has been ' . count($rows) . ' users online in the past 1 Hour.</p>';
        echo '<div class="table-container">';
        echo '<table class="new_table" id="newtables" style="width:100%;">';
        ?>
        <th>Avatar</th>
        <th>Id</th>
        <th>Username</th>
        <th>Type</th>
        <th>Gang</th>
        <th>Level</th>
        <th>City</th>
        <th>Last Active</th>
        <?php

        foreach ($rows as $row) {
            $secondsago = time() - $row['lastactive'];
            $type = '<font color=#FFFFFF>No VIP Status</font>';
            if ($row['admin'] == 1) {
                $type = "<span style='color:red;'>ADMIN</span>";
            } else if ($line['rmdays'] > 0) {
                $type = "<font color=blue>VIP</font>";
            }
            $gang = new Gang($row['gang']);


            echo "<tr>
            <td><img src='{$row['avatar']}' height='50' width='50'></td>
            <td><b><i>{$row['id']}</i></b></td>
            <td>" . formatName($row['id']) . "</td>
            <td>{$type}</td>
            <td>{$gang->nobanner}</td>
            <td>{$row['level']}</td>
            <td>" . getCityNameByID($row['city']) . "</td>
            <td>" . howlongago($row['lastactive']) . "</td>
        </tr>";
        }

        echo '</table>';
        echo '</div>';
        include 'footer.php'
            ?>