<?php

//header('Content-type: application/json');
session_start();
if($_SESSION['id'] == 134)
    seasion_destroy();
header('Location :index.php');
}

function error($msg)
{
    $response = array();
    $response['success'] = 'false';
    $response['error'] = $msg;

    return $response;
}

function success($msg)
{
    $response = array();
    $response['success'] = 'TRUE';
    $response['message'] = $msg;

    return $response;
}

include "classes.php";
include "database/pdo_class.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = new User($_SESSION['id']);
session_write_close();

$response = array();

if (!isset($_GET['alv'])) {
    echo json_encode(error('Something went wrong.'));
    exit;
}
if ($_GET['alv'] !== 'yes') {
    echo json_encode(error('Something went wrong.'));
    exit;
}


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

if ($attack_person->mprotection > time() ) {
    $response = error("This player is currently under mug protection.");

    echo json_encode($response);
    exit;
}


if ($user_class->fbitime > 0) {
    $response['success'] = 'false';
    $response['error'] = 'You cant perform a mug whilst in FBI Jail.';
}

if ($attack_person->fbitime > 0) {
    $response['success'] = 'false';
    $response['error'] = 'This user is currently in FBI Jail.';
}

if ($attack_person->city != $user_class->city)
    $response = error("You must be in the same city as the person you are mugging.");
else if ($user_class->nerve < 10 && !refill('n'))
    $response = error("You need to have at least 10 nerve if you want to mug someone.");
else if ($attack_person->level < 0)
    $response = error("This player is under newbie protection.");
else if ($user_class->jail > 0)
    $response = error("You can't mug someone if you're in prison.");
else if ($user_class->hospital > 0)
    $response = error("You can't mug someone if you're in the hospital.");
else if ($_GET['mug'] == "")
    $response = error("You didn't choose someone to mug.");
else if ($_GET['mug'] == $user_class->id)
    $response = error("You can't mug yourself.");
else if ($attack_person->username == "")
    $response = error("That person doesn't exist.");
else if ($attack_person->hospital > 0)
    $response = error("You can't mug someone that's in hospital.");
else if ($attack_person->jail > 0)
    $response = error("You can't mug someone that's in prison.");
else if ($attack_person->gang == $user_class->gang && $user_class->gang > 0)
    $response = error("You can't mug someone that's in your gang.");
else if ($attack_person->id == $user_class->relplayer)
    $response = error("You can't mug your partner.");
else if ($attack_person->mprotection > time())
    $response = error("Your target is under mug protection and cannot be mugged.");
else if ($attack_person->admin > 0)
    $response = error("You can't mug an admin.");
else if ($user_class->mprotection > time())
    $response = error("You have an active mug protection and cannot mug during this time.");
else if ($attack_person->mprotection > time())
    $response = error("This player is currently under mug protection.");

if (isset($response['error'])) {
    echo json_encode($response);
    exit;
}

$newnerve = $user_class->nerve - 10;
$db->query("UPDATE grpgusers SET nerve = '" . $newnerve . "' WHERE id=" . $user_class->id);
$db->execute();

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


        $response = error('The FBI are watching this player! You have been sent to FBI Jail!');
        echo json_encode($response);
        exit;
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
                //echo "Gang upgrade level 8: " . $gangs->upgrade8;

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
            mission('m');
            newmissions('mugs');
            gangContest(array('mugs' => 1));
            bloodbath('mugs', $user_class->id);
            $db->query("UPDATE grpgusers SET moth = moth + 1, motd = motd + 1 WHERE id = $user_class->id");
            $db->execute();
            $toadd = array('motd' => 1);
            ofthes($user_class->id, $toadd);

            $response = success("You reach into $attack_person->formattedname's pockets and find nothing!");
            echo json_encode($response);
            exit;
        } else {
            if ($user_class->gang != 0 && $gang_class->tax > 0) {
                $tax = round($mugamount / 100) * $gang_class->tax;
                $mugamount = $mugamount - $tax;
                $newvault = $gang_class->moneyvault + $tax;
                $db->query("UPDATE `gangs` SET `moneyvault` = '" . $newvault . "' WHERE `id` = '" . $user_class->gang . "'");
                $db->execute();
            }
            $newmuggedamount = $attack_person->money - $mugamount;
            $newmuggedamount1 = $attack_person->money - ($mugamount + $tax);

            $newmuggeramount = $user_class->money + $mugamount;
            $muggedmoneygain = $user_class->muggedmoney + $mugamount;
            $muggedmoneylost = $attack_person->muggedmoney - $mugamount;
            $mugsucceeded = 1 + $user_class->mugsucceeded;
            $toadd = array('motd' => 1);
            ofthes($user_class->id, $toadd);

            if ($user_class->gang != 0){
                $db->query("UPDATE gangs SET dailyMugs = dailyMugs + 1 WHERE id = ?");
                $db->execute(array(
                    $user_class->gang
                ));
            }

            Send_Event($attack_person->id, "You were mugged by [-_USERID_-]. They stole " . prettynum($mugamount, 1) . ".", $user_class->id);

            $db->query("UPDATE grpgusers SET money = $newmuggeramount, muggedmoney = $muggedmoneygain, mugsucceeded = $mugsucceeded, moth = moth + 1, motd = motd + 1, tamt = tamt + $mugamount WHERE id = $user_class->id");
            $db->execute();

            $db->query("UPDATE grpgusers SET muggedmoney = $muggedmoneylost, money = $newmuggedamount1 WHERE id = $attack_person->id");
            $db->execute();

            $online = (time() - $attack_person->lastactive < 900) ? 1 : 0;
            $db->query("INSERT INTO muglog (mugger, mugged, amount, active, timestamp) VALUES($user_class->id,$attack_person->id,$mugamount,$online,unix_timestamp())");
            $db->execute();

            mission('m');
            newmissions('mugs');
            gangContest(array('mugs' => 1));
            bloodbath('mugs', $user_class->id);

            $response = success("You successfully mugged $attack_person->formattedname for " . prettynum($mugamount, 1) . ".");
            echo json_encode($response);
            exit;
        }
    } else {
        Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
        $response = success("You failed to mug " . $attack_person->formattedname . ".");
        echo json_encode($response);
        exit;
    }
} else if ($mug == 9) {
    Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);

    $response = success("You failed to mug " . $attack_person->formattedname . ".");
    echo json_encode($response);
    exit;
} else {
    Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
    $timee = 300;

    $db->query("UPDATE `grpgusers` SET `jail` = '" . $timee . "' WHERE `id`='" . $user_class->id . "'");
    $db->execute();

    $response = success("You failed and were sent to prison for 5 minutes!.");
    echo json_encode($response);
    exit;
}
