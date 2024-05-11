<?php
require "header.php";
if ($user_class->admin < 1) {
    die();
}
$db->query("SELECT * FROM pack_logs ORDER BY `id` DESC LIMIT 25");
$db->execute();
$fetch = $db->fetch_row();
?>

<h1>Last 25 Packs Bought</h1>
<table>
    <?php
    foreach ($fetch as $row) {
        ?>
        <tr>
            <td><?= formatName($row['userid']); ?></td>
            <td><?= $row['pack']; ?></td>
            <td><?= number_format($row['credits_before']); ?></td>
            <td><?= number_format($row['credits_now']); ?></td>
        </tr>
        <?php

    }
    ?>
    </table>

    <?php
    include "footer.php";
    ?>