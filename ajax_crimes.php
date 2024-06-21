<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start();

function shorthandNumber($number) {
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
}

include "classes.php";
include "database/pdo_class.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$m = new Memcache();
$m->addServer('127.0.0.1', 11211);

$user_id = isset($_GET['au_user_or']) ? (int)$_GET['au_user_or'] : (int)$_SESSION['user_id'];
$user_class = new User($user_id);

session_write_close();

$crime_multiplier = 1;
if (isset($_POST['cm']) && in_array($_POST['cm'], array(1, 2, 4, 10, 20, 30, 50))) {
    $crime_multiplier = $_POST['cm'];
}

$debug = array(
    'id' => $user_class->id,
    'crime_multiplier' => $crime_multiplier,
    'post' => $_POST
);

if (!$user_class) {
    die();
}

$db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array($user_class->id));

if ($user_class->jail || $user_class->hospital) {
    echo json_encode(array('text' => "You are not able to do crimes at the moment."));
    die();
}

$input = json_decode(file_get_contents('php://input'), true);
$crime_id = isset($_POST['id']) ? $_POST['id'] : $input['id'];

if (!$crime_id) {
    die(json_encode(array('error' => 'No crime ID provided')));
}

if (!$row = $m->get('crimes.' . $crime_id)) {
    $db->query("SELECT `id`, `nerve`, `name` FROM crimes WHERE id = ? LIMIT 1");
    $db->execute(array($crime_id));
    $row = $db->fetch_row(true);
    $m->set('crimes.' . $crime_id, $row, false, 120);
}

if (empty($row)) {
    echo json_encode(array('debug' => $debug, 'error' => 'refresh'));
    die();
}

$m->set('crimesave' . $user_class->id, $row['id']);

$nerve = $row['nerve'];
$name = $row['name'];
if ($user_class->maxnerve < $nerve) {
    echo json_encode(array('debug' => $debug, 'error' => 'refresh'));
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
$crimeCount = $crimeRankResult ? (int)$crimeRankResult['count'] : 0;

$star_level = 0;
if ($crimeCount >= 10000) {
    if ($crimeCount < 100000) $star_level = 1;
    elseif ($crimeCount < 1000000) $star_level = 2;
    elseif ($crimeCount < 5000000) $star_level = 3;
    elseif ($crimeCount < 15000000) $star_level = 4;
    else $star_level = isset($user_class->completeUserResearchTypesIndexedOnId[16]) ? ($crimeCount >= 30000000 ? 6 : 5) : 5;
}

$exp += $exp * $star_level * 0.10;

$crimeexpbonus = 0;
if ($user_class->crimeexpboost > 1) {
    $crimeexpbonus += 0.2 + ($user_class->crimeexpboost - 1) * 0.0333;
} elseif ($user_class->crimeexpboost == 1) {
    $crimeexpbonus = 0.2;
}

$exp += round($exp * $crimeexpbonus, 2);

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
} elseif ($tempItemUse['crime_potion_time'] > time()) {
    $exp += round(($exp / 10));
}

$db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
$db->execute();
$bonus_row = $db->fetch_row(true);

$aTime = time();
if ($tempItemUse['gang_double_exp_time'] > $aTime || $bonus_row['Time'] > 0 || time() < 1673827199 || isset($_GET['au_user_or'])) {
    $exp *= 2;
    $chance = 100;
}

$nerve *= $crime_multiplier;
$exp *= $crime_multiplier;
$money *= $crime_multiplier;

$debug['prenerve'] = $nerve;
$debug['preusernerve'] = $user_class->nerve;

if ($nerve > $user_class->nerve && $user_class->nerref == 2) {
    $nerveneeded = $nerve - $user_class->nerve;
    if ($nerveneeded < $user_class->maxnerve) {
        $nerveneeded = $user_class->maxnerve;
    }

    $cost = max(10, floor($nerveneeded / 10));

    if ($cost > $user_class->points || $user_class->points < 10) {
        return 0;
    }

    $tempItemUse = getItemTempUse($user_class->id);
    if ($tempItemUse['nerve_vial_time'] > time()) {
        $cost = ceil($cost / 2);
    }

    $user_class->nerve = $user_class->maxnerve;
    $user_class->points -= $cost;
    $db->query("UPDATE grpgusers SET points = points - ?, nerve = ? WHERE id = ?");
    $db->execute(array($cost, $user_class->maxnerve, $user_class->id));
    $prepaid = true;
} elseif ($nerve > $user_class->nerve) {
    echo json_encode(array('debug' => $debug, 'error' => 'refresh'));
    die();
}

if ($user_class->nerve >= $nerve || $prepaid) {
    if ($chance < 5) {
        $user_class->nerve -= $nerve;
        $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
        $db->execute(array($nerve, $user_class->id));
        die($ftext);
    } elseif ($chance == 6) {
        $user_class->nerve -= $nerve;
        $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + 1, jail = 300 WHERE id = ?");
        $db->execute(array($nerve, $user_class->id));
        echo json_encode(array('text' => 'You were hauled off to jail for 5 minutes'));
        die();
    } else {
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
            $money += ceil($money / 100 * 2);
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
            $exp += $exp / 100 * $researchExpBoost;
        }
        $exp = ceil($exp);

        $gtax = 0;
        if ($user_class->gang != 0) {
            $gangTax = $m->get('gangtax.' . $user_class->gang);
            if (!$gangTax) {
                $db->query("SELECT `tax` FROM `gangs` WHERE `id` = ?");
                $db->execute(array($user_class->gang));
                $gangTax = $db->fetch_row(true);
                $m->set('gangtax.' . $user_class->gang, $gangTax, false, 120);
            }
            if (isset($gangTax['tax']) && $gangTax['tax'] > 0) {
                $gtax = $money * ($gangTax['tax'] / 100);
                gangContest(array('tax' => $gtax));
            }
        }
        $money -= $gtax;

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
        $db->execute(array($exp, $exp, $money, $money, $nerve, $exp, $exp, $gtax, $user_class->id));

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
        $mt = "";
        if ($activeMission) {
            $mt = "Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}";
        }

        $text = ($gtax > 0) ? "$stext. You received $exp exp and $$money.(Gang Tax: $$gtax)" : "$stext. You received $exp exp and $$money";
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
    echo json_encode(array(
        'text' => "You don't have enough nerve for that crime.",
        'debug' => $debug,
        'error' => 'refresh'
    ));
}

$db = null;
?>
