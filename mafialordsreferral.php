<?php
include 'header.php';

<?php
echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Referral System</title>';
echo '<style>';
echo '.glowing-border {';
echo '  border: 2px solid grey;';
echo '  padding: 10px;';
echo '  box-shadow: 0 0 8px grey;';
echo '}';
echo 'table {';
echo '  width: 100%;';
echo '  border-collapse: collapse;';
echo '}';
echo 'td, th {';
echo '  padding: 10px;';
echo '  text-align: left;';
echo '}';
echo '.refer-link {';
echo '  word-break: break-all; /* Ensure the link doesn\'t overflow */';
echo '}';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<div class="glowing-border">';
echo '  <table>';
echo '    <tr>';
echo '      <th colspan="2">Referral System</th>';
echo '    </tr>';
echo '    <tr>';
echo '      <td>Your Referrer Link:</td>';
echo '      <td class="refer-link">https://s2.TheMafiaLife.com/register.php?referer=135</td>';
echo '    </tr>';
echo '    <tr>';
echo '      <td>Reward:</td>';
echo '      <td>50 Credits + 100 Points per referral.</td>';
echo '    </tr>';
echo '  </table>';
echo '</div>';

echo '</body>';
echo '</html>';
?>


echo'<h3>Players You Have Referred to TML</h3>';
	echo'<hr>';
echo'<div class="floaty" style="width:75%;">';
	
	echo'<table id="newtables" style="width:100%;">';
		echo'<tr>';
			echo'<th>Mobster</th>';
			echo'<th>State</th>';
			echo'<th>Reward [pts]</th>';
		echo'</tr>';
		$db->query("SELECT * FROM referrals WHERE referrer = ? ORDER BY id DESC");
		$db->execute(array(
			$user_class->id
		));
	$rows = $db->fetch_row();
	if(!count($rows)){
		echo'<tr>';
			echo'<td colspan="3">You have no referrals</td>';
		echo'</tr>';
	} else {
		foreach($rows as $row){
			$credited = ($row['credited'] == 0) ? "Pending" : "Approved";
			$points = ($row['credited'] == 0) ? "0" : "100 + 50 Credits";
			echo'<tr>';
				echo'<td>' . formatName($row['referred']) . '</td>';
				echo'<td>' . $credited . '</td>';
				echo'<td>' . $points . '</td>';
			echo'</tr>';
		}
	}
	echo'</table>';
echo'</div>';
echo'<h3>Top 10 Referrers</h3>';
	echo'<hr>';
echo'<div class="floaty" style="width:75%;">';
	
	echo'<table id="newtables" style="width:100%;">';
		echo'<tr>';
			echo'<th>Rank</th>';
			echo'<th>Username</th>';
			echo'<th>Referrals</th>';
		echo'</tr>';
		$db->query("SELECT COUNT(*) count, referrer FROM referrals r LEFT JOIN bans b ON r.referrer = b.id WHERE b.id IS NULL AND credited = 1 GROUP BY referrer ORDER BY count DESC LIMIT 10");
		$db->execute();
		$rows = $db->fetch_row();
		$r = 0;
		foreach($rows as $row){
			echo'<tr>';
				echo'<td width="10%">' . ++$r . '.</td>';
				echo'<td width="32%">' . formatName($row['referrer']) . '</td>';
				echo'<td width="14%">' . prettynum($row['count']) . '</td>';
			echo'</tr>';
		}
	echo'</table>';
echo'</div>';
include 'footer.php';
?>