<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start();
if (isset($_SESSION['user_id'])) {
    $_SESSION['id'] = $_SESSION['user_id'];
}

include "database/pdo_class.php";
include "classes.php";
include "codeparser.php";


$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

$user_class = new User($_SESSION['id']);

if ($m->get('crime.' . $user_class->id . time())) {
    $m->increment('crime.' . $user_class->id . time());
} else {
    $m->set('crime.' . $user_class->id . time(), 1, MEMCACHE_COMPRESSED);
}

if ($m->get('crime.' . $user_class->id . time()) > 100) {
    die("Error, going too fast.");
}

$lcl = $m->get('lastcrimeload.' . $user_class->id);
$lpl = $m->get('lastpageload.' . $user_class->id);

if ($lpl > $lcl) {
    die("Error training.");
}

if (isset($_POST['amnt'])) {
    security($_POST['amnt'], 'num');
}

if ($user_class->hospital > 0) {
    die("You can't train at the gym if you are in the hospital.");
}

$modifier = 1.0;

$query = "SELECT upgrade6 FROM gangs WHERE id = :gangId";
$db->query($query);
$db->bind(':gangId', $user_class->gang, PDO::PARAM_INT);
$row = $db->fetch_row(true);

if ($row) {
    $upgrade_level = $row['upgrade6'];
    $bonus_per_level = 0.05; // 5% per level
    $modifier = isset($modifier) ? $modifier : 1;

    if ($upgrade_level > 0) {
        $bonus_percentage = $bonus_per_level * $upgrade_level; 
        $modifier *= (1 + $bonus_percentage); 
    }
}

if (!isset($_POST['stat']) || !in_array($_POST['stat'], array('strength', 'defense', 'speed'))) {
    die("Invalid stat.");
}
$stat = $_POST['stat'];

$amount = isset($_POST['amnt']) ? (int)$_POST['amnt'] : 0; 

$user_class->directawake -= round(0.75 * $amount);
$modifier = 1.5; 

$prestigeBonus = (0.20 * $user_class->prestige) + 1.5;
$modifier = max($prestigeBonus, $modifier);

if ($user_class->pack1 == 3) {
    $modifier *= 1.20;
}

$user_class->directawake = max(0, $user_class->directawake);

if (isset($_POST['what']) && $_POST['what'] == 'trainrefill') {
    $ptsforawake = 100 - (($user_class->directawake / $user_class->directmaxawake) * 100);
    $ptsreq = 10 + ceil($ptsforawake);
    
    if ($ptsreq > $user_class->points) {
        die("You do not have enough points to train.");
    }
    
    if (isset($_POST['amnt']) && $_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        $user_class->{$stat} += $add;
        $user_class->dailytrains += $add;
        $user_class->points -= $ptsreq;
        
        $stmt = "UPDATE grpgusers SET $stat = :stat, dailytrains = :dailytrains, points = points - :ptsreq, energy = :maxenergy WHERE id = :id";
        $db->query($stmt);
        $db->bind(':stat', $user_class->{$stat});
        $db->bind(':dailytrains', $user_class->dailytrains);
        $db->bind(':ptsreq', $ptsreq);
        $db->bind(':maxenergy', $user_class->maxenergy);
        $db->bind(':id', $user_class->id);
        $db->execute();
        
        die("You trained with {$_POST['amnt']} energy and received " . prettynum($add) . " $stat.|" . number_format($user_class->points) . "|" . prettynum($user_class->{$stat}) . "|$user_class->maxenergy");
    } else {
        die("You don't have enough energy.");
    }
}

if (isset($_POST['what']) && $_POST['what'] == 'train') {
    $mega_train_multiplier = isset($_POST['mega_train']) && $_POST['mega_train'] === 'yes' ? 10 : 1;

    if ($_POST['amnt'] <= $user_class->energy && $_POST['amnt'] > 0) {
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        $user_class->$stat += $add;
        $user_class->dailytrains += $add;

        $awakeReduction = round(0.03 * $_POST['amnt']); 
        $user_class->directawake -= $awakeReduction;
        $user_class->directawake = max(0, $user_class->directawake);

        $ptsforawake = ceil(($awakeReduction / $user_class->directmaxawake) * 100);
        $basePtsReq = 10 + $ptsforawake;
        $ptsreq = $basePtsReq * $mega_train_multiplier;

        if ($ptsreq > $user_class->points) {
            die("You do not have enough points to train.");
        }

        $user_class->points -= $ptsreq;

        $stmt = "UPDATE grpgusers SET $stat = :statValue, dailytrains = :dailyTrains, awake = :awake, energy = :energy, points = points - :ptsreq WHERE id = :userId";
        $db->query($stmt);
        $db->bind(':statValue', $user_class->{$stat});
        $db->bind(':dailyTrains', $user_class->dailytrains);
        $db->bind(':awake', $user_class->directawake);
        $db->bind(':energy', $user_class->energy);
        $db->bind(':ptsreq', $ptsreq);
        $db->bind(':userId', $user_class->id);
        $db->execute();
        
        $displayedEnergyUsed = $mega_train_multiplier == 10 ? $_POST['amnt'] * $mega_train_multiplier : $_POST['amnt'];
        echo "You trained with " . $displayedEnergyUsed . " energy and received " . prettynum($add) . " $stat.|" . prettynum($user_class->$stat) . "|".genBars();
        echo "|" . $user_class->energy;
        exit;
    } else {
        die("You don't have enough energy.");
    }
}

if (isset($_POST['what']) && $_POST['what'] == 'refill') {
    if (!in_array($_POST['att'], ['energy', 'awake', 'both'])) {
        die("Invalid stat.");
    }

    $att = $_POST['att'];
    $pointsToUse = 0;

    if ($att === 'energy' || $att === 'both') {
        if ($user_class->energy == $user_class->maxenergy) {
            die("Your energy is already full.");
        }
        if ($user_class->points < 10) {
            die("You do not have enough points to refill your energy.");
        }
        $user_class->energy = $user_class->maxenergy;
        $pointsToUse += 10; 
    }

    if ($att === 'awake' || $att === 'both') {
        if ($user_class->directawake == $user_class->directmaxawake) {
            die("Your awake is already full.");
        }
        $ptsForAwake = 100 - floor(($user_class->directawake / $user_class->directmaxawake) * 100);
        if ($user_class->points < $ptsForAwake) {
            die("You do not have enough points to refill your awake.");
        }
        $user_class->directawake = $user_class->directmaxawake;
        $pointsToUse += $ptsForAwake;
    }

    if ($user_class->points < $pointsToUse) {
        die("You do not have enough points.");
    }

    $stmt = "UPDATE grpgusers SET energy = :energy, awake = :awake, points = points - :pointsToUse WHERE id = :id";
    $db->query($stmt);
    $db->bind(':energy', $user_class->energy);
    $db->bind(':awake', $user_class->directawake);
    $db->bind(':pointsToUse', $pointsToUse);
    $db->bind(':id', $user_class->id);
    $db->execute();

    $user_class->points -= $pointsToUse; 

    $responseMessage = "You have refilled ";
    if ($att === 'both') {
        $responseMessage .= "your energy/awake for $pointsToUse points.";
    } elseif ($att === 'energy') {
        $responseMessage .= "your energy for 10 points.";
    } else {
        $responseMessage .= "your awake for $ptsForAwake points.";
    }

    $responseMessage .= "|" . number_format($user_class->points) . "|".genBars();
    $responseMessage .= "|$user_class->energy";

    die($responseMessage);
}
?>
