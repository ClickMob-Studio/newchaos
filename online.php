<?php
include 'header.php';
?>
<div class='box_top'>Users Online</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
$result = mysql_query("SELECT * FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC");
$res = mysql_query("SELECT * FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400 ORDER BY lastactive DESC");
echo '<p>There has been ' . mysql_num_rows($res) . ' users online in the past 24 hours.</p>';
echo '<table>';
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
		$secondsago = time()-$line['lastactive'];
		if (true) {
			$user_online = new User($line['id']);
            echo "<tr>
            <td><img src='{$user_online->avatar}' height='50' width='50'></td>
            <td><b><i>{$user_online->id}</i></b></td>
            <td>{$user_online->formattedname}</td>
            <td>{$user_online->type}</td>
            <td>{$user_online->formattedgang}</td>
            <td>{$user_online->level}</td>
            <td>{$user_online->cityname}</td>
            <td>".howlongago($user_online->lastactive)."</td>
        </tr>";
			
		}
	}
echo '</table>';

include 'footer.php'
?>