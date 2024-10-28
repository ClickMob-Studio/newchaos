<?php
include 'header.php';
?>

<div class='box_top'>Federal Jail</div>
<div class='box_middle'>
    <div class='pad'>
        <table style="width:100%;">
            <tr>
                <th style="text-align: left;">Avatar</th>
                <th style="text-align: left;">Name</th>
                <th style="text-align: left;">Last Active</th>
            </tr>

            <?php
            $db->query("SELECT id, avatar, lastactive FROM grpgusers WHERE banned > 0 id ASC");
            $db->execute();
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                $avatar = ($row['avatar']) ? '<img src="' . $row['avatar'] . '" style="width:75px;height:75px;" />' : '';
                ?>
                <tr>
                    <td><?= $avatar ?></td>
                    <td><?= formatName($row['id']) ?></td>
                    <td><?= howlongago($row['lastactive']) ?></td>
                </tr>
            <?php } ?>
        </table>

        <?php include 'footer.php'; ?>
