<?php
include "ajax_header.php";

$user_class = new User($_SESSION['id']);
$pet_class = new Pet($_SESSION['id']);
if (isset($_POST['amnt']))
    $egy = security($_POST['amnt']);
if ($pet_class->hospital > 0) {
    die("You can't train at the gym if your pet is in the hospital.");
}
$modifier = 1.0;
if (
    !isset($_POST['stat']) || in_array($_POST['stat'], array(
        'str',
        'def',
        'spe'
    ))
) {
    $stat = $_POST['stat'];
} else {
    die("Invalid stat.");
}
if ($pet_class->awake >= 2000) {
    $pet_class->awake -= (round(.75 * $_POST['amnt']));
    $modifier = .46;
} else
    $pet_class->awake -= (round(2.5 * $_POST['amnt']));
$modifier *= 1.5;
$pet_class->awake = ($pet_class->awake < 0) ? 0 : $pet_class->awake;
if (isset($_POST['what']) and $_POST['what'] == 'trainrefill') {
    $ptsforawake = 100 - (($pet_class->awake / $pet_class->maxawake) * 100);
    $ptsreq = 10 + ceil($ptsforawake);
    if ($ptsreq > $user_class->points)
        die("You do not have enough points to train.");

    if ($_POST['amnt'] <= $pet_class->energy && $_POST['amnt'] > 0) {
        $add = round($egy * ($pet_class->awake / 100 * 3.14 / 2) * 1.25 * $modifier);

        if (isset($user_class->completeUserResearchTypesIndexedOnId[11])) {
            $resAddInc = $add / 100 * 5;
            $add = $add + $resAddInc;
        }
        $researchAddBoost = 0;
        if (isset($user_class->completeUserResearchTypesIndexedOnId[38])) {
            $researchAddBoost += 10;
        }
        if (isset($user_class->completeUserResearchTypesIndexedOnId[40])) {
            $researchAddBoost += 10;
        }
        if (isset($user_class->completeUserResearchTypesIndexedOnId[43])) {
            $researchAddBoost += 10;
        }
        if ($researchAddBoost > 0) {
            $resAddInc = $add / 100 * $researchAddBoost;
            $add = $add + $resAddInc;
        }

        $user_boosts = get_skill_boosts($user_class->skills);
        if (isset($user_boosts['pet_gym_boost']) && $user_boosts['pet_gym_boost'] > 0) {
            $add = floor($add * $user_boosts['gym_boost']);
        }

        $pet_class->$stat += $add;
        $user_class->points -= $ptsreq;
        perform_query("UPDATE pets SET $stat = ?, energy = ?, awake = ? WHERE userid = ?", [$pet_class->{$stat}, $pet_class->maxenergy, $pet_class->maxawake, $user_class->id]);
        perform_query("UPDATE grpgusers SET points = points - ? WHERE id = ?", [$ptsreq, $user_class->id]);
        addToPetladder($pet_class->id, 'gym', $add);
        die("You trained with {$_POST['amnt']} energy and received " . prettynum($add) . " $stat.|" . prettynum($user_class->points) . "|" . prettynum($pet_class->$stat) . "|$pet_class->maxenergy");
    } else
        die("You don't have enough energy.");
}
if (isset($_POST['what']) and $_POST['what'] == 'train') {
    if ($_POST['amnt'] <= $pet_class->energy && $_POST['amnt'] > 0) {
        $add = round($egy * ($pet_class->awake / 100 * 3.14 / 2) * 1.25 * $modifier);
        if (isset($user_class->completeUserResearchTypesIndexedOnId[11])) {
            $resAddInc = $add / 100 * 5;
            $add = $add + $resAddInc;
        }
        $pet_class->$stat += $add;
        $pet_class->energy -= $_POST['amnt'];
        $pet_class->awakepercent = floor(($pet_class->awake / $pet_class->maxawake) * 100);
        $pet_class->formattedawake = $pet_class->awake . " / " . $pet_class->maxawake . " [" . $pet_class->awakepercent . "%]";
        $pet_class->energypercent = floor(($pet_class->energy / $pet_class->maxenergy) * 100);
        $pet_class->formattedenergy = $pet_class->energy . " / " . $pet_class->maxenergy . " [" . $pet_class->energypercent . "%]";
        perform_query("UPDATE pets SET $stat = ?, awake = ?, energy = ? WHERE userid = ?", [$pet_class->{$stat}, $pet_class->awake, $pet_class->energy, $user_class->id]);
        addToPetladder($pet_class->id, 'gym', $add);
        print ("You trained with {$_POST['amnt']} energy and received " . prettynum($add) . " $stat.|" . prettynum($pet_class->$stat) . "|$pet_class->energy|");
        include 'includepet.php';
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
        if ($pet_class->energy == $pet_class->maxenergy)
            die("Your energy is already full.");
        elseif ($user_class->points < 10)
            die("You do not have enough points to refill your energy.");
        else {
            $pet_class->energy = $pet_class->maxenergy;
            $user_class->points -= 10;
            $pet_class->energypercent = floor(($pet_class->energy / $pet_class->maxenergy) * 100);
            $pet_class->formattedenergy = $pet_class->energy . " / " . $pet_class->maxenergy . " [" . $pet_class->energypercent . "%]";
            perform_query("UPDATE grpgusers SET points = points - 10 WHERE id = ?", [$user_class->id]);
            perform_query("UPDATE pets SET energy = ? WHERE userid = ?", [$pet_class->maxenergy, $user_class->id]);
            print ("You have refilled your energy for 10 points.|$user_class->points|$pet_class->energy|");
            include 'includepet.php';
            die();
        }
    } elseif ($att == 'awake') {
        $ptstouse = 100 - $pet_class->awakepercent;
        if ($pet_class->awake == $pet_class->maxawake)
            die("Your awake is already full.");
        elseif ($user_class->points < $ptstouse)
            die("You do not have enough points to refill your awake.");
        else {
            $pet_class->awake = $pet_class->maxawake;
            $user_class->points -= $ptstouse;
            $pet_class->awakepercent = 100;
            perform_query("UPDATE grpgusers SET points = points - ? WHERE id = ?", [$ptstouse, $user_class->id]);
            perform_query("UPDATE pets SET awake = ? WHERE userid = ?", [$pet_class->maxawake, $user_class->id]);
            print ("You have refilled your awake for $ptstouse points.|$user_class->points|$pet_class->energy|");
            include 'includepet.php';
            die();
        }
    } elseif ($att == 'both') {
        $ptstouse = 100 - $pet_class->awakepercent;
        $ptstouse += 10;
        if ($pet_class->awake == $pet_class->maxawake)
            die("Your awake is already full.");
        elseif ($user_class->points < $ptstouse)
            die("You do not have enough points to refill your awake/energy.");
        elseif ($pet_class->energy == $pet_class->maxenergy)
            die("Your energy is already full.");
        else {
            $pet_class->awake = $pet_class->maxawake;
            $pet_class->energy = $pet_class->maxenergy;
            $user_class->points -= $ptstouse;
            $pet_class->awakepercent = 100;
            $pet_class->energypercent = floor(($pet_class->energy / $pet_class->maxenergy) * 100);
            $pet_class->formattedenergy = $pet_class->energy . " / " . $pet_class->maxenergy . " [" . $pet_class->energypercent . "%]";
            perform_query("UPDATE pets SET energy = ?, awake = ? WHERE userid = ?", [$pet_class->maxenergy, $pet_class->maxawake, $user_class->id]);
            perform_query("UPDATE grpgusers SET points = points - ? WHERE id = ?", [$ptstouse, $user_class->id]);
            print ("You have refilled your energy/awake for $ptstouse points.|$user_class->points|$pet_class->energy|");
            include 'includepet.php';
            die();
        }
    }
}
?>