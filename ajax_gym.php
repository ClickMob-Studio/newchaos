<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start();

try {
    $data = json_decode(file_get_contents("php://input"), true);

    // Log the incoming data for debugging
    error_log('Received data: ' . print_r($data, true));

    if (!isset($data['user_id'])) {
        throw new Exception("User ID is not set.");
    } else {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['id'] = $data['user_id'];
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
        throw new Exception("Error, going too fast.");
    }

    $lcl = $m->get('lastcrimeload.' . $user_class->id);
    $lpl = $m->get('lastpageload.' . $user_class->id);

    if ($lpl > $lcl) {
        throw new Exception("Error training.");
    }

    if (isset($data['amnt'])) {
        security($data['amnt'], 'num');
    }

    if ($user_class->hospital > 0) {
        throw new Exception("You can't train at the gym if you are in the hospital.");
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

    if (!isset($data['stat']) || !in_array($data['stat'], array('strength', 'defense', 'speed'))) {
        error_log('Invalid stat: ' . $data['stat']); // Debugging: log the invalid stat
        throw new Exception("Invalid stat.");
    }

    $stat = $data['stat'];

    $amount = isset($data['amnt']) ? (int)$data['amnt'] : 0; 

    $user_class->directawake -= round(0.75 * $amount);
    $modifier = 1.5; 

    $prestigeBonus = (0.20 * $user_class->prestige) + 1.5;
    $modifier = max($prestigeBonus, $modifier);

    if ($user_class->pack1 == 3) {
        $modifier *= 1.20;
    }

    $user_class->directawake = max(0, $user_class->directawake);

    if (isset($data['what']) && $data['what'] == 'trainrefill') {
        $ptsforawake = 100 - (($user_class->directawake / $user_class->directmaxawake) * 100);
        $ptsreq = 10 + ceil($ptsforawake);
        
        if ($ptsreq > $user_class->points) {
            throw new Exception("You do not have enough points to train.");
        }
        
        if (isset($data['amnt']) && $data['amnt'] <= $user_class->energy && $data['amnt'] > 0) {
            $add = round($data['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);

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
            $add = ceil($add);

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
            
            echo json_encode(array(
                'message' => "You trained with {$data['amnt']} energy and received " . prettynum($add) . " $stat.",
                'points' => number_format($user_class->points),
                'stat' => prettynum($user_class->{$stat}),
                'energy' => $user_class->maxenergy
            ));
            exit;
        } else {
            throw new Exception("You don't have enough energy.");
        }
    }

    if (isset($data['what']) && $data['what'] == 'train') {
        $mega_train_multiplier = isset($data['mega_train']) && $data['mega_train'] === 'yes' ? 10 : 1;

        if ($data['amnt'] <= $user_class->energy && $data['amnt'] > 0) {
            $add = round($data['amnt'] * ($user_class->awake / 100 * 6 / 2) * $modifier);
            $user_class->$stat += $add;
            $user_class->dailytrains += $add;

            $awakeReduction = round(0.03 * $data['amnt']); 
            $user_class->directawake -= $awakeReduction;
            $user_class->directawake = max(0, $user_class->directawake);

            $ptsforawake = ceil(($awakeReduction / $user_class->directmaxawake) * 100);
            $basePtsReq = 10 + $ptsforawake;
            $ptsreq = $basePtsReq * $mega_train_multiplier;

            if ($ptsreq > $user_class->points) {
                throw new Exception("You do not have enough points to train.");
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
            
            $displayedEnergyUsed = $mega_train_multiplier == 10 ? $data['amnt'] * $mega_train_multiplier : $data['amnt'];
            echo json_encode(array(
                'message' => "You trained with " . $displayedEnergyUsed . " energy and received " . prettynum($add) . " $stat.",
                'stat' => prettynum($user_class->$stat),
                'energy' => $user_class->energy
            ));
            exit;
        } else {
            throw new Exception("You don't have enough energy.");
        }
    }

    if (isset($data['what']) && $data['what'] == 'refill') {
        if (!in_array($data['att'], ['energy', 'awake', 'both'])) {
            throw new Exception("Invalid stat.");
        }

        $att = $data['att'];
        $pointsToUse = 0;

        if ($att === 'energy' || $att === 'both') {
            if ($user_class->energy == $user_class->maxenergy) {
                throw new Exception("Your energy is already full.");
            }
            if ($user_class->points < 10) {
                throw new Exception("You do not have enough points to refill your energy.");
            }
            $user_class->energy = $user_class->maxenergy;
            $pointsToUse += 10; 
        }

        if ($att === 'awake' || $att === 'both') {
            if ($user_class->directawake == $user_class->directmaxawake) {
                throw new Exception("Your awake is already full.");
            }
            $ptsForAwake = 100 - floor(($user_class->directawake / $user_class->directmaxawake) * 100);
            if ($user_class->points < $ptsForAwake) {
                throw new Exception("You do not have enough points to refill your awake.");
            }
            $user_class->directawake = $user_class->directmaxawake;
            $pointsToUse += $ptsForAwake;
        }

        if ($user_class->points < $pointsToUse) {
            throw new Exception("You do not have enough points.");
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

        echo json_encode(array(
            'message' => $responseMessage,
            'points' => number_format($user_class->points),
            'energy' => $user_class->energy
        ));
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(array('error' => $e->getMessage()));
}
?>
