<?php
include 'header.php';
?>

<div class='box_top'>Federal Jail</div>
<div class='box_middle'>
    <div class='pad'>
        <table style="width:100%;">
            <tr>
                <th >Player</th>
                <th >Type</th>
            </tr>

            <?php
            $db->query("SELECT * FROM bans");
            $db->execute();
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                ?>
                <tr>
                    <td><?= formatName($row['id']) ?></td>
                    <td>Permanent Ban</td>
                </tr>
            <?php } ?>
        </table>

        <?php include 'footer.php'; ?>
