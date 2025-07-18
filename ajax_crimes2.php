<?php

ob_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
//header('Content-type: application/json');
session_start();

function shorthandNumber($number)
{
    if ($number >= 1000000000) { // Check if the number is at least a billion
        $shorthand = round($number / 1000000000, 2) . 'B'; // Convert to billions, round to 2 decimal places, and append 'B'
        return $shorthand;
    } elseif ($number >= 1000000) { // Check if the number is at least a million
        $shorthand = round($number / 1000000, 2) . 'M'; // Convert to millions, round to 2 decimal places, and append 'M'
        return $shorthand;
    } elseif ($number >= 1000) { // Check if the number is at least a thousand
        $shorthand = round($number / 1000, 1) . 'k'; // Convert to thousands, round to 1 decimal place, and append 'k'
        return $shorthand;
    }
    return number_format($number); // Return the original number if it's less than 1000
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id'])) {
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['id'] = $data['user_id'];
}

include "classes.php";
include "database/pdo_class.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['au_user_or']) && (int) $_GET['au_user_or']) {
    $user_class = new User((int) $_GET['au_user_or']);
} else {
    $user_class = new User($_SESSION['id']);
}

session_write_close();

// $logger = new Katzgrau\KLogger\Logger('/var/www/logs/speedcrimes', Psr\Log\LogLevel::INFO, array(
//     'prefix' => $user_class->id . "-",
// ));

$tempItemUse = getItemTempUse($user_class->id);

$crime_multiplier = 1;
if (isset($_POST['cm'])) {
    $allowed = array(1, 2, 4, 10, 15, 20, 30, 50);
    if (in_array($_POST['cm'], $allowed)) {
        $crime_multiplier = $_POST['cm'];
    }
}

if ($crime_multiplier == 20) {
    if ($tempItemUse['crime_15_multiplier_time'] < time()) {
        echo json_encode(array(
            'text' => "You do not have access to 20x crimes.",
            //'error' => 'refresh'
        ));
        $debug['error'] = "15X CRIMES";
        //$logger->info("", $debug);
        die();

    }
}

$debug = array(
    'id' => $user_class->id,
    'crime_multiplier' => $crime_multiplier,
    'post' => $_POST
);

if (!$user_class) {
    die();
}

set_last_active($user_class->id);

if ($user_class->jail || $user_class->hospital) {
    echo json_encode(array(
        'text' => "You are not able to do crimes at the moment.",
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
        echo json_encode(array(
            'debug' => $debug,
            'error' => 'refresh'
        ));
        die();
    }

    $nerve = $row['nerve'];
    $name = $row['name'];
    if ($user_class->maxnerve < $nerve) {
        echo json_encode(array(
            'debug' => $debug,
            'error' => 'refresh'
        ));
        die();
    }

    $time = floor(($nerve - ($nerve * 0.5)) * 6);
    $stext = 'You successfully managed to ' . $name;
    $ftext = 'You failed to ' . $name;
    $chance = rand(1, 250);
    $money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
    if ($id == 51 && $tempItemUse['ghost_vacuum_time'] > time()) {
        $exp = ceil($user_class->maxexp / 5000);
    } else if ($id == 52) {
        $currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
        if (isset($currentQuestSeason['id'])) {
            $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
            $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
            $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

            if (isset($questSeasonMission['requirements']->whitecollar_fraud) && (int) $questSeasonMissionUser['progress']->whitecollar_fraud < 10) {
                $exp = ceil($user_class->maxexp / 4);
                updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'whitecollar_fraud', 1);
                $crime_multiplier = 1;
            } else {
                echo json_encode(array(
                    'error' => 'cannot perform the crime at this time.'
                ));
                die();
            }
        }
    } else {
        $exp = ((10 * $nerve) + 8 * ($nerve - 1)) * 1.0;
    }
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
    } elseif ($crimeCount >= 100000 && $crimeCount < 1000000) {
        $star_level = 2;
    } elseif ($crimeCount >= 1000000 && $crimeCount < 5000000) {
        $star_level = 3;
    } elseif ($crimeCount >= 5000000 && $crimeCount < 15000000) {
        $star_level = 4;
    } elseif ($crimeCount >= 15000000) {
        if (isset($user_class->completeUserResearchTypesIndexedOnId[16])) {
            if ($crimeCount >= 30000000) {
                $star_level = 6;
            } else {
                $star_level = 5;
            }
        } else {
            $star_level = 5;
        }
    } else {
        $star_level = 0; // No bonus if the conditions are not met
    }

    $raw_exp = $exp;

    $bonus_exp_per_star_level = 0.10; // 10% bonus per star level
    $star_bonus_exp = $exp * $star_level * $bonus_exp_per_star_level;
    $exp += $star_bonus_exp;

    // Check if equipped weapon is an easter booster
    if ($user_class->eqweapon == 335) {
        $exp += $raw_exp * 0.1; // 10% bonus for easter booster
    } else if ($user_class->eqweapon == 347) {
        $exp += $raw_exp * 0.15; // 15% bonus for super easter booster
        $money += $money * 0.1; // 10% bonus for super easter booster
    }

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

    if ($tempItemUse['crime_booster_time'] > time()) {
        $exp += round(($exp / 5));
    } else if ($tempItemUse['crime_potion_time'] > time()) {
        $exp += round(($exp / 10));
    }


    $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);

    //$debug['worked'] = $bonus_row;

    $tempItemUse = getItemTempUse($user_class->id);
    $aTime = time();
    if ($tempItemUse['gang_double_exp_time'] > $aTime) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    } else {
        if ($bonus_row['Time'] > 0) {
            $exp *= 2;
            $money *= 1;
            $chance = 100;
        }
    }

    if (time() < 1673827199) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    }

    if (isset($_GET['au_user_or']) && (int) $_GET['au_user_or']) {
        $chance = 100;
    }

    // Crime Multiplier Adjustments
    $mission_nerve = $nerve;
    $nerve = ($nerve * $crime_multiplier);
    $exp = ($exp * $crime_multiplier);
    $money = ($money * $crime_multiplier);

    $prepaid = false;

    $debug['prenerve'] = $nerve;
    $debug['preusernerve'] = $user_class->nerve;
    if ($nerve > $user_class->nerve && $user_class->nerref == 2) {
        $nerveneeded = $nerve - $user_class->nerve;
        if ($nerveneeded < $user_class->maxnerve) {
            $debug['override'] = 1;
            $nerveneeded = $user_class->maxnerve;
        }

        $debug['refill'] = 'now';
        $debug['nerve'] = $nerve;
        $debug['usernerve'] = $user_class->nerve;
        $debug['usermaxnerve'] = $user_class->maxnerve;
        $debug['nerveneeded'] = $nerveneeded;

        $cost = floor($nerveneeded / 10);
        $debug['cost1'] = $cost;
        if ($cost < 10) {
            $cost = 10;
        }

        if ($cost > $user_class->points) {
            return 0;
        } else if ($user_class->points < 10) {
            return 0;
        }

        if ($user_class->id == 2) {
            //Send_Event(2, $nerveneeded . ' - ' . $user_class->maxnerve . ' - ' . $cost, 2);
        }

        $tempItemUse = getItemTempUse($user_class->id);
        $now = time();

        if ($tempItemUse['nerve_vial_time'] > $now) {
            $extraCost = $cost / 2;
            $cost = ceil($cost - ($extraCost / 2));

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
    } else if ($nerve > $user_class->nerve) {
        $debug['error'] = "Refil Not Enabled";
        echo json_encode(array(
            'debug' => $debug,
            'error' => 'refresh'
        ));

        //$logger->info("", $debug);
        die();
    }

    //    if ($user_class->nerve < $nerve && !$prepaid) {
//        refill('n');
//    }

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
        } elseif ($chance == 6) {
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
            updateGangActiveMission('crimes', $crime_multiplier);

            mission('c', $crime_multiplier);
            gangContest(array('crimes' => $crime_multiplier, 'exp' => $exp));
            bloodbath('crimes', $user_class->id, $bbnerve / $user_class->level, $crime_multiplier);

            $userPrestigeSkills = getUserPrestigeSkills($user_class);
            if ($userPrestigeSkills['crime_cash_unlock'] > 0) {
                $money = $money + ($money / 100 * 10);
            }
            if ($userPrestigeSkills['crime_cash_boost_level'] > 0) {
                $money = $money + ($money / 100 * (2 * $userPrestigeSkills['crime_cash_boost_level']));
            }

            if (isset($user_class->completeUserResearchTypesIndexedOnId[1])) {
                $mPerIc = ceil($money / 100 * 2);
                $money = $money + $mPerIc;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[35])) {
                $mPerIc = ceil($money / 100 * 2);
                $money = $money + $mPerIc;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[49])) {
                $mPerIc = ceil($money / 100 * 2);
                $money = $money + $mPerIc;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[50])) {
                $mPerIc = ceil($money / 100 * 2);
                $money = $money + $mPerIc;
            }

            $researchExpBoost = 0;
            if (isset($user_class->completeUserResearchTypesIndexedOnId[4])) {
                $researchExpBoost += 5;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[7])) {
                $researchExpBoost += 5;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[9])) {
                $researchExpBoost += 5;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[12])) {
                $researchExpBoost += 10;
            }
            if (isset($user_class->completeUserResearchTypesIndexedOnId[32])) {
                $researchExpBoost += 10;
            }

            if ($researchExpBoost > 0) {

                $resExpInc = $exp / 100 * $researchExpBoost;
                $exp = $exp + $resExpInc;
            }
            if ($userPrestigeSkills['crime_exp_unlock'] > 0) {
                $exp = $exp + ($exp / 100 * 20);
            }
            $exp = ceil($exp);

            $gtax = 0;
            if ($user_class->gang != 0) {
                $db->query("SELECT `tax` FROM `gangs` WHERE `id` = ?");
                $db->execute(array($user_class->gang));
                $gangTax = $db->fetch_row(true);

                // Check if 'tax' index exists and is greater than 0
                if (isset($gangTax['tax']) && $gangTax['tax'] > 0) {
                    // Use the retrieved tax value for calculation
                    $gtax = $money * ($gangTax['tax'] / 100);
                    gangContest(array('tax' => $gtax));
                }
            }
            $money = $money - $gtax;
            $totaltax = $gtax;

            $debug['exp_earned'] = $exp;

            // UserCompLeaderboard
            $maxnervePercCheck = $mission_nerve / $user_class->maxnerve * 100;
            if ($maxnervePercCheck >= 50) {
                addToUserCompLeaderboard($user_class->id, 'crimes_complete', $crime_multiplier);
            }
            addToRelCompLeaderboard($user_class->id, 'crimes_complete', $crime_multiplier);
            //addToUserCompLeaderboard($user_class->id, 'crimes_complete', $crime_multiplier);

            $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
            $db->execute();
            $activityContest = $db->fetch_row(true);
            if ($activityContest['type'] == 'crimes') {
                addToUserCompLeaderboard($user_class->id, 'activity_complete', $crime_multiplier);
                addToRelCompLeaderboard($user_class->id, 'activity_complete', $crime_multiplier);
            }

            addToGangCompLeaderboard($user_class->gang, 'crimes_complete', $crime_multiplier);
            $bpCategory = getBpCategory();
            if ($bpCategory) {
                addToBpCategoryUser($bpCategory, $user_class, 'crimes', $crime_multiplier);
            }

            addToUserOperations($user_class, 'crimes', $crime_multiplier);
            payoutChristmasGift($user_class->id);

            $currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
            if (isset($currentQuestSeason['id'])) {
                $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
                $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
                if (isset($questSeasonMissionUser['id'])) {
                    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

                    if (isset($questSeasonMission['requirements']->crime_cash) && $questSeasonMission['requirements']->crime_cash > 0) {
                        updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'crime_cash', $money);
                    }

                }
            }

            if ($user_class->gang > 0) {
                $db->query("SELECT upgrade_crimecash FROM gangs WHERE id = " . $user_class->gang);
                $db->execute();
                $u = $db->fetch_row(true);
                $percent = $u['upgrade_crimecash'] * 2;
                $money += round(($money * $percent) / 100);
            }

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
                'debug' => $debug,
                'text' => $text,
                'stats' => array(
                    'points' => number_format($user_class->points),
                    'mb_points' => shorthandNumber($user_class->points),
                    'money' => number_format($user_class->money),
                    'mb_money' => shorthandNumber($user_class->money),
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
            'text' => "You don't have enough nerve for that crime.",
            'debug' => $debug,
            'error' => 'refresh'
        ));
    }
}
$db = null;
