<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
$energyneeded = floor($user_class->maxenergy / 5);
if ($user_class->energy < $energyneeded)
	refill('e');
if ($user_class->energy < $energyneeded)
	backalleyerror("You need at least 20% of your energy to explore the back alley!");
if ($user_class->hospital > 0)
	backalleyerror("You cannot go in the back alley if you are in the hospital.");
if ($user_class->jail > 0)
	backalleyerror("You cannot go in the back alley if you are in Jail.");
$names = array(
	'Frank Sinatra',
	'Al Capone',
	'Henry Hill',
	'Ronnie Kray',
	'Reggie Kray',
	'Paul Castellano'
);
$attuser = array_rand($names);
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
echo $itext . "<br />";
if ($randout == 1) {
	echo 'You hit <font color=red>' . $attuser . '</font> for 20 damage. <br />';
	echo '<font color=red>' . $attuser . '</font> hit you for ' . $user_class->hp . ' damage. <br /><br />';
	echo '<br /><h3><font color=red><b>FAILED!</b></font></h3><br />';
	echo $ftext;
	$hosp = 300;

	$hwhen = date("g:i:sa", time());
	perform_query("UPDATE `grpgusers` SET `hwho` = ?, `hhow` = 'backalley', `hwhen` = ?, `hospital` = ? WHERE id = ?", [$attuser, $hwhen, $hosp, $user_class->id]);

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

		perform_query("UPDATE `grpgusers` SET `exp` = `exp` + ?, `money` = `money` + ?, `points` = `points` + ? WHERE `id` = ?", [$expgain, $randnum14, $points, $user_class->id]);
	} else if ($randfind < 80) {
		// found money only
		$randnum13 = rand(5, 25);
		$randnum14 = $randnum13 * ($user_class->level + 2);
		if ($randnum14 > 10000) {
			$randnum14 = 10000;
		}

		if ($randfind2 == 1) {
			$rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp,  $" . prettynum($randnum14) . ",";
		} else {
			$rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and $" . prettynum($randnum14);
		}

		perform_query("UPDATE `grpgusers` SET `exp` = `exp` + ?, `money` = `money` + ? WHERE `id` = ?", [$expgain, $randnum14, $user_class->id]);
	} else {
		// found points only
		$points = rand(5, 15);
		if ($randfind2 == 1) {
			$rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp, " . prettynum($points) . " points,";
		} else {
			$rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and " . prettynum($points) . " points";
		}

		perform_query("UPDATE `grpgusers` SET `exp` = `exp` + ?, `points` = `points` + ? WHERE `id` = ?", [$expgain, $points, $user_class->id]);
	}
	$rtext .= "!</font>";

	perform_query("UPDATE `grpgusers` SET `energy` = `energy` - ?, `backalleywins` = `backalleywins` + 1 WHERE `id` = ?", [$energyneeded, $user_class->id]);

	$toadd = array('baotd' => 1);
	ofthes($user_class->id, $toadd);

	echo $rtext;
}

echo "<br /><br /><form method='POST' action='backalley.php?use=yes'>
			<input type='submit' name='CheckOut' value='Check the alley again' style='background: #000000;color: #FFFFFF;'/>
		</form></td></tr>";
function backalleyerror($text)
{
	echo '<div class="floaty" style="background:rgba(128,0,0,.25);">' . $text . '</div>';
	die();
}
?>