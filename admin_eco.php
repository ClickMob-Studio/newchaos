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
        <th>Inactive Users</th>
        <th>Users</th>
    </thead>
<tbody>
    <?php foreach ($fetch as $row):?>
        <tr>
            <td><?php echo $row['timestamp']?></td>
            <td><?php echo $row['money']?></td>
            <td><?php echo $row['credits']?></td>
            <td><?php echo $row['points']?></td>
            <td><?php echo $row['inactive_users']?></td>
            <td><?php echo $row['users']?></td>
        </tr>
    <?php endforeach;?>
</tbody>
</table>
