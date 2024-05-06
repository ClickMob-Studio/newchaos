<?php
include 'header.php';
$db = database::getInstance();

?>
<div class='box_top'>World Stats</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
// Total Mobsters
$totalmobsters = $db->query("SELECT COUNT(*) FROM `grpgusers` WHERE `rmdays`='0' AND admin <> 1");
$totalmobsters = $db->fetch_single();

// Total Respected Mobsters
$totalrm = $db->query("SELECT COUNT(*) FROM `grpgusers` WHERE `rmdays`!='0' AND admin <> 1");
$totalrm = $db->fetch_single();

$totalall = $totalmobsters + $totalrm;

// Total Money
$db->query("SELECT `money` FROM `grpgusers` WHERE `money` != '0' AND admin <> 1");
$money = 0;
while ($line = $db->fetch_row()) {
    $money += $line['money'];
}

// Total Points
$db->query("SELECT `points` FROM `grpgusers` WHERE `points` != '0' AND admin <> 1");
$points = 0;
while ($line = $db->fetch_row()) {
    $points += $line['points'];
}

// Total Crimes
$db->query("SELECT `crimesucceeded` FROM `grpgusers` WHERE `crimesucceeded` != '0' AND admin <> 1");
$crimes = 0;
while ($line = $db->fetch_row()) {
    $crimes += $line['crimesucceeded'];
}

// Total Kills
$db->query("SELECT `battlewon` FROM `grpgusers` WHERE `battlewon` != '0' AND admin <> 1");
$kills = 0;
while ($line = $db->fetch_row()) {
    $kills += $line['battlewon'];
}

// Total Deaths
$db->query("SELECT `battlelost` FROM `grpgusers` WHERE `battlelost` != '0' AND admin <> 1");
$deaths = 0;
while ($line = $db->fetch_row()) {
    $deaths += $line['battlelost'];
}

// Total Bank
$db->query("SELECT `bank` FROM `grpgusers` WHERE `bank` != '0' AND admin <> 1");
$bank = 0;
while ($line = $db->fetch_row()) {
    $bank += $line['bank'];
}

// Males
$male = $db->query("SELECT COUNT(*) FROM `grpgusers` WHERE `gender` = 'Male' AND admin <> 1");
$male = $db->fetch_single();
$malepercent = round(($male / $totalall) * 100);

// Females
$female = $db->query("SELECT COUNT(*) FROM `grpgusers` WHERE `gender` = 'Female' AND admin <> 1");
$female = $db->fetch_single();
$femalepercent = round(($female / $totalall) * 100);

// Gangs
$gangs = $db->query("SELECT COUNT(*) FROM `gangs`");
$gangs = $db->fetch_single();

// Total Gang Money
$db->query("SELECT `moneyvault` FROM `gangs` WHERE `moneyvault` != '0'");
$gangmoney = 0;
while ($line = $db->fetch_row()) {
    $gangmoney += $line['moneyvault'];
}
?>

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