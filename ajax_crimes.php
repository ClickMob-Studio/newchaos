<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'includes/functions.php';

start_session_guarded();

function shorthandNumber($number)
{
    if ($number >= 1000000000) {
        return round($number / 1000000000, 2) . 'B';
    } elseif ($number >= 1000000) {
        return round($number / 1000000, 2) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'k';
    }
    return number_format($number);
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

$user_class = null;
if (isset($_GET['au_user_or']) && (int) $_GET['au_user_or']) {
    $user_class = new User((int) $_GET['au_user_or']);
} elseif (isset($_SESSION['id'])) {
    $user_class = new User($_SESSION['id']);
}

session_write_close();

if (!$user_class) {
    echo json_encode(['error' => 'User not found']);
    die();
}

$db->startTrans();

try {
    set_last_active($user_class->id);

    if ($user_class->jail || $user_class->hospital) {
        echo json_encode(['text' => "You are not able to do crimes at the moment."]);
        $db->rollBack();
        die();
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($_POST['id']) ? $_POST['id'] : (isset($input['id']) ? $input['id'] : null);
    $crime_multiplier = isset($_POST['cm']) ? (int) $_POST['cm'] : 1;

    if ($id) {
        $crime_key = 'crimes.' . $id;

        $db->query("SELECT `id`, `nerve`, `name` FROM crimes WHERE id = ? LIMIT 1");
        $db->execute(array($id));
        $row = $db->fetch_row(true);

        if (empty($row)) {
            echo json_encode(['error' => 'refresh']);
            $db->rollBack();
            die();
        }

        $nerve = $row['nerve'] * $crime_multiplier;
        $name = $row['name'];

        if ($user_class->nerve < $nerve) {
            echo json_encode(['error' => 'refresh', 'text' => "You don't have enough nerve for that crime."]);
            $db->rollBack();
            die();
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

        $crimeCount = $crimeRankResult ? (int) $crimeRankResult['count'] : 0;

        $star_level = 0;
        if ($crimeCount >= 10000 && $crimeCount < 100000) {
            $star_level = 1;
        } elseif ($crimeCount >= 100000 && $crimeCount < 1000000) {
            $star_level = 2;
        } elseif ($crimeCount >= 1000000 && $crimeCount < 5000000) {
            $star_level = 3;
        } elseif ($crimeCount >= 5000000 && $crimeCount < 15000000) {
            $star_level = 4;
        } elseif ($crimeCount >= 15000000) {
            $star_level = 5;
        }

        $bonus_exp_per_star_level = 0.10;
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

        if ($tempItemUse['gang_double_exp_time'] > time() || $bonus_row['Time'] > 0 || time() < 1673827199) {
            $exp *= 2;
            $chance = 100;
        }

        $prepaid = false;
        if ($nerve > $user_class->nerve && $user_class->nerref == 2) {
            $nerveneeded = $nerve - $user_class->nerve;
            if ($nerveneeded < $user_class->maxnerve) {
                $nerveneeded = $user_class->maxnerve;
            }

            $cost = floor($nerveneeded / 10);
            if ($cost < 10) {
                $cost = 10;
            }

            if ($cost > $user_class->points || $user_class->points < 10) {
                echo json_encode(['error' => 'not enough points for refill']);
                $db->rollBack();
                die();
            }

            $tempItemUse = getItemTempUse($user_class->id);
            $now = time();
            if ($tempItemUse['nerve_vial_time'] > $now) {
                $cost = ceil($cost / 2);
            }

            $user_class->nerve = $user_class->maxnerve;
            $user_class->points -= $cost;
            $db->query("UPDATE grpgusers SET points = points - ?, nerve = ? WHERE id = ?");
            $db->execute(array($cost, $user_class->maxnerve, $user_class->id));
            $prepaid = true;
        } else if ($nerve > $user_class->nerve) {
            echo json_encode(['error' => 'refresh', 'text' => "You don't have enough nerve for that crime."]);
            $db->rollBack();
            die();
        }

        if ($user_class->nerve >= $nerve || $prepaid) {
            if ($chance < 5) {
                $user_class->nerve -= $nerve;
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
                $db->execute(array($nerve, $user_class->id));
                $db->endTrans();
                echo json_encode([
                    'text' => $ftext . ".",
                    'stats' => [
                        'points' => number_format($user_class->points),
                        'mb_points' => shorthandNumber($user_class->points),
                        'money' => number_format($user_class->money),
                        'mb_money' => shorthandNumber($user_class->money),
                        'level' => number_format($user_class->level)
                    ],
                    'bars' => [
                        'energy' => ['percent' => $user_class->energypercent, 'title' => $user_class->formattedenergy],
                        'nerve' => ['percent' => $user_class->nervepercent, 'title' => $user_class->formattednerve],
                        'awake' => ['percent' => $user_class->awakepercent, 'title' => $user_class->awakepercent],
                        'exp' => ['percent' => $user_class->exppercent, 'title' => $user_class->exppercent]
                    ]
                ]);
                die();
            } elseif ($chance == 6) {
                $user_class->nerve -= $nerve;
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + 1, jail = 300 WHERE id = ?");
                $db->execute(array($nerve, $user_class->id));
                $db->endTrans();
                echo json_encode(['text' => 'You were hauled off to jail for 5 minutes']);
                die();
            } else {
                $bbnerve = $prepaid ? $nerve : ($nerve / $user_class->level);

                if ($row['nerve'] >= 50) {
                    $which = "crimes50";
                } elseif ($row['nerve'] >= 25) {
                    $which = "crimes25";
                } elseif ($row['nerve'] >= 10) {
                    $which = "crimes10";
                } elseif ($row['nerve'] >= 5) {
                    $which = "crimes5";
                } else {
                    $which = "crimes1";
                }
                newmissions($which, $crime_multiplier);
                mission('c', $crime_multiplier);
                gangContest(['crimes' => $crime_multiplier, 'exp' => $exp]);
                bloodbath('crimes', $user_class->id, $bbnerve / $user_class->level, $crime_multiplier);

                $userPrestigeSkills = getUserPrestigeSkills($user_class);
                if ($userPrestigeSkills['crime_cash_unlock'] > 0) {
                    $money += ($money / 100 * 10);
                }
                if ($userPrestigeSkills['crime_cash_boost_level'] > 0) {
                    $money += ($money / 100 * (2 * $userPrestigeSkills['crime_cash_boost_level']));
                }

                $gtax = 0;
                if ($user_class->gang != 0) {
                    $db->query("SELECT `tax` FROM `gangs` WHERE `id` = ?");
                    $db->execute(array($user_class->gang));
                    $gangTax = $db->fetch_row(true);

                    if (isset($gangTax['tax']) && $gangTax['tax'] > 0) {
                        $gtax = $money * ($gangTax['tax'] / 100);
                        gangContest(['tax' => $gtax]);
                    }
                }
                $money -= $gtax;
                $totaltax = $gtax;

                $maxnervePercCheck = $row['nerve'] / $user_class->maxnerve * 100;
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
                $db->execute(array($exp, $exp, $money, $money, $nerve, $exp, $exp, $totaltax, $user_class->id));

                $db->query("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
                $db->execute(array($gtax, $user_class->gang));

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

                $db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = 'no' LIMIT 1");
                $db->execute(array($user_class->id));
                $activeMission = $db->fetch_row(true);

                $mt = $activeMission ? "Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}" : "";

                $text = ($gtax > 0) ? "$stext. You received $exp exp and $$money.(Gang Tax: $$gtax)" : "$stext. You received $exp exp and $$money";

                echo json_encode([
                    'text' => $text,
                    'stats' => [
                        'points' => number_format($user_class->points),
                        'mb_points' => shorthandNumber($user_class->points),
                        'money' => number_format($user_class->money),
                        'mb_money' => shorthandNumber($user_class->money),
                        'level' => number_format($user_class->level),
                        'mission' => $mt
                    ],
                    'bars' => [
                        'energy' => ['percent' => $user_class->energypercent, 'title' => $user_class->formattedenergy],
                        'nerve' => ['percent' => $user_class->nervepercent, 'title' => $user_class->formattednerve],
                        'awake' => ['percent' => $user_class->awakepercent, 'title' => $user_class->awakepercent],
                        'exp' => ['percent' => $user_class->exppercent, 'title' => $user_class->exppercent]
                    ]
                ]);
                die();
            }
        } else {
            echo json_encode(['error' => 'refresh', 'text' => "You don't have enough nerve for that crime."]);
        }
    }
    $db->endTrans();
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['error' => 'Exception', 'message' => $e->getMessage()]);
    die();
}

$db = null;
?>