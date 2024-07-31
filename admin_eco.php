<?php 
require "header.php";
if($user_class->admin < 1)
exit();

$db->query("SELECT * FROM daily_eco ORDER BY `timestamp` DESC");
$db->execute();
$fetch = $db->fetch_row();
?>

<h1>Daily Economy</h1>
<table>
    <thead>
        <th>TimeStamp</th>
        <th>Money</th>
        <th>Credits</th>
        <th>Points</th>
        <th>Raid Tokens</th>
        <th>Inactive Users</th>
        <th>Users</th>
    </thead>
<tbody>
    <?php foreach ($fetch as $row):?>
        <tr>
            <td><?php echo date("d-m-Y", $row['timestamp'])?></td>
            <td>$<?php echo number_format($row['money'])?></td>
            <td><?php echo number_format($row['credits'])?></td>
            <td><?php echo number_format($row['points'])?></td>
            <td><?php echo number_format($row['raidtokens'])?></td>
            <td><?php echo $row['inactive_users']?></td>
            <td><?php echo $row['users']?></td>
        </tr>
    <?php endforeach;?>
</tbody>
</table>
