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
	<td class="contenthead">
		<center> <img
				src='https://cdn.discordapp.com/attachments/983144300806299668/1031718239941697586/21d67757b8b2d5961b9331cb1f329d3ccddc1eb234792a17fb6b2fe783d70869373dfd00.gif'>
		</center>
	</td>
</tr>
<tr>
	<td class="contentcontent">

<tr>
	<td>
		<h2 class="text-14 m-0">18th - 31st October 2022 (Ends 23:59 31st)</h2>
		<p class="text-14">Earn Pumpkins <img
				src='https://cdn.discordapp.com/attachments/983144300806299668/1031280107437957231/ezgif.com-gif-maker_13.png'>
			based on your activity around different parts of the game!</p>


		This is your chance to win some amazing prizes! You will be awarded points towards earning your pumpkins for
		actions in the game! Each Hour the value of certain actions will be randomised for that hour!. Here are the
		prizes.</br></br>

		- <font color=gold>1st Place</font> : 250k Points - 50 Ghosts</br>

		- <font color=silver>2nd Place</font> : 100k Points - 25 Ghosts</br>

		- <font color=bronze>3rd Place</font> : 50k Points - 10 Ghosts</br>
		</br>

		Along with the top 3 Players recieving these prizes. All users will also receive a prize if they hit these
		Pumpkin Thresholds </br>
		</br>

		- <font color=bronze>5 Pumpkins</font> : 10,000 Points - 5 Ghosts</br>

		- <font color=silver>50 Pumpkins</font> : 50,000 Points - 20 Ghosts</br>

		- <font color=gold>500 Pumpkins</font> : 150,000 Points - 50 Ghosts</br>

		</br>

		This Competition Will end on the 31/10/2022 at 23:59am (Game Time)
		</br></br>
	</td>
</tr>

<table width="100%" style="border: 1px solid #444444;" cellpadding="4" cellspacing="0">
	<tr>
		<td style="border-right: 1px solid #444444;">
			<center><b><u>Pumpkin Counts</u></b></center><br />
			<table width="100%">
				<tr>
					<td><b>#</b></td>
					<td><b>Username</b></td>
					<td><b>Pumpkins Count</b></td>
				</tr>
				<?php
				$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `halloween` DESC LIMIT 25");
				$rank = 0;
				while ($line = mysql_fetch_array($result)) {
					$rank++;
					$user_name = new User($line['id']);
					echo '<tr><td width="10%">' . $rank . '.</td><td width="55%">' . $user_name->formattedname . '</td><td width="35%">' . prettynum($line['halloween']) . '  <img src="https://cdn.discordapp.com/attachments/983144300806299668/1031280107437957231/ezgif.com-gif-maker_13.png">
</td></tr>';



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