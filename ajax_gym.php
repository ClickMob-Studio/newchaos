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

// Assuming $user_class instantiation remains valid. Ensure User class is compatible with PDO for any DB operations.
$user_class = new User($_SESSION['id']);

// Memcache logic - assuming $m is a correctly initialized Memcache object
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

// Validate the amount posted
if (isset($_POST['amnt'])) {
    // Here, ensure the `security` function properly sanitizes and validates `$_POST['amnt']`. Consider using filter_input() or similar for validation.
    security($_POST['amnt'], 'num');
}

// Check hospital status
if ($user_class->hospital > 0) {
    die("You can't train at the gym if you are in the hospital.");
}

$modifier = 1.0;

$query = "SELECT upgrade6 FROM gangs WHERE id = :gangId";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':gangId', $user_class->gang, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $upgrade_level = $row['upgrade6'];

    // Define the bonus percentage per level
    $bonus_per_level = 0.05; // 5% per level

    // Initialize modifier if not already set using ternary operator instead of ?? for older PHP versions
    $modifier = isset($modifier) ? $modifier : 1;

    // Apply the bonus to the modifier
    if ($upgrade_level > 0) {
        $bonus_percentage = $bonus_per_level * $upgrade_level; // Calculate total bonus percentage
        $modifier *= (1 + $bonus_percentage); // Apply the total bonus
    }
}

// Validate 'stat' input
if (!isset($_POST['stat']) || !in_array($_POST['stat'], array('strength', 'defense', 'speed'))) {
    die("Invalid stat.");
}
$stat = $_POST['stat'];

// Assuming $_POST['amnt'] is set and is a numeric value. You might want to validate this as well.
$amount = isset($_POST['amnt']) ? (int)$_POST['amnt'] : 0; // Cast to int for safety

// Adjust directawake based on the amount. Ensure that $user_class->directawake and $_POST['amnt'] are properly initialized and sanitized.
$user_class->directawake -= round(0.75 * $amount);

// Initialize or update the modifier
$modifier = 1.5; // Starting modifier

// Increase the modifier based on the user's prestige
$prestigeBonus = (0.20 * $user_class->prestige) + 1.5;
$modifier = max($prestigeBonus, $modifier);

// Apply a 20% bonus if the user has pack1 set to 3
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
        // Calculate the amount to add to the stat
        $add = round($_POST['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
        
        // Update the user's stat, dailytrains, and points
        $user_class->{$stat} += $add;
        $user_class->dailytrains += $add;
        $user_class->points -= $ptsreq;
        
        // Prepare the PDO statement for updating the user's attributes in the database
        $stmt = $pdo->prepare("UPDATE grpgusers SET $stat = :stat, dailytrains = :dailytrains, points = points - :ptsreq, energy = :maxenergy WHERE id = :id");
        $stmt->execute([
            ':stat' => $user_class->{$stat},
            ':dailytrains' => $user_class->dailytrains,
            ':ptsreq' => $ptsreq,
            ':maxenergy' => $user_class->maxenergy,
            ':id' => $user_class->id
        ]);
        
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

        // Using PDO to update the user's stats
        $stmt = $pdo->prepare("UPDATE grpgusers SET $stat = :statValue, dailytrains = :dailyTrains, awake = :awake, energy = :energy, points = points - :ptsreq WHERE id = :userId");
        $stmt->execute([
            ':statValue' => $user_class->{$stat},
            ':dailyTrains' => $user_class->dailytrains,
            ':awake' => $user_class->directawake,
            ':energy' => $user_class->energy,
            ':ptsreq' => $ptsreq,
            ':userId' => $user_class->id
        ]);
        
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
        $pointsToUse += 10; // 10 points to refill energy
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
        $pointsToUse += $ptsForAwake; // Additional points required to refill awake
    }

    if ($user_class->points < $pointsToUse) {
        die("You do not have enough points.");
    }

    // Update the database with the new values
    $stmt = $pdo->prepare("UPDATE grpgusers SET energy = :energy, awake = :awake, points = points - :pointsToUse WHERE id = :id");
    $stmt->execute([
        ':energy' => $user_class->energy,
        ':awake' => $user_class->directawake,
        ':pointsToUse' => $pointsToUse,
        ':id' => $user_class->id
    ]);

    $user_class->points -= $pointsToUse; // Update points after successful database update

    // Prepare the response
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