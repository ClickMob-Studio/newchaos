<?php

require_once 'includes/functions.php';

start_session_guarded();

function get_respect_for_level($level_diff)
{
    $range = range(-100, 100);
    $rtn = array();
    $respect_payout = 0;
    for ($i = 0; $i <= count($range) - 1; $i++) {
        $rtn[$range[$i]] = ($respect_payout < .01) ? .01 : $respect_payout;
        $respect_payout += .005;
    }
    return $rtn[max(-100, min(100, $level_diff))];
}
function get_user_streak($userid)
{
    global $db;

    $db->query("SELECT streak FROM user_kill_streaks WHERE userid = ?");
    $db->execute(array(
        $userid
    ));
    $user_streak = $db->fetch_row(true);
    return $user_streak['streak'];
}
function add_user_streak($userid)
{
    global $db;
    $db->query("INSERT INTO user_kill_streaks (userid, streak) VALUES (?, 1) ON DUPLICATE KEY UPDATE streak = streak + 1");
    $db->execute([$userid]);
}
function kill_user_streak($userid)
{
    global $db;
    $db->query("DELETE FROM user_kill_streaks WHERE userid = ?");
    $db->execute([$userid]);
}
function print_pre($print)
{
    //    echo "<pre>";
    //    print_r($print);
    //    echo "<pre>";
}
function fetchGangUpgradeLevel($gangId)
{
    global $db;

    if (!$gangId) {
        // If no gang ID is provided, return 0
        return 0;
    }

    $db->query("SELECT upgrade7 FROM gangs WHERE id = " . $gangId);
    $db->execute();
    $result = $db->fetch_row(true);

    if ($result['upgrade7']) {
        return $result['upgrade7'];
    }

    return 0;
}


function error($msg)
{
    $response = array();
    $response['success'] = false;
    $response['error'] = $msg;

    return $response;
}

function success($msg)
{
    $response = array();
    $response['success'] = true;
    $response['message'] = $msg;

    return $response;
}

include_once "classes.php";
include_once "database/pdo_class.php";

$user_class = new User($_SESSION['id']);
session_write_close();

$response = array();

if (!isset($_GET['alv'])) {
    echo json_encode(error('Something went wrong.'));
    exit;
}
if ($_GET['alv'] !== 'yes') {
    echo json_encode(error('Something went wrong.'));
    exit;
}

$modifier = ($user_class->rmdays > 0) ? 0.2 : 0.25;

$energyneeded = floor($user_class->maxenergy * $modifier);

$tempItemUse = getItemTempUse($user_class->id);
if ($tempItemUse['love_potions_time'] > time()) {
    $energyneeded = 0;

    removeItemTempUse($user_class->id, 'love_potions_time', 1);
}

if (($user_class->energy <= $energyneeded || $user_class->energypercent <= 0) && $user_class->ngyref == 2) {
    manual_refill('e');
}

$attack_person = new User($_GET['attack']);

$throneAttack = false;
if (isset($_GET['throne']) && $_GET['throne'] == 'attack') {
    if ($user_class->gender === 'Male' && $attack_person->king < 1) {
        $throneAttack = true;
    }

    if ($user_class->gender === 'Female' && $attack_person->queen < 1) {
        $throneAttack = true;
    }
}
if (isset($_GET['thrones']) && $_GET['thrones'] == 'attack') {
    $throneAttack = true;
}

if ($user_class->id != 0) {
    $error = "";
    $error = ($user_class->energy < $energyneeded) ? "You need 25% energy if you want to attack someone." : $error;
    $error = ($user_class->hppercent < 25) ? "You need to have over 25% HP to attack someone." : $error;
    $error = ($user_class->hospital > 0) ? "You can't attack someone if you are in hospital." : $error;
    $error = ($_GET['attack'] == "") ? "You didn't choose someone to attack." : $error;
    $error = ($_GET['attack'] == $user_class->id) ? "You can't attack yourself." : $error;


    $currentTime = time();
    $oneHourAgo = $currentTime - 3600; // 3600 seconds = 1 hour

    // Check if user's HP is below 25%
    if ($user_class->hppercent < 25) {
        $error = "You need to have over 25% HP to attack someone.";
    }
    // Combine checks for user's attack protection and attack_person's last active time
    else if ($user_class->aprotection > $currentTime && $attack_person->lastactive > $oneHourAgo) {
        $error = "You cannot attack due to you having an active protection or the target's recent activity.";
    }
    // If none of the conditions are met, then no error
    else {
        $error = ""; // No error, ready to proceed
    }

    if (!empty($error)) {
        echo json_encode(error($error));
        exit;
    }

    if ($user_class->aprotection != 0) {
        $error = "";
        $error = ($user_class->energy < $energyneeded) ? "You need 25% energy if you want to attack someone." : $error;
        //$error = ($user_class->energypercent <= 0) ? "You need 25% energy if you want to attack someone." : $error;
        $error = ($user_class->hppercent < 25) ? "You need to have over 25% HP to attack someone." : $error;
        $error = ($user_class->hospital > 0) ? "You can't attack someone if you are in hospital." : $error;
        $error = ($_GET['attack'] == "") ? "You didn't choose someone to attack." : $error;
        $error = ($_GET['attack'] == $user_class->id) ? "You can't attack yourself." : $error;

    }
    if ($attack_person->id >= 00 and $attack_person->id <= 00) {
        $attack_person->level = $user_class->level + $attack_person->id - 410;
        $attack_person->hp = $attack_person->purehp = $attack_person->maxhp = $attack_person->puremaxhp = $attack_person->level * 50;
        $attack_person->hppercent = 100;
        $attack_person->formattedhp = $attack_person->hp . " / " . $attack_person->maxhp . " [100%]";
        $attack_person->city = $user_class->city;
        $attack_person->jail = 0;
        $attack_person->moddedstrength = rand(750 * (($attack_person->id - 404) / 10), 2200 * (($attack_person->id - 404) / 10));
        $attack_person->moddeddefense = rand(750 * (($attack_person->id - 404) / 10), 2200 * (($attack_person->id - 404) / 10));
        $attack_person->moddedspeed = rand(750 * (($attack_person->id - 404) / 10), 2200 * (($attack_person->id - 404) / 10));

        $user_class->moddedstrength = rand(1000, 5000);
        $user_class->moddeddefense = rand(1000, 5000);
        $user_class->moddedspeed = rand(1000, 5000);
    }
    $error = ($user_class->hospital > 0) ? "You can't attack someone if you are in hospital." : $error;

    $error = ($user_class->jail > 0 && $attack_person->jail == 0) ? "You can't attack someone if you are in prison." : $error;
    $error = ($attack_person->jail > 0 && $user_class->jail == 0) ? "You can't attack someone that is in prison." : $error;
    $error = ($attack_person->city != $user_class->city && $user_class->id != 0) ? "You must be in the same city as the person you're attacking!" : $error;
    $error = ($attack_person->username == "") ? "That person doesn't exist." : $error;
    $error = ($attack_person->hospital > 0 && !$throneAttack) ? "You can't attack someone that is in hospital." : $error;

    $error = ($attack_person->hppercent < 25) ? "They need over 25% HP to be attacked." : $error;
    $error = ($attack_person->admin == 1 && $user_class->admin < 1) ? "Im sorry, You cannot attack the owner" : $error;
    $error = ($attack_person->id == $user_class->id) ? "Why would you want to attack yourself?" : $error;
    $error = ($attack_person->aprotection > time() && !$throneAttack) ? "This Mobster is under Attack Protection." : $error;

    $db->query("SELECT COUNT(*) FROM attackladder WHERE `user` = ?");
    $db->execute([$attack_person->id]);
    $attackLadder = $db->fetch_row();

    if (count($attackLadder) == 0) {
        $error = ($attack_person->aprotection > time()) ? "This Mobster is under Attack Protection." : $error;
        $error = ($user_class->gang == $attack_person->gang && $user_class->gang > 0 && !$throneAttack) ? "You can't attack someone in your gang." : $error;
    }

    if (!empty($error)) {
        echo json_encode(error($error));
        exit;
    }

    $agreed = (isset($_GET['agreed'])) ? $_GET['agreed'] : 0;
}

$yourhp = $user_class->hp;
$theirhp = $attack_person->hp;

if ($user_class->jail) {
    $user_class->moddeddefense = $user_class->defense;
    $user_class->moddedspeed = $user_class->speed;
    $user_class->moddedstrength = $user_class->strength;
    $attack_person->moddeddefense = $attack_person->defense;
    $attack_person->moddedspeed = $attack_person->speed;
    $attack_person->moddedstrength = $attack_person->strength;
}

// Right before this comment is where you should apply the gang upgrade logic
// Fetch the gang upgrade levels
$userGangUpgradeLevel = fetchGangUpgradeLevel($user_class->gang);
$attackGangUpgradeLevel = fetchGangUpgradeLevel($attack_person->gang);

// Calculate the stat bonus multipliers
$userStatBonusMultiplier = 1 + ($userGangUpgradeLevel * 0.10);
$attackStatBonusMultiplier = 1 + ($attackGangUpgradeLevel * 0.10);

// Apply the stat bonus multipliers to the user_class and attack_person
$user_class->moddedstrength = round($user_class->moddedstrength * $userStatBonusMultiplier);
$user_class->moddeddefense = round($user_class->moddeddefense * $userStatBonusMultiplier);
$user_class->moddedspeed = round($user_class->moddedspeed * $userStatBonusMultiplier);
if ($user_class->gang > 0) {
    // Strength
    $db->query("SELECT upgrade1 FROM gangs WHERE id = " . $user_class->gang);
    $db->execute();
    $u = $db->fetch_row(true);
    $percent = $u['upgrade1'] * 20;
    $user_class->moddedstrength += round(($user_class->moddedstrength * $percent) / 100);

    // Defense
    $db->query("SELECT upgrade2 FROM gangs WHERE id = " . $user_class->gang);
    $db->execute();
    $u = $db->fetch_row(true);
    $percent = $u['upgrade2'] * 20;
    $user_class->moddeddefense += round(($user_class->moddeddefense * $percent) / 100);

    // Speed
    $db->query("SELECT upgrade3 FROM gangs WHERE id = " . $user_class->gang);
    $db->execute();
    $u = $db->fetch_row(true);
    $percent = $u['upgrade3'] * 20;
    $user_class->moddedspeed += round(($user_class->moddedspeed * $percent) / 100);
}

$attack_person->moddedstrength = round($attack_person->moddedstrength * $attackStatBonusMultiplier);
$attack_person->moddeddefense = round($attack_person->moddeddefense * $attackStatBonusMultiplier);
$attack_person->moddedspeed = round($attack_person->moddedspeed * $attackStatBonusMultiplier);
if ($attack_person->gang > 0) {
    // Strength
    $db->query("SELECT upgrade1 FROM gangs WHERE id = " . $attack_person->gang);
    $db->execute();
    $u = $db->fetch_row(true);
    $percent = $u['upgrade1'] * 20;
    $attack_person->moddedstrength += round(($attack_person->moddedstrength * $percent) / 100);

    // Defense
    $db->query("SELECT upgrade2 FROM gangs WHERE id = " . $attack_person->gang);
    $db->execute();
    $u = $db->fetch_row(true);
    $percent = $u['upgrade2'] * 20;
    $attack_person->moddeddefense += round(($attack_person->moddeddefense * $percent) / 100);

    // Speed
    $db->query("SELECT upgrade3 FROM gangs WHERE id = " . $attack_person->gang);
    $db->execute();
    $u = $db->fetch_row(true);
    $percent = $u['upgrade3'] * 20;
    $attack_person->moddedspeed += round(($attack_person->moddedspeed * $percent) / 100);
}

$userspeed = $user_class->moddedspeed;
$attackspeed = $attack_person->moddedspeed;

$wait = ($userspeed > $attackspeed) ? 1 : 0;
$number = 0;

$rtn = array();
if ($user_class->invincible == 0) {
    if ($attack_person->invincible == 0) {
        while ($yourhp > 0 && $theirhp > 0) {
            $damage = round($attack_person->moddedstrength) - $user_class->moddeddefense;
            $damage = ($damage < 1) ? 1 : $damage;
            if ($wait == 0) {
                $yourhp = $yourhp - $damage;
                $number++;
                $rtn[] = $number . ":&nbsp;" . $attack_person->formattedname . " hit you for " . prettynum($damage) . " damage using their " . $attack_person->weaponname . ". <br>";
            } else {
                $wait = 0;
            }
            if ($yourhp > 0) {
                $damage = round($user_class->moddedstrength) - $attack_person->moddeddefense;
                $damage = ($damage < 1) ? 1 : $damage;
                $theirhp = $theirhp - $damage;
                $number++;
                $rtn[] = $number . ":&nbsp;" . "You hit " . $attack_person->formattedname . " for " . prettynum($damage) . " damage using your " . $user_class->weaponname . ". <br>";
            }
        }
    } else {
        $yourhp = 0;
    }
} else {
    $theirhp = 0;
}

$respect_earnt = 0;
if ($theirhp <= 0) {
    $winner = $user_class->id;
    $moneywon = floor($attack_person->money / rand(8, 9));
    $expwon = 100 - (100 * ($user_class->level - $attack_person->level));
    $expwon = ($expwon < 20) ? 20 : $expwon;
    $expwon = ($expwon > 10000) ? 10000 : $expwon;
    $expwon = floor($expwon);
    $theirhp = 0;

    $db->query("SELECT `name` FROM cities WHERE `id` = " . $user_class->city);
    $db->execute();
    $cityn = $db->fetch_row(true);
    $cityname = $cityn['name'];
    $db->query("SELECT `id`, `city`, `king`, `queen` FROM `grpgusers` WHERE `id` = ?");
    $db->execute([$attack_person->id]);
    $row = $db->fetch_row();
    if (isset($row[0])) {
        $row = $row[0];
        // Check if the attacked person is king and the winner is male
        if ($row['king'] == $user_class->city) {
            // Dethrone the current king
            $db->query("UPDATE `grpgusers` SET `king` = 0, `queen` = 0 WHERE `id` = ?");
            $db->execute([$attack_person->id]);
            // Crown the new king
            $db->query("UPDATE `grpgusers` SET `king` = ?, `queen` = 0 WHERE `id` = ?");
            $db->execute([$user_class->city, $winner->id]);

            // Send event notifications
            Send_Event($attack_person->id, "You have been defeated and lost your status as Boss of " . $cityname . ".");
            Send_Event($winner, "Congratulations! You have defeated the Boss of " . $cityname . ".");
        }

        // Check if the attacked person is queen and the winner is female
        if ($row['queen'] == $user_class->city) {
            // Dethrone the current queen
            $db->query("UPDATE `grpgusers` SET `queen` = 0, `king` = 0 WHERE `id` = ?");
            $db->execute([$attack_person->id]);

            // Crown the new queen
            $db->query("UPDATE `grpgusers` SET `queen` = ?, `king` = 0 WHERE `id` = ?");
            $db->execute([$user_class->city, $winner->id]);

            // Send event notifications
            Send_Event($attack_person->id, "You have been defeated and lost your status as Under Boss of " . $cityname . ".");
            Send_Event($winner, "Congratulations! You have defeated the Under Boss of " . $cityname . ".");
        }
    }

    $spots = range(1, 10);
    $db->query("SELECT * FROM attackladder");
    $db->execute();
    $attackLadder = $db->fetch_row();

    $spotsTaken = [];
    foreach ($attackLadder as $s) {
        $spotsTaken[] = $s['spot'];
    }

    $spare = array_diff($spots, $spotsTaken);

    $winnerKey = array_search($winner, array_column($attackLadder, 'user'));
    $attackedKey = array_search($attack_person->id, array_column($attackLadder, 'user'));

    if ($user_class->id == 0) {
        var_dump($winnerKey);
        var_dump($attackedKey);
    }

    if ($winnerKey === false && $attackedKey === false && !empty($spare)) {
        $db->query("INSERT INTO attackladder (user, spot, last_attack) VALUES (?, ?, ?)");
        $db->execute([$winner, reset($spare), time()]);
    } else {
        if ($attackedKey !== false && $winnerKey !== false) { // both are on the ladder
            if ($attackLadder[$winnerKey]['spot'] > $attackLadder[$attackedKey]['spot']) {

                $db->query("UPDATE `attackladder` SET `spot` = ?, last_attack = ? WHERE `user` = ?");
                $db->execute([$attackLadder[$attackedKey]['spot'], time(), $winner]);

                $db->query("UPDATE `attackladder` SET `spot` = ? WHERE `user` = ?");
                $db->execute([$attackLadder[$attackedKey]['spot'] + 1, $attack_person->id]);

                // winner spot = 8
                // attacked spot = 2
                // winner takes 2
                // attacked spot + 1 (3)
                // cascade down +1 (3 = 4)
                // cascade down +1 (4 = 5) etc..
                // until 10

                $db->query("UPDATE `attackladder` SET `spot` = `spot` + 1 WHERE `user` = ? AND `spot` > ?");
                $db->execute([$attack_person->id, $attackLadder[$attackedKey]['spot'] + 1]);

                $db->query("DELETE FROM `attackladder` WHERE `spot` > 10");
                $db->execute();

                // if (($attackLadder[$winnerKey]['spot'] + 2) <= 10) {
                //     for ($i = $attackLadder[$winnerKey]['spot'] + 2; $i <= 10; $i++) {

                //     }
                // }

                Send_Event($attack_person->id, "[-_USERID_-] You've been knocked from your place in the Attack Ladder ", $attack_person->id);
            }
        } else if ($attackedKey !== false && $winnerKey === false) { // attacked person is on ladder but winner is not
            $db->query("UPDATE `attackladder` SET `user` = ?, last_attack = ? WHERE `spot` = ?");
            $db->execute([$winner, time(), $attackLadder[$attackedKey]['spot']]);
            Send_Event($attack_person->id, "[-_USERID_-] You've been knocked from your place in the Attack Ladder ", $attack_person->id);
        }
    }

    $db->query("UPDATE `attackladder` SET `last_attack` = ? WHERE `user` = ?");
    $db->execute([time(), $winner]);

    bloodbath('defendlost', $attack_person->id);
    bloodbath('attackswon', $user_class->id);
    $toadd = array('kotd' => 1);
    ofthes($user_class->id, $toadd);
    $db->query("UPDATE grpgusers SET koth = koth + 1, loth = loth + ?, todaysexp = todaysexp + ?, exp = exp + ?, money = money + ?, battlewon = battlewon + 1, battlemoney = battlemoney + ?, todayskills = todayskills + 1, killcomp1 = killcomp1 + 1 WHERE id = ?");
    $db->execute(array(
        $expwon,
        $expwon,
        $expwon,
        $moneywon,
        $moneywon,
        $user_class->id
    ));

    $currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
    if (isset($currentQuestSeason['id'])) {
        $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
        $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
        $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
        if (
            isset($questSeasonMission['requirements']['attacks']) &&
            (int) $questSeasonMissionUser['progress']['attacks'] < (int) $questSeasonMission['requirements']['attacks']
        ) {
            updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'attacks', 1);
        }
    }

    $db->query("UPDATE grpgusers SET money = money - ?, hwho = ?, hhow = 'wasattacked', hwhen = ?, hospital = 120, battlelost = battlelost + 1, delay = delay + 10, battlemoney = battlemoney - ? WHERE id = ?");
    $db->execute(array(
        $moneywon,
        $user_class->id,
        date("g:i:sa", time()),
        $moneywon,
        $attack_person->id
    ));
    $db->query("UPDATE pets SET exp = exp + ($expwon) / 10 WHERE userid = ? AND leash = 1");
    $db->execute([$user_class->id]);

    // UserCompLeaderboard
    addToUserCompLeaderboard($user_class->id, 'attacks_complete', 1);
    addToRelCompLeaderboard($user_class->id, 'attacks_complete', 1);
    addToGangCompLeaderboard($user_class->gang, 'attacks_complete', 1);
    $bpCategory = getBpCategory();
    if ($bpCategory) {
        addToBpCategoryUser($bpCategory, $user_class, 'attacks', 1);
    }

    $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
    $db->execute();
    $activityContest = $db->fetch_row(true);
    if ($activityContest['type'] == 'attacks') {
        addToUserCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
        addToRelCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
    }

    Send_Event($attack_person->id, "[-_USERID_-] attacked you and won! They gained " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . ".", $user_class->id);
    $count = count($rtn);
    if ($count > 5) {
        //echo $rtn[0] . $rtn[1] . '...<br />' . $rtn[$count - 3] . $rtn[$count - 2] . $rtn[$count - 1];
    } else {
        foreach ($rtn as $text) {
            //echo $text;
        }
    }
    $message = "You attacked " . $attack_person->formattedname . " and won! You gain " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . ".";
    if ($user_class->gang != 0) {
        $db->query("UPDATE gangs SET exp = exp + ?, bbattackwon = bbattackwon + 1, dailyKills = dailyKills + 1 WHERE id = ?");
        $db->execute(array(
            $expwon,
            $user_class->gang
        ));
    }

    contribute_mission('k');
    newmissions('kills');
    updateGangActiveMission('kills', 1);
    gangContest(array(
        'kills' => 1,
        'exp' => $expwon
    ));

    if ((time() - $attack_person->lastactive) < 900) {
        addToUserOperations($user_class, 'online_attacks', 1);
    }
}

if ($yourhp <= 0) {
    $winner = $attack_person->id;
    $moneywon = floor($user_class->money / rand(8, 9));
    $expwon = 100 - (100 * ($attack_person->level - $user_class->level));
    $expwon = ($expwon < 20) ? 20 : $expwon;
    $expwon = ($expwon > 10000) ? 10000 : $expwon;
    $expwon *= (.15 * $attack_person->prestige) + 1;
    $expwon *= (.01 * $attack_person->battlemult);
    $expwon = floor($expwon);
    $expwon2 = $expwon;
    $yourhp = 0;

    bloodbath('attackslost', $user_class->id);
    bloodbath('defendwon', $attack_person->id);
    $db->query("UPDATE grpgusers SET todaysexp = todaysexp + ?, exp = exp + ?, money = money + ?, battlewon = battlewon + 1, battlemoney = battlemoney + ?, todayskills = todayskills + 1 WHERE id = ?");
    $db->execute(array(
        $expwon,
        $expwon,
        $moneywon,
        $moneywon,
        $attack_person->id
    ));
    $db->query("UPDATE grpgusers SET money = money - ?, hwho = ?, hhow = 'attacked', hwhen = ?, hospital = 300, battlelost = battlelost + 1, delay = delay + 10, battlemoney = battlemoney - ? WHERE id = ?");
    $db->execute(array(
        $moneywon,
        $attack_person->id,
        date("g:i:sa", time()),
        $moneywon,
        $user_class->id
    ));
    $db->query("UPDATE gangs SET bbattacklost = bbattacklost + 1 WHERE id = ?");
    $db->execute(array(
        $user_class->gang
    ));
    $db->query("UPDATE pets SET exp = exp + ($expwon) / 10 WHERE userid = ? AND leash = 1");
    $db->execute([$attack_person->id]);
    Send_Event($attack_person->id, "[-_USERID_-] attacked you and lost! You gained " . prettynum($expwon) . " exp and stole $" . prettynum($moneywon) . ".", $user_class->id);
    $count = count($rtn);
    if ($count > 5) {
        //echo $rtn[0] . $rtn[1] . '...<br />' . $rtn[$count - 3] . $rtn[$count - 2] . $rtn[$count - 1];
    } else {
        foreach ($rtn as $text) {
            //echo $text;
        }
    }
    $message = $attack_person->formattedname . " won the battle!";
    if ($attack_person->gang != 0) {
        $db->query("UPDATE gangs SET exp = exp + ? WHERE id = ?");
        $db->execute(array(
            $expwon,
            $attack_person->gang
        ));
    }
}
$db->query("INSERT INTO attacklog (`timestamp`, attacker, defender, winner, exp, money, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
$db->execute([time(), $user_class->id, $attack_person->id, $winner, $expwon, $moneywon, (time() - $attack_person->lastactive <= 900) ? 1 : 0]);
$expwon2 = intval($expwon);
if ($attack_person->gang != 0) {
    $active = (time() - $attack_person->lastactive < 900) ? 1 : 0;
    $db->query("INSERT INTO deflog (timestamp, gangid, attacker, defender, winner, gangexp, active, respect) VALUES (unix_timestamp(), ?, ?, ?, ?, ?, ?, ?)");
    $db->execute(array(
        $attack_person->gang,
        $user_class->id,
        $attack_person->id,
        $winner,
        $expwon2,
        $active,
        $respect_earnt
    ));
}
if ($user_class->gang != 0) {
    $active = (time() - $attack_person->lastactive < 900) ? 1 : 0;
    $db->query("INSERT INTO attlog (timestamp, gangid, attacker, defender, winner, gangexp, active, respect) VALUES (unix_timestamp(), ?, ?, ?, ?, ?, ?, ?)");
    $db->execute(array(
        $user_class->gang,
        $user_class->id,
        $attack_person->id,
        $winner,
        $expwon2,
        $active,
        $respect_earnt
    ));
}
$winner_class = new User($winner);
$db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1");
$db->execute(array(
    $user_class->gang,
    $user_class->gang,
    $attack_person->gang,
    $attack_person->gang
));
if ($winner_class->gang != 0 && $db->num_rows()) {
    $row = $db->fetch_row(true);
    if (time() < $row['timeending']) {
        $active = (time() - $attack_person->lastactive < 900) ? 50 : 10;
        $wingang = ($winner_class->gang == $row['gang1']) ? 1 : 2;
        $db->query("UPDATE gangwars SET gang{$wingang}score = gang{$wingang}score + ? WHERE warid = ?");
        $db->execute(array(
            $active,
            $row['warid']
        ));
        //print "<br />You have also gained $active gang war points for your gang.";
    }
}
//$user_class->stamina -= 1;
$theirhp = ($theirhp > $attack_person->puremaxhp) ? $attack_person->puremaxhp : $theirhp;
$yourhp = ($yourhp > $user_class->puremaxhp) ? $user_class->puremaxhp : $yourhp;
$db->query("UPDATE grpgusers SET hp = ? WHERE id = ?");
$db->execute(array(
    $theirhp,
    $attack_person->id
));



$db->query("UPDATE `grpgusers` SET `energy` = `energy` - {$energyneeded}, `last_attack_time` = " . time() . " WHERE `id` = {$user_class->id}");
$db->execute();

echo json_encode(success($message));
exit;
