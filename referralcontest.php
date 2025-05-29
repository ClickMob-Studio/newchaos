<?php
include("header.php");
if (isset($_POST['resetref'])) {
	perform_query("UPDATE `grpgusers` SET `killcomp` = '0'");
	echo Message("The kill counts have been reset.");
}
if (isset($_POST['resetexp'])) {
	perform_query("UPDATE `grpgusers` SET `expcount` = '0'");
	echo Message("The exp counts have been reset.");
}
?>
<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead"></td>
</tr>
<tr>
	<td class="contentcontent">

<tr>
	<td>
		<center>
			<font color=orange>
				<font size=4><b>Welcome to The Referral Contest</b></font>
			</font>
		</center>
		<br>
		<br>
		<center>
			<font size=5>
				<font color=red>This is your chance to win some amazing prizes! All you need to do is refer more players
					than
					your fellow players.</font>
			</font>
		</center> </br></br>

		</br>

		<center>
			<font size=4>
				<font color=orange>5 Refferals</font>
			</font>
		</center>
		<br>
		<center>
			<font size=3>Unlock an exclusive in-game weapon or item{New item being released into circulation}{Choices
				will become available</font>
		</center></br>


		<center>
			<font size=4>
				<font color=orange>10 Refferals</font>
			</font>
		</center>
		<br>
		<center>
			<font size=4>Gain access to a hidden secret location with rare treasures</font>
		</center></br>


		<center>
			<font size=4>
				<font color=orange>15 Refferals</font>
			</font>
		</center>
		<br>
		<center>
			<font size=4>$75 store credit</font>
			</font>
		</center></br>


		<center>
			<font size=4>
				<font color=orange>20 Refferals</font>
			</font>
		</center>
		<br>
		<center>
			<font size=4>Unlock a special limited edition item, New Legendary exclusive item</font>
		</center></br>



		</br>
		<center>
			<font color=orange>
				<font size=4>This Competition Will end on the 18th of August at 23:59am(Game Time)</font>
			</font>
		</center>
		<br>
		<br>
		<table width="100%" style="border: 3px solid #444444;" cellpadding="4" cellspacing="0">
			<tr>
				<td style="border-right: 3px solid #444444;">
					<center><b><u>REWARDS</u></b></center><br />
					<table width="100%">
						<tr>
							<td><b>Rank</b></td>
							<td><b>Reward</b></td>
						</tr>
						<tr>
							<td><b>1</b></td>
							<td><b>$100 in real cash + Custom item trophy + $50,000,000 in game currency.</b></td>
						</tr>
						<td><b>2</b></td>
						<td><b>$50 in real cash + $25,000,000 in game currency.</b></td>
			</tr>
			<td><b>3</b></td>
			<td><b>Custom Item set.</b></td>
</tr>
</table>

</br>

</td>
</tr>
</br>
<table width="100%" style="border: 3px solid #444444;" cellpadding="4" cellspacing="0">
	<tr>
		<td style="border-right: 1px solid #444444;">
			<center><b><u>Players referred during the competition</u></b></center><br />
			<table width="100%">
				<tr>
					<td><b>#</b></td>
					<td><b>Username</b></td>
					<td><b>Referrals</b></td>
				</tr>
				<?php
				$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `refcomp` DESC LIMIT 3");
				$rank = 0;
				while ($line = mysql_fetch_array($result)) {
					$rank++;
					$user_name = new User($line['id']);
					echo '<tr><td width="10%">' . $rank . '.</td><td width="55%">' . $user_name->formattedname . '</td><td width="35%">' . prettynum($line['refcomp']) . '</td></tr>';



				}
				?>
			</table>


	</tr>
</table>

</td>
</tr>

<?php

include("footer.php");
?>