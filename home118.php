<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
		<link rel="stylesheet" href="css/login.css" media="screen"/>
<?php
$stats = new User_Stats("1");
?>

        <title>MeanStreets</title>

    </head>
    <body>
		<div id="logo-text"></div>
		<div id="logo"></div>
		<div id="content">
			<div class="flexcont">
				<div class="flexele">
					<span id="errors" style="color:red;font-size:16px;font-weight:bold;"><?php echo ($_SESSION['failmessage']) ? $_SESSION['failmessage'] : "" ; ?></span>
					<form name="login" action="login.php" method="post" accept-charset="utf-8">
						<label for="username">Username</label><br />
						<input class="textbox" type="text" name="username" id="username" required> <br /><br />
						<label for="userpass">Password</label><br />
						<input class="textbox" type="password" name="password" id="password" required> <br /><br />
						<input id="button-login" type="submit" value=""/><br /><br />
						<a href="register.php">Sign up Here</a>&nbsp;&nbsp;<a href="http://facebook.com/meanstreetsmafia">Can't log in?</a>
					</form>
				</div>
				<div class="flexele">
					<?php
					$db->query("SELECT id, lastactive FROM grpgusers ORDER BY lastactive DESC LIMIT 5");
					$rows = $db->fetch_row();
					$i = 1;
					echo'<table style="margin:auto;text-align:left;">';
						echo'<tr>';
							echo'<th colspan="3">Last Active Players</th>';
						echo'</tr>';
					foreach($rows as $row){
						echo'<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td style="text-align:center;">'.howLongAgo($row['lastactive']).'</td></tr>';
					}
					echo'</table>';
					?>
				</div>
			</div>
			<div class="flexcont" style="text-align:center;">
				<div class="flexele">
					<?php
					$db->query("SELECT id FROM grpgusers WHERE admin <> 1 AND id <> 103 ORDER BY total DESC LIMIT 5");
					$rows = $db->fetch_row();
					$i = 1;
					echo'<table style="margin:auto;text-align:left;">';
						echo'<tr>';
							echo'<th colspan="2">Top 5 Strongest Players</th>';
						echo'</tr>';
					foreach($rows as $row){
						echo'<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td></tr>';
					}
					echo'</table>';
					?>
				</div>
				<div class="flexele">
					<?php
					$db->query("SELECT id, level FROM grpgusers WHERE admin <> 1 AND id <> 103 ORDER BY level DESC LIMIT 5");
					$rows = $db->fetch_row();
					$i = 1;
					echo'<table style="margin:auto;text-align:left;">';
						echo'<tr>';
							echo'<th colspan="3">Top 5 Highest Leveled Players</th>';
						echo'</tr>';
					foreach($rows as $row){
						echo'<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td style="text-align:center;">'.$row['level'].'</td></tr>';
					}
					echo'</table>';
					?>
				</div>
			</div>
			<div class="flexcont" style="text-align:center;">
				<div class="flexele">
					<img src="images/newlogin/SS-1.png" style="width:100%;height:100%;"/>
				</div>
				<div class="flexele">
					<img src="images/newlogin/SS-2.png" style="width:100%;height:100%;"/>
				</div>
				<div class="flexele">
					<img src="images/newlogin/SS-3.png" style="width:100%;height:100%;"/>
				</div>
			</div>
		</div>
        <footer>
            <p><font color=white>Players Registered (<font color=yellow><?php echo $stats->playerstotal; ?></font>) | Mobsters Online (<font color=yellow><?php echo $stats->playersloggedin; ?></font>) | Last 24 hours (<font color=yellow><?php echo $stats->playersonlineinlastday; ?></font>)</font></p>
        <footer>
    </body>
</html>
<?php
$_SESSION['failmessage'] = "";
?>