<?php
include 'header.php';
if(isset($_GET['jailbreak'])){
    $jailbreak = $_GET['jailbreak'];
}else{
    $jailbreak = '';
}

if ($jailbreak != ""){
    if(empty($_GET['token'])){
        echo Message("There has been a issue");
    }
    if($_GET['token'] != $_SESSION['token']){
        echo Message("F5 use on jail is not allowed");
        exit();
    }else{
        unset($_SESSION['token']);
    }

$jailed_person = new User($jailbreak);
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
	$chance = rand(1,(100 * $crime - ($user_class->speed / 25)));
	//$money = 785;
	$nerve = 10;
    $exp = 2500;
	if ($user_class->nerve >= $nerve) {
		if($chance <= 75) {
			$_SESSION['message'] = "Success! You receive ".$exp." exp";
			$exp = $exp + $user_class->exp;
			$crimesucceeded = 1 + $user_class->crimesucceeded;
			$crimemoney = $money + $user_class->crimemoney;
			$money = $money + $user_class->money;
			$nerve = $user_class->nerve - $nerve;
            if ($user_class->gang != 0) {
                mysql_query("UPDATE gangs SET dailyBusts = dailyBusts + 1 WHERE id = ".$user_class->gang);
            }
            mysql_query("UPDATE grpgusers SET `both` = `both` + 1, `epoints` = `epoints` + `eventbusts`, `bustcomp` = `bustcomp` + 1, exp = exp + ".$exp.", busts = busts + 1, points = points + 3, nerve = nerve - ".$nerve." WHERE id = ".$user_class->id);
               
			$result = mysql_query("UPDATE `grpgusers` SET `jail` = '0' WHERE `id`='".$jailed_person->id."'");
			//send even to that person
			Send_Event($jailed_person->id, "You have been busted out of jail by ".$user_class->formattedusername);
		}elseif ($chance >= 150) {
			$_SESSION['message'] = "You were caught. You were hauled off to jail for 10  minutes.";
			$crimefailed = 1 + $user_class->crimefailed;
			$jail = 10800;
			$nerve = $user_class->nerve - $nerve;
			$result = mysql_query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 600, nerve = nerve - ".$nerve." WHERE id =".$user_class->id);
		}else{
			$_SESSION['message'] ="You failed.";
			$crimefailed = 1 + $user_class->crimefailed;
			$nerve = $user_class->nerve - $nerve;
			$result = mysql_query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ".$nerve." WHERE id = '".$_SESSION['id']."'");
		}
	} else {
		echo Message("You don't have enough nerve for that crime.");
	}
	include 'footer.php';
	die();
}
?>
<tr><td class="contenthead">Jail</td></tr>
<?php
if(isset($_SESSION['message'])){
    echo Message($_SESSION['message']);
    unset($_SESSION['message']);
}
?>
<tr><td class="contentcontent">
<table width='100%' cellpadding='4' cellspacing='0'>
	<tr>

		<td>Mobster</td>

		<td>Time Left</td>

		<td>Actions</td>

	</tr>
	<?
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `jail` DESC");

	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$secondsago = time()-$line['lastactive'];
			$user_jail = new User($line['id']);
			if (floor($user_jail->jail / 60) != 1) {
			$plural = "s";
			 }
             function generateRandomString($length = 10) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $index = mt_rand(0, strlen($characters) - 1);
                    $randomString .= $characters[$index];
                }
                return $randomString;
            }
            $token = generateRandomString(10);
            $_SESSION['token'] = $token;
			 if($user_jail->jail != 0){
			echo "<tr><td>".$user_jail->formattedname."</td><td>".floor($user_jail->jail / 60)." minute".$plural."</td><td><a href = '?jailbreak=".$user_jail->id."&token=".$token."'>Break Out</a></td></tr>";
			}
	}
	?>
</table>
</td></tr>
<?
include 'footer.php';
?>