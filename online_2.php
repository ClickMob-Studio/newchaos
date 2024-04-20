<?php
include 'header.php';
?>
    <div class='box_top'>Users Online</div>
    <div class='box_middle'>
    <div class='pad'>
<?php
$result = mysql_query("SELECT * FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
$res = mysql_query("SELECT `id` FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400 ORDER BY lastactive DESC");
echo '<p>There has been ' . mysql_num_rows($res) . ' users online in the past 24 hours.</p>';
echo '<p>There has been ' . mysql_num_rows($result) . ' users online in the past 1 Hour.</p>';
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
while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    if (true) {
        $secondsago = time()-$line['lastactive'];
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
            <td>" . formatName($line['lineid']) . "</td>
            <td>{$type}</td>
            <td>{$gang->formattedname}</td>
            <td>{$line['level']}</td>
            <td>{$line['cityname']}</td>
            <td>".howlongago($line['lastactive'])."</td>
        </tr>";

    }
}
echo '</table>';
echo '</div>';
include 'footer.php'
?>