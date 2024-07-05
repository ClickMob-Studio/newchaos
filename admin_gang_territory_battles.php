<?php
require "header.php";
if($user_class->admin < 1) {
    exit();
}

$db->query("SELECT * FROM gang_territory_zone_battle WHERE is_complete = 1 ORDER BY `timme_started` DESC");
$db->execute();
$fetch = $db->fetch_row();

?>

<h1>Protection Rackety Battles</h1>
<table>
    <thead>
    <th>Attacking Gang</th>
    <th>Defending Gang</th>
    <th>View</th>
    </thead>
    <tbody>
    <?php foreach ($fetch as $row):?>
        <tr>
            <td>ID: <?php $row['attacking_gang_id'] ?></td>
            <td>ID: <?php $row['defending_gang_id'] ?></td>
            <td><a href="gang_territory_battle_result.php?id=<?php echo $row['id'] ?>">View</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

