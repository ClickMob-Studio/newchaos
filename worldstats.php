<?php
include 'header.php';
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `rmdays`='0' AND admin <> 1");
$totalmobsters = mysql_num_rows($result);
$result2 = mysql_query("SELECT * FROM `grpgusers` WHERE `rmdays`!='0' AND admin <> 1");
$totalrm = mysql_num_rows($result2);
$totalall = $totalmobsters + $totalrm;
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `money` != '0' AND admin <> 1");
$money = 0;
while($line = mysql_fetch_array($result)) {
	$money = $money + $line['money'];
}
//Total Points Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `points` != '0' AND admin <> 1");
$points = 0;
while($line = mysql_fetch_array($result)) {
	$points = $points + $line['points'];
}
//Total Crimes Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `crimesucceeded` != '0' AND admin <> 1");
$crimes = 0;
while($line = mysql_fetch_array($result)) {
	$crimes = $crimes + $line['crimesucceeded'];
}
//Total Kills Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `battlewon` != '0' AND admin <> 1");
$kills = 0;
while($line = mysql_fetch_array($result)) {
	$kills = $kills + $line['battlewon'];
}
//Total Deaths Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `battlelost` != '0' AND admin <> 1");
$deaths = 0;
while($line = mysql_fetch_array($result)) {
	$deaths = $deaths + $line['battlelost'];
}
//Total Bank Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `bank` != '0' AND admin <> 1");
$bank = 0;
while($line = mysql_fetch_array($result)) {
	$bank = $bank + $line['bank'];
}
//Male Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `gender` = 'Male' AND admin <> 1");
$male = mysql_num_rows($result);
$malepercent = round(($male / $totalall) * 100);
//Female Stuff
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `gender` = 'Female' AND admin <> 1");
$female = mysql_num_rows($result);
$femalepercent = round(($female / $totalall) * 100);
//Gangs Stuff
$result = mysql_query("SELECT * FROM `gangs`");
$gangs = mysql_num_rows($result);
//Total Gang Money Stuff
$result = mysql_query("SELECT * FROM `gangs` WHERE `moneyvault` != '0'");
$gangmoney = 0;
while($line = mysql_fetch_array($result)) {
	$gangmoney = $gangmoney + $line['moneyvault'];
}
?>
<h3>World Stats</h3>
<hr>
<tr><td class="contentcontent">
<table id="newtables" style="width:100%;table-layout:fixed;">
<tr>
	<th>Mobsters:</th>
	<td><?php echo prettynum($totalmobsters) ?></td>
	<th>Respected Mobsters:</th>
	<td><?php echo prettynum($totalrm) ?></td>
</tr>
<tr>
	<th>Male:</th>
	<td><?php echo prettynum($male)."&nbsp;[".$malepercent."%]"; ?></td>
	<th>Female:</th>
	<td><?php echo prettynum($female)."&nbsp;[".$femalepercent."%]"; ?></td>
</tr>
<tr>
	<th>Total Money:</th>
	<td>$<?php echo prettynum($money) ?></td>
	<th>Total Bank:</th>
	<td>$<?php echo prettynum($bank) ?></td>
</tr>
<tr>
	<th>Points:</th>
	<td><?php echo prettynum($points) ?></td>
	<th>Crimes:</th>
	<td><?php echo prettynum($crimes) ?></td>
</tr>
<tr>
	<th>Total Kills:</th>
	<td><?php echo prettynum($kills) ?></td>
	<th>Total Deaths:</th>
	<td><?php echo prettynum($deaths) ?></td>
</tr>
<tr>
	<th>Gangs:</th>
	<td><?php echo prettynum($gangs) ?></td>
	<th>Gang Money:</th>
	<td>$<?php echo prettynum($gangmoney) ?></td>
</tr>
</table>
</td></tr>
<?php
include 'footer.php';
?>