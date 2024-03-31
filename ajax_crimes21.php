<?php

//header('Content-type: application/json');
session_start();

include "classes.php";
include "dbcon.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = new User($_SESSION['id']);
session_write_close();


$crime_multiplier = 1;
if (isset($_POST['cm'])) {
    $cmInput = (int) $_POST['cm']; // Cast to integer to ensure the value is treated as a number
    $allowed = [1, 2, 4, 10]; // Short array syntax for readability
    if (in_array($cmInput, $allowed)) {
        $crime_multiplier = $cmInput;
    }
}

$debug = array(
    'id'               => $user_class->id,
    'crime_multiplier' => $crime_multiplier
);

// if($m->get('crime.'.$user_class->id . time()))
//     $m->increment('crime.'.$user_class->id . time());
// else
//     $m->set('crime.'.$user_class->id . time(), 1, MEMCACHE_COMPRESSED);

// if($m->get('crime.'.$user_class->id . time()) > 100)
//     die("Error, going too fast.");

// $lcl = $m->get('lastcrimeload.'.$user_class->id);
// $lpl = $m->get('lastpageload.'.$user_class->id);
// if($lpl > $lcl)
//     die("Error training.");

if (!$user_class) {
    die();
}

$stmt = $pdo->prepare("UPDATE grpgusers SET lastactive = UNIX_TIMESTAMP() WHERE id = ?");
$stmt->execute([$user_class->id]);

if ($user_class->jail || $user_class->hospital) {
    header('Content-Type: application/json'); // Set the content type to application/json
    echo json_encode(array(
        'text' => "<b>You are not able to do crimes at the moment.</b>",
     
    ));
    
    $debug['error'] = "Jail OR Hospital";
    
    die(); 
}

// Reading JSON input from the request body
$input = json_decode(file_get_contents('php://input'), true);
if (is_null($input)) {
    // Handle error or invalid JSON input
    header('Content-Type: application/json'); // Set the content type to application/json
    echo json_encode(array('error' => 'Invalid JSON'));
    die(); // Invalid input provided
}


if (isset($_POST['id']) || isset($input['id'])) {
    if (!$row = $m->get('crimes.' . $id)) {
        // Prepare the SQL statement using PDO
        $stmt = $pdo->prepare("SELECT `id`, `nerve`, `name` FROM crimes WHERE id = ? LIMIT 1");
        
        // Execute the statement with the parameter
        $stmt->execute([$id]);
        
        // Fetch the result row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if the row was fetched successfully
        if ($row) {
            // Assuming $m->set is the method to cache the data, adjust parameters as per your caching mechanism
            $m->set('crimes.' . $id, $row, 120); // Updated to match a generic caching method signature
        } else {
            // Handle case where no data is found for the given id
            echo 'No data found for the given ID';
            die();
        }
    }

    $debug['crime'] = $id;
    $debug['nerve'] = $user_class->nerve;
    $debug['nerref'] = $user_class->nerref;


    $m->set('crimesave' . $user_class->id, $row['id']);

$nerve = $row['nerve'];
$name = $row['name'];

// Check if user's max nerve is less than the crime's required nerve
if ($user_class->maxnerve < $nerve) {
    die("Insufficient nerve.");
}

$time = floor(($nerve - ($nerve * 0.5)) * 6);
$stext = 'You successfully managed to ' . $name;
$ftext = 'You failed to ' . $name;
$chance = rand(0, 100);
$money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
$exp = ((10 * $nerve) + 2 * ($nerve - 1));

// Preparing the SQL query using PDO for fetching the crime count
$stmt = $pdo->prepare("SELECT `count` FROM crimeranks WHERE userid = ? AND crimeid = ?");
$stmt->execute([$user_class->id, $row['id']]);
$crimeRankResult = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if $crimeRankResult has data
if ($crimeRankResult) {
    // Safely cast the count to an integer
    $crimeCount = (int) $crimeRankResult['count'];
} else {
    // Default to 0 if no data found
    $crimeCount = 0;
}

// Determine the star level based on the crime count
if ($crimeCount >= 10000 && $crimeCount < 100000) {
    $star_level = 1;
} elseif ($crimeCount >= 100000 && $crimeCount < 1000000) { // Adjusted for consistency
    $star_level = 2;
} elseif ($crimeCount >= 1000000 && $crimeCount < 200000000) { // Adjusted range for logical progression
    $star_level = 3;
} elseif ($crimeCount >= 200000000 && $crimeCount < 400000000) {
    $star_level = 4;
} elseif ($crimeCount >= 400000000) { // Simplified for clarity
    $star_level = 5;
} else {
    $star_level = 0; // No star level if the conditions are not met
}

// Initial experience calculation
$exp = (10 * $nerve) + 2 * ($nerve - 1);

// Apply 10% bonus experience per star level
$bonus_exp_per_star_level = 0.10; // 10% bonus per star level
$star_bonus_exp = $exp * $star_level * $bonus_exp_per_star_level;
$exp += $star_bonus_exp;

// Calculate additional experience bonus based on user's crime experience boost
$crimeexpbonus = 0;
if ($user_class->crimeexpboost > 1) {
    $crimeexpbonus += 0.2; // Base boost for having any crimeexpboost
    // Additional boost for each level of crimeexpboost beyond the first
    $crimeexpbonus += ($user_class->crimeexpboost - 1) * 0.0333;
} elseif ($user_class->crimeexpboost == 1) {
    $crimeexpbonus = 0.2; // Base boost for crimeexpboost at level 1
}

// Apply the calculated crime experience bonus
$exp *= (1 + $crimeexpbonus);
// Adjusting the final experience gain
$bonus = $exp * $crimeexpbonus;
$exp = round($exp + $bonus, 2);

// Apply prestige bonus
if ($user_class->prestige > 0) {
    $exp *= (.20 * $user_class->prestige) + 1;
}

// Check and apply the experience pill effect
if ($user_class->exppill >= time()) {
    $exp *= 2.0;
    $chance = 100; // Ensure success
}

// Fetching game bonus using PDO
$stmt = $pdo->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
$bonus_row = $stmt->fetch(PDO::FETCH_ASSOC); // Using PDO's fetch method

$debug['worked'] = $bonus_row; // Debugging

// Apply game bonus if active
if ($bonus_row && $bonus_row['Time'] > 0) {
    $exp *= 2;
    // The following line seems redundant since it effectively does nothing:
    // $money *= 1; // This line can be omitted as it has no effect.
    $chance = 100; // Ensure success
}
// Check if current time is less than a specific timestamp
if (time() < 1673827199) {
    $exp *= 2;
    $money *= 1;
    $chance = 100;
}

// Crime Multiplier Adjustments
$mission_nerve = $nerve;
$nerve = ($nerve * $crime_multiplier);
$exp   = ($exp * $crime_multiplier);
$money = ($money * $crime_multiplier);

$prepaid = false;

if ($crime_multiplier > 1) {
    if ($nerve > $user_class->nerve) {
        if ($user_class->nerref == 2) {
            $nerveneeded = $nerve - $user_class->nerve;
            $cost = floor($nerveneeded / 10);
            $cost = max(10, $cost); // Ensure cost is at least 10

            if ($cost <= $user_class->points) {
                // Deduct the cost from user's points
                $user_class->points -= $cost;

                // Update points in the database
                $stmt = $pdo->prepare("UPDATE grpgusers SET points = points - :cost WHERE id = :id");
                $stmt->execute(array(
                    ':cost' => $cost,
                    ':id' => $user_class->id
                ));

                $prepaid = true;
            } else {
                return 0; // Not enough points, return an error
            }
        } else {
            $debug['error'] = "Refil Not Enabled";
            //$logger->info("", $debug);
            die();
        }
    }
}
if ($user_class->nerve < $nerve && !$prepaid) {
    refill('n');
}

if ($user_class->nerve >= $nerve || $prepaid) {
    if ($prepaid) {
        $bbnerve = $nerve;
        $nerve = 0;
    } else {
        $bbnerve = $nerve / $user_class->level;
    }

    if ($chance < 5 || $chance < 7) {
        $failed = $chance < 5 ? 'Failed Crime' : 'Jail for 5 Minutes';
        
        $user_class->nerve -= $nerve;

        // Update user's stats in the database
        $stmt = $pdo->prepare("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + ?, jail = ? WHERE id = ?");
        $stmt->execute([$nerve, $chance < 7 ? 1 : 0, $chance < 7 ? 300 : 0, $user_class->id]);

        if ($chance < 7) {
            echo json_encode(['text' => 'You were hauled off to jail for 5 minutes']);
            die();
        } else {
            $debug['response'] = $failed;
            die($ftext . ".|" . number_format($user_class->points) . "|" . number_format($user_class->money) . "|" . number_format($user_class->level) . "|" . genBars());
        }
    } else {
        // Calculate mission related values
        $debug['mission_nerve'] = $mission_nerve;
        if ($mission_nerve >= 50) {
            $which = "crimes50";
        } elseif ($mission_nerve >= 25) {
            $which = "crimes25";
        } elseif ($mission_nerve >= 10) {
            $which = "crimes10";
        } elseif ($mission_nerve >= 5) {
            $which = "crimes5";
        } else {
            $which = "crimes1";
        }

        // Execute mission and related actions
        newmissions($which, $crime_multiplier);
        mission('c', $crime_multiplier);
        gangContest(['crimes' => $crime_multiplier, 'exp' => $exp]);
        bloodbath('crimes', $user_class->id, $bbnerve / $user_class->level, $crime_multiplier);

        // Calculate gang tax if user belongs to a gang
        $gtax = 0;
        if ($user_class->gang != 0) {
            $stmt = $pdo->prepare("SELECT `tax` FROM `gangs` WHERE `id` = ?");
            $stmt->execute([$user_class->gang]);
            $gangTax = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($gangTax && isset($gangTax['tax']) && $gangTax['tax'] > 0) {
                $gtax = $money * ($gangTax['tax'] / 100);
            }
        }
        $money -= $gtax;
        $totaltax = $gtax;
        
        $user_class->money += $money;
        $user_class->nerve -= $nerve;
        
        // Update user's stats in the database
        $stmt = $pdo->prepare("UPDATE grpgusers SET loth = loth + ?, exp = exp + ?, crimesucceeded = crimesucceeded + 1, crimemoney = crimemoney + ?, `money` = `money` + ?, nerve = nerve - ?, todaysexp = todaysexp + ?, expcount = expcount + ?, totaltax = totaltax + ? WHERE id = ?");
        $stmt->execute([$exp, $exp, $money, $money, $nerve, $exp, $exp, $totaltax, $user_class->id]);
        
        // Update gang's moneyvault in the database
        $stmt = $pdo->prepare("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
        $stmt->execute([$gtax, $user_class->gang]);

        $stmt = $pdo->prepare("SELECT id FROM crimeranks WHERE userid = ? AND crimeid = ?");
        $stmt->execute([$user_class->id, $id]);
        $crimeRank = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($crimeRank) {
            // A row exists, so update the count
            $stmt = $pdo->prepare("UPDATE crimeranks SET count = count + 1 WHERE id = ?");
            $stmt->execute([$crimeRank['id']]);
        } else {
            // No row exists for this user and crimeid, so insert a new row
            $stmt = $pdo->prepare("INSERT INTO crimeranks (userid, crimeid, count) VALUES (?, ?, 1)");
            $stmt->execute([$user_class->id, $id]);
        }
        $stmt = $pdo->prepare("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = 'no' LIMIT 1");
        $stmt->execute([$user_class->id]);
        $activeMission = $stmt->fetch(PDO::FETCH_ASSOC);
        $mt = "";
        if ($activeMission) {
            $mt = "Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}";
        }
        
        $text = ($gtax > 0) ? "$stext. You received $exp exp and $$money.(Gang Tax: $$gtax)" : "$stext. You received $exp exp and $$money";
        
        $debug['response'] = "Success! $text";
        
        echo json_encode([
            'text' => $text,
            'stats' => [
                'points' => number_format($user_class->points),
                'money' => number_format($user_class->money),
                'level' => number_format($user_class->level),
                'mission' => $mt
            ],
            'bars' => [
                'energy' => [
                    'percent' => $user_class->energypercent,
                    'title' => $user_class->formattedenergy
                ],
                'nerve' => [
                    'percent' => $user_class->nervepercent,
                    'title' => $user_class->formattednerve
                ],
                'awake' => [
                    'percent' => $user_class->awakepercent,
                    'title' => $user_class->awakepercent
                ],
                'exp' => [
                    'percent' => $user_class->exppercent,
                    'title' => $user_class->exppercent
                ],
            ]
        ]);
    }
    } else {
        $debug['error'] = "Not Enough Nerve";
        //$logger->info("", $debug);
        echo json_encode(array(
            'text' => "<b>You don't have enough nerve for that crime.</b>",
            'error' => 'refresh'
        ));
    }
}
$db = null;
