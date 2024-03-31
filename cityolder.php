<?php
include 'header.php';
$db->query("SELECT * FROM luckyboxes WHERE playerid = ?");
$db->execute(array(
    $user_class->id
));
$boxesrows = $db->num_rows();
$db->query("SELECT id FROM grpgusers ORDER BY todayskills DESC LIMIT 1");
$db->execute();
$worked = $db->fetch_row(true);
$hitman  = new User($worked['id']);
$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 0 ORDER BY `todaysexp` DESC LIMIT 1");
$db->execute();
$worked2 = $db->fetch_row(true);
$leveler    = new User($worked2['id']);
$db->query("SELECT COUNT(*) FROM fiftyfifty");
$db->execute();
$count5050 = $db->fetch_single();

$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 1");
$db->execute();
$rows = $db->fetch_row();


// Assuming we have a city variable for the current user's city
$current_city = $user_class->city;

// PHP to fetch king's information including avatar
$king_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE king = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($king_query) {
    $king_result = mysql_fetch_assoc($king_query);
} else {
    $king_result = null;
}

// PHP to fetch queen's information including avatar
$queen_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE queen = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($queen_query) {
    $queen_result = mysql_fetch_assoc($queen_query);
} else {
    $queen_result = null;
}


$admin_ids = array_map(function($a) {
    return $a['id'];
}, $rows);

?>
<br>
<div class="contenthead floaty">
    
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
    
    <h4>Welcome to <!_-cityname-_!></h4></span>


<div class="vip-container" style="display: flex; justify-content: space-around; align-items: flex-start;">
    <!-- King of the City -->
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
        <?php if ($king_result): ?>
            <img src="<?php echo htmlspecialchars($king_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="King's Avatar" class="user-avatar">
            <h4>King of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($king_result['id']); ?></strong></p>
        <?php else: ?>
            <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No King" class="vacant-throne">
            <h4>VACANT</h4>
            <p>King of <!_-cityname-_!></p>
        <?php endif; ?>
<a href="/attack.php?attack=<?php echo $king_result['id']; ?>" class="challenge-btn">Challenge</a>

</div>
    <!-- Queen of the City -->
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
        <?php if ($queen_result): ?>
            <img src="<?php echo htmlspecialchars($queen_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="Queen's Avatar" class="user-avatar">
            <h4>Queen of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($queen_result['id']); ?></strong></p>
        <?php else: ?>
            <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No Queen" class="vacant-throne">
            <h4>VACANT</h4>
            <p>Queen of <!_-cityname-_!></p>
        <?php endif; ?>
        <a href="/attack.php?attack=<?php echo $queen_result['id']; ?>" class="challenge-btn">Challenge</a>
    </div>
</div>



</div>
<center><table class="styled-table" border=0 cellpadding='0'>


 <div class='divider'></div>
	<table width="100%" border="0">
  <!-- Economic Activities -->
  <tr height="20px">
    <th><center><font color=white>Economic Activities</center></th>
    <th><center><font color=white>Gaming and Entertainment</center></th>
    <th><center><font color=white>Personal and Pet Management</center></th>
  </tr>
  <tr height="100" align="center">
    <td valign="top">
      <a href='stores.php'><font color=yellow>Item Stores</a><br />
            <a href='pharmacy.php'><font color=green>General Pharmacy</a><br />

      <a href="itemmarket.php"><font color=yellow><font color=yellow><font color=yellow><font color=yellow><font color=yellow><font color=yellow>Item Market</a><br />
      <a href="pointmarket.php"><font color=yellow><font color=yellow><font color=yellow><font color=yellow><font color=yellow>Points Market</a><br />
      <a href="goldmarket.php"><font color=gold>Gold Market</font></a><br />
      <a href='rentalmarket.php'><font color=yellow><font color=yellow><font color=yellow><font color=yellow>House Rental Market</a><br />
      <a href='jobs.php'><font color=yellow><font color=yellow><font color=yellow>Job Center</a><br />
      <a href='house.php'><font color=yellow><font color=yellow>Estate Agency</a><br />
      <a href='portfolio.php'><font color=yellow>Your Properties</a>
    </td>
     <td valign='top'>
        <a href='halloffame.php'><font color=red><font color=red><font color=red><font color=red><font color=red>Hall of Fame</a><br />
        <a href='viewstaff.php'><font color=red>View Game Staff</font></a><br />
        <a href='otds.php'><font color=red><font color=red><font color=red><font color=red>Daily HOF</a><br />
        <a href='oth.php'><font color=red><font color=red><font color=red>Hourly HOF</a><br />
        <a href='ratings.php'><font color=red><font color=red>Users Ratings</a><br />
        <a href='worldstats.php'><font color=red>Game Stats</a><br />
        <a href='pointsdealer.php'><font color=red>Points Dealer</a>
    </td>
    
    <td valign="top">
      <a href='mypets.php'><font color=pink>My Pet</a><br />
      <a href='petcrime.php'><font color=pink>Pet Crimes</a><br />
      <a href='petgym.php'><font color=pink>Pet Gym</a><br />
      <a href='pethouse.php'><font color=pink>Pet House</a><br />
      <a href='pethof.php'><font color=pink>Pet HOF</a><br />
      <a href='petmarket.php'><font color=pink>Pet Market</a><br />
      <a href='pettrack.php'><font color=pink>Pet Track</a><br />
      <a href='petjail.php'><font color=pink>Pet Pound</a>
    </td>
  </tr>
  
  
		<!-- Community and Social -->
<tr height='20px'>
    <th><center><font color=white>Community and Social</center></th>
    <th><center><font color=white>Statistics and Achievements</center></th>
    <th><center><font color=white>Miscellaneous</center></th>
</tr>
<tr height='100' align='center'>
    <td valign='top'>
        <a href='gang_list.php'><font color="orange">Gang List</a><br />
        <a href='citizens.php'><font color="orange">User List</a><br />
        <a href='contactlist.php'><font color="orange">Contact List</a><br />
        <a href='vote.php'><font color="orange">Vote</a><br />
        <a href='tickets.php'><font color="orange">Support Center</a><br />
        <a href='refer.php'><font color="orange">Your Referrals</a><br />
        
    </td>
    <td valign="top">
      <a href="casinonew.php"><font color="green">Casino</font></a><br />
      <a href='lucky_boxes.php'><font color="green">Lucky Boxes</a><br />
      <a href="FruitMachine.php"><font color="green">Fruit Machine</a><br />
      <a href='thedoors.php'><font color="green">The Doors</a><br />
      <a href='bloodbath.php'><font color="yellow">Bloodbath</font></a><br />
      <a href='missions.php'><font color="green">Missions</a><br />
      <a href='chapel.php'><font color="green">Chapel</a><br />
    </td>
    <td valign='top'>
        <a href='thecity.php'><font color="bronze">Search The City</a><br />
        <a href='prayer.php'><font color="bronze">Pray</a><br />
        <a href='attackLadder.php'><font color=bronze>Attack Ladder</a><br />
        <a href='hitlist.php'><font color=bronze>Hitlist</a><br />
        <a href='pointsden.php'><font color=bronze>Points Den</a><br />
        <a href='uni.php'><font color=bronze>Education</font></a><br />
        <a href='travel.php'><font color=bronze>Travel</a><br />
    </td>
</tr>


<div class="contenthead floaty">
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
<h4>City links</h4></span>

</div>
</table>
<div class="contenthead floaty">
<h4>Hourlys and Dailys</h4></span>
	<table width="100%" border="0">

		<tr height='20px'>
			<th><center><font color=#b1aa9d>Killer Of the Hour</font></center></th>
			<th><center><font color=#b1aa9d>Leveller Of the Hour</font></center></th>
			<th><center><font color=#b1aa9d>Buster Of the Hour</font></center></th>
		</tr>
		
		<tr height=100 align='center'>
			<td valign=top>
<div class='flexele floaty' style="margin:2px;">
		<?php
		if(!$m->get('koth.userid')){
			$db->query("SELECT id, koth FROM grpgusers WHERE `admin` != 1 ORDER BY koth DESC LIMIT 1");
			$db->execute();
			$koth = $db->fetch_row(true);
			$m->set('koth.userid', $koth['id'],false, 60);
			$m->set('koth.kills', $koth['koth'],false, 60);
		}
		if ($m->get('koth.kills') == 0) {
			print "Nobody<br /><br />";
		} else {
			print "<br />" . formatName($m->get('koth.userid')) . "<br /><br />Killed: " . prettynum($m->get('koth.kills')) . " Mobsters.<br /><br />You: " . prettynum($user_class->koth) . " Kills<br /><br />";
		}
		?>
	Reward: 250 Points
	</div>


			</td>
			<td valign=top>
<div class='flexele floaty' style="margin:2px;">
		<?php
		if(!$m->get('loth.userid')){
			$db->query("SELECT id, loth FROM grpgusers WHERE `admin` = 0 ORDER BY loth DESC LIMIT 1");
			$db->execute();
			$loth = $db->fetch_row(true);
			$m->set('loth.userid', $loth['id'],false, 60);
			$m->set('loth.exp', $loth['loth'],false, 60);
		}
		if ($m->get('loth.exp') == 0) {
			print "Nobody<br /><br />";
		} else {
		print "<br />" . formatName($m->get('loth.userid')) . "<br /><br />Gained: " . prettynum($m->get('loth.exp')) . " EXP.<br /><br />You: " . prettynum($user_class->loth) . " EXP<br /><br />";
		}
		?>
	Reward: 250 Points</font>
	</div>

			</td>
			<td valign=top>
<div class='flexele floaty' style="margin:2px;">
		<?php
		if(!$m->get('both.userid')){
			$db->query("SELECT id, `both` FROM grpgusers WHERE `admin` = 0 ORDER BY `both` DESC LIMIT 1");
			$db->execute();
			$both = $db->fetch_row(true);
			$m->set('both.userid', $both['id'],false, 60);
			$m->set('both.busts', $both['both'],false, 60);
		}
		if ($m->get('both.busts') == 0) {
			print "Nobody<br /><br />";
		} else {
		print "<br />" . formatName($m->get('both.userid')) . "<br /><br />Busted: " . prettynum($m->get('both.busts')) . " Mobsters.<br /><br />You busted: " . prettynum($user_class->both) . " Mobsters<br /><br />";
		}
		?>	Reward: 250 Points
	</div>
							</td>
		</tr>



<tr height='20px'>
			<th><center><font color=#b1aa9d>Killer Of the Day</font></center></th>
			<th><center><font color=#b1aa9d>Leveller Of the Day</font></center></th>
			<th><center><font color=#b1aa9d>Buster Of the Day</font></center></th>
		</tr>
		<tr height=100 align='center'>
			<td valign=top>
<div class='flexele floaty' style="margin:2px;">
		<?php

		if(!$m->get('kotd.userid')){
			$db->query("SELECT userid, kotd FROM ofthes WHERE userid NOT IN (?) ORDER BY kotd DESC LIMIT 1");
			$db->execute([$admin_ids]);
			$kotd = $db->fetch_row(true);
			$m->set('kotd.userid', $kotd['userid'],false, 60);
			$m->set('kotd.kotd', $kotd['kotd'],false, 60);
		}
		$db->query("SELECT * FROM ofthes WHERE userid = ?");
		$db->execute(array(
			$user_class->id
		));
		$ofthes = $db->fetch_row(true);
		print "<br />" . formatName($m->get('kotd.userid')) . "<br /><br />Killed: " . prettynum($m->get('kotd.kotd')) . " Mobsters.<br /><br />You Killed: " . prettynum($user_class->todayskills) . " Mobsters<br /><br />";
		?>	Reward: 2,000 Points
	</div>


			</td>
			<td valign=top>
<div class='flexele floaty' style="margin:2px;">
		<?php
		if(!$m->get('lotd.id')){
			$db->query("SELECT id, todaysexp FROM grpgusers WHERE `admin` != 1 ORDER BY todaysexp DESC LIMIT 1");
			$db->execute();
			$lotd = $db->fetch_row(true);
			$m->set('lotd.id', $lotd['id'],false, 60);
			$m->set('lotd.todaysexp', $lotd['todaysexp'],false, 60);
		}
		$db->query("SELECT * FROM grpgusers WHERE id = ?");
		$db->execute(array(
			$user_class->id
		));
		$grpgusers = $db->fetch_row(true);
		print "<br />" . formatName($m->get('lotd.id')) . "<br /><br />Gained: " . prettynum($m->get('lotd.todaysexp')) . " EXP<br /><br />You: " . prettynum($grpgusers['todaysexp']) . " EXP<br /><br />";
		?>	Reward: 2,000 Points
	</div>
	</div>

			</td>
			<td valign=top>
<div class='flexele floaty' style="margin:2px;">
		<?php
		if(!$m->get('botd.userid')){
			$db->query("SELECT userid, botd FROM ofthes WHERE userid NOT IN (?) ORDER BY botd DESC LIMIT 1");
			$db->execute([$admin_ids]);
			$botd = $db->fetch_row(true);
			$m->set('botd.userid', $botd['userid'],false, 60);
			$m->set('botd.botd', $botd['botd'],false, 60);
		}
		$db->query("SELECT * FROM ofthes WHERE userid = ?");
		$db->execute(array(
			$user_class->id
		));
		$ofthes = $db->fetch_row(true);
		print "<br />" . formatName($m->get('botd.userid')) . "<br /><br />Busted: " . prettynum($m->get('botd.botd')) . " Mobsters.<br /><br />You busted: " . prettynum($ofthes['botd']) . " Mobsters<br /><br />";
		?>	Reward: 1,500 Points
	</div>
		</tr>




		<tr height='20px'>
			<th><center><font color=#b1aa9d>Mugger Of the Hour</font></center></th>
			<th><center><font color=#b1aa9d>Mugger Of the Day</font></center></th>
			<th><center><font color=#b1aa9d>Total Amount Mugged today</font></center></th>
		</tr>
		<tr height=100 align='center'>
			<td valign=top>
				<div class='flexele floaty' style="margin:2px;">
					<?php
					if (!$moth = $m->get('moth')) {
						$db->query("SELECT id, moth FROM grpgusers WHERE `admin` != 1 ORDER BY moth DESC LIMIT 1");
						$db->execute();
						$moth = $db->fetch_row(true);
						$m->set('moth', $moth, false, 10);
					}
					if ($moth['moth'] == 0) {
						print "Nobody<br/><br/>";
					} else {
						print "<br />" . formatName($moth['id']) . "<br /><br />Mugs: " . prettynum($moth['moth']) . "<br /><br />You: " . prettynum($user_class->moth) . " Mugs<br /><br />";
					}
					print "Reward: 250 Points";
					?>
				</div>
			</td>
			<td valign=top>
				<div class='flexele floaty' style="margin:2px;">
					<?php
					if (!$motd = $m->get('motd')) {
						$db->query("SELECT userid, motd FROM ofthes WHERE userid NOT IN (?) ORDER BY motd DESC LIMIT 1");
						$db->execute([$admin_ids]);
						$motd = $db->fetch_row(true);
						$m->set('motd', $motd,false, 60);
					}
					$db->query("SELECT userid, motd FROM ofthes WHERE userid = ?");
					$db->execute([$user_class->id]);
					$mymotd = $db->fetch_row(true);
					if ($motd['motd'] == 0) {
						print "Nobody<br/><br/>";
					} else {
					print "<br />" . formatName($motd['userid']) . "<br /><br />Mugs: " . prettynum($motd['motd']) . "<br /><br />You: " . prettynum($mymotd['motd']) . " Mugs<br /><br />";
					}
					print "Reward: 2,500 Points"
					?>
				</div>
			</td>
			<td valign=top>
				<div class='flexele floaty' style="margin:2px;">
					<?php
					if (!$tamt = $m->get('tamt')) {
						$db->query("SELECT id, tamt FROM grpgusers WHERE `admin` != 1 ORDER BY tamt DESC LIMIT 1");
						$db->execute();
						$tamt = $db->fetch_row(true);
						$m->set('tamt', $tamt,false, 60);
					}
					if ($tamt['tamt'] == 0) {
						print "Nobody<br/><br/>";
					} else {
					print "<br />" . formatName($tamt['id']) . "<br /><br />Mugged: $" . prettynum($tamt['tamt']) . "<br /><br />You: $" . prettynum($user_class->tamt) . "<br /><br />";
					}
					?>
					Reward: 2,500 Points
				</div>
			</td>
		</tr>







<tr>







<!-- <br>
<center><font color=white>This is your referral link: https://themafialife.com/register.php?referer=<?php echo $user_class->id; ?></span><br>
Every <span class="color:yellow;">Valid</span> signup on this link gets you 100 Points + 50 cyellowit</font>
<br> -->
	</table>
</div>
			<div class="spacer"></div>
		</div>




<?php
include 'footer.php';
?>
<style>
    .special-users {
        background-color: #333; 
        padding: 10px 0; 
        text-align: center;
        margin-bottom: 20px;
    }
    
    .user {
        display: inline-block;
        margin: 0 10px;
    }
    
    .user img {
        width: 50px; 
        height: 50px;
        border-radius: 50%;
        display: block;
        margin: 0 auto;
    }
    
    .user span {
        color: #fff;
        display: block;
    }

    .styled-table {
        border-collapse: collapse;
        width: 100%;
        margin: 25px 0;
        font-size: 18px;
        text-align: left;
    }

    .styled-table th, .styled-table td {
        padding: 10px 12px;
        border: 1px solid #ddd;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .styled-table tbody tr:hover {
        background-color: #f5f5f5;
    }

    .styled-table thead {
        background-color: #f2f2f2;
    }
</style>

<style>
    .scrolling-section {
        display: flex;
        overflow-x: scroll;
        background-color: #333;
        padding: 20px 0;
    }
    
    .of-the-hour, .of-the-day {
        flex: 0 0 100%;
        display: flex;
        justify-content: space-around;
    }

    .user {
        width: 20%;
        text-align: center;
        color: #fff;
    }

    .user img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: block;
        margin: 0 auto 10px;
    }

    .user-details span {
        display: block;
    }
</style>
