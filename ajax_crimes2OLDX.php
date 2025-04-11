<?php

//header('Content-type: application/json');
session_start();

include "classes.php";
include "database/pdo_class.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_class = new User($_SESSION['id']);
session_write_close();

// $logger = new Katzgrau\KLogger\Logger('/var/www/logs/speedcrimes', Psr\Log\LogLevel::INFO, array(
//     'prefix' => $user_class->id . "-",
// ));

$crime_multiplier = 1;
if (isset($_POST['cm'])) {
    $allowed = array(1, 2, 4, 10);
    if (in_array($_POST['cm'], $allowed)) {
        $crime_multiplier = $_POST['cm'];
    }
}

$debug = array(
    'id' => $user_class->id,
    'crime_multiplier' => $crime_multiplier
);

if (!$user_class) {
    die();
}

$db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array(
    $user_class->id
));

if ($user_class->jail || $user_class->hospital) {
    echo json_encode(array(
        'text' => "<b>You are not able to do crimes at the moment.</b>",
        //'error' => 'refresh'
    ));
    $debug['error'] = "Jail OR Hospital";
    //$logger->info("", $debug);
    die();
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['id']) || isset($input['id'])) {
    $id = (isset($_POST['id'])) ? $_POST['id'] : $input['id'];

    $db->query("SELECT `id`, `nerve`, `name` FROM crimes WHERE id = ? LIMIT 1");
    $db->execute([$id]);
    $row = $db->fetch_row(true);

    $debug['crime'] = $id;
    $debug['nerve'] = $user_class->nerve;
    $debug['nerref'] = $user_class->nerref;

    if (empty($row)) {
        $debug['error'] = "Empty Crimes Row";
        //$logger->info("", $debug);
        die();
    }

    $nerve = $row['nerve'];
    $name = $row['name'];
    if ($user_class->maxnerve < $nerve) {
        die();
    }

    $time = floor(($nerve - ($nerve * 0.5)) * 6);
    $stext = 'You successfully managed to ' . $name;
    $ftext = 'You failed to ' . $name;
    $chance = rand(2, 100);
    $money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
    $exp = ((10 * $nerve) + 8 * ($nerve - 1)) * 1.0;
    // Fetch the crime count and determine the star level
    $db->query("SELECT `count` FROM crimeranks WHERE userid = ? AND crimeid = ?");
    $db->execute(array($user_class->id, $row['id']));
    $crimeRankResult = $db->fetch_row(true);

    if ($crimeRankResult) {
        $crimeCount = (int) $crimeRankResult['count'];
    } else {
        $crimeCount = 0;
    }

    // Determine the star level based on the crime count
    if ($crimeCount >= 10000 && $crimeCount < 100000) {
        $star_level = 1;
    } elseif ($crimeCount >= 100000 && $crimeCount < 10000000000) {
        $star_level = 2;
    } elseif ($crimeCount >= 10000000000 && $crimeCount < 200000000) {
        $star_level = 3;
    } elseif ($crimeCount >= 200000000 && $crimeCount < 400000000) {
        $star_level = 4;
    } elseif ($crimeCount >= 400000000) {
        $star_level = 5;
    } else {
        $star_level = 0; // No bonus if the conditions are not met
    }

    $bonus_exp_per_star_level = 0.10; // 10% bonus per star level
    $star_bonus_exp = $exp * $star_level * $bonus_exp_per_star_level;
    $exp += $star_bonus_exp;

    $crimeexpbonus = 0;
    if ($user_class->crimeexpboost > 1) {
        $crimeexpbonus += 0.2;
        $crimeexpbonus += ($user_class->crimeexpboost - 1) * 0.0333;
    } elseif ($user_class->crimeexpboost == 1) {
        $crimeexpbonus = 0.2;
    }

    $bonus = $exp * $crimeexpbonus;
    $exp = round($exp + $bonus, 2);


    if ($user_class->prestige > 0) {
        $exp *= (.20 * $user_class->prestige) + 1;
    }




    if ($user_class->exppill >= time()) {
        $exp *= 2.0;
        $chance = 100;
    }

    $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);

    $debug['worked'] = $bonus_row;

    if ($bonus_row['Time'] > 0) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    }


    if (time() < 1673827199) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    }

    // Crime Multiplier Adjustments
    $mission_nerve = $nerve;
    $nerve = ($nerve * $crime_multiplier);
    $exp = ($exp * $crime_multiplier);
    $money = ($money * $crime_multiplier);

    $prepaid = false;

    if ($crime_multiplier > 1) {
        if ($nerve > $user_class->maxnerve) {
            if ($user_class->nerref == 2) {
                $nerveneeded = $nerve - $user_class->maxnerve;
                $debug['nerve_needed'] = $nerveneeded;
                $cost = floor($nerveneeded / 10);
                if ($cost < 10) {
                    $cost = 10;
                }
                if ($cost > $user_class->points) {
                    return 0;
                }

                $debug['cost'] = $cost;

                $user_class->nerve = $user_class->maxnerve;

                $user_class->points -= $cost;
                $db->query("UPDATE grpgusers SET points = points - ?, nerve = ? WHERE id = ?");
                $db->execute(array(
                    $cost,
                    $user_class->maxnerve,
                    $user_class->id
                ));

                $prepaid = true;
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
        if ($chance < 5) {
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
            $debug['response'] = "Failed Crime";
            //$logger->info("", $debug);
            die($ftext . ".|" . number_format($user_class->points) . "|" . number_format($user_class->money) . "|" . number_format($user_class->level) . "|" . genBars());
        } elseif ($chance < 7) {
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + 1, jail = 300 WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
            $debug['response'] = "Jail for 5 Minutes";
            //$logger->info("", $debug);
            echo json_encode(array(
                'text' => 'You were hauled off to jail for 5 minutes',
                //'error' => 'refresh'
            ));
            die();
            //die("$ftext. You were hauled off to jail for 5 minutes.|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
        } else {
            //$mission_nerve = $nerve / $crime_multiplier;
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
            // $db->query("INSERT INTO crime_log (userid, nerve, exp) VALUES (?, ?, ?)");
            // $db->execute(array($user_class->id, $nerve, $exp));
            newmissions($which, $crime_multiplier);
            mission('c', $crime_multiplier);
            gangContest(array('crimes' => $crime_multiplier, 'exp' => $exp));
            bloodbath('crimes', $user_class->id, $bbnerve / $user_class->level, $crime_multiplier);

            $gtax = 0;
            if ($user_class->gang != 0) {
                $db->query("SELECT `tax` FROM `gangs` WHERE `id` = ?");
                $db->execute(array($user_class->gang));
                $gangTax = $db->fetch_row(true);

                // Check if 'tax' index exists and is greater than 0
                if (isset($gangTax['tax']) && $gangTax['tax'] > 0) {
                    // Use the retrieved tax value for calculation
                    $gtax = $money * ($gangTax['tax'] / 100);
                }
            }
            $money = $money - $gtax;
            $totaltax = $gtax;

            $user_class->money += $money;
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET loth = loth + ?, exp = exp + ?, crimesucceeded = crimesucceeded + 1, crimemoney = crimemoney + ?, `money` = `money` + ?, nerve = nerve - ?, todaysexp = todaysexp + ?, expcount = expcount + ?, totaltax = totaltax + ? WHERE id = ?");
            $db->execute(array(
                $exp,
                $exp,
                $money,
                $money,
                $nerve,
                $exp,
                $exp,
                $totaltax,
                $user_class->id
            ));

            $db->query("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
            $db->execute(array(
                $gtax,
                $user_class->gang
            ));


            // Check if the user has already performed this crime and a row exists in crimeranks
            $db->query("SELECT id FROM crimeranks WHERE userid = ? AND crimeid = ?");
            $db->execute(array($user_class->id, $id));
            $crimeRank = $db->fetch_row(true);

            if ($crimeRank) {
                // A row exists, so update the count
                $db->query("UPDATE crimeranks SET count = count + 1 WHERE id = ?");
                $db->execute(array($crimeRank['id']));
            } else {
                // No row exists for this user and crimeid, so insert a new row
                $db->query("INSERT INTO crimeranks (userid, crimeid, count) VALUES (?, ?, 1)");
                $db->execute(array($user_class->id, $id));
            }
            $db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = \"no\" LIMIT 1");
            $db->execute(array(
                $user_class->id
            ));
            $activeMission = $db->fetch_row(true);
            $mt = "";
            if ($activeMission) {
                $mt = "Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}";
            }

            $text = ($gtax > 0) ? "$stext. You received $exp exp and $$money.(Gang Tax: $$gtax)" : "$stext. You received $exp exp and $$money";

            $debug['response'] = "Success! $text";
            //$logger->info("", $debug);
            echo json_encode(array(
                'text' => $text,
                'stats' => array(
                    'points' => number_format($user_class->points),
                    'money' => number_format($user_class->money),
                    'level' => number_format($user_class->level),
                    'mission' => $mt
                ),
                'bars' => array(
                    'energy' => array(
                        'percent' => $user_class->energypercent,
                        'title' => $user_class->formattedenergy
                    ),
                    'nerve' => array(
                        'percent' => $user_class->nervepercent,
                        'title' => $user_class->formattednerve
                    ),
                    'awake' => array(
                        'percent' => $user_class->awakepercent,
                        'title' => $user_class->awakepercent
                    ),
                    'exp' => array(
                        'percent' => $user_class->exppercent,
                        'title' => $user_class->exppercent
                    ),
                )
            ));
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
