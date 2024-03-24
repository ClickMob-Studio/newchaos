<?php
include("header.php");
if (isset($_POST['resetref'])) {
	$result = mysql_query("UPDATE `grpgusers` SET `killcomp` = '0'");
	echo Message("The kill counts have been reset.");
}
if (isset($_POST['resetexp'])) {
	$result = mysql_query("UPDATE `grpgusers` SET `expcount` = '0'");
	echo Message("The exp counts have been reset.");
}
?>
<tr>
	<td class="contentspacer"></td>
</tr>
<div class="contenthead floaty">
  <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>ML Kill Contest</h4></span>
<tr>
	<td>
		<center>
			<font color=red><b>Welcome to MafiaLords Kill contest</b></font>
		</center><br>This is your chance to win some amazing prizes! All you need to do is win more attacks than
		your fellow players. Here are the prizes.</br></br>

		- <font color=gold>1st Place</font><br>100,000 Points<br><font color=green>$75,000,000</font><br>2 Points Per Kill<br>50x Raid tokens<br><br>

		- <font color=silver>2nd Place</font><br>75,000 Points<br><font color=green>$50,000,000</font><br>1 Point Per Kill<br>25x Raid tokens<br><br>

		- <font color=bronze>3rd Place</font><br>50,000 Points<br><font color=green>$25,000,000</font><br>10x Raid tokens<br><br>
		</br>

		Along with the top 3 killers recieving these prizes. All users will also receive a prize if they hit these Kill Thresholds </br>
		</br>

		- <font color=bronze>100 Kills</font> : 5,000 Points</br>

		- <font color=silver>1,000 Kills</font> : 12,000 Points</br>

		- <font color=gold>10,000 Kills</font> : 25,000 Points</br>

		</br>

		This Competition Will end on the 28th of February 2024 at 23:59am (Game Time)
		</br></br>
	</td>
</tr>

<div class="contenthead floaty">
  <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Kill Counters</h4></span>
	<table id="newtables" style="width:100%;">
				<tr>
					<td><b>#</b></td>
					<td><b>Username</b></td>
					<td><b>Kill Comp</b></td>
				</tr>
				<?php
				$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `killcomp` DESC LIMIT 25");
				//$result = mysql_query("SELECT id, killcomp, FIND_IN_SET( killcomp, ( SELECT GROUP_CONCAT( killcomp ORDER BY killcomp DESC ) FROM grpgusers ) ) AS rank FROM `grpgusers` ORDER BY `killcomp` DESC LIMIT 25");
				$rank = 0;
				while ($line = mysql_fetch_array($result)) {
					$rank++;
					$user_name = new User($line['id']);
					echo '<tr><td width="10%">' . $rank . '.</td><td width="55%">' . $user_name->formattedname . '</td><td width="35%">' . prettynum($line['killcomp']) . '</td></tr>';
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