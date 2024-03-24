<?php
include 'header.php';
$db->query("SELECT * FROM luckyboxes WHERE playerid = ?");
$db->execute(array(
    $user_class->id
));
$boxesrows = $db->num_rows();
$db->query("SELECT id FROM grpgusers ORDER BY todayskills DESC LIMIT 1");
$db->execute();
$worked = $db->fetch_row(true);
$hitman  = new User($worked['id']);
$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 0 ORDER BY `todaysexp` DESC LIMIT 1");
$db->execute();
$worked2 = $db->fetch_row(true);
$leveler    = new User($worked2['id']);
$db->query("SELECT COUNT(*) FROM fiftyfifty");
$db->execute();
$count5050 = $db->fetch_single();

$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 1");
$db->execute();
$rows = $db->fetch_row();

$admin_ids = array_map(function($a) {
    return $a['id'];
}, $rows);

?>
<?php
	$db->query("SELECT id, dailyKills FROM gangs ORDER BY dailyKills DESC LIMIT 3");
	$db->execute();
	$rows = $db->fetch_row();
	foreach($rows as $row){
		$gang[] = new formatGang($row['id']);
		$gangkills[] = $row['dailyKills'];
	}

	$ignoreGangs = "11, 31"; // admin gangs to exclude
	$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyKills` DESC LIMIT 1");
	$db->execute();
	$topKills = $db->fetch_row()[0]['id'];

	$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyCrimes` DESC LIMIT 1");
	$db->execute();
	$topCrimes = $db->fetch_row()[0]['id'];

	$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyBusts` DESC LIMIT 1");
	$db->execute();
	$topBusts = $db->fetch_row()[0]['id'];

	$db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyMugs` DESC LIMIT 1");
	$db->execute();
	$topMugs = $db->fetch_row()[0]['id'];

	$topKillsGang = new Gang($topKills);
	$topCrimesGang = new Gang($topCrimes);
	$topBustsGang = new Gang($topBusts);
	$topMugsGang = new Gang($topMugs);

	//<div class="gangotd_i">' . $gang->formattedname . '</div>

	echo '<div class="floaty" style="margin-top:-10px">
	<h3 style="margin:0;font-size: 1.5em;color:#fffffbbf;">Gang Of The Day</h3>
	<div class="gangotd" style="font-size: 1.15em">
		<div class="gangotd_i"><a href="viewgang.php?id=' . $topKillsGang->id . '"><img src="' . $topKillsGang->banner . '" width="80" /></a>' . $topKillsGang->name . '</br>Kills: ' . number_format($topKillsGang->dailyKills, 0) . '</br><span class="text-green">+100 Respect</span></div>
		<div class="gangotd_i"><a href="viewgang.php?id=' . $topCrimesGang->id . '"><img src="' . $topCrimesGang->banner . '" width="80" /></a>' . $topCrimesGang->name . '</br>Crimes: ' . prettynum($topCrimesGang->dailyCrimes) . '</br><span class="text-green">+100 Respect</span></div>
		<div class="gangotd_i"><a href="viewgang.php?id=' . $topBustsGang->id . '"><img src="' . $topBustsGang->banner . '" width="80" /></a>' . $topBustsGang->name . '</br>Busts: ' . number_format($topBustsGang->dailyBusts, 0) . '</br><span class="text-green">+100 Respect</span></div>
		<div class="gangotd_i"><a href="viewgang.php?id=' . $topMugsGang->id . '"><img src="' . $topMugsGang->banner . '" width="80" /></a>' . $topMugsGang->name . '</br>Mugs: ' . number_format($topMugsGang->dailyMugs, 0) . '</br><span class="text-green">+100 Respect</span></div>
	</div>
	</div>';

?>
<br>

	</div>

			</td>
	 	</table>
</div>
			<div class="spacer"></div>
		</div>




<?php
include 'footer.php';
?>