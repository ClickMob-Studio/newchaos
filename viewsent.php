<?php
include 'header.php';
security($_GET['id']);
$db->query("SELECT * FROM bans WHERE type = 'mail' AND id = ?");
$db->execute(array(
    $user_class->id
));
$row = $db->fetch_row(true);
if (!empty($row))
    diefun('&nbsp;You have been mail banned for ' . $row['days'] . ' days.');
$db->query("SELECT * FROM pms WHERE id = ?");
$db->execute(array(
    $_GET['id']
));
$row = $db->fetch_row(true);
print mailHeader()."
        <table id='newtables' style='width:100%;table-layout:fixed;'>";
            if (!empty($_GET['id'])) {
                if ($row['from'] == $user_class->id) {
                    $string = strip_tags($row['msgtext']);
                    $output = BBCodeParse($string);
                    echo "
	<tr>
                    <th>" . formatName($row['from']) . "</td>
                    <th>{$row['subject']}</td>
                    <th>" . date("F d, Y g:i:sa", $row['timesent']) . "</td>
	</tr>
                    <tr>
                        <td colspan='3'><br /><br />$output<br /><br /><br /></td>
                    </tr>
                </table>
            </td></tr>";
    }
}
include 'footer.php';
?>