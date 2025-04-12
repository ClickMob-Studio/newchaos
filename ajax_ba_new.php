<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
//header('Content-type: application/json');
session_start();

$redis = new Redis();
$redis->connect("127.0.1", 6379);

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id'])) {
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['id'] = $data['user_id'];
}

function error($msg, $goldRushCredits = 0, $userBaStats = array())
{
    $response = array();
    $response['success'] = false;
    $response['error'] = $msg;
    $response['gold_rush_credits'] = $goldRushCredits;
    $response['user_ba_stats'] = $userBaStats;
    $response['zombie_rush_credits'] = 0;
    if (isset($userBaStats['zombie_rush_credits'])) {
        $response['zombie_rush_credits'] = $userBaStats['zombie_rush_credits'];
    }


    return $response;
}

function success($msg, $goldRushCredits = 0, $medPackCount = 0, $userBaStats = null)
{
    $response = array();
    $response['success'] = true;
    $response['message'] = $msg;
    $response['gold_rush_credits'] = $goldRushCredits;
    $response['med_pack_count'] = $medPackCount;
    $response['user_ba_stats'] = $userBaStats;
    if (isset($userBaStats['zombie_rush_credits'])) {
        $response['zombie_rush_credits'] = $userBaStats['zombie_rush_credits'];
    }

    return $response;
}

include "classes.php";
include "database/pdo_class.php";

$user_class = new User($_SESSION['id']);
set_last_active($user_class->id);

session_write_close();

if (!isset($_GET['alv'])) {
    echo json_encode(error('Something went wrong.'));
    exit;
}
if ($_GET['alv'] !== 'yes') {
    echo json_encode(error('Something went wrong.'));
    exit;
}

$userPrestigeSkills = getUserPrestigeSkills($user_class);
$userBaStats = getUserBaStats($user_class);

// USE MED PACK
if (isset($_GET['ba_action']) && $_GET['ba_action'] == 'use_med_pack') {
    if ($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital) {
        echo json_encode(error('You already have full HP and are not in the hospital.'));
        exit;
    }

    $totalMedPackCount = check_items(14, $user_class->id);

    if (!$totalMedPackCount) {
        echo json_encode(success('You do not have any Med Packs.'));
        exit;
    }

    $medPackCount = check_items(14, $user_class->id);
    if ($medPackCount > 0) {
        $hosp = floor(($user_class->hospital / 100) * 100);
        $newhosp = $user_class->hospital - $hosp;
        $newhosp = ($newhosp < 0) ? 0 : $newhosp;
        $hp = floor(($user_class->puremaxhp / 4) * 100);
        $hp = $user_class->purehp + $hp;
        $hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
        $db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
        $db->execute([
            $newhosp,
            $hp,
            $user_class->id
        ]);

        Take_Item(14, $user_class->id);

        echo json_encode(array(
            'success' => true,
            'message' => 'You successfully used a Medi Cert 100%.',
            'med_pack_count' => ($totalMedPackCount - 1)
        ));
        exit;
    }
}

// ENERGY REFILL
if (isset($_GET['ba_action']) && $_GET['ba_action'] == 'refill_energy') {
    if (10 > $user_class->points) {
        echo json_encode(error('You do not have enough points to refill your energy.', $userBaStats['gold_rush_credits']));
        exit;
    }
    if ($user_class->energy == $user_class->maxenergy) {
        echo json_encode(error('You already have full energy.', $userBaStats['gold_rush_credits']));
        exit;
    }

    $user_class->energypercent = 100;
    $user_class->energy = $user_class->maxenergy;
    $user_class->points -= 10;
    $db->query("UPDATE grpgusers SET energy = ?, points = points - 10 WHERE id = ?");
    $db->execute(array(
        $user_class->energy,
        $user_class->id
    ));

    echo json_encode(success('You have refilled your energy.'));
    exit;
}

$energyneeded = floor($user_class->maxenergy / 5);
if ($user_class->energy < $energyneeded) {
    refill('e');

    if ($user_class->energy < $energyneeded) {
        echo json_encode(error('You failed to refill your energy in order to search the Back Alley.', $userBaStats['gold_rush_credits']));
        exit;
    } else {
        echo json_encode(error('You successfully refilled your energy, you can continue to search the Back Alley.', $userBaStats['gold_rush_credits']));
        exit;
    }
}

if ($user_class->energy < $energyneeded) {
    echo json_encode(error("You need at least 20% of your energy to explore the back alley!", $userBaStats['gold_rush_credits']));
    exit;

    if ($user_class->ngyref > 0) {
        $user_class->energypercent = 100;
        $user_class->energy = $user_class->maxenergy;
        $user_class->points -= 10;
        $db->query("UPDATE grpgusers SET energy = ?, points = points - 10 WHERE id = ?");
        $db->execute(array(
            $user_class->energy,
            $user_class->id
        ));

        echo json_encode(success('You have refilled your energy.'));
        exit;
    }
}
if ($user_class->jail > 0) {
    echo json_encode(error("You cannot go in the back alley if you are in Jail.", $userBaStats['gold_rush_credits']));
    exit;
}
if ($user_class->hospital > 0) {
    echo json_encode(error("You cannot go in the back alley if you are in Hospital.", $userBaStats['gold_rush_credits']));
    exit;
}

$db->query("UPDATE grpgusers SET energy = energy - " . $energyneeded . " WHERE id = " . $user_class->id);
$db->execute();

mission('ba', 1, $user_class, $db);
$toadd = array('baotd' => 1);
ofthes($user_class->id, $toadd);
gangContest(['backalley' => 1]);

$bpCategory = getBpCategory();
if ($bpCategory) {
    addToBpCategoryUser($bpCategory, $user_class, 'backalley', 1);
}

addToUserCompLeaderboard($user_class->id, 'ba_complete', 1);
addToRelCompLeaderboard($user_class->id, 'ba_complete', 1);

$db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
$db->execute();
$activityContest = $db->fetch_row(true);
if ($activityContest['type'] == 'backalley') {
    addToUserCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
    addToRelCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
}

if ($user_class->gang > 0) {
    addToGangCompLeaderboard($user_class->gang, 'ba_complete', 1);
}

updateGangActiveMission('backalleys', 1);
addToUserOperations($user_class, 'backalleys', 1);
payoutChristmasGift($user_class->id);

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

    if (isset($questSeasonMission['requirements']->backalley)) {
        updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'backalley', 1);
    }
}

// ATTACKERS
$baAttackerNames = array();
$baAttackerNames[] = "Private Niev";
$baAttackerNames[] = "Private First Class Xali";
$baAttackerNames[] = "Sergeant Beck";
$baAttackerNames[] = "Sergeant First Class Walter";
$baAttackerNames[] = "Captain Jericho";
$baAttackerNames[] = "Colonel Pete";

// SCENARIOS
$baAttackerScenarios = array();

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You slowly walk down the alley and reach a dead end. You turn around to walk back and __ANAME__ is blocking your way, ready to fight!";
$baAttackerScenario['success'] = "You beat them up whilst they pleaded for mercy!";
$baAttackerScenario['fail'] = "They really kicked your butt, spiting in your face as they walk off in triumph.";
$baAttackerScenarios[] = $baAttackerScenario;

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You slowly walk down the alley and reach a dead end. You turn around to walk back and __ANAME__ is blocking your way, ready to fight!";
$baAttackerScenario['success'] = "You punch them into the wall and leave them bleeding on the street.";
$baAttackerScenario['fail'] = "They knock you back down on the alleyway, and instead of getting back up, you lay there as they laugh and walk away.";
$baAttackerScenarios[] = $baAttackerScenario;

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You go with a buddy down the alley and __ANAME__ walks in front of you ready to fight! Your buddy runs away, leaving you there to fight them!";
$baAttackerScenario['success'] = "They run away, chasing your friend down as they have a grudge against them. Well that was rather anti-climatic";
$baAttackerScenario['fail'] = "They knock you out with one blow. Your buddy was smart to run!";
$baAttackerScenarios[] = $baAttackerScenario;

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You meet up with __ANAME__ in the alley to buy some contraband, but it turns out that they're wearing a wire!";
$baAttackerScenario['success'] = "You beat them up, tearing the wire apart! You then run away in order to not get caught!";
$baAttackerScenario['fail'] = "They knock you down, leaving you there for dead. Guess you were not as strong as you thought!";
$baAttackerScenarios[] = $baAttackerScenario;

$attacker = $baAttackerNames[mt_rand(0, (count($baAttackerNames) - 1))];
$scenario = $baAttackerScenarios[mt_rand(0, (count($baAttackerScenarios) - 1))];
$scenario['start'] = str_replace('__ANAME__', $attacker, $scenario['start']);


// Outcomes
// - 10% Loose & Go Hosp
// - 20% Loose & Don't Hosp
// - 30% Win Cash & EXP
// - 30% Win Cash & Item
// - 10% Nothing, onto next turn


$totalMedPackCount = check_items(14, $user_class->id);

if ($userBaStats['gold_rush_credits'] > 0) {
    // Outcomes
    // - 20% Win Cash & EXP
    // - 50% Win Cash & Item
    // - 15% Win Points
    // - 15% Win Raid Tokens
    $outcome = mt_rand(1, 100);
    if ($outcome <= 20) {
        // 20% Win Cash & EXP
        $cashWon = mt_rand(10, 5000) * $userBaStats['level'];
        if ($userPrestigeSkills['ba_cash_unlock'] > 0) {
            $cashWon = $cashWon + ($cashWon / 100 * 10);
            $cashWon = ceil($cashWon);
        }
        $expWon = round(($user_class->maxexp / 1000) * mt_rand(1, 3));
        if ($user_class->level < 100) {
            $expWon = round(($user_class->maxexp / 100) * mt_rand(1, 8));
        }
        $expWon = round($expWon / mt_rand(2, 4));
        $expWon = $expWon + (($expWon / 100) * (6 * $userBaStats['level']));

        //$expWon = $expWon / 2;
        $baExpWon = mt_rand(5, 25);

        $db->query("UPDATE `grpgusers` SET `money` = `money` + " . $cashWon . ", `exp` = `exp` + " . $expWon . ", `backalleywins` = `backalleywins` + 1  WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `gold_rush_credits` = `gold_rush_credits` - 1, `turns` = `turns` + 1, `wins` = `wins` + 1, `cash_gained` = `cash_gained` +  " . $cashWon . ", `exp_gained` = `exp_gained` + " . $expWon . " WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        addUserBaStatExp($userBaStats, $baExpWon, $user_class);
        $userBaStats['gold_rush_credits'] = $userBaStats['gold_rush_credits'] - 1;
        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['wins'] = $userBaStats['wins'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You won $' . number_format($cashWon, 0) . ' & ' . number_format($expWon, 0) . ' EXP!</span>';

        $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    } else if ($outcome <= 70) {
        // 40% Win Cash & Item
        $cashWon = mt_rand(10, 5000) * $userBaStats['level'];
        if ($userPrestigeSkills['ba_cash_unlock'] > 0) {
            $cashWon = $cashWon + ($cashWon / 100 * 10);
            $cashWon = ceil($cashWon);
        }
        $baExpWon = mt_rand(5, 25);

        $itemIds = array();
        //$itemIds[10] = 13; // Med Cert 75
        $itemIds[20] = 14; // Med Cert 100
        $itemIds[45] = 10; // Double Exp [1Hour]
        $itemIds[60] = 42; // Mystery Box
        $itemIds[80] = 194; // Raid Speedup
        $itemIds[100] = 251; // Raid Pass

        $itemChance = mt_rand(1, 100);
        foreach ($itemIds as $key => $itemId) {
            if ($itemChance <= $key) {
                $itemWonId = $itemId;

                if ($itemWonId == 14) {
                    $totalMedPackCount = $totalMedPackCount + 1;
                    $itemName = 'Medi Cert 100%';
                    Give_Item($itemWonId, $user_class->id);
                } else {
                    Give_Item($itemWonId, $user_class->id);
                    $db->query("SELECT `itemname` FROM `items` WHERE id = " . $itemWonId);
                    $db->execute();
                    $itemName = $db->fetch_single();
                }

                break;
            }
        }

        $db->query("UPDATE `grpgusers` SET `money` = `money` + " . $cashWon . ", `backalleywins` = `backalleywins` + 1 WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `gold_rush_credits` = `gold_rush_credits` - 1, `turns` = `turns` + 1, `wins` = `wins` + 1, `items_gained` = `items_gained` + 1, `cash_gained` = `cash_gained` + " . $cashWon . "  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        addUserBaStatExp($userBaStats, $baExpWon, $user_class);
        $userBaStats['gold_rush_credits'] = $userBaStats['gold_rush_credits'] - 1;
        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['wins'] = $userBaStats['wins'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You won $' . number_format($cashWon, 0) . ' & found 1 x ' . $itemName . '!</span>';

        $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    } else if ($outcome <= 85) {
        // 15% Win Points
        $pointsWon = mt_rand(5, 45) * $userBaStats['level'];

        $baExpWon = mt_rand(5, 25);

        $db->query("UPDATE `grpgusers` SET `points` = `points` + " . $pointsWon . ", `backalleywins` = `backalleywins` + 1 WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `gold_rush_credits` = `gold_rush_credits` - 1, `turns` = `turns` + 1, `wins` = `wins` + 1, `points_gained` = `points_gained` + " . $pointsWon . "  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        addUserBaStatExp($userBaStats, $baExpWon, $user_class);
        $userBaStats['gold_rush_credits'] = $userBaStats['gold_rush_credits'] - 1;
        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['wins'] = $userBaStats['wins'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You won ' . number_format($pointsWon, 0) . ' points!</span>';

        $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    } else {
        // 15% Win Raid Tokens
        $raidTokensWon = mt_rand(1, 3) * $userBaStats['level'];
        $baExpWon = mt_rand(5, 25);

        $db->query("UPDATE `grpgusers` SET `raidtokens` = `raidtokens` + " . $raidTokensWon . ", `backalleywins` = `backalleywins` + 1 WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `gold_rush_credits` = `gold_rush_credits` - 1, `turns` = `turns` + 1, `wins` = `wins` + 1  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        addUserBaStatExp($userBaStats, $baExpWon, $user_class);
        $userBaStats['gold_rush_credits'] = $userBaStats['gold_rush_credits'] - 1;
        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['wins'] = $userBaStats['wins'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You won ' . number_format($raidTokensWon, 0) . ' raid tokens!</span>';

        $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    }
} else {
    $goldRushChance = mt_rand(1, 12500);
    if ($userPrestigeSkills['ba_gold_rush_unlock'] > 0) {
        $goldRushChance = mt_rand(1, 9000);
    }
    if ($goldRushChance == 2) {
        $db->query("UPDATE user_ba_stats SET gold_rush_credits = gold_rush_credits + 15 WHERE user_id = " . $user_class->id);
        $db->execute();

        $fullResponse = 'You walk down the back alley and feel a surge of power! You enter Gold Rush Mode!';

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount));
        exit;
    }

    // Outcomes
    // - 10% Loose & Go Hosp
    // - 20% Loose & Don't Hosp
    // - 30% Win Cash & EXP
    // - 10% Win Cash & Item
    // - 10% Nothing, onto next turn
    $outcome = mt_rand(1, 100);
    if ($outcome <= 30) {
        // 10% Loose & Go Hosp
        $hosp = 120;
        $db->query("UPDATE `grpgusers` SET `hwho` = '{$attacker}', `hhow` = 'backalley', `hospital` = '" . $hosp . "' WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `turns` = `turns` + 1, `losses` = `losses` + 1  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['losses'] = $userBaStats['losses'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: red; font-weight:bold;">' . $scenario['fail'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: red;">You will need to spend some time in the hospital!</span>';

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    } else if ($outcome <= 55) {
        // 30% Win Cash & EXP
        $cashWon = mt_rand(10, 1000) * $userBaStats['level'];
        if ($userPrestigeSkills['ba_cash_unlock'] > 0) {
            $cashWon = $cashWon + ($cashWon / 100 * 10);
            $cashWon = ceil($cashWon);
        }
        $expWon = round(($user_class->maxexp / 1000) * mt_rand(1, 2));
        if ($user_class->level < 100) {
            $expWon = round(($user_class->maxexp / 100) * mt_rand(1, 4));
        }
        $expWon = round($expWon / mt_rand(2, 5));
        $expWon = $expWon + (($expWon / 100) * (5 * $userBaStats['level']));
        if ($expWon < 100) {
            $expWon = 100;
        }
        $baExpWon = mt_rand(1, 15);

        $db->query("UPDATE `grpgusers` SET `money` = `money` + " . $cashWon . ", `exp` = `exp` + " . $expWon . ", `backalleywins` = `backalleywins` + 1  WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `turns` = `turns` + 1, `wins` = `wins` + 1, `cash_gained` = `cash_gained` + " . $cashWon . ", `exp_gained` = `exp_gained` + " . $expWon . "  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        addUserBaStatExp($userBaStats, $baExpWon, $user_class);
        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['wins'] = $userBaStats['wins'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You won $' . number_format($cashWon, 0) . ' & ' . number_format($expWon, 0) . ' EXP!</span>';

        $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    } else if ($outcome <= 85) {
        // 30% Win Cash & Item
        $cashWon = mt_rand(100, 1500) * $userBaStats['level'];
        if ($userPrestigeSkills['ba_cash_unlock'] > 0) {
            $cashWon = $cashWon + ($cashWon / 100 * 10);
            $cashWon = ceil($cashWon);
        }
        $baExpWon = mt_rand(1, 15);

        $userItemDropLog = getUserItemDropLog($user_class->id);

        $itemIds = array();
        $itemIds[35] = 1; // Bowie Knife
        $itemIds[70] = 3; // Army Boots
        $itemIds[99] = 14; // Med Cert 100
        $itemIds[100] = 253; // Gold Rush Token

        $itemChance = mt_rand(1, 100);
        foreach ($itemIds as $key => $itemId) {
            if ($itemChance <= $key) {
                $itemWonId = $itemId;

                $totalMedPackCount = $totalMedPackCount + 1;

                Give_Item($itemWonId, $user_class->id);

                if ($itemWonId == 14) {
                    $itemName = 'Medi Cert 100%';
                } else {
                    $db->query("SELECT `itemname` FROM `items` WHERE id = " . $itemWonId);
                    $db->execute();
                    $itemName = $db->fetch_single();
                }

                break;
            }
        }

        $db->query("UPDATE `grpgusers` SET `money` = `money` + " . $cashWon . ", `backalleywins` = `backalleywins` + 1 WHERE `id` = '" . $user_class->id . "'");
        $db->execute();

        $db->query("UPDATE `user_ba_stats` SET `turns` = `turns` + 1, `wins` = `wins` + 1, `items_gained` = `items_gained` + 1, `cash_gained` = `cash_gained` + " . $cashWon . "  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        addUserBaStatExp($userBaStats, $baExpWon, $user_class);
        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['wins'] = $userBaStats['wins'] + 1;

        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You won $' . number_format($cashWon, 0) . ' & found 1 x ' . $itemName . '!</span>';

        $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    } else if ($outcome <= 100) {
        if ($userPrestigeSkills['ba_raidtokens_unlock'] < 1) {
            $rtorpChance = 100;
        } else {
            $rtorpChance = mt_rand(1, 100);
        }
        if ($rtorpChance > 25) {
            $pointsWon = mt_rand(2, 4) * $userBaStats['level'];
            $baExpWon = mt_rand(1, 15);

            $db->query("UPDATE `grpgusers` SET `points` = `points` + " . $pointsWon . ", `backalleywins` = `backalleywins` + 1 WHERE `id` = '" . $user_class->id . "'");
            $db->execute();

            $db->query("UPDATE `user_ba_stats` SET `turns` = `turns` + 1, `wins` = `wins` + 1, `points_gained` = `points_gained` + " . $pointsWon . "  WHERE `user_id` = '" . $user_class->id . "'");
            $db->execute();

            addUserBaStatExp($userBaStats, $baExpWon, $user_class);
            $userBaStats['turns'] = $userBaStats['turns'] + 1;
            $userBaStats['wins'] = $userBaStats['wins'] + 1;

            $fullResponse = $scenario['start'];
            $fullResponse .= '<br />';
            $fullResponse .= '<br />';
            $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
            $fullResponse .= '<br />';
            $fullResponse .= '<span style="font-weight: bold; color: green;">You won ' . number_format($pointsWon, 0) . ' points!</span>';

            $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

            echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
            exit;
        } else {
            $raidTokensWon = mt_rand(1, 2) * $userBaStats['level'];
            $baExpWon = mt_rand(1, 15);

            $db->query("UPDATE `grpgusers` SET `raidtokens` = `raidtokens` + " . $raidTokensWon . ", `backalleywins` = `backalleywins` + 1 WHERE `id` = '" . $user_class->id . "'");
            $db->execute();

            addUserBaStatExp($userBaStats, $baExpWon, $user_class);
            $userBaStats['turns'] = $userBaStats['turns'] + 1;
            $userBaStats['wins'] = $userBaStats['wins'] + 1;

            $fullResponse = $scenario['start'];
            $fullResponse .= '<br />';
            $fullResponse .= '<br />';
            $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';
            $fullResponse .= '<br />';
            $fullResponse .= '<span style="font-weight: bold; color: green;">You won ' . number_format($raidTokensWon, 0) . ' raid tokens!</span>';

            $fullResponse = check_for_easter_egg($fullResponse, $user_class, $userBaStats['gold_rush_credits']);

            echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
            exit;
        }
    } else {
        $fullResponse = $scenario['start'];
        $fullResponse .= '<br />';
        $fullResponse .= '<br />';
        $fullResponse .= '<span style="color: red; font-weight:bold;">' . $scenario['fail'] . '</span>';
        $fullResponse .= '<br />';
        $fullResponse .= '<strong>Luckily, you won\'t need to spend any time in the hospital!</strong>';

        $db->query("UPDATE `user_ba_stats` SET `turns` = `turns` + 1, `losses` = `losses` + 1  WHERE `user_id` = '" . $user_class->id . "'");
        $db->execute();

        $userBaStats['turns'] = $userBaStats['turns'] + 1;
        $userBaStats['losses'] = $userBaStats['losses'] + 1;

        echo json_encode(success($fullResponse, $userBaStats['gold_rush_credits'], $totalMedPackCount, $userBaStats));
        exit;
    }

}

function check_for_easter_egg($fullResponse, $user_class, $goldRushEnabled = 0)
{
    global $db, $redis;


    $egg = did_find_easter_egg($user_class);
    if (!$egg && $goldRushEnabled) {
        $egg = did_find_easter_egg($user_class);
    }

    if ($egg > 0) {
        $item = $redis->get('item_' . $egg);
        if (!$item) {
            $db->query("SELECT * FROM items WHERE id = " . $egg);
            $db->execute();
            $item = $db->fetch_row(true);
            $redis->setEx("item_" . $egg, 3600, json_encode($item));
        } else {
            $item = json_decode($item, true);
        }

        $fullResponse .= '<br /><br />';
        $fullResponse .= '<span style="font-weight: bold; color: green;">You also found 1x ' . item_popup($item['itemname'], $egg, '#ff00b1') . '!</span>';
    }

    return $fullResponse;
}

function did_find_easter_egg($user_class)
{
    // if ($user_class->admin < 1) {
    //     return 0;
    // }

    $probability = mt_rand(1, 400);
    if ($probability <= 1) {
        // Ultra rare egg 0,5% chance
        Give_Item(338, $user_class->id);
        return 338;
    } else if ($probability <= 3) {
        // Rare egg 1% chance
        Give_Item(337, $user_class->id);
        return 337;
    } else if ($probability <= 14) {
        // Common egg 2,5% chance
        Give_Item(336, $user_class->id);
        return 336;
    }

    return 0;
}