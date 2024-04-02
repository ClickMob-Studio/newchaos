<?php
include "ajax_header.php";
//mysql_select_db('game', mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14'));
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


// Fetch the Player's Gang Point Upgrades
$result = mysqli_query($connection, "SELECT upgrade6 FROM `gangs` WHERE `id` = '" . mysqli_real_escape_string($connection, $user_class->gang) . "'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $upgrade_level = $row['upgrade6'];

    // Define the bonus percentage per level
    $bonus_per_level = 0.05; // 5% per level

    // Initialize modifier if not already set
    $modifier = $modifier ?? 1; // Ensure $modifier is set, if not default to 1

    // Apply the bonus to the modifier
    if ($upgrade_level > 0) {
        $bonus_percentage = $bonus_per_level * $upgrade_level;  // Calculate total bonus percentage
        $modifier *= (1 + $bonus_percentage); // Apply the total bonus
    }
}



if (!isset($_POST['stat']) || in_array($_POST['stat'], array(
            'strength',
            'defense',
            'speed'
        ))) {
    $stat = $_POST['stat'];
} else {
    die("Invalid stat.");
}
$user_class->directawake -= (round(.75 * $_POST['amnt']));
$modifier *= 1.5;

$modifier = max(((0.20 * $user_class->prestige) + 1.5), 1.5);

// Check if the user has pack1 = 3 and apply the 20% bonus
if ($user_class->pack1 == 3) {
    $modifier *= 1.20;
}






// if ($user_class->prestige > 0) {
//     $modifier *= (.15 * $user_class->prestige_gym) + 1;
// }

$user_class->directawake = ($user_class->directawake < 0) ? 0 : $user_class->directawake;
if (isset($_POST['what']) AND $_POST['what'] == 'trainrefill') {
    $ptsforawake = 100 - (($user_class->directawake / $user_class->directmaxawake) * 100);
    $ptsreq = 10 + ceil($ptsforawake);
    if ($ptsreq > $user_class->points)
        die("You do not have enough points to train.");
    if ($_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        $user_class->$stat += $add;
        $user_class->dailytrains += $add;
        $user_class->points -= $ptsreq;
        mysql_query("UPDATE grpgusers SET $stat = '" . $user_class->{$stat} . "', dailytrains = $user_class->dailytrains, points = points - $ptsreq, energy = $user_class->maxenergy WHERE id = $user_class->id");
        die("You trained with {$_POST['amnt']} energy and received " . prettynum($add) . " $stat.|" . number_format($user_class->points) . "|" . prettynum($user_class->$stat) . "|$user_class->maxenergy");
    } else
        die("You don't have enough energy.");
}
if (isset($_POST['what']) AND $_POST['what'] == 'train') {
    $mega_train_multiplier = (isset($_POST['mega_train']) && $_POST['mega_train'] === 'yes') ? 10 : 1;

    if ($_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        $user_class->$stat += $add;
        $user_class->dailytrains += $add;

        // Awake reduction
        $awakeReduction = round(0.03 * $_POST['amnt']); // Assuming 3% awake reduction
        $user_class->directawake -= $awakeReduction;
        $user_class->directawake = max(0, $user_class->directawake);

        // Calculate points required for the train
        $ptsforawake = ceil(($awakeReduction / $user_class->directmaxawake) * 100);
        $basePtsReq = 10 + $ptsforawake; // Base refill cost
        $ptsreq = $basePtsReq * $mega_train_multiplier; // Apply multiplier for x10 mode

        if ($ptsreq > $user_class->points) {
            die("You do not have enough points to train.");
        }

        // Deduct points and update stats
        $user_class->points -= $ptsreq;
        mysql_query("UPDATE grpgusers SET $stat = '" . $user_class->{$stat} . "', dailytrains = $user_class->dailytrains, awake = $user_class->directawake, energy = $user_class->energy, points = points - $ptsreq WHERE id = $user_class->id");
        
        $displayedEnergyUsed = $mega_train_multiplier == 10 ? $_POST['amnt'] * $mega_train_multiplier : $_POST['amnt'];
        print("You trained with " . $displayedEnergyUsed . " energy and received " . prettynum($add) . " $stat.|" . prettynum($user_class->$stat) . "|".genBars());
        print"|" . $user_class->energy;
        die();
    } else {
        die("You don't have enough energy.");
    }
}if (isset($_POST['what']) AND $_POST['what'] == 'refill') {
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