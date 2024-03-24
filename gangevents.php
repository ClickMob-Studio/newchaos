<?php
include 'header.php';
include("gangheaders.php");
    $gang_class = new Gang($user_class->gang);
    ?>
    <table id="newtables" style="width:100%;">
		<tr>
            <th colspan="2">Gang Events</th>
        </tr>
        <tr>
            <th>Description</th>
            <th>Time</th>
        </tr>
        <?php
		$db->query("SELECT * FROM gangevents WHERE gang = ? ORDER BY timesent DESC LIMIT 30");
		$db->execute(array(
			$user_class->gang
		));
		$rows = $db->fetch_row();
		foreach($rows as $row){
			$extra_user = new User($row['extra']);
			$text = str_replace('[-_USERID_-]', $extra_user->formattedname, $row['text']);
			echo'<tr>';
				echo'<td width="70%">' . $text . '</td>';
				echo'<td width="30%">' . date("d M Y, g:ia", $row['timesent']) . '</td>';
			echo'</tr>';
        }
        ?>
    </table>
<?php
include 'footer.php';
?>