<?php
include 'header.php';
?>
<div class='box_top'>Users Online</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        $db->query("SELECT * FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
        $pastHour = $db->fetch_row();

        $db->query("SELECT COUNT(*) AS count FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400");
        $pastDayCount = $db->fetch_single();

        echo '<p>There has been ' . $pastDayCount . ' users online in the past 24 hours.</p>';
        echo '<p>There has been ' . count($pastHour) . ' users online in the past 1 Hour.</p>';
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
        foreach ($pastHour as $line) {
            if (true) {
                $secondsago = time() - $line['lastactive'];
                $type = '<font color=#FFFFFF>No VIP Status</font>';
                if ($line['admin'] == 1) {
                    $type = "<span style='color:red;'>ADMIN</span>";
                } else if ($line['rmdays'] > 0) {
                    $type = "<font color=blue>VIP</font>";
                }
                $gang = new Gang($line['gang']);


                echo "<tr>
            <td><img src='{$line['avatar']}' height='50' width='50'></td>
            <td><b><i>{$line['id']}</i></b></td>
            <td>" . formatName($line['id']) . "</td>
            <td>{$type}</td>
            <td>" . (isset($gang) ? $gang->nobanner : '') . "</td>
            <td>{$line['level']}</td>
            <td>" . getCityNameByID($line['city']) . "</td>
            <td>" . howlongago($line['lastactive']) . "</td>
        </tr>";

            }
        }
        echo '</table>';
        echo '</div>';
        include 'footer.php'
            ?>