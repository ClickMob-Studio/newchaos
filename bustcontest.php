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
	<td class="contenthead">Bust Contest</td>
</tr>
<tr>
	<td class="contentcontent">

<tr>
	<td>
		<center>
			<font color=red><b>Welcome to The Bust contest</b></font>
		</center><br>This is your chance to win some amazing prizes! All you need to do is win more attacks than
		your fellow players. Here are the prizes.</br></br>

		- <font color=gold>1st Place</font> : 90 Day Gradient & RM Days + 1 Point Per Bust</br>

		- <font color=silver>2nd Place</font> : 60 Day Gradient & RM Days + 50,000 Points.</br>

		- <font color=bronze>3rd Place</font> : 30 Day Gradient & RM Days + 25,000 Points.</br>
		</br>

		Along with the top 3 busters recieving these prizes. All users will also receive a prize if they hit these Bust
		Thresholds </br>
		</br>

		- <font color=bronze>100 Busts</font> : 5,000 Points</br>

		- <font color=silver>1,000 Busts</font> : 10,000 Points</br>

		- <font color=gold>10,000 Busts</font> : 25,000 Points</br>

		</br>

		This Competition Will end on the 21/08/2022 at 23:59am (Game Time)
		</br></br>
	</td>
</tr>

<table width="100%" style="border: 1px solid #444444;" cellpadding="4" cellspacing="0">
	<tr>
		<td style="border-right: 1px solid #444444;">
			<center><b><u>Bust Counts</u></b></center><br />
			<table width="100%">
				<tr>
					<td><b>#</b></td>
					<td><b>Username</b></td>
					<td><b>Bust Comp</b></td>
				</tr>
				<?php
				$db->query("SELECT * FROM grpgusers ORDER BY bustcomp DESC LIMIT 25");
				$db->execute();
				$result = $db->fetch_row();
				$rank = 0;
				foreach ($result as $line) {
					$rank++;
					$user_name = new User($line['id']);
					echo '<tr><td width="10%">' . $rank . '.</td><td width="55%">' . $user_name->formattedname . '</td><td width="35%">' . prettynum($line['bustcomp']) . '</td></tr>';



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