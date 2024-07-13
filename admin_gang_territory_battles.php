<?php
require "header.php";
if($user_class->admin < 1) {
    exit();
}

$db->query("SELECT * FROM gang_territory_zone_battle WHERE is_complete = 1 ORDER BY `time_started` DESC LIMIT 10");
$db->execute();
$fetch = $db->fetch_row();

?>

<h1>Protection Racket Battles</h1>
<table>
    <thead>
    <th>Attacking Gang</th>
    <th>Defending Gang</th>
    <th>Time Started</th>
    <th>View</th>
    </thead>
    <tbody>
    <?php foreach ($fetch as $row):?>
        <?php
        $attackingGang = new Gang($row['attacking_gang_id']);
        $defendingGang = new Gang($row['defending_gang_id']);
        ?>
        <tr>
            <td><?php echo $attackingGang->name ?></td>
            <td><?php echo $defendingGang->name ?></td>
            <td><?php echo date( "d/m/Y H", $row['time_started']); ?></td>
            <td><a href="gang_territory_battle_result.php?id=<?php echo $row['id'] ?>">View</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

