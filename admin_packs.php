<?php
require "header.php";
if ($user_class->admin < 1) {
    die();
}
$db->query("SELECT * FROM pack_logs ORDER BY `id` DESC LIMIT 25");
$db->execute();
$fetch = $db->fetch_row();
?>
<div class="container mt-3">
    <h1>Search Packs by User ID</h1>
    <form method="GET" class="mb-3">
        <div class="input-group mb-3">
            <input type="text" name="userid" class="form-control" placeholder="Enter User ID" required>
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <?php 
    if(isset($_GET['userid'])){
    $userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;


    $query = "SELECT * FROM pack_logs WHERE userid = ? ORDER BY `id` DESC";
    $db->query($query);

$db->execute(array($userid));
$fetch = $db->fetch_row();
if (!empty($fetch)) {
    echo '<h1>Last 25 Packs Bought</h1>';
    echo '<table class="table" style="color:white">';
    echo '<thead><tr><th>User ID</th><th>Pack</th><th>Credits Before</th><th>Credits After</th></tr></thead>';
    echo '<tbody>';
    foreach ($fetch as $row) {
        echo '<tr>';
        echo '<td>' . formatName($row['userid']) . '</td>';
        echo '<td>' . $row['pack'] . '</td>';
        echo '<td>' . number_format($row['credits_before']) . '</td>';
        echo '<td>' . number_format($row['credits_now']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No results found.</p>';
}
?>

</div> 
<?php
}?>

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