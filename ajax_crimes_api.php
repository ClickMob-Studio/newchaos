<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-type: application/json');

require_once 'includes/functions.php';

start_session_guarded();

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

include_once "classes.php";
include_once "database/pdo_class.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    if (isset($_GET['au_user_or']) && (int) $_GET['au_user_or']) {
        $user_class = new User((int) $_GET['au_user_or']);
    } else {
        $user_class = new User($_SESSION['id']);
    }

    session_write_close();

    $crime_multiplier = 1;
    if (isset($data['cm'])) {
        $allowed = array(1, 2, 4, 10, 20, 30, 50);
        if (in_array($data['cm'], $allowed)) {
            $crime_multiplier = $data['cm'];
        }
    }

    $debug = array(
        'id' => $user_class->id,
        'crime_multiplier' => $crime_multiplier,
        'data' => $data
    );

    if (!$user_class) {
        throw new Exception("Invalid user.");
    }

    set_last_active($user_class->id);

    if ($user_class->jail || $user_class->hospital) {
        throw new Exception("You are not able to do crimes at the moment.");
    }

    if (isset($data['crime_id'])) {
        $id = $data['crime_id'];

        $db->query("SELECT `id`, `nerve`, `name` FROM crimes WHERE id = ? LIMIT 1");
        $db->execute([$id]);
        $row = $db->fetch_row(true);

        if (empty($row)) {
            throw new Exception("Not enough nerve to complete this crime");
        }

        $nerve = $row['nerve'];
        $name = $row['name'];
        if ($user_class->maxnerve < $nerve) {
            throw new Exception("Not enough nerve for the crime.");
        }

        $time = floor(($nerve - ($nerve * 0.5)) * 6);
        $stext = 'You successfully managed to ' . $name;
        $ftext = 'You failed to ' . $name;
        $chance = rand(1, 250);
        $money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
        $exp = ((10 * $nerve) + 8 * ($nerve - 1)) * 1.0;

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

        $tempItemUse = getItemTempUse($user_class->id);
        if ($tempItemUse['crime_booster_time'] > time()) {
            $exp += round(($exp / 5));
        } else if ($tempItemUse['crime_potion_time'] > time()) {
            $exp += round(($exp / 10));
        }

        $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
        $db->execute();
        $bonus_row = $db->fetch_row(true);

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
                throw new Exception("Not enough points for refill.");
            } else if ($user_class->points < 10) {
                throw new Exception("Not enough points for refill.");
            }

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
            throw new Exception("Refill not enabled.");
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
                throw new Exception($ftext . ".|" . number_format($user_class->points) . "|" . number_format($user_class->money) . "|" . number_format($user_class->level) . "|" . genBars());
            } elseif ($chance == 6) {
                $user_class->nerve -= $nerve;
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + 1, jail = 300 WHERE id = ?");
                $db->execute(array(
                    $nerve,
                    $user_class->id
                ));
                echo json_encode(array(
                    'text' => 'You were hauled off to jail for 5 minutes',
                    'error' => 'refresh'
                ));
                die();
            } else {
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
                newmissions($which, $crime_multiplier);
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

                if ($researchExpBoost > 0) {
                    $resExpInc = $exp / 100 * $researchExpBoost;
                    $exp = $exp + $resExpInc;
                }
                $exp = ceil($exp);

                $gtax = 0;
                if ($user_class->gang != 0) {
                    $db->query("SELECT `tax` FROM `gangs` WHERE `id` = ?");
                    $db->execute(array($user_class->gang));
                    $gangTax = $db->fetch_row(true);
                    if (isset($gangTax['tax']) && $gangTax['tax'] > 0) {
                        $gtax = $money * ($gangTax['tax'] / 100);
                        gangContest(array('tax' => $gtax));
                    }
                }
                $money = $money - $gtax;
                $totaltax = $gtax;

                $debug['exp_earned'] = $exp;

                $maxnervePercCheck = $mission_nerve / $user_class->maxnerve * 100;
                if ($maxnervePercCheck >= 50) {
                    addToUserCompLeaderboard($user_class->id, 'crimes_complete', $crime_multiplier);
                }

                $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
                $db->execute();
                $activityContest = $db->fetch_row(true);
                if ($activityContest['type'] == 'crimes') {
                    addToUserCompLeaderboard($user_class->id, 'activity_complete', $crime_multiplier);
                }

                addToGangCompLeaderboard($user_class->gang, 'crimes_complete', $crime_multiplier);
                $bpCategory = getBpCategory();
                if ($bpCategory) {
                    addToBpCategoryUser($bpCategory, $user_class, 'crimes', $crime_multiplier);
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

                $db->query("SELECT id FROM crimeranks WHERE userid = ? AND crimeid = ?");
                $db->execute(array($user_class->id, $id));
                $crimeRank = $db->fetch_row(true);

                if ($crimeRank) {
                    $db->query("UPDATE crimeranks SET count = count + 1 WHERE id = ?");
                    $db->execute(array($crimeRank['id']));
                } else {
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
                exit;
            }
        } else {
            throw new Exception("Not enough nerve.");
        }
    } else {
        throw new Exception("Invalid crime ID.");
    }
} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage(),
        'debug' => isset($debug) ? $debug : null
    ));
}
$db = null;
?>