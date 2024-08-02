
<?php
include "ajax_header.php";
mysql_select_db('chaoscit_game', mysql_connect('localhost', 'chaoscit_user', '3lrKBlrfMGl2ic14'));
$user_class = new User($_SESSION['id']);
if($m->get('crime.'.$user_class->id . time()))
	$m->increment('crime.'.$user_class->id . time());
else
    $m->set('crime.'.$user_class->id . time(), 1, MEMCACHE_COMPRESSED);
if($m->get('crime.'.$user_class->id . time()) > 100)
	die("Error, going too fast.");
$lcl = $m->get('lastcrimeload.'.$user_class->id);
$lpl = $m->get('lastpageload.'.$user_class->id);
if($lpl > $lcl)
    die("Error training.");
if (isset($_POST['amnt']))
    security($_POST['amnt'], 'num');
if ($user_class->hospital > 0) {
    die("You can't train at the gym if you are in the hospital.");
}
$modifier = 1.0;

$multiplier = 1;
if (isset($_POST['multiplier']) && (int)$_POST['multiplier'] && (int)$_POST['multiplier'] == 10) {
    $multiplier = 10;
}

// Additional Code for Mega Train Feature

$mega_train_multiplier = (isset($_POST['mega_train']) && $_POST['mega_train'] === 'yes') ? 10 : 1;

$gymBonus = 0;
$result = mysql_query("SELECT time FROM gamebonus WHERE ID = 2 LIMIT 1");
if ($result) {
    $gymbonus = mysql_fetch_assoc($result);
    if ($gymbonus && $gymbonus['time'] > 0) {
        $gymBonus = 2;
    }
}

// Fetch the Player's Gang Upgrades
$result = mysql_query("SELECT upgrade1, upgrade2, upgrade3, upgrade4, upgrade5, upgrade6 from `gangs` WHERE `id` = '" . $user_class->gang . "'");
if ($result) {
    $gang_upgrades = mysql_fetch_assoc($result);

    // Check the Training Stat and Apply the Corresponding Bonus
    switch($stat) {
        case 'strength':
            $upgrade_level = $gang_upgrades['upgrade1'];
            break;
        case 'defense':
            $upgrade_level = $gang_upgrades['upgrade2'];
            break;
        case 'speed':
            $upgrade_level = $gang_upgrades['upgrade3'];
            break;
        default:
            $upgrade_level = 0;
    }

    // Apply the bonus to the modifier
    if ($upgrade_level > 0) {
        $bonus_percentage = 0.05 * $upgrade_level;  // 5% per level
        $modifier *= (1 + $bonus_percentage);
    }
}

if (!isset($_POST['stat']) || in_array($_POST['stat'], array(
            'strength',
            'defense',
            'speed',
            'agility',
        ))) {
    $stat = $_POST['stat'];
} else {
    die("Invalid stat.");
}

$user_class->directawake -= (round(.75 * $_POST['amnt']));
$modifier *= $mega_train_multiplier;  // Applying the multiplier to the modifier
/*$user_class->directawake -= (round(.75 * $_POST['amnt']));*/
$modifier *= 1.5;

$modifier = max(((0.20 * $user_class->prestige) + 1.5), 1.5);

// Check if the user has pack1 = 3 and apply the 20% bonus
if ($user_class->pack1 == 3) {
    $modifier *= 1.20;
}
 // Check if the user has pack1 = 5 and apply the 25% bonus to mugged amount
    if ($gang_class->upgrade6 >= 1) {
      $bonus = 1 + (0.05 * $gang_class->upgrade6); // Correctly calculates the total bonus multiplier

    // Correctly applies the bonus multiplier to the modifier
    $modifier *= $bonus;
}

// Assuming this comes after initializing $modifier and validating $_POST['stat']
if ($user_class->gang) { // Check if the user is in a gang
    $result = mysql_query("SELECT upgrade6 FROM `gangs` WHERE `id` = '" . mysql_real_escape_string($user_class->gang) . "'");
    if ($result) {
        $gang_upgrades = mysql_fetch_assoc($result);
        if ($gang_upgrades && $gang_upgrades['upgrade6'] >= 1) {
            $modifier *= (1 + (0.05 * $gang_upgrades['upgrade6'])); // Apply the bonus correctly
        }
    }
}

// if ($user_class->prestige > 0) {
//     $modifier *= (.15 * $user_class->prestige_gym) + 1;
// }

$user_class->directawake = ($user_class->directawake < 0) ? 0 : $user_class->directawake;
if (isset($_POST['what']) AND $_POST['what'] == 'trainrefill') {
    // Determine if Mega Train is active
    $mega_train_multiplier = (isset($_POST['mega_train']) && $_POST['mega_train'] === 'yes') ? 10 : 1;

    // Points required for awake refill remains based on the current awake level
    $ptsforawake = 100 - (($user_class->directawake / $user_class->directmaxawake) * 100);
    $ptsreq = 10 + ceil($ptsforawake); // Base points required for refill, not multiplied

    if ($multiplier > 1) {
        $ptsreq = $ptsreq * $multiplier;
    }

    // Check if user has enough points for the operation
    if ($user_class->points < $ptsreq * $mega_train_multiplier) {
        die("You do not have enough points to train.");
    }

    if ($_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        // Calculate stats addition based on the multiplier
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier) * $mega_train_multiplier;
        $researchAddBoost = 0;
        if (isset($user_class->completeUserResearchTypesIndexedOnId[2])) {
            $researchAddBoost += 5;
        }
        if (isset($user_class->completeUserResearchTypesIndexedOnId[10])) {
            $researchAddBoost += 5;
        }
        if ($researchAddBoost > 0) {
            $resAddInc = $add / 100 * $researchAddBoost;
            $add = $add + $resAddInc;
        }

        if ($gymBonus > 0) {
            $add = $add * 2;
        }

        $add = ceil($add);

        if ($multiplier > 1) {
            $add = $add * $multiplier;
        }

        $user_class->$stat += $add;
        $user_class->dailytrains += $add;

        // Deduct points based on the operation
        $user_class->points -= $ptsreq * $mega_train_multiplier; // Deduct the points for x10 operation

        $bpCategory = getBpCategory();
        if ($bpCategory) {
            addToBpCategoryUser($bpCategory, $user_class, 'trains', 1);
        }

        // Update the database with the new stats and points
        mysql_query("UPDATE grpgusers SET $stat = '" . $user_class->{$stat} . "', dailytrains = $user_class->dailytrains, points = $user_class->points, energy = $user_class->maxenergy WHERE id = $user_class->id");

        // Visual representation of energy used is adjusted for the x10 mode but does not affect awake reduction
        $displayed_energy_used = $_POST['amnt']; // Actual energy used for the calculation
        $mega_train_message = ($mega_train_multiplier == 10) ? "Mega Training is active - X10 TRAIN! " : "";
        die($mega_train_message . "You trained with " . $displayed_energy_used . " energy and received " . prettynum($add) . " $stat.|" . number_format($user_class->points) . "|" . prettynum($user_class->$stat) . "|$user_class->maxenergy");
    } else {
        die("You don't have enough energy.");
    }
}if (isset($_POST['what']) AND $_POST['what'] == 'train') {
    if ($_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        $user_class->$stat += $add;
        $user_class->dailytrains += $add;
        $user_class->energy -= $_POST['amnt'];
        $user_class->energypercent = floor(($user_class->energy / $user_class->maxenergy) * 100);
        $user_class->formattedenergy = $user_class->energy . " / " . $user_class->maxenergy . " [" . $user_class->energypercent . "%]";
        $user_class->awakepercent = floor(($user_class->directawake / $user_class->directmaxawake) * 100);
        mysql_query("UPDATE grpgusers SET $stat = '" . $user_class->{$stat} . "', dailytrains = $user_class->dailytrains, awake = $user_class->directawake, energy = $user_class->energy WHERE id = $user_class->id");

        $bpCategory = getBpCategory();
        if ($bpCategory) {
            addToBpCategoryUser($bpCategory, $user_class, 'trains', 1);
        }

        print("You trained with {$_POST['amnt']} energy and received " . prettynum($add) . " $stat.|" . prettynum($user_class->$stat) . "|".genBars());
        print"|$user_class->energy";
        die();
    } else
        die("You don't have enough energy.");
}
if (isset($_POST['what']) AND $_POST['what'] == 'refill') {
    if (in_array($_POST['att'], array(
                'energy',
                'awake',
                'both'
            ))) {
        $att = $_POST['att'];
    } else {
        die("Invalid stat.");
    }
    if ($att == 'energy') {
        if ($user_class->energy == $user_class->maxenergy)
            die("Your energy is already full.");
        elseif ($user_class->points < 10)
            die("You do not have enough points to refill your energy.");
        else {
            $user_class->energy = $user_class->maxenergy;
            $user_class->points -= 10;
            $user_class->energypercent = floor(($user_class->energy / $user_class->maxenergy) * 100);
            $user_class->formattedenergy = $user_class->energy . " / " . $user_class->maxenergy . " [" . $user_class->energypercent . "%]";
            mysql_query("UPDATE grpgusers SET energy = $user_class->maxenergy, points = points - 10 WHERE id = $user_class->id");
            print("You have refilled your energy for 10 points.|" . number_format($user_class->points) . "|".genBars());
            print"|$user_class->energy";
            die();
        }
    } elseif ($att == 'awake') {
        $ptstouse = 100 - $user_class->awakepercent;
        if ($user_class->directawake == $user_class->directmaxawake)
            die("Your awake is already full.");
        elseif ($user_class->points < $ptstouse)
            die("You do not have enough points to refill your awake.");
        else {
            $user_class->directawake = $user_class->directmaxawake;
            $user_class->points -= $ptstouse;
            $user_class->awakepercent = 100;
            mysql_query("UPDATE grpgusers SET awake = $user_class->directawake, points = points - $ptstouse WHERE id = $user_class->id");
            print("You have refilled your awake for $ptstouse points.|" . number_format($user_class->points) . "|".genBars());
            print"|$user_class->energy";
            die();
        }
    } elseif ($att == 'both') {
        $ptstouse = 100 - $user_class->awakepercent;
        $ptstouse += 10;
        if ($user_class->directawake == $user_class->directmaxawake)
            die("Your awake is already full.");
        elseif ($user_class->points < $ptstouse)
            die("You do not have enough points to refill your awake/energy.");
        elseif ($user_class->energy == $user_class->maxenergy)
            die("Your energy is already full.");
        else {
            $user_class->directawake = $user_class->directmaxawake;
            $user_class->energy = $user_class->maxenergy;
            $user_class->points -= $ptstouse;
            $user_class->awakepercent = 100;
            $user_class->energypercent = floor(($user_class->energy / $user_class->maxenergy) * 100);
            $user_class->formattedenergy = $user_class->energy . " / " . $user_class->maxenergy . " [" . $user_class->energypercent . "%]";
            mysql_query("UPDATE grpgusers SET energy = $user_class->maxenergy, awake = $user_class->directawake, points = points - $ptstouse WHERE id = $user_class->id");
            print("You have refilled your energy/awake for $ptstouse points.|" . number_format($user_class->points) . "|".genBars());
            print"|$user_class->energy";
            die();
        }
    }
}
?>