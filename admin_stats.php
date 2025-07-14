<?php
if ($user_class->admin < 1) {
	exit();
}

require "header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$respected = 0;
$cash = 0;
$bank = 0;
$attacks = 0;
$crimes = 0;
$crimef = 0;
$points = 0;

$db->query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
$db->execute();
$players = $db->fetch_row();

$totalmobsters = count($players);

foreach ($players as $l) {
	$cash = $cash + $l['money'];
	$bank = $bank + $l['bank'];
	$attacks = $attacks + $l['battlewon'];
	$crimes = $crimes + $l['crimesucceeded'];
	$crimef = $crimef + $l['crimefailed'];
	$points = $points + $l['points'];
	if ($l['rmdays'] > 0) {
		$respected++;
	}
	if ($l['whichbank'] == 1) {
		$banks++;
	}
}
$apoints = $points / $totalmobsters;
$aitems = $items / $totalmobsters;


print "<tr><td class='contenthead'>World Stats</td></tr>
<tr><td class='contentcontent'>

<table width='100%' cellpadding='4' cellspacing='0'>
<tr>
	<td class='textl' width='15%'>Mobsters:</td>
	<td class='textr' width='35%'> $totalmobsters </td>
	<td class='textl'>Respected Mobsters:</td>
	<td class='textr'> {$respected} </td>
</tr>
<tr>
	<td class='textl' width='15%'>Total Money:</td>
	<td class='textr' width='35%'> \${$cash} </td>
	<td class='textl'>Total Attacks:</td>
	<td class='textr'> {$attacks} </td>
</tr>
<tr>
	<td class='textl'>Total Banked:</td>
	<td class='textr'> \${$bank} </td>
	<td class='textl' width='15%'>Total Banks:</td>
	<td class='textr' width='35%'> {$banks} </td>
</tr>
<tr>
	<td class='textl'>Successful Crimes:</td>
	<td class='textr'> {$crimes} </td>
	<td class='textl' width='15%'>Failed Crimes:</td>
	<td class='textr' width='35%'> {$crimef} </td>
</tr>
<tr>
	<td class='textl'>Total Gangs:</td>
	<td class='textr'> {$total['gangs']} </td>
	<td class='textl' width='15%'>Top Gang:</td>
	<td class='textr' width='35%'> {$tgang} </td>
</tr>
<tr>
	<td class='textl'>Total Points:</td>
	<td class='textr'> {$points} </td>
	<td class='textl' width='15%'>Average Points:</td>
	<td class='textr' width='35%'> {$apoints} </td>
</tr>
</table>
</td></tr>";
include 'footer.php';
?>