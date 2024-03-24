<?php
include 'header.php';
if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
    ?>
    <table id="newtables" style="width:100%;table-layout:fixed;">
        <tr>
            <th colspan="4">Gang Crime Log</th>
        </tr>
        <tr>
            <th>Name</th>
            <th>Reward</th>
            <th>Starter</th>
            <th>Time</th>
        </tr>
        <?php
        $result = mysql_query("SELECT * from gcrimelog WHERE gangid = $gang_class->id ORDER BY timestamp DESC");
        while ($row = mysql_fetch_array($result)) {
            if ($row['gangid'] == $gang_class->id) {
                $extra = new User($row['userid']);
                echo "
    <tr>
        <td>" . $row['text'] . "</td>
        <td>" . $row['reward'] . "</td>
        <td>" . $extra->formattedname . "</td>
        <td>" . date("d M Y, g:ia", $row['timestamp']) . "</td>
    </tr>
            ";
            }
        }
        ?>
    </table>
    </td></tr>
    <?php
} else {
    echo Message("You aren't in a gang.");
}
include("gangheaders.php");
include 'footer.php';
?>