<?
include 'header.php';
echo '<tr><td class="contenthead">Users Online In The Last 5 Minutes</td></tr>';
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `lastactive` DESC");
echo '<table><tr><td class="contentcontent">';
	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$secondsago = time()-$line['lastactive'];
		if ($secondsago<=300) {
			$user_online = new User($line['id']);
            echo "<tr>
            <td><img src='{$user['avatar']}' height='50' width='50'></td>
            <td><b><i>{$user_online->id}</i></b></td>
            <td>{$user_online->formattedname}</td>
            <td>{$user_online->type}</td>
            <td>{$user_online->formattedgang}</td>
            <td>{$user_online->level}</td>
            <td>{$user_online->cityname}</td>
            <td>{$user->lastactive}</td>
        </tr>";
			
		}
	}
echo '</td></tr</table>';

include 'footer.php'
?>