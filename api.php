<?php
include "ajax_header.php";
print"<html style='background:#333;'>";
print"<span style='color:pink;font-size:2em;'>Counter: " . $m->get('apicounter') . "</span>";
$uid = $_SESSION['id'];
if($uid == 9)
	$uid = 21;
$user_class = new User($uid);
if(isset($_GET['name']))
	die(formatName($_GET['name']));

if(isset($_GET['reset'])){
	$m->set('apicounter', 0);
} else {

for($i = 0; $i < 10; $i++){
if(isset($_GET['kd']))
	tap($_GET['kd']);

$user_class = new User($uid);
if(isset($_GET['fetch']))
	take($_GET['fetch']);
$user_class = new User($uid);
if(isset($_GET['kd']))
	$m->increment('apicounter');
}
$db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array(
	$user_class->id
));
}

print"<span style='color:pink;font-size:2em;'>Counter: " . $m->get('apicounter') . "</span>";



































































function take($id){
	global $user_class, $db;
	switch($id){
		case 11:
		case 12:
		case 13:
		case 14:
			if($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital)
				die("You already have full HP and are not in the hospital.");
			else
				Take_Item($id, $user_class->id);
			$db->query("SELECT * FROM items WHERE id = ?");
			$db->execute(array(
				$id
			));
			$row = $db->fetch_row(true);
			$hosp = floor(($user_class->hospital / 100) * $row['reduce']);
			$newhosp = $user_class->hospital - $hosp;
			$newhosp = ($newhosp < 0) ? 0 : $newhosp;
			$hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
			$hp = $user_class->purehp + $hp;
			$hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
			$db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
			$db->execute(array(
				$newhosp,
				$hp,
				$user_class->id
			));
			break;
		default:
			die();
	}
	$db->query("SELECT quantity FROM inventory WHERE userid = ? AND itemid = ?");
	$db->execute(array(
		$user_class->id,
		$id
	));
	$qty = $db->fetch_single();
	if($qty == 0)
		echo("You are out of items.");
	else
		echo("You have $qty items.");
}
function tap($id){
	global $user_class, $db;
	if ($user_class->energypercent < 25)
		refill('e');
	$error = "";
	$error = ($user_class->energypercent < 25) ? "You need to have at least 25% of your energy if you want to attack someone." : $error;
	$error = ($user_class->jail > 0) ? "You can't attack someone if you are in prison." : $error;
	$error = ($id == "") ? "You didn't choose someone to attack." : $error;
	$error = ($id == $user_class->id) ? "You can't attack yourself." : $error;
	$attack_person = new User($id);
	$error = ($attack_person->city != $user_class->city && $user_class->id != 146) ? "You must be in the same city as the person you're attacking!" : $error;
	$error = ($attack_person->username == "") ? "That person doesn't exist." : $error;
	$error = ($attack_person->hospital > 0) ? "You can't attack someone thats in hospital." : $error;
	$error = ($attack_person->jail > 0) ? "You can't attack someone thats in prison." : $error;
	$error = ($user_class->gang == $attack_person->gang && $user_class->gang > 0) ? "You can't attack someone in your gang." : $error;
	$error = ($attack_person->hppercent < 25) ? "They Need Over 25% HP to be attacked." : $error;
	$error = ($attack_person->admin == 1) ? "Im sorry, You cannot attack the owner" : $error;
	$gang_class = new Gang($user_class->gang);
	$their_gang = new Gang($attack_person->gang);
	if (!empty($error))
		die($error . "<br /><br /><a href='index.php'>Home</a>");
	$yourhp = $user_class->hp;
	$theirhp = $attack_person->hp;
	$userspeed = $user_class->moddedspeed;
	$attackspeed = $attack_person->moddedspeed;
	$wait = ($userspeed > $attackspeed) ? 1 : 0;
	$number = 0;
	if ($user_class->invincible == 0)
		if ($attack_person->invincible == 0)
			while ($yourhp > 0 && $theirhp > 0) {
				$damage = round($attack_person->moddedstrength) - $user_class->moddeddefense;
				$damage = ($damage < 1) ? 1 : $damage;
				if ($wait == 0) {
					$yourhp = $yourhp - $damage;
					$number++;
					echo $number . ":&nbsp;" . $attack_person->formattedname . " hit you for " . prettynum($damage) . " damage using their " . $attack_person->weaponname . ". <br>";
				} else
					$wait = 0;
				if ($yourhp > 0) {
					$damage = round($user_class->moddedstrength) - $attack_person->moddeddefense;
					$damage = ($damage < 1) ? 1 : $damage;
					$theirhp = $theirhp - $damage;
					$number++;
					echo $number . ":&nbsp;" . "You hit " . $attack_person->formattedname . " for " . prettynum($damage) . " damage using your " . $user_class->weaponname . ". <br>";
				}
			} else
			$yourhp = 0;
	else
		$theirhp = 0;
	if ($theirhp <= 0) {
		$winner = $user_class->id;
		$moneywon = floor($attack_person->money / rand(8, 9));
		$expwon = 100 - (100 * ($user_class->level - $attack_person->level));
		$expwon = ($expwon < 10) ? 10 : $expwon;
		$expwon = ($expwon > 12000) ? 12000 : $expwon;
		$expwon *= (.15 * $user_class->prestige) + 1;
		$expwon = floor($expwon);
		$newexp = $expwon + $user_class->exp;
		$expwon2 = $expwon;
		$theirhp = 0;
		bloodbath('defendlost', $attack_person->id);
		bloodbath('attackswon', $user_class->id);
		$db->query("UPDATE grpgusers SET koth = koth + 1, loth = loth + ?, todaysexp = todaysexp + ?, exp = exp + ?, money = money + ?, battlewon = battlewon + 1, battlemoney = battlemoney + ?, todayskills = todayskills + 1, killcomp = killcomp + 1 WHERE id = ?");
		$db->execute(array(
		   $expwon,
		   $expwon,
		   $expwon,
		   $moneywon, 
		   $moneywon, 
		   $user_class->id
		));
		$db->query("UPDATE grpgusers SET money = money - ?, hwho = ?, hhow = 'wasattacked', hwhen = ?, hospital = 300, battlelost = battlelost + 1, delay = delay + 10, battlemoney = battlemoney - ? WHERE id = ?");
		$db->execute(array(
			$moneywon,
			$user_class->id,
			date("g:i:sa", time()),
			$moneywon,
			$attack_person->id
		));
		$db->query("UPDATE pets SET exp = exp + ($expwon) / 10 WHERE userid = $user_class->id AND leash = 1");
		$db->execute();
		Send_Event($attack_person->id, "[-_USERID_-] attacked you and won! They gained " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . ".", $user_class->id);
		Send_Event1($attack_person->id, "Was attacked by [-_USERID_-]  and lost the fight! They gained " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . ".", $user_class->id);
		echo"You attacked " . $attack_person->formattedname . " and won! You gain " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . "." . $wartext;
		if ($user_class->gang != 0){
			$db->query("UPDATE gangs SET exp = exp + ?, bbattackwon = bbattackwon + 1 WHERE id = ?");
			$db->execute(array(
				$expwon,
				$user_class->gang
			));
		}
		mission('k');
		gangContest(array(
			'kills' => 1,
			'exp' => $expwon
		));
	}
	if ($yourhp <= 0) {
		$winner = $attack_person->id;
		$moneywon = floor($user_class->money / rand(8, 9));
		$expwon = 100 - (100 * ($attack_person->level - $user_class->level));
		$expwon = ($expwon < 10) ? 10 : $expwon;
		$expwon = ($expwon > 12000) ? 12000 : $expwon;
		$expwon *= (.15 * $attack_person->prestige) + 1;
		$expwon = floor($expwon);
		$expwon2 = $expwon;
		$yourhp = 0;
		bloodbath('attackslost', $user_class->id);
		bloodbath('defendwon', $attack_person->id);
		$db->query("UPDATE grpgusers SET todaysexp = todaysexp + ?, exp = exp + ?, money = money + ?, battlewon = battlewon + 1, battlemoney = battlemoney + ?, todayskills = todayskills + 1 WHERE id = ?");
		$db->execute(array(
			$expwon,
			$expwon,
			$moneywon,
			$moneywon, 
			$attack_person->id
		));
		$db->query("UPDATE grpgusers SET money = money - ?, hwho = ?, hhow = 'attacked', hwhen = ?, hospital = 300, battlelost = battlelost + 1, delay = delay + 10, battlemoney = battlemoney - ? WHERE id = ?");
		$db->execute(array(
			$moneywon,
			$attack_person->id,
			date("g:i:sa", time()),
			$moneywon,
			$user_class->id
		));
		$db->query("UPDATE gangs SET bbattacklost = bbattacklost + 1 WHERE id = ?");
		$db->execute(array(
			$user_class->gang
		));
		$db->query("UPDATE pets SET exp = exp + ($expwon) / 10 WHERE userid = $attack_person->id AND leash = 1");
		$db->execute();
		Send_Event($attack_person->id, "[-_USERID_-] attacked you and lost! You gained " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . ".", $user_class->id);
		echo $attack_person->formattedname . " won the battle!";
		if ($attack_person->gang != 0){
			$db->query("UPDATE gangs SET exp = exp + ? WHERE id = ?");
			$db->execute(array(
				$expwon,
				$attack_person->gang
			));
		}
	}
	$db->query("INSERT INTO attacklog (`timestamp`, attacker, defender, winner, exp, money, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$db->execute([time(), $user_class->id, $attack_person->id, $winner, $expwon, $moneywon, (time() - $attack_person->lastactive <= 900) ? 1 : 0]);
	if ($attack_person->gang != 0) {
		$active = (time() - $attack_person->lastactive < 900) ? 1 : 0;
		$db->query("INSERT INTO deflog (timestamp, gangid, attacker, defender, winner, gangexp, active) VALUES (unix_timestamp(), ?, ?, ?, ?, ?, ?)");
		$db->execute(array(
			$attack_person->gang,
			$user_class->id,
			$attack_person->id,
			$winner,
			$expwon2,
			$active 
		));
	}
	if ($user_class->gang != 0) {
		$active = (time() - $attack_person->lastactive < 900) ? 1 : 0;
		$db->query("INSERT INTO attlog (timestamp, gangid, attacker, defender, winner, gangexp, active) VALUES (unix_timestamp(), ?, ?, ?, ?, ?, ?)");
		$db->execute(array(
			$user_class->gang,
			$user_class->id,
			$attack_person->id,
			$winner,
			$expwon2,
			$active 
		));
	}
	$winner_class = new User($winner);
	$db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1");
	$db->execute(array(
		$user_class->gang,
		$user_class->gang,
		$attack_person->gang,
		$attack_person->gang
	));
	if ($winner_class->gang != 0 && $db->num_rows()){
		$row = $db->fetch_row(true);
		if (time() < $row['timeending']) {
			$active = (time() - $attack_person->lastactive < 900) ? 50 : 10;
			$wingang = ($winner_class->gang == $row['gang1']) ? 1 : 2;
			$db->query("UPDATE gangwars SET gang{$wingang}score = gang{$wingang}score + ? WHERE warid = ?");
			$db->execute(array(
				$active,
				$row['warid']
			));
			print "<br />You have also gained $active gang war points for your gang.";
		}
	}
	$user_class->energy -= floor(($user_class->maxenergy / 100) * 25);
	$theirhp = ($theirhp > $attack_person->puremaxhp) ? $attack_person->puremaxhp : $theirhp;
	$yourhp = ($yourhp > $user_class->puremaxhp) ? $user_class->puremaxhp : $yourhp;
	$db->query("UPDATE grpgusers SET hp = ? WHERE id = ?");
	$db->execute(array(
		$theirhp,
		$attack_person->id
	));
	$db->query("UPDATE grpgusers SET hp = ?, energy = ? WHERE id = ?");
	$db->execute(array(
		$yourhp,
		$user_class->energy,
		$user_class->id
	));
}
if (isset($_GET['ba'])) {
    $file = '/usr/share/nginx/logs/actlog.txt';
    $current = "21|-|-|/backalley.php?use=yes|-|-|array()|-|-|" . time() . ";\n";
    file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
    $energyneeded = floor($user_class->maxenergy / 5);
    if ($user_class->energy < $energyneeded)
        refill('e');
    if ($user_class->energy < $energyneeded) {
        echo Message("You need at least 20% of your energy to explore the back alley!");
        include 'footer.php';
        die();
    }
    $randname = rand(1, 6);
    switch ($randname) {
        case 1:
            $attuser = "Private Niev";
            break;
        case 2:
            $attuser = "Private First Class Xali";
            break;
        case 3:
            $attuser = "Sergeant Beck";
            break;
        case 4:
            $attuser = "Sergeant First Class Walter";
            break;
        case 5:
            $attuser = "Captain Jericho";
            break;
        default:
            $attuser = "Colonel Pete";
            break;
    }
    $randscenario = rand(1, 4);
    switch ($randscenario) {
        case 1:
            $itext = "You slowly walk down the alley and reach a dead end. You turn around to walk back and"
                    . $attuser . " is blocking your way, ready to fight!";
            $stext = "You beat them up whilst they pleaded for mercy!";
            $ftext = "They really kicked your butt, spiting in your face as they walk off in triumph.";
            break;
        case 2:
            $itext = "You walk confidently down the alley and " . $attuser . " hits you from behind. What a coward!
				You get up ready to kick their butt!";
            $stext = "You punch them into the wall and leave them bleeding on the street.";
            $ftext = "They knock you back down on the alleyway, and instead of getting back up, you lay there as they
				laugh and walk away.";
            break;
        case 3:
            $itext = "You go with a buddy down the alley and " . $attuser . " walks in front of you ready to fight!
				Your buddy runs away, leaving you there to fight them!";
            $stext = "They run away, chasing your friend down as they have a grudge against them. Well that was rather
				anti-climatic.";
            $ftext = "They knock you out with one blow. Your buddy was smart to run!";
            break;
        default:
            $itext = "You meet up with " . $attuser . " in the alley to buy some contraband, but it turns out that they're
				wearing a wire!";
            $stext = "You beat them up, tearing the wire apart! You then run away in order to not get caught!";
            $ftext = "They knock you down, leaving you there for the cops. Guess you were not as strong as you thought!";
            break;
    }
    $randout = rand(1, 4);
    ?>
    <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Back Alley</td></tr>
    <tr>
        <td class="contentcontent">
    <center>
	<br /><br />
        <?php
        echo $itext . "<br />";
        if ($randout == 1) {
            echo 'You hit <font color=red>' . $attuser . '</font> for 20 damage. <br />';
            echo '<font color=red>' . $attuser . '</font> hit you for ' . $user_class->hp . ' damage. <br /><br />';
            echo '<br /><h3><font color=red><b>FAILED!</b></font></h3><br />';
            echo $ftext;
            $hosp = 300;
			$db->query("UPDATE grpgusers SET hwho = ?, hhow = 'backalley', hwhen = unix_timestamp(), hospital = ? WHERE id = ?");
			$db->execute(array(
				$attuser,
				$hosp,
				$user_class->id
			));
            echo "</center></td></tr>";
            include 'footer.php';
            die();
        } else {
            if ($randscenario != 3) {
                $randnum = rand(10, 30);
                echo '<font color=red>' . $attuser . '</font> hit you for 20 damange. <br />';
                echo 'You hit <font color=red>' . $attuser . '</font> for ' . $user_class->moddedstrength . ' damange. <br /><br />';
            }
            echo '<br /><h3><font color=darkgreen><b>SUCCESS!</b></font></h3><br />';
            echo $stext . '<br />';
            $expgain = round(((rand(1, 5) / 100) * $user_class->maxexp)); // experience gained
            if ($expgain > 2500) {
                $expgain = 2500;
            }
			$expwon *= (.15 * $user_class->prestige) + 1;
			$expwon = floor($expwon);
            $randfind = rand(1, 100); // found points, money, or both
            if ($randfind < 15) {
                // found points & money
                $points = rand(5, 15);
                $randnum13 = rand(5, 25);
                $randnum14 = $randnum13 * ($user_class->level + 2);
                if ($randfind2 == 1) {
                    $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp, $" . prettynum($randnum14) . ", " . $points . " points,";
                } else {
                    $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and $" . prettynum($randnum14);
                }
				$db->query("UPDATE grpgusers SET exp = exp + ?, money = money + ?, points = points + ? WHERE id = ?");
				$db->execute(array(
					$expgain,
					$randnum14,
					$points,
					$user_class->id
				));

            } else if ($randfind < 80) {
                // found money only
                $randnum13 = rand(5, 25);
                $randnum14 = MIN($randnum13 * ($user_class->level + 2), 10000);
                if ($randfind2 == 1) {
                    $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp,  $" . prettynum($randnum14) . ",";
                } else {
                    $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and $" . prettynum($randnum14);
                }
				$db->query("UPDATE grpgusers SET exp = exp + ?, money = money + ? WHERE id = ?");
				$db->execute(array(
					$expgain,
					$randnum14,
					$user_class->id
				));
            } else {
                // found points only
                $points = rand(5, 15);
                if ($randfind2 == 1) {
                    $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp, " . prettynum($points) . " points,";
                } else {
                    $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and " . prettynum($points) . " points";
                }
				$db->query("UPDATE grpgusers SET exp = exp + ?, points = points + ? WHERE id = ?");
				$db->execute(array(
					$expgain,
					$points,
					$user_class->id
				));
            }
            $rtext .= "!</font>";
			$db->query("UPDATE grpgusers SET energy = energy - ?, backalleywins = backalleywins + 1 WHERE id = ?");
			$db->execute(array(
				$energyneeded,
				$user_class->id
			));
            $toadd = array('baotd' => 1);
            ofthes($user_class->id, $toadd);
            echo $rtext;
        }
        echo "<br /><br /><form method='POST' action='backalley.php?use=yes'>
				<input type='submit' name='CheckOut' value='Check the alley again' style='background: #000000;color: #FFFFFF;'/>
			</form></td></tr>";
    }
	print $user_class->exp;
?>