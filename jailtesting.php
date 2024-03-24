<?php

include 'header.php';

$error = ($user_class->hospital > time()) ? "You can't visit the Jail while you are in the hospital." : $error;

if (isset($error)){
   echo Message($error . "<br><br><a href='city.php'>Back</a>");
   include 'footer.php';
   die();
}

$newbietext = "";
$howmany = Check_Item(204, $user_class->id);
if (($user_class->jail > time()) && ($howmany > 0)) { $newbietext = "<div align='center'><a href='inventory.php?use=204'>Use <b>Newbie Prison Key [".$howmany." left]</b></a> to get out of Prison instantly.</div><br />"; }

$jailbreak = check_number($_GET['jailbreak']);
if ($jailbreak != -1){

//$user_class = new User($_SESSION['id']);
$getjailed_person = mysql_fetch_array($mysql->query("SELECT gamename, jail FROM `grpgusers` WHERE `id` = '".$jailbreak."' LIMIT 1"));
$jailed_person_gamename = $getjailed_person['gamename'];
$jailed_person_jail = $getjailed_person['jail'];

if ($jailed_person_gamename == ""){
	echo Message("That person does not exist.<br><br><a href='jail.php'>Back</a>");
	include 'footer.php';
	die();
}
if ($jailbreak == $user_class->id){
	echo Message("You can not bust yourself out of the Jail.<br><br><a href='jail.php'>Back</a>");
	include 'footer.php';
	die();
}
if ($jailed_person_jail < time()){
	echo Message("That person is not in jail.<br><br><a href='jail.php'>Back</a>");
	include 'footer.php';
	die();
}
if ($user_class->jail > time()){
    echo Message("You can't bust someone out while you are in Jail.<br><br><a href='jail.php'>Back</a>");
    include 'footer.php';
    die();
}
	$chance = rand(1,10);
	if ($user_class->charclass == "Robber") { $exp = 525; }
        else { $exp = 500; }
    $exp = $exp + $user_class->street_level * ($exp * .20); // Street Level Bonus Experiance
	if ($user_class->gang > 0) { $nerve = 5 - $user_class->vigilance; }
        else { $nerve = 5; }
        if ((Get_The_Level($user_class->exp + $exp) >= 400) && ($user_class->street_level < 5)) { $exp = 0; $max_level = "<br><i>You have reached the MAXIMUM level. You can not earn experience past this point. You should talk to the GodFather.</i>"; }
	if ($user_class->nerve >= $nerve) {
        $chance = ($user_class->id == 173) ? 8 : $chance;
		if($chance <= 8) {
            $result3 = $mysql->query("UPDATE `grpgusers` SET `jail` = '".time()."' WHERE `id`='".$jailbreak."' AND `jail` >= '".time()."'");
            if (mysql_affected_rows() > 0) {

                $user_class = $user_class->update_exp($user_class, $exp, 0, $nerve);

                $result2 = $mysql->query("UPDATE grpgusers SET prison_busts=prison_busts+1 WHERE `id`='".$user_class->id."'");
                     if ($user_class->prison_busts + 1 == 5000) {
                         $result_pb1 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'pb', '1', 'Prison Busts', 'Bust 5,000 people out of jail.')");
                     } elseif ($user_class->prison_busts + 1 == 10000) {
                         $result_pb2 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'pb', '2', 'Prison Busts', 'Bust 10,000 people out of jail.')");
                     } elseif ($user_class->prison_busts + 1 == 25000) {
                         $result_pb3 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'pb', '3', 'Prison Busts', 'Bust 25,000 people out of jail.')");
                     }
                // Check Missions
                $result222 = $mysql->query("SELECT * FROM `missions` WHERE `userid`='".$user_class->id."' AND `outcome` = '0'");
                if (mysql_num_rows($result222) > 0) {
                    $worked222 = mysql_fetch_array($result222);
                    if ($worked222['50busts'] + 1 == 10) {
                        $result2221 = $mysql->query("UPDATE `grpgusers` SET `points` = `points` + '100' WHERE `id` = '".$user_class->id."' LIMIT 1");
                        $user_class->points += 100;
                        Send_Event($user_class->id, "You received <b>100 Points</b> for successfully completing 10 Prison Busts. (<a href='missions.php'>Missions</a>)");
                        $result_log1 = $mysql->query("INSERT INTO `missions_log` VALUES ('', '".$user_class->id."', '".$worked222['id']."', '10 Busts', '".time()."')");
                    }
                    $result333 = $mysql->query("UPDATE `missions` SET `50busts` = LEAST((`50busts` + 1), 10) WHERE `userid` = '".$user_class->id."' AND `outcome` = '0' LIMIT 1");
                }
                // End Missions

                // Bloodbath Busts
                $currenttime = time();
                $result112 = $mysql->query("SELECT * FROM `competition_bloodbath`");
                $worked112 = mysql_fetch_array($result112);
                if (($currenttime >= $worked112['starts']) && ($currenttime <= $worked112['ends'])) {
                    $mysql->query( "INSERT INTO `competition_bloodbath_users` VALUES ('".$user_class->id."', '0', '0', '0', '0', '0', '0', '1', '0') ON DUPLICATE KEY UPDATE `most_busts`=`most_busts`+'1'" );
                }

                echo Message("Success! You receive ".$exp." exp.".$max_level."<br><br><a href='jail.php'>Back</a>");
                //send even to that person
                Send_Event($jailbreak, "You have been busted out of jail by ".$user_class->formattedname .".", "bust");

            }
		} elseif ($chance == 9) {
			echo Message("You were caught. You were hauled off to jail for " . 20 . " minutes.");
			$jail = 1200;
			$result = $mysql->query("UPDATE `grpgusers` SET `jail` = ".time()." + 1200, `nerve`=`nerve`-'".$nerve."', `prison_caught`=`prison_caught`+'1' WHERE `id`='".$user_class->id."'");
            $user_class->jail = time() + 1200;
            $user_class->nerve -= $nerve;
                 if ($user_class->prison_caught + 1 == 5000) {
                     $result_i1 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'i', '1', 'Imprisonment', 'Get caught at 5,000 crimes.')");
                 } elseif ($user_class->prison_caught + 1 == 10000) {
                     $result_i2 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'i', '2', 'Imprisonment', 'Get caught at 10,000 crimes.')");
                 } elseif ($user_class->prison_caught + 1 == 25000) {
                     $result_i3 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'i', '3', 'Imprisonment', 'Get caught at 25,000 crimes.')");
                 }
		} else{
			echo Message("You failed.");
			$crimefailed = 1 + $user_class->crimefailed;
			$result = $mysql->query("UPDATE `grpgusers` SET `nerve`=`nerve`-'".$nerve."', `crimefailed`=`crimefailed`+'1' WHERE `id`='".$user_class->id."'");
            $user_class->nerve -= $nerve;
                 if ($user_class->crimefailed + 1 == 50000) {
                     $result_cf1 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'cf', '1', 'Crime Failed', 'Fail to commit 50,000 crimes.')");
                 } elseif ($user_class->crimefailed + 1 == 200000) {
                     $result_cf2 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'cf', '2', 'Crime Failed', 'Fail to commit 200,000 crimes.')");
                 } elseif ($user_class->crimefailed + 1 == 500000) {
                     $result_cf3 = $mysql->query("INSERT IGNORE INTO `medals` (`id`, `userid`, `timewhen`, `whatmedal`, `medallevel`, `desc`, `desc2`) VALUES ('', '".$user_class->id."', '".time()."', 'cf', '3', 'Crime Failed', 'Fail to commit 500,000 crimes.')");
                 }
		}
	} else {
		echo Message("You don't have enough nerve for that crime.");
	}
    $checkmail = $mysql->query("select count(*) as jail from grpgusers where jail > ".time()."");
    $nummsgs = mysql_fetch_array($checkmail);
    if ($nummsgs["jail"] > 0) { $jail = "<span style='color:#f41818'>[".$nummsgs["jail"]."]</span>"; }
        else { $jail = "[".$nummsgs["jail"]."]"; }
    ?>
    <script>$(document).ready(function(){    $("#jail_container").html( "<?php echo $jail;?>");});</script>
	<?php

    include 'footer.php';
    die();
}
?>
<div class="contenthead">Prison</div><!--contenthead-->
<div class="contentcontent">
<?php echo $newbietext; ?>
<table id='cleanTable' width='100%' cellpadding='4' cellspacing='0'>
	<tr>
		<td id='headerRow'>Gangsta</td>
		<td id='headerRow'>Time Left [Mins]</td>
		<td id='headerRow'>Actions</td>
	</tr>
	<?php

$result = $mysql->query("SELECT grpgusers.id, grpgusers.jail, grpgusers.gamename, grpgusers.exp, grpgusers.gang, grpgusers.lastactive, grpgusers.rmdays, grpgusers.namestyle, grpgusers.gndays, grpgusers.gncode, grpgusers.cindays, grpgusers.cinapproved, grpgusers.street_level, grpgusers.accesslevel, gangs.id as gangid, gangs.leader, gangs.name, gangs.tagcolor, gangs.tag from grpgusers left join gangs on gangs.id=grpgusers.gang where grpgusers.jail > ".time()." ORDER BY grpgusers.jail DESC LIMIT 10");
	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    if($line['jail'] != 0){
			echo "<tr>
                <td id='dottedRow'>".$user_class->make_user_name($line)."</td>
                <td id='dottedRow'>".floor(($line['jail'] - time()) / 60)."</td>
                <td id='dottedRow'><a href = 'jail.php?jailbreak=".$line['id']."'>Break Out</a></td>
                </tr>";
		}
	}
	?>
</table>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<?php

include 'footer.php';
?>
