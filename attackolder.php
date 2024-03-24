<?php

include 'header.php';
$_SESSION['lastattack'] = ($_SESSION['lastattack'] == "") ? time() : $_SESSION['lastattack'];

// echo '<script>
// window.history.pushState(null, "", window.location.href);
// window.onpopstate = function () {
//     window.history.pushState(null, "", window.location.href);
// };
// </script>';

// if ($user_class->id == 150) {
//     echo Message("attack");
//     exit();
// }

$ref = $_SERVER['HTTP_REFERER'];
$id = $_GET['attack'];
//if ($ref == "" || ($ref != "https://themafialife.com/profiles.php?id=$id" && $ref != "https://themafialife.com/search.php" && $ref != "https://themafialife.com/attackLadder.php")) {
 //   diefun("Invalid Request - Please ensure you are attacking from the players profile or the Search Player page");
// }

$backSearch = ($_SERVER['HTTP_REFERER'] == 'https://themafialife.com/search.php') ? '<div class="floaty" style="margin:0;font-weight:800;padding:10px"><a href="javascript:history.go(-1);">Back to Search</a></div>' : '';

if ($user_class->id == 99999999) {
    if (isset($_GET['csrf']) && !empty($_GET['csrf'])) {
        if ($_GET['csrf'] != $_SESSION['csrf']) {
            $_SESSION['csrf'] = md5(uniqid(rand(), true));
            echo Message("Invalid Request - Please try again");
            exit();
        }
    } else {
        $_SESSION['csrf'] = md5(uniqid(rand(), true));
        echo Message("Invalid Request - Please try again");
        exit();
    }
}
$_SESSION['csrf'] = md5(uniqid(rand(), true));
//}

$modifier = ($user_class->rmdays > 0) ? 0.2 : 0.25;

$energyneeded = floor($user_class->maxenergy * $modifier);

if (($user_class->energy <= $energyneeded || $user_class->energypercent <= 0) && $user_class->ngyref == 2) {
    manual_refill('e');
}

$attack_person = new User($_GET['attack']);

if ($user_class->id != 174) {
    $error = "";
    $error = ($user_class->energy < $energyneeded) ? "You need 25% energy if you want to attack someone." : $error;
    //$error = ($user_class->energypercent <= 0) ? "You need 25% energy if you want to attack someone." : $error;
    $error = ($user_class->hppercent < 25) ? "You need to have over 25% HP to attack someone." : $error;
    $error = ($user_class->hospital > 0) ? "You can't attack someone if you are in hospital." : $error;
    $error = ($_GET['attack'] == "") ? "You didn't choose someone to attack." : $error;
    $error = ($_GET['attack'] == $user_class->id) ? "You can't attack yourself." : $error;

    if (!empty($error)) {
        echo $backSearch;
        diefun($error . "<br /><br /><a href='index.php'>Home</a>");
    }

    if ($attack_person->id >= 00 and $attack_person->id <= 00) {
        $attack_person->level = $user_class->level + $attack_person->id - 410;
        $attack_person->hp = $attack_person->purehp = $attack_person->maxhp = $attack_person->puremaxhp = $attack_person->level * 50;
        $attack_person->hppercent = 100;
        $attack_person->formattedhp = $attack_person->hp . " / " . $attack_person->maxhp . " [100%]";
        $attack_person->city = $user_class->city;
        $attack_person->jail = 0;
        $attack_person->moddedstrength = rand(750 * (($attack_person->id - 404) / 10), 2200 * (($attack_person->id - 404) / 10));
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
        $attack_person->moddeddefense = rand(750 * (($attack_person->id - 404) / 10), 2200 * (($attack_person->id - 404) / 10));
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
        $attack_person->moddedspeed = rand(750 * (($attack_person->id - 404) / 10), 2200 * (($attack_person->id - 404) / 10));
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
        $user_class->moddedstrength = rand(1000, 5000);
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
        $user_class->moddeddefense = rand(1000, 5000);
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
        $user_class->moddedspeed = rand(1000, 5000);
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
    }

    $error = ($user_class->jail > 0 && $attack_person->jail == 0) ? "You can't attack someone if you are in prison." : $error;
    $error = ($attack_person->jail > 0 && $user_class->jail == 0) ? "You can't attack someone thats in prison." : $error;
    $error = ($attack_person->city != $user_class->city && $user_class->id != 0) ? "You must be in the same city as the person you're attacking!" : $error;
    $error = ($attack_person->username == "") ? "That person doesn't exist." : $error;
    $error = ($attack_person->hospital > 0) ? "You can't attack someone thats in hospital." : $error;
    $error = ($user_class->gang == $attack_person->gang && $user_class->gang > 0) ? "You can't attack someone in your gang." : $error;
    $error = ($attack_person->hppercent < 25) ? "They Need Over 25% HP to be attacked." : $error;
    $error = ($attack_person->admin == 1) ? "Im sorry, You cannot attack the owner" : $error;
    $error = ($attack_person->fbitime > 0) ? "Im sorry, This user is in FBI Jail." : $error;
    $error = ($user_class->fbitime > 0) ? "Im sorry, you are in FBI Jail." : $error;
    $error = ($attack_person->aprotection > time()) ? "This user is under attack protection." : $error;






    //

    $db->query("SELECT COUNT(*) FROM attackladder WHERE `user` = ?");
    $db->execute([$attack_person->id]);
    $attackLadder = $db->fetch_row();

    if (count($attackLadder) == 0) {
        $error = ($attack_person->aprotection > time()) ? "This Mobster is under Attack Protection." : $error;
    }
    //$gang_class = new Gang($user_class->gang);
    //$their_gang = new Gang($attack_person->gang);

    if (!empty($error)) {
        echo $backSearch;
        diefun($error . "<br /><br /><a href='index.php'>Home.</a>");
    }

    $agreed = (isset($_GET['agreed'])) ? $_GET['agreed'] : 0;
}

// if ($user_class->id == 174) {

//     $attack_person->gang = 999;

//     if ($user_class->aprotection > time() && $user_class->gang > 0 && $attack_person->gang > 0 && $agreed == 0) {
//             $db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1");
//             $db->execute(array(
//                 $user_class->gang,
//                 $user_class->gang,
//                 $attack_person->gang,
//                 $attack_person->gang
//             ));
//             $atWar = ($db->num_rows()) ? true : false;

//             if ($atWar) {
//                 diefun('<h3>Warning: You are currently under attack protection.</br></br>As your gang is in an active war with this players gang,</br> attacking this player will terminate your attack protection immediately.</h3></br><a style="font-size: 1.2em" href="attack.php?attack=' . $attack_person->id . '&agreed=1">I understand and wish to continue the attack!</a>');
//             }
//     } else if ($agreed == 1) {
//         $user_class->aprotection = time();
//         $db->query("UPDATE grpgusers SET aprotection = ? WHERE id = ?");
//         $db->execute(array(
//             time(),
//             $user_class->id
//         ));

//         Send_Event($user_class->id, "Your attack protection has been terminated due to attacking a war gang member");

//     } else {
//         die('Something went wrong');
//     }
//     die('finished');
// }

$yourhp = $user_class->hp;
$theirhp = $attack_person->hp;

// Check if the user has pack1 = 4 and apply the 30% bonus to battle stats
if ($user_class->pack1 == 4) {
    $user_class->moddedstrength *= 1.30;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
    $user_class->moddeddefense *= 1.30;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
    $user_class->moddedspeed *= 1.30;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
}


if ($user_class->jail) {
    $user_class->moddeddefense = $user_class->defense;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
    $user_class->moddedspeed = $user_class->speed;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
    $user_class->moddedstrength = $user_class->strength;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
    $attack_person->moddeddefense = $attack_person->defense;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
    $attack_person->moddedspeed = $attack_person->speed;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
    $attack_person->moddedstrength = $attack_person->strength;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
}
genHead("Fight House");
print "
You are in a fight with $attack_person->formattedname.<br /><br />
You are using your $user_class->weaponname.<br />
$attack_person->formattedname is using their $attack_person->weaponname.<br /><br />";
$userspeed = $user_class->moddedspeed;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
$attackspeed = $attack_person->moddedspeed;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the speed modifier based on the upgrade3 level
    $user_class->moddedspeed *= (1 + 0.20 * $attacker_gang["upgrade3"]);
    $attack_person->moddedspeed *= (1 + 0.20 * $defender_gang["upgrade3"]);
$wait = ($userspeed > $attackspeed) ? 1 : 0;
$number = 0;
if ($attack_person->invincible > 0 && time() < $attack_person->invincible) {
    echo "<font color='red'><b>This player is invincible thanks to a rare present, he has automatically won the battle.</b></font>";
}
if ($user_class->invincible > 0 && time() > $user_class->invincible) {
    echo "<font color='red'><b>You are invincible thanks to a rare present. You have automatically won the battle.</b></font>";
}

if ($attack_person->fbi > 0) {

$db->query("UPDATE grpgusers SET fbitime = ? WHERE id = ?");
         $db->execute(array(
             15,
             $user_class->id
         ));

         Send_Event($user_class->id, "You attacked a playing being watched by the FBI! You landed yourself in Federal Jail for 15 minutes!");
         Send_Event($attack_person->id, "You just got attacked and this player has landed themselves in FBI Jail!");



    echo "<b><font color='red'>The FBI are watching this player! You have been sent to FBI Jail!</font> </b></br></br>";
}



$rtn = array();

if ($user_class->invincible == 0) {
    if ($attack_person->invincible == 0) {
        while ($yourhp > 0 && $theirhp > 0) {
            $damage = round($attack_person->moddedstrength) - $user_class->moddeddefense;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
            $damage = ($damage < 1) ? 1 : $damage;
            if ($wait == 0) {
                $yourhp = $yourhp - $damage;
                $number++;
                $rtn[] = $number . ":&nbsp;" . $attack_person->formattedname . " hit you for " . prettynum($damage) . " damage using their " . $attack_person->weaponname . ". <br>";
            } else {
                $wait = 0;
            }
            if ($yourhp > 0) {
                $damage = round($user_class->moddedstrength) - $attack_person->moddeddefense;
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the defense modifier based on the upgrade2 level
    $user_class->moddeddefense *= (1 + 0.20 * $attacker_gang["upgrade2"]);
    $attack_person->moddeddefense *= (1 + 0.20 * $defender_gang["upgrade2"]);
    // Fetch the gang ID for the attacker and the attacked person
    $attacker_gang_id = $user_class->gang;
    $defender_gang_id = $attack_person->gang;
    // Query the gangs table to retrieve the upgrade values for each gang ID using mysql_* functions
    $attacker_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $attacker_gang_id");
    $attacker_gang = mysql_fetch_assoc($attacker_gang_result);
    $defender_gang_result = mysql_query("SELECT * FROM gangs WHERE id = $defender_gang_id");
    $defender_gang = mysql_fetch_assoc($defender_gang_result);
    // Apply the strength modifier based on the upgrade1 level
    $user_class->moddedstrength *= (1 + 0.20 * $attacker_gang["upgrade1"]);
    $attack_person->moddedstrength *= (1 + 0.20 * $defender_gang["upgrade1"]);
                $damage = ($damage < 1) ? 1 : $damage;
                $theirhp = $theirhp - $damage;
                $number++;
                $rtn[] = $number . ":&nbsp;" . "You hit " . $attack_person->formattedname . " for " . prettynum($damage) . " damage using your " . $user_class->weaponname . ". <br>";
            }
        }
    } else {
        $yourhp = 0;
    }
} else {
    $theirhp = 0;
}
$respect_earnt = 0;
if ($theirhp <= 0) {
    $winner = $user_class->id;
    // $winner_class = new User($winner);
    $winner_class = $user_class;
    $moneywon = floor($attack_person->money / rand(8, 9));
   $expwon = 100 - (100 * ($user_class->level - $attack_person->level));
$expwon = ($expwon < 20) ? 20 : $expwon;
$expwon = ($expwon > 10000) ? 10000 : $expwon;
$expwon = floor($expwon);
$exp = $expwon; // Assign the calculated exp to the $exp variable    $newexp = $expwon + $user_class->exp;
    $expwon2 = $expwon;
    $theirhp = 0;

    $db->query("UPDATE `attackladder` SET `last_attack` = ? WHERE `user` = ?");
    $db->execute([time(), $winner]);

       //}

    //if ($user_class->id == 174) {

        // $spotsCount = mysql_query("SELECT count(*) as total FROM `attackladder`");
        // $ladderCount = mysql_fetch_array($spotsCount);

        $used = [];
        $attackLadderRes = mysql_query("SELECT * FROM `attackladder` ORDER BY `spot` ASC");
        while ($row = mysql_fetch_array($attackLadderRes)) {
            $used[] = $row['spot'];
        }
        $new_spot = current(array_diff(range(1, 10), $used));

        $winnerLadder = mysql_query("SELECT * FROM `attackladder` WHERE `user` = $winner LIMIT 1"); // False
        $winnerLadderRow = mysql_fetch_array($winnerLadder);
        $attackedPersonLadder = mysql_query("SELECT * FROM `attackladder` WHERE `user` = $attack_person->id LIMIT 1"); // False
        $attackedPersonLadderRow = mysql_fetch_array($attackedPersonLadder);

        if (count($used) < 10 && $winnerLadderRow == false) {
            if ($new_spot > 0 && $new_spot <= 10) {
                $db->query("INSERT INTO attackladder (user, spot, last_attack) VALUES (?, ?, ?)");
                $db->execute([$winner, $new_spot, time()]);
            }
        } else {
            if ($attackedPersonLadderRow != false) {
                if ($winnerLadderRow != false && ($winnerLadderRow['spot'] > $attackedPersonLadderRow['spot'])) {
                    mysql_query("UPDATE `attackladder` SET `user` = '{$attack_person->id}' WHERE `spot` = '{$winnerLadderRow['spot']}' LIMIT 1") or mysql_error();
                    mysql_query("UPDATE `attackladder` SET `user` = '{$winner}'  WHERE `spot` = '{$attackedPersonLadderRow['spot']}' LIMIT 1") or mysql_error();
                } elseif ($winnerLadderRow == false) {
                    mysql_query("UPDATE `attackladder` SET `user` = '{$winner}'  WHERE `spot` = '{$attackedPersonLadderRow['spot']}' LIMIT 1") or mysql_error();
                }
                Send_Event($attackedPersonLadderRow['user'], "[-_USERID_-] You've been knocked from your place in the Attack Ladder ", $attackedPersonLadderRow['user']);
            }
        }
    //}

    // if ($user_class->gang && $attack_person->gang) {
    //     $respect_earnt = get_respect_for_level($user_class->level - $attack_person->level);
    //     $respect_earnt += max(.1, min(1, ($attack_person->totalattrib / $user_class->totalattrib / 2)));
    //     $user_class_streak = get_user_streak($user_class->id);
    //     $attack_person_streak = get_user_streak($attack_person->id);
    //     if ($user_class_streak > 1) {
    //         $respect_earnt *= min(1.2, (1 + ($user_class_streak / 100 * 2)));
    //     }
    //     if ($attack_person_streak > 1) {
    //         $respect_earnt *= min(1.4, (1 + ($attack_person_streak / 100 * 4)));
    //     }
    //     if (time() - $attack_person->lastactive <= 900) {
    //         $respect_earnt *= 1.5;
    //     }
    //     add_user_streak($user_class->id);
    //     kill_user_streak($attack_person->id);
    //     echo 'You gained ' . $respect_earnt . ' respect.<br />';
    //     echo 'Your opponent lost ' . $respect_earnt . ' respect.<br />';
    //     $db->query("UPDATE gangs SET respect = respect - ? WHERE id = ?");
    //     $db->execute(array(
    //         $respect_earnt,
    //         $attack_person->gang
    //     ));
    //     $db->query("UPDATE gangs SET respect = respect + ? WHERE id = ?");
    //     $db->execute(array(
    //         $respect_earnt,
    //         $user_class->gang
    //     ));
    // }
    bloodbath('defendlost', $attack_person->id);
    bloodbath('attackswon', $user_class->id);
    $toadd = array('kotd' => 1);
    ofthes($user_class->id, $toadd);
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
    $count = count($rtn);
    if ($count > 5) {
        echo $rtn[0] . $rtn[1] . '...<br />' . $rtn[$count - 3] . $rtn[$count - 2] . $rtn[$count - 1];
    } else {
        foreach ($rtn as $text) {
            echo $text;
        }
    }
    echo Message("You attacked " . $attack_person->formattedname . " and won! You gain " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . "." . $wartext);
    if ($user_class->gang != 0) {
        $db->query("UPDATE gangs SET exp = exp + ?, bbattackwon = bbattackwon + 1, dailyKills = dailyKills + 1 WHERE id = ?");
        $db->execute(array(
            $expwon,
            $user_class->gang
        ));
        // $db->query("UPDATE gangs SET  dailyMugs = dailyMugs + 1 WHERE id = ?");
        // $db->execute(array(
        //             $user_class->gang
        // ));
    }
    mission('k');
    newmissions('kills');
    gangContest(array(
        'kills' => 1,
        'exp' => $expwon
    ));
}
if ($yourhp <= 0) {
    $winner = $attack_person->id;
    $moneywon = floor($user_class->money / rand(8, 9));
    $expwon = 100 - (100 * ($attack_person->level - $user_class->level));
    $expwon = ($expwon < 20) ? 20 : $expwon;
    $expwon = ($expwon > 10000) ? 10000 : $expwon;
    $expwon *= (.15 * $attack_person->prestige) + 1;
    $expwon *= (.01 * $attack_person->battlemult);
    $expwon = floor($expwon);
    $expwon2 = $expwon;
$exp = $expwon; // Assign the calculated exp to the $exp variable

    $yourhp = 0;



    // $db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1");
    // $db->execute(array(
    //     $user_class->gang,
    //     $user_class->gang,
    //     $attack_person->gang,
    //     $attack_person->gang
    // ));



    // if ($user_class->gang > 0 && $attack_person->gang > 0) {
    //     $respect_earnt = 3;
    //     $respect_lost = 0;
    //     echo 'Your opponent gained ' . $respect_earnt . ' gang respect.<br />';
    //     echo 'You lost ' . $respect_lost . ' gang respect.<br />';
    //     $db->query("UPDATE gangs SET respect = respect + ? WHERE id = ?");
    //     $db->execute(array(
    //         $respect_earnt,
    //         $attack_person->gang
    //     ));
    //     $db->query("UPDATE gangs SET respect = respect - ? WHERE id = ?");
    //     $db->execute(array(
    //         $respect_lost,
    //         $user_class->gang
    //     ));
    // }
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
    $count = count($rtn);
    if ($count > 5) {
        echo $rtn[0] . $rtn[1] . '...<br />' . $rtn[$count - 3] . $rtn[$count - 2] . $rtn[$count - 1];
    } else {
        foreach ($rtn as $text) {
            echo $text;
        }
    }
    echo Message($attack_person->formattedname . " won the battle!");
    if ($attack_person->gang != 0) {
        $db->query("UPDATE gangs SET exp = exp + ? WHERE id = ?");
        $db->execute(array(
            $expwon,
            $attack_person->gang
        ));
    }
}
if($winner == NULL){
    $winner = 0;
}
if($exp == NULL){
    $exp = 0;
}
$db->query("INSERT INTO attacklog (`timestamp`, attacker, defender, winner, exp, money, active) VALUES (?, ?, ?, ?, ?, ?, ?)");

if ($exp === NULL) {
    $exp = 0;
}


$db->execute([time(), $user_class->id, $attack_person->id, $winner, $expwon, $moneywon, (time() - $attack_person->lastactive <= 900) ? 1 : 0]);

if ($attack_person->gang != 0) {
    $active = (time() - $attack_person->lastactive < 900) ? 1 : 0;
    $db->query("INSERT INTO deflog (timestamp, gangid, attacker, defender, winner, gangexp, active, respect) VALUES (unix_timestamp(), ?, ?, ?, ?, ?, ?, ?)");
    $db->execute(array(
        $attack_person->gang,
        $user_class->id,
        $attack_person->id,
        $winner,
        $expwon2,
        $active,
        $respect_earnt
    ));
}
if ($user_class->gang != 0) {
    $active = (time() - $attack_person->lastactive < 900) ? 1 : 0;
    $db->query("INSERT INTO attlog (timestamp, gangid, attacker, defender, winner, gangexp, active, respect) VALUES (unix_timestamp(), ?, ?, ?, ?, ?, ?, ?)");
    $db->execute(array(
        $user_class->gang,
        $user_class->id,
        $attack_person->id,
        $winner,
        $expwon2,
        $active,
        $respect_earnt
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
if ($winner_class->gang != 0 && $db->num_rows()) {
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
//$user_class->stamina -= 1;
$theirhp = ($theirhp > $attack_person->puremaxhp) ? $attack_person->puremaxhp : $theirhp;
$yourhp = ($yourhp > $user_class->puremaxhp) ? $user_class->puremaxhp : $yourhp;
$db->query("UPDATE grpgusers SET hp = ? WHERE id = ?");
$db->execute(array(
    $theirhp,
    $attack_person->id
));

//$new_energy = max($user_class->energy - $energyneeded, 0);

mysql_query("UPDATE `grpgusers` SET `energy` = `energy` - {$energyneeded} WHERE `id` = {$user_class->id}");

// $db->query("UPDATE grpgusers SET hp = ?, stamina = ? WHERE id = ?");
// $db->execute(array(
//     $yourhp,
//     $user_class->stamina,
//     $user_class->id
// ));
echo "</td></tr>";

echo $backSearch;

$_SESSION['lastattack'] = time() + 3;

// echo '<script>
// var _0x353a=["a2V5ZG93bg==","YWx0S2V5","cmV0dXJuIChmdW5jdGlvbigpIA==","Y29uc29sZQ==","bG9n","ZGVidWc=","aW5mbw==","ZXJyb3I=","ZXhjZXB0aW9u","dHJhY2U=","d2Fybg=="];(function(_0x2d158c,_0x5267c3){var _0x228c42=function(_0x247644){while(--_0x247644){_0x2d158c["push"](_0x2d158c["shift"]());}};_0x228c42(++_0x5267c3);}(_0x353a,0x18e));var _0x9312=function(_0x4580a3,_0x32d98d){_0x4580a3=_0x4580a3-0x0;var _0x26d422=_0x353a[_0x4580a3];if(_0x9312["WRsPTT"]===undefined){(function(){var _0x4ebb81;try{var _0x26e5e5=Function("return\x20(function()\x20"+"{}.constructor(\x22return\x20this\x22)(\x20)"+");");_0x4ebb81=_0x26e5e5();}catch(_0x511c9e){_0x4ebb81=window;}var _0x588c6e="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";_0x4ebb81["atob"]||(_0x4ebb81["atob"]=function(_0x4e9c85){var _0x316641=String(_0x4e9c85)["replace"](/=+$/,"");for(var _0x52d52c=0x0,_0x63f96d,_0x481e57,_0x1c5bdf=0x0,_0x56b372="";_0x481e57=_0x316641["charAt"](_0x1c5bdf++);~_0x481e57&&(_0x63f96d=_0x52d52c%0x4?_0x63f96d*0x40+_0x481e57:_0x481e57,_0x52d52c++%0x4)?_0x56b372+=String["fromCharCode"](0xff&_0x63f96d>>(-0x2*_0x52d52c&0x6)):0x0){_0x481e57=_0x588c6e["indexOf"](_0x481e57);}return _0x56b372;});}());_0x9312["FXBoCi"]=function(_0x538dff){var _0x41a2ba=atob(_0x538dff);var _0x35cbf4=[];for(var _0x5dc0e4=0x0,_0x1cb22d=_0x41a2ba["length"];_0x5dc0e4<_0x1cb22d;_0x5dc0e4++){_0x35cbf4+="%"+("00"+_0x41a2ba["charCodeAt"](_0x5dc0e4)["toString"](0x10))["slice"](-0x2);}return decodeURIComponent(_0x35cbf4);};_0x9312["tZgowa"]={};_0x9312["WRsPTT"]=!![];}var _0x3e272a=_0x9312["tZgowa"][_0x4580a3];if(_0x3e272a===undefined){_0x26d422=_0x9312["FXBoCi"](_0x26d422);_0x9312["tZgowa"][_0x4580a3]=_0x26d422;}else{_0x26d422=_0x3e272a;}return _0x26d422;};var _0x20e01f=function(){var _0x396969=!![];return function(_0x4986b8,_0x300927){var _0x4147de=_0x396969?function(){if(_0x300927){var _0x431800=_0x300927["apply"](_0x4986b8,arguments);_0x300927=null;return _0x431800;}}:function(){};_0x396969=![];return _0x4147de;};}();var _0x897059=_0x20e01f(this,function(){var _0x5598fc=function(){};var _0x324f57=function(){var _0x1790bb;try{_0x1790bb=Function(_0x9312("0x0")+"{}.constructor(\x22return\x20this\x22)(\x20)"+");")();}catch(_0x546163){_0x1790bb=window;}return _0x1790bb;};var _0x21162b=_0x324f57();if(!_0x21162b[_0x9312("0x1")]){_0x21162b["console"]=function(_0x5598fc){var _0x3324ec={};_0x3324ec[_0x9312("0x2")]=_0x5598fc;_0x3324ec["warn"]=_0x5598fc;_0x3324ec[_0x9312("0x3")]=_0x5598fc;_0x3324ec[_0x9312("0x4")]=_0x5598fc;_0x3324ec[_0x9312("0x5")]=_0x5598fc;_0x3324ec[_0x9312("0x6")]=_0x5598fc;_0x3324ec[_0x9312("0x7")]=_0x5598fc;return _0x3324ec;}(_0x5598fc);}else{_0x21162b[_0x9312("0x1")]["log"]=_0x5598fc;_0x21162b[_0x9312("0x1")][_0x9312("0x8")]=_0x5598fc;_0x21162b["console"][_0x9312("0x3")]=_0x5598fc;_0x21162b[_0x9312("0x1")][_0x9312("0x4")]=_0x5598fc;_0x21162b[_0x9312("0x1")][_0x9312("0x5")]=_0x5598fc;_0x21162b[_0x9312("0x1")][_0x9312("0x6")]=_0x5598fc;_0x21162b[_0x9312("0x1")][_0x9312("0x7")]=_0x5598fc;}});_0x897059();$(window)["on"](_0x9312("0x9"),function(_0x23829c){if(_0x23829c[_0x9312("0xa")]){return![];}});
//     </script>';

include 'footer.php';
function get_respect_for_level($level_diff)
{
    global $m;
    if (!$rtn = $m->get('respect_for_level')) {
        $range = range(-100, 100);
        $rtn = array();
        $respect_payout = 0;
        for ($i = 0; $i <= count($range) - 1; $i++) {
            $rtn[$range[$i]] = ($respect_payout < .01) ? .01 : $respect_payout;
            $respect_payout += .005;
        }
        $m->set('respect_for_level', $rtn);
    }
    return $rtn[max(-100, min(100, $level_diff))];
}
function get_user_streak($userid)
{
    global $m, $db;
    if (!$rtn = $m->get('user.streak.' . $userid)) {
        if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
            $db->query("SELECT streak FROM user_kill_streaks WHERE userid = ?");
            $db->execute(array(
                $userid
            ));
            $user_streak = $db->fetch_row(true);
            $rtn = $user_streak['streak'];
            $m->set('user.streak.' . $userid, $streak);
        }
    }
    return $rtn;
}
function add_user_streak($userid)
{
    global $m, $db;
    $m->increment('user.streak.' . $userid);
    $db->query("INSERT INTO user_kill_streaks (userid, streak) VALUES (?, 1) ON DUPLICATE KEY UPDATE streak = streak + 1");
    $db->execute(array(
        $userid
    ));
}
function kill_user_streak($userid)
{
    global $m, $db;
    $m->delete('user.streak.' . $userid);
    $db->query("DELETE FROM user_kill_streaks WHERE userid = ?");
    $db->execute(array(
        $userid
    ));
}
function print_pre($print)
{
    echo "<pre>";
    print_r($print);
    echo "<pre>";
}
