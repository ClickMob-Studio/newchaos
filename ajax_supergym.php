<?php

include "ajax_header.php";
include_once "includes/functions.php";

$canPerformAction = canPerformAction($_SESSION['id'], 'gym');
if (!$canPerformAction) {
    echo json_encode(array(
        'error' => 'You are performing actions too quickly. Please wait a moment and try again.'
    ));
    die();
}

$user_class = new User($_SESSION['id']);


if (isset($_POST['amnt']))
    security($_POST['amnt'], 'num');
if ($user_class->hospital > 0) {
    die("You can't train at the gym if you are in the hospital.");
}

$modifier = 1.0;
$multiplier = 1;

if (isset($_POST['multiplier']) && (int) $_POST['multiplier'] && (int) $_POST['multiplier'] == 10) {
    $tempItemUse = getItemTempUse($user_class->id);
    if ($tempItemUse['gym_10_multiplier_time'] > time()) {
        $multiplier = 10;
    }
}

if (isset($_POST['multiplier']) && (int) $_POST['multiplier'] && (int) $_POST['multiplier'] == 50 && $user_class->is_auto_user > 0) {
    $multiplier = 50;
}

// Additional Code for Mega Train Feature

$mega_train_multiplier = (isset($_POST['mega_train']) && $_POST['mega_train'] === 'yes') ? 10 : 1;


$gymBonus = 0.0;

$now = time();
$db->query("SELECT * FROM scheduledevents WHERE type = 'gym' AND `start` <= ? AND `end` >= ? LIMIT 1");
$db->execute([$now, $now]);
$scheduledevent = $db->fetch_row(true);
if ($scheduledevent) {
    $gymBonus = (float) $scheduledevent['multiplier'];
}

// Fetch the Player's Gang Upgrades
$db->query("SELECT upgrade1, upgrade2, upgrade3, upgrade4, upgrade5, upgrade6 FROM gangs WHERE id = ? LIMIT 1");
$db->execute([$user_class->gang]);
$gang_upgrades = $db->fetch_row(true);

if (
    isset($_POST['stat']) && in_array($_POST['stat'], array(
        'strength',
        'defense',
        'speed',
        'agility',
    ))
) {
    $stat = $_POST['stat'];
} else {
    die("Invalid stat: " . $_POST['stat']);
}

$user_class->directawake -= (round(.75 * $_POST['amnt']));
$modifier *= $mega_train_multiplier;  // Applying the multiplier to the modifier
$modifier *= 1.5;

$modifier = max(((0.20 * $user_class->prestige) + 1.5), 1.5);

// Check if the user has pack1 = 3 and apply the 20% bonus
if ($user_class->pack1 == 3) {
    $modifier *= 1.20;
}

// Assuming this comes after initializing $modifier and validating $_POST['stat']
if (isset($gang_upgrades) && $gang_upgrades['upgrade6'] >= 1) {
    $modifier *= (1 + (0.05 * $gang_upgrades['upgrade6'])); // Apply the bonus correctly
}

$user_class->directawake = ($user_class->directawake < 0) ? 0 : $user_class->directawake;
if (isset($_POST['what']) and $_POST['what'] == 'trainrefill') {
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
        if (isset($user_class->completeUserResearchTypesIndexedOnId[36])) {
            $researchAddBoost += 5;
        }
        if ($researchAddBoost > 0) {
            $resAddInc = $add / 100 * $researchAddBoost;
            $add = $add + $resAddInc;
        }

        $tempItemUse = getItemTempUse($user_class->id);
        if ($tempItemUse['gym_protein_bar_time'] > time()) {
            $gymProteinBarAdd = $add / 100 * 20;
            $add = $add + $gymProteinBarAdd;
        }
        if ($tempItemUse['double_gym_time'] > time()) {
            $add = $add * 2;
        }

        if ($gymBonus > 0) {
            $add = $add * $gymBonus;
        }

        $add = ceil($add);

        if ($multiplier > 1) {
            $add = $add * $multiplier;
        }

        $user_boosts = get_skill_boosts($user_class->skills);
        if (isset($user_boosts['gym_boost']) && $user_boosts['gym_boost'] > 0) {
            $add = floor($add * $user_boosts['gym_boost']);
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
        perform_query("UPDATE grpgusers SET $stat = ?, dailytrains = ?, points = ?, energy = ? WHERE id = ?", [$user_class->{$stat}, $user_class->dailytrains, $user_class->points, $user_class->maxenergy, $user_class->id]);

        // Visual representation of energy used is adjusted for the x10 mode but does not affect awake reduction
        $displayed_energy_used = $_POST['amnt']; // Actual energy used for the calculation
        $mega_train_message = ($mega_train_multiplier == 10) ? "Mega Training is active - X10 TRAIN! " : "";
        die($mega_train_message . "You trained with " . $displayed_energy_used . " energy and received " . prettynum($add) . " $stat.|" . number_format($user_class->points) . "|" . prettynum($user_class->$stat) . "|$user_class->maxenergy");
    } else {
        die("You don't have enough energy.");
    }
}
if (isset($_POST['what']) and $_POST['what'] == 'train') {
    if ($_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        $user_class->$stat += $add;
        $user_class->dailytrains += $add;
        $user_class->energy -= $_POST['amnt'];
        $user_class->energypercent = floor(($user_class->energy / $user_class->maxenergy) * 100);
        $user_class->formattedenergy = $user_class->energy . " / " . $user_class->maxenergy . " [" . $user_class->energypercent . "%]";
        $user_class->awakepercent = floor(($user_class->directawake / $user_class->directmaxawake) * 100);
        perform_query("UPDATE grpgusers SET $stat = ?, dailytrains = ?, awake = ?, energy = ? WHERE id = ?", [$user_class->{$stat}, $user_class->dailytrains, $user_class->directawake, $user_class->energy, $user_class->id]);

        $bpCategory = getBpCategory();
        if ($bpCategory) {
            addToBpCategoryUser($bpCategory, $user_class, 'trains', 1);
        }

        print ("You trained with {$_POST['amnt']} energy and received " . prettynum($add) . " $stat.|" . prettynum($user_class->$stat) . "|" . genBars());
        print "|$user_class->energy";
        die();
    } else
        die("You don't have enough energy.");
}
if (isset($_POST['what']) and $_POST['what'] == 'refill') {
    if (
        in_array($_POST['att'], array(
            'energy',
            'awake',
            'both'
        ))
    ) {
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
            perform_query("UPDATE grpgusers SET energy = ?, points = points - 10 WHERE id = ?", [$user_class->maxenergy, $user_class->id]);
            print ("You have refilled your energy for 10 points.|" . number_format($user_class->points) . "|" . genBars());
            print "|$user_class->energy";
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
            perform_query("UPDATE grpgusers SET awake = ?, points = points - ? WHERE id = ?", [$user_class->directawake, $ptstouse, $user_class->id]);
            print ("You have refilled your awake for $ptstouse points.|" . number_format($user_class->points) . "|" . genBars());
            print "|$user_class->energy";
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
            perform_query("UPDATE grpgusers SET energy = ?, awake = ?, points = points - ? WHERE id = ?", [$user_class->maxenergy, $user_class->directawake, $ptstouse, $user_class->id]);
            print ("You have refilled your energy/awake for $ptstouse points.|" . number_format($user_class->points) . "|" . genBars());
            print "|$user_class->energy";
            die();
        }
    }
}
?>