<?php

include "ajax_header.php";
include "SlimUser.php";
$user_class = new SlimUser($_SESSION['id']);

//
// FETCH JAIL USERS
//

if (isset($_GET['action'])  && $_GET['action'] == 'fetch_users') {
    $ignore = array($user_class->id);
    $ignore = implode(',', $ignore);

    $db->query("SELECT id, jail FROM grpgusers WHERE jail > 0 AND id NOT IN ($ignore) ORDER BY RAND() LIMIT 4");
    $db->execute();
    $rows = $db->fetch_row();

    foreach ($rows as $key => $row) {
        $rows[$key]['username'] = str_replace('</a>', '', preg_replace('/<a[^>]*>/', '', formatName($row['id'])));

        $time = floor($row['jail'] / 60) . 'm';
        $rows[$key]['time'] = $time;
    }

    if ($user_class->jail_bot_credits > 0 && $user_class->is_jail_bots_active) {
        $i = 1;
        $limit = $user_class->jail_bot_credits;
        if ($limit > 15) {
            $limit = 15;
        }

        while ($i <= $limit) {
            $row = array();
            $row['id'] = 'bot';
            $row['username'] = 'Bot';
            $row['time'] = '2m';

            $rows[] = $row;


            $i++;
        }
    }

    echo json_encode($rows);
}

//
// BUST BOTS
//
if (isset($_GET['jailbreak'])  && $_GET['jailbreak'] == 'bot') {
    $error = false;
    $expEarned = mt_rand(1, 10);

    $error = false;
    if ($user_class->jail_bot_credits < 1) {
        $error = 'You do not have any bot credits remaining.';
    }
    if ($user_class->hospital > 0) {
        $error = "You can't break people out of jail whilst your in hospital.";
    }
    if ($user_class->jail > 0) {
        $error = "You can't break people out of jail whilst your in jail.";
    }

    if (!$error) {
        $exp = $expEarned + $user_class->exp;
        $crimesucceeded = 1 + $user_class->crimesucceeded;

        $db->query("UPDATE grpgusers SET `both` = `both` + 1, `epoints` = `epoints` + `eventbusts`, `bustcomp` = `bustcomp` + 1, exp =  ".$exp.", busts = busts + 1, jail_bot_credits = jail_bot_credits - 1 WHERE id = ".$user_class->id);
        $db->execute();

        $user_class->jail_bot_credits = $user_class->jail_bot_credits - 1;
        mission('b');
        newmissions('busts');
        updateGangActiveMission('busts', 1);
        gangContest(array(
            'busts' => 1,
            'exp' => $exp
        ));
        $toadd = array('botd' => 1);
        ofthes($user_class->id, $toadd);
        bloodbath('busts', $user_class->id);
        addToUserOperations($user_class, 'busts', 1);

        addToGangCompLeaderboard($user_class->gang, 'busts_complete', 1);

        addToUserCompLeaderboard($user_class->id, 'busts_complete', 1);

        $currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
        if (isset($currentQuestSeason['id'])) {
            $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
            $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
            $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
        }
        if (isset($questSeasonMission['requirements']->busts)) {
            updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'busts', 1);
        }


        payoutChristmasGift($user_class->id);
        $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
        $db->execute();
        $activityContest = $db->fetch_row(true);
        if ($activityContest['type'] == 'busts') {
            addToUserCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
        }

        $bpCategory = getBpCategory();
        if ($bpCategory) {
            addToBpCategoryUser($bpCategory, $user_class, 'busts', 1);
        }

        echo json_encode(array(
            'success' => true,
            'jail_bot_credits' => $user_class->jail_bot_credits,
            'message' => "Success! You receive ".$expEarned." exp "
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'error' => $error
        ));
    }
}
