<?php
include 'header.php';



if ($user_class->jail > 0) {
    echo '<a href="spendpoints.php?spend=bail"><b><font color=red>You can Bail yourself out for 10 Points</font></b></a>';
}



$jailbreak = $_GET['jailbreak'];
if ($jailbreak != ""){

$jailed_person = new User($jailbreak);


if ($_GET['bail'] == "points") {
    if ($user_class->points >= 10) {
        if ($user_class->jail == 0) {
            echo Message("You're not in jail!");
        } else {
            $newpoints = $user_class->points - 10;

            $newjail = 0;
                       $result = mysql_query("UPDATE `grpgusers` SET `jail` = '" . $newjail . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You spent 10 points and bailed yourself.");
        }
    } else {
        echo Message("You don't have enough points to do that.");
    }
}



// New code to handle bail request
if (isset($_GET['bailout'])) {
    // Check if current user is in jail and has at least 10 points
    if ($user_class->jail > 0 && $user_class->points >= 10) {
        // Deduct 10 points from user's total
        $new_points = $user_class->points - 10;
        $result = mysql_query("UPDATE `grpgusers` SET `points` = '".$new_points."', `jail` = '0' WHERE `id`='".$_SESSION['id']."'");
        if ($result) {
            echo Message("You have successfully bailed yourself out!");
        } else {
            echo Message("Something went wrong. Please try again.");
        }
    } else if ($user_class->points < 10) {
        echo Message("You don't have enough points to bail yourself out.");
    }
}



if ($jailed_person->formattedname == ""){
	echo Message("That person does not exist.");
	include 'footer.php';
	die();
}
if ($jailed_person->jail == "0"){
	echo Message("That person is not in jail.");
	include 'footer.php';
	die();
}
if ($jailed_person->jail == "1"){
	echo Message("That person is not in jail.");
	include 'footer.php';
	die();
}
if ($jailed_person->id == "$user_class->id"){
	echo Message("You cannot bust yourself!");
	include 'footer.php';
	die();
}
if ($user_class->jail >= "1"){
	echo Message("You cannot bust someone when in jail.");
	include 'footer.php';
	die();
}
if ($user_class->hospital >= "1"){
	echo Message("You cannot bust someone when in hospital.");
	include 'footer.php';
	die();
}
if ($user_class->fbijail >= "1"){
	echo Message("You cannot bust someone when in FBI jail.");
	include 'footer.php';
	die();
}


	$chance = rand(1,(100 * $crime - ($user_class->speed / 25)));
	$money = 2500;
	$exp = 1000;
	$nerve = 10;
	if ($user_class->nerve >= $nerve) {
		if($chance <= 75) {
                        echo Message("Success! You receive ".$exp." exp and $".$money);
			$crimesucceeded = 1 + $user_class->crimesucceeded;
			$money = $money + $user_class->money;
			$exp = 1000 + $user_class->exp;
$both = 1 + $user_class->both;
$busts = 1 + $user_class->busts;


			$money = 2500 + $user_class->money;

			$nerve = $user_class->nerve - $nerve;




 mission('b');
                newmissions('busts');
                gangContest(array(
                    'busts' => 1,
                    'exp' => $exp
                ));


$toadd = array('botd' => 1);
                ofthes($user_class->id, $toadd);
                bloodbath('busts', $user_class->id);

                        $result = mysql_query("UPDATE `grpgusers` SET `both` = '".$both."' WHERE `id`='".$user_class->id."'");
                        $result = mysql_query("UPDATE `grpgusers` SET `busts` = '".$busts."' WHERE `id`='".$user_class->id."'");

			$result = mysql_query("UPDATE `grpgusers` SET `exp` = '".$exp."',`exp` = '".$exp."', `crimesucceeded` = '".$crimesucceeded."', `crimemoney` = '".$crimemoney."', `money` = '".$money."', `nerve` = '".$nerve."' WHERE `id`='".$_SESSION['id']."'");
			$result = mysql_query("UPDATE `grpgusers` SET `jail` = '0' WHERE `id`='".$jailed_person->id."'");

 // Increment the dailyBusts for the user's gang
    $update_gang_query = "UPDATE gangs SET dailyBusts = dailyBusts + 1 WHERE id = " . $user_class->gang;
    mysql_query($update_gang_query);


			//send even to that person




			Send_Event($jailed_person->id, "You have been busted out of jail by ".$user_class->formattedname);
		}elseif ($chance >= 150) {
			echo Message("You were caught. You were hauled off to jail for " . 200 . " minutes.");
			$crimefailed = 1 + $user_class->crimefailed;
			$jail = 10800;
			$nerve = $user_class->nerve - $nerve;
			$result = mysql_query("UPDATE `grpgusers` SET `crimefailed` = '".$crimefailed."', `jail` = '".$jail."', `nerve` = '".$nerve."' WHERE `id`='".$_SESSION['id']."'");
		}else{
			echo Message("You failed.");
			$crimefailed = 1 + $user_class->crimefailed;
			$nerve = $user_class->nerve - $nerve;
			$result = mysql_query("UPDATE `grpgusers` SET `crimefailed` = '".$crimefailed."', `nerve` = '".$nerve."' WHERE `id`='".$_SESSION['id']."'");
		}
	} else {
		echo Message("You don't have enough nerve for that crime.");
	}
	include 'footer.php';
	die();
}
?>
<tr><td class="contenthead">Jail </td></tr>
<tr><td class="contentcontent">
<table width='100%' cellpadding='4' cellspacing='0'>
	<tr>

		<td>Mobster</td>

		<td>Time Left</td>

		<td>Actions</td>

	</tr>
	<?php
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `jail` DESC");

	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$secondsago = time()-$line['lastactive'];
			$user_jail = new User($line['id']);
			if (floor($user_jail->jail / 60) != 1) {
			$plural = "s";
			 }
			 if($user_jail->jail != 0){
			echo "<tr><td>".$user_jail->formattedname."</td><td>".floor($user_jail->jail / 60)." minute".$plural."</td><td><a href = 'jail.php?jailbreak=".$user_jail->id."'>Break Out</a></td></tr>";
			}
	}
	?>
</table>
</td></tr>
<?php
include 'footer.php';
?>