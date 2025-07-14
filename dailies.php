<?php
include "header.php";
$db->query("SELECT uid FROM dond WHERE uid = ?");
$db->execute(array(
	$user_class->id
));
$dond = ($db->fetch_single()) ? true : false;
$db->query("SELECT SUM(tickets) FROM ptslottery WHERE userid = $user_class->id");
$db->execute();
$ptscount = $db->fetch_single();
$ptscount = ($ptscount > 0) ? $ptscount : 0;
$db->query("SELECT SUM(tickets) FROM cashlottery WHERE userid = $user_class->id");
$db->execute();
$cashcount = $db->fetch_single();
$cashcount = ($cashcount > 0) ? $cashcount : 0;
$db->query("SELECT count(*) FROM votes WHERE userid = ?");
$db->execute(array(
	$user_class->id
));
$votes = $db->fetch_single();
$stuff = array(
	$voting = ($votes == 5) ? "<span style='color:green;'>5 / 5 Votes</span>" : "<span style='color:red;'>$votes / 5 Votes</span>",
	$searchdowntown = ($user_class->searchdowntown == 0) ? "<span style='color:green;'>" . (100 - $user_class->searchdowntown) . " / 100 Searches</span>" : "<span style='color:red;'>" . (100 - $user_class->searchdowntown) . " / 100 Searches</span>",
	$lucky = ($user_class->luckydip == 0) ? "<span style='color:green;'>1 / 1 Dips</span>" : "<span style='color:red;'>0 / 1 Dips</span>",
	$doors = ($user_class->doors == 0) ? "<span style='color:green;'>" . (3 - $user_class->doors) . " / 3 Doors Opened</span>" : "<span style='color:red;'>" . (3 - $user_class->doors) . " / 3 Doors Opened</span>",
	$roulette = ($user_class->spins == 0) ? "<span style='color:green;'>" . (20 - $user_class->spins) . " / 20 Spins</span>" : "<span style='color:red;'>" . (20 - $user_class->spins) . " / 20 Spins</span>",
	$smuggle = ($user_class->psmuggling == 0) ? "<span style='color:green;'>" . (6 - $user_class->psmuggling) . " / 6 Smuggles</span>" : "<span style='color:red;'>" . (6 - $user_class->psmuggling) . " / 6 Smuggles</span>",
	$cash = ($cashcount == 50) ? "<span style='color:green;'>$cashcount / 50 Tickets</span>" : "<span style='color:red;'>$cashcount / 50 Tickets</span>",
	$pts = ($ptscount == 25) ? "<span style='color:green;'>$ptscount / 25 Tickets</span>" : "<span style='color:red;'>$ptscount / 25 Tickets</span>",
	$dond = ($dond) ? "<span style='color:green;'>1 / 1 Tries</span>" : "<span style='color:red;'> 0 / 1 Tries</span>"
);
$tally = 0;
foreach ($stuff as $t)
	if (strpos($t, 'green') !== false)
		$tally++;
$tally = (int) (($tally / count($stuff)) * 100);
echo '<div class="floaty" style="margin:2px;">';
echo '<span style="color:red;font-weight:bold;">My Dailies</span>';
echo '</div>';
echo '<div class="floaty" style="margin:2px;">';
echo '<div class="progress-bar blue stripes" style="height:34px;width:100%;text-align:left;">';
echo '<span style="width:' . $tally . '%;height:34px;line-height:34px;text-indent:5px;font-size:18px;color:white;font-weight:900;background:rgba(62,190,0,.75);">' . $tally . '%</span>';
echo '</div>';
echo '<hr style="border:0;border-bottom:thin solid #333;" />';
echo '<table id="newtables">';
echo '<tr>';
echo '<th>Search Downtown</td>';
echo '<td>' . $searchdowntown . '</td>';
echo '<td><a href="thecity.php">[Go Search]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Lucky Dip</td>';
echo '<td>' . $lucky . '</td>';
echo '<td><a href="luckydip.php">[Go Dip]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>The Doors</td>';
echo '<td>' . $doors . '</td>';
echo '<td><a href="thedoors.php">[Go Open Doors]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Roulette</td>';
echo '<td>' . $roulette . '</td>';
echo '<td><a href="roulettespin.php">[Go Spin]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Point Smuggling</td>';
echo '<td>' . $smuggle . '</td>';
echo '<td><a href="psmuggling.php">[Go Smuggle]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Voting</td>';
echo '<td>' . $voting . '</td>';
echo '<td><a href="vote.php">[Go Vote]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Point Lottery</td>';
echo '<td>' . $pts . '</td>';
echo '<td><a href="ptslottery.php">[Test your Luck]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Money Lottery</td>';
echo '<td>' . $cash . '</td>';
echo '<td><a href="cashlottery.php">[Test your Luck]</a></td>';
echo '</tr>';
echo '<tr>';
echo '<th>Lucky Boxes</td>';
echo '<td>' . $dond . '</td>';
echo '<td><a href="lucky_boxes.php">[Lucky Boxes]</a></td>';
echo '</tr>';
echo '</table>';
echo '</div>';
include "footer.php";
?>