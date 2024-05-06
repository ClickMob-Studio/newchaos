<?php
include 'header.php';
?>
<div class='box_top'>World Stats</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
// Total Mobsters
$db->query("SELECT COUNT(*) as count FROM `grpgusers` WHERE `rmdays`='0' AND admin <> 1");
$totalmobstersRow = $db->fetch_row(true);
$totalmobsters = $totalmobstersRow['count'];

// Total Respected Mobsters
$db->query("SELECT COUNT(*) as count FROM `grpgusers` WHERE `rmdays`!='0' AND admin <> 1");
$totalrmRow = $db->fetch_row(true);
$totalrm = $totalrmRow['count'];

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
$db->query("SELECT COUNT(*) as count FROM `grpgusers` WHERE `gender` = 'Male' AND admin <> 1");
$maleRow = $db->fetch_row(true);
$male = $maleRow['count'];
$malepercent = round(($male / $totalall) * 100);

// Females
$db->query("SELECT COUNT(*) as count FROM `grpgusers` WHERE `gender` = 'Female' AND admin <> 1");
$femaleRow = $db->fetch_row(true);
$female = $femaleRow['count'];
$femalepercent = round(($female / $totalall) * 100);

// Gangs
$db->query("SELECT COUNT(*) as count FROM `gangs`");
$gangsRow = $db->fetch_row(true);
$gangs = $gangsRow['count'];

// Total Gang Money
$db->query("SELECT `moneyvault` FROM `gangs` WHERE `moneyvault` != '0'");
$gangmoney = 0;
while ($line = $db->fetch_row()) {
    $gangmoney += $line['moneyvault'];
}
?>

<tr><td class="contentcontent">
<table id="newtables" style="width:100%;table-layout:fixed;">
    <!-- Table rows here using the variables defined above -->
</table>
</td></tr>
<?php
include 'footer.php';
?>
