<?php
include 'header.php';
exit;
?>
<div class='box_top'>Mug</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php

                                //macroTokenCheck($user_class);

$attack_person = new User($_GET['mug']);
$gang_class = new Gang($user_class->gang);
// Only modify city for special NPCs or characters, if intended
if ($attack_person->id >= 336 AND $attack_person->id <= 353) {
    // Adjusted properties for special NPCs or characters
    $attack_person->level = $user_class->level + $attack_person->id - 339;
    $attack_person->hp = $attack_person->purehp = $attack_person->maxhp = $attack_person->puremaxhp = $attack_person->level * 50;
    $attack_person->hppercent = 100;
    $attack_person->formattedhp = $attack_person->hp . " / " . $attack_person->maxhp . " [100%]";
    // Comment out or conditionally execute this line to avoid bypassing city check
    // $attack_person->city = $user_class->city; // REMOVE OR MODIFY THIS LINE
    $attack_person->jail = 0;
    $attack_person->moddedstrength = rand(750 * (($attack_person->id - 333) / 10), 2200 * (($attack_person->id - 333) / 10));
    $attack_person->moddedspeed = rand(750 * (($attack_person->id - 333) / 10), 2200 * (($attack_person->id - 333) / 10));
    $user_class->moddedstrength = rand(1000, 5000);
    $user_class->moddeddefense = rand(1000, 5000);
    $user_class->moddedspeed = rand(1000, 5000);
}



if ($attack_person->mprotection > time() )
    error("This Mobster is under Mug Protection.");

if ($user_class->fbitime > 0)
    error("You cant perform a mug whilst in FBI Jail.");

if ($attack_person->fbitime > 0)
    error("This user is currently in FBI Jail.");




if ($attack_person->city != $user_class->city)
    error("You must be in the same city as the person you are mugging.");
else if ($user_class->nerve < 10 && !refill('n'))
    error("You need to have at least 10 nerve if you want to mug someone.");
else if ($attack_person->level < 0)
    error("This player is under newbie protection.");
else if ($user_class->jail > 0)
    error("You can't mug someone if you're in prison.");
else if ($user_class->hospital > 0)
    error("You can't mug someone if you're in the hospital.");
else if ($_GET['mug'] == "")
    error("You didn't choose someone to mug.");
else if ($_GET['mug'] == $user_class->id)
    error("You can't mug yourself.");
else if ($attack_person->username == "")
    error("That person doesn't exist.");
else if ($attack_person->hospital > 0)
    error("You can't mug someone that's in hospital.");
else if ($attack_person->jail > 0)
    error("You can't mug someone that's in prison.");
else if ($attack_person->gang == $user_class->gang && $user_class->gang > 0)
    error("You can't mug someone that's in your gang.");
else if ($attack_person->id == $user_class->relplayer)
    error("You can't mug your partner.");
else if ($attack_person->mprotection > time())
    error("Your target is under mug protection and cannot be mugged.");
else if ($attack_person->admin > 0)
    error("You can't mug an admin.");
else if ($user_class->mprotection > time())
    error("You Have an active mug protection and cannot mug during this time.");


$mug = mt_rand(0, 10);

// If they have used a Police Badge, override a jail with a fail
if ($mug > 9 && $user_class->bustpill > 0) {
    $mug = 9;
}

if ($mug <= 8) {
    $success = true;

    $robinfo = explode("|", $attack_person->robInfo);
    if ($robinfo[0] == 1) {
        $rand = mt_rand(0, 2);
        if ($rand != 0) {
            $success = false;
        }
    }


if ($attack_person->fbi > 0) {

$db->query("UPDATE grpgusers SET fbitime = ? WHERE id = ?");
         $db->execute(array(
             15,
             $user_class->id
         ));

         Send_Event($user_class->id, "You mugged a player being watched by the FBI! You landed yourself in Federal Jail for 15 minutes!");
         Send_Event($attack_person->id, "You just got mugged and this player has landed themselves in FBI Jail!");



    echo "<b><font color='red'>The FBI are watching this player! You have been sent to FBI Jail!</font> </b></br></br>";
}


if ($success) {
    $divide = rand(7, 8);
    $mugamount = floor($attack_person->money / $divide);

    // Check if the user has pack1 = 5 and apply the 25% bonus to mugged amount
    if ($gang_class->upgrade8 >= 1) {
                $mugamount = floor($mugamount * (1 + 0.10 * $gang_class->upgrade8));
    }

    // Check if the user is in a gang
    if ($user_class->gang > 0) {
        // Fetch the gang's upgrade levels
        $db->query("SELECT upgrade8 FROM gangs WHERE id = ?");
        $db->execute(array($user_class->gang));
        $gangs = $db->fetch_row();

        // Check if upgrade8 is set and is numeric
 if ($gangs->upgrade8 > 0) {
            $gangs->upgrade8 = $gangs['upgrade8']; // Assuming $gangs is an object you're using to store gang info
        echo "Gang upgrade level 8: " . $gangs->upgrade8;

            // Apply the upgrade only if upgrade8 is greater than 0
            if ($gangs->upgrade8 > 0) {
                $mugamount = floor($mugamount * (1 + 0.10 * $gangs->upgrade8));
            }
        } else {
            // Log error if upgrade8 is not set or not numeric
            error_log("upgrade8 is not set or not a valid number for gang ID: " . $user_class->gang);
        }
    }

        if ($mugamount < 1) {
            echo Message("You reach into $attack_person->formattedname's pockets and find nothing!");
            mission('m');
            newmissions('mugs');
            updateGangActiveMission('mugs', 1);
            gangContest(array('mugs' => 1));
            bloodbath('mugs', $user_class->id);
            mysql_query("UPDATE grpgusers SET moth = moth + 1, motd = motd + 1 WHERE id = $user_class->id");
            $toadd = array('motd' => 1);
            ofthes($user_class->id, $toadd);
        } else {
            if ($user_class->gang != 0 && $gang_class->tax > 0) {
                $tax = round($mugamount / 100) * $gang_class->tax;
                $mugamount = $mugamount - $tax;
                gangContest(array(
                    'tax' => $tax
                ));
                $newvault = $gang_class->moneyvault + $tax;
                $result2 = mysql_query("UPDATE `gangs` SET `moneyvault` = '" . $newvault . "' WHERE `id` = '" . $user_class->gang . "'");
            }
            $newmuggedamount = $attack_person->money - $mugamount;
            $newmuggedamount1 = $attack_person->money - ($mugamount + $tax);

            $newmuggeramount = $user_class->money + $mugamount;
            $muggedmoneygain = $user_class->muggedmoney + $mugamount;
            $muggedmoneylost = $attack_person->muggedmoney - $mugamount;
            $mugsucceeded = 1 + $user_class->mugsucceeded;
            $toadd = array('motd' => 1);
            ofthes($user_class->id, $toadd);
            echo Message("You successfully mugged $attack_person->formattedname for " . prettynum($mugamount, 1) . ".<br /><br /><a href='mug.php?mug={$_GET['mug']}'>Mug Again</a>");

if ($user_class->gang != 0){
        $db->query("UPDATE gangs SET dailyMugs = dailyMugs + 1 WHERE id = ?");
        $db->execute(array(
            $user_class->gang
        ));
    }



            Send_Event($attack_person->id, "You were mugged by [-_USERID_-]. They stole " . prettynum($mugamount, 1) . ".", $user_class->id);
            mysql_query("UPDATE grpgusers SET money = $newmuggeramount, muggedmoney = $muggedmoneygain, mugsucceeded = $mugsucceeded, moth = moth + 1, motd = motd + 1, tamt = tamt + $mugamount WHERE id = $user_class->id");
            mysql_query("UPDATE grpgusers SET muggedmoney = $muggedmoneylost, money = $newmuggedamount1 WHERE id = $attack_person->id");
            $online = (time() - $attack_person->lastactive < 900) ? 1 : 0;
            mysql_query("INSERT INTO muglog (mugger, mugged, amount, active, timestamp) VALUES($user_class->id,$attack_person->id,$mugamount,$online,unix_timestamp())");
            mission('m');
            newmissions('mugs');
            updateGangActiveMission('mugs', 1);
            gangContest(array('mugs' => 1));
            bloodbath('mugs', $user_class->id);

            db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
            $db->execute();
            $activityContest = $db->fetch_row();
            if ($activityContest['type'] == 'mugs') {
                addToUserCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
            }
        }
    } else {
        echo Message("You failed to mug " . $attack_person->formattedname . ".<br /><br /><a href='mug.php?mug=" . $_GET['mug'] . "'>Try Again</a>");
        //Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
    }
} else if ($mug == 9) {
    echo Message("You failed to mug " . $attack_person->formattedname . ".<br /><br /><a href='mug.php?mug=" . $_GET['mug'] . "'>Try Again</a>");
    //Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
} else {
    echo Message("You failed and were sent to prison for 5 minutes!.");
    //Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
    $timee = 300;
    $result = mysql_query("UPDATE `grpgusers` SET `jail` = '" . $timee . "' WHERE `id`='" . $user_class->id . "'");
}
$newnerve = $user_class->nerve - 10;
$result = mysql_query("UPDATE `grpgusers` SET `nerve` = '" . $newnerve . "', `last_mug_time` = " . time() . " WHERE `id`='" . $user_class->id . "'");
include 'footer.php';
function error($msg) {
    echo Message($msg);
    include 'footer.php';
    die();
}
