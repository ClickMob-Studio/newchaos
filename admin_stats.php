<?php 
require "header.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if($user_class->admin < 1 ){
exit();
}

$cash= 0;
$respected = 0;
$banked = 0;
$banks = 0;
$attacks = 0;
$crimes = 0;
$crimef = 0;
$gangs= 0;
$tgang = "";
$points = 1;
$events = 0;
$pms = 0;
$items = 0;
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC");
$query1 = mysql_query("SELECT * FROM `gangs` ORDER BY `level` DESC LIMIT 1");
$query2 = mysql_query("SELECT * FROM `gangs` ORDER BY `level`");
$events = mysql_query("SELECT `id` FROM `events`");
$pms = mysql_query("SELECT `id` FROM `pms`");
$items = mysql_query("SELECT `id` FROM `items`");
$items = mysql_num_rows($items);
$events = mysql_num_rows($events);
$pms = mysql_num_rows($pms);
$gangs= mysql_num_rows($query2);
$gangs = mysql_fetch_array($query1);
$tgang = $gangs['name'];
$totalmobsters = mysql_num_rows($result);
while ($l = mysql_fetch_array($result)) {
	$cash = $cash+$l['money'];
	$bank = $bank+$l['bank'];
	$attacks = $attacks+$l['battlewon'];
	$crimes = $crimes+$l['crimesucceeded'];
	$crimef = $crimef+$l['crimefailed'];
	$points = $points+$l['points'];
	if ($l['rmdays'] > 0) {
		$respected++;
	}
	if ($l['whichbank'] == 1) {
		$banks++;
	}
}
$apoints = $points / $totalmobsters;
$aitems = $items / $totalmobsters;


print"<tr><td class='contenthead'>World Stats</td></tr>
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
<tr>
	<td class='textl'>Total Messages:</td>
	<td class='textr'> {$pms} </td>
	<td class='textl' width='15%'>Total Events:</td>
	<td class='textr' width='35%'> {$events} </td>
</tr>
<tr>
	<td class='textl'>Total Items:</td>
	<td class='textr'> {$items} </td>
	<td class='textl' width='15%'>Average Items </td>
	<td class='textr' width='35%'> {$aitems} </td>
</tr>
</table>
</td></tr>";
include 'footer.php';
?>