<?php
require "header.php";
if($user_class->admin < 1)
    exit();

$db->query("SELECT COUNT(ID) AS overall, userid, reason FROM `autoclick_detection` GROUP BY userid, reason;");
$db->execute();
$fetch = $db->fetch_row();
?>

<h1>Click Checks</h1>
<table>
    <thead>
        <th>User</th>
        <th>Reason</th>
        <th>Violation Count</th>
    </thead>
    <tbody>
    <?php foreach ($fetch as $row):?>
        <tr>
            <td><?php echo formatName($row['userid']) ?></td>
            <td><?php echo $row['reason']?></td>
            <td><?php echo number_format($row['overall'])?></td>
    <?php endforeach;?>
    </tbody>
</table>
