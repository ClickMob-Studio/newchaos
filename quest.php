<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
}


if (!$currentQuestSeason) {
    $nextQuestSeason = getNextQuestSeason($user_class->id, $user_class->admin);

    if ($nextQuestSeason && isset($nextQuestSeason['id'])) {
        $db->query('INSERT INTO quest_season_user (user_id, quest_season_id, is_complete) VALUES (?, ?, 0)');
        $db->execute(array($user_class->id, $nextQuestSeason['id']));

        echo "
            <div class='alert alert-success'>
                <strong>Success!</strong> You have completed the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
                <a href='quest.php' class='btn btn-primary'>Start the next quest</a>.
            </div>
        ";
        exit;
    } else {
        echo "
            <div class='alert alert-success'>
                <strong>Success!</strong> You have completed the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
                You have completed all available quests. Check back another time for more quests.
            </div>
        ";
        exit;
    }
} else {
    if ($questSeasonUser['is_complete'] > 0) {
        $nextQuestSeason = getNextQuestSeason($user_class->id, $user_class->admin);

        if ($nextQuestSeason && isset($nextQuestSeason['id'])) {
            $db->query('INSERT INTO quest_season_user (user_id, quest_season_id, is_complete) VALUES (?, ?, 0)');
            $db->execute(array($user_class->id, $nextQuestSeason['id']));

            echo "
            <div class='alert alert-success'>
                <strong>Success!</strong> You have completed the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
                <a href='quest.php' class='btn btn-primary'>Start the next quest</a>.
            </div>
        ";
            exit;
        } else {
            echo "
            <div class='alert alert-success'>
                <strong>Success!</strong> You have completed the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
                You have completed all available quests. Check back another time for more quests.
            </div>
        ";
            exit;
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'next_mission' && $questSeasonMissionUser['is_complete'] > 0 && $questSeasonMissionUser['is_paid_out'] > 0) {
    $currentMissionId = $questSeasonMission['id'];
    $db->query('SELECT * FROM quest_season_mission WHERE quest_season_id = ? AND id > ? ORDER BY id ASC LIMIT 1');
    $db->execute(array($currentQuestSeason['id'], $currentMissionId));
    $nextMission = $db->fetch_row(true);

    if ($nextMission) {
        $progress = array();
        $nextMission['requirements'] = json_decode($nextMission['requirements']);
        foreach ($nextMission['requirements'] as $key => $req) {
            $progress[$key] = 0;
        }

        $db->query('INSERT INTO quest_season_mission_user (user_id, quest_season_id, quest_season_mission_id, progress, is_complete) VALUES (?, ?, ?, ?, 0)');
        $db->execute(array($user_class->id, $currentQuestSeason['id'], $nextMission['id'], json_encode($progress)));
    } else {
        // Mark the quest season as completed
        $db->query('UPDATE quest_season_user SET is_complete = 1 WHERE user_id = ? AND quest_season_id = ?');
        $db->execute(array($user_class->id, $currentQuestSeason['id']));
    }


    header('Location: quest.php');
    exit;
}

if (isset($questSeasonMissionUser) && $questSeasonMissionUser && $questSeasonMissionUser['is_complete'] > 0) {
    $payoutsToDisplay = '';
    if (!$questSeasonMissionUser['is_paid_out']) {
        $payouts = json_decode($questSeasonMission['payouts'], true);
        $payoutsToDisplay = 'You have received the following payouts:<br />';
        $payoutsToDisplay .= '<ul>';
        foreach ($payouts as $field => $value) {
            if ($field === 'items') {
                foreach ($value as $key => $item) {
                    Give_Item($item['id'], $user_class->id, $item['quantity']);

                    $payoutsToDisplay .= '<li>' . number_format($item['quantity'], 0) . ' x ' . Item_Name($item['id']) . '</li>';
                }
            } else {
                if ($field === 'exp') {
                    $value = $user_class->maxexp / 100 * $value;
                }
                $payoutsToDisplay .= '<li>' . number_format($value, 0) . ' ' . ucwords($field) . '</li>';
                $db->query('UPDATE grpgusers SET ' . $field . ' = ' . $field . ' + ? WHERE id = ?');
                $db->execute(array($value, $user_class->id));
            }
        }
        $payoutsToDisplay .= '</ul>';

        $db->query('UPDATE quest_season_mission_user SET is_paid_out = 1 WHERE id = ?');
        $db->execute(array($questSeasonMissionUser['id']));
        invalidateQuestSeasonCache($user_class->id, $currentQuestSeason['id']);
    }


    $currentMissionId = $questSeasonMission['id'];
    $db->query('SELECT * FROM quest_season_mission WHERE quest_season_id = ? AND id > ? ORDER BY id ASC LIMIT 1');
    $db->execute(array($currentQuestSeason['id'], $currentMissionId));
    $nextMission = $db->fetch_row(true);
    if ($nextMission) {
        $progress = array();
        $nextMission['requirements'] = json_decode($nextMission['requirements']);
        foreach ($nextMission['requirements'] as $key => $req) {
            $progress[$key] = 0;
        }

        $db->query('INSERT INTO quest_season_mission_user (user_id, quest_season_id, quest_season_mission_id, progress, is_complete) VALUES (?, ?, ?, ?, 0)');
        $db->execute([$user_class->id, $currentQuestSeason['id'], $nextMission['id'], json_encode($progress)]);
        invalidateQuestSeasonCache($user_class->id, $currentQuestSeason['id']);
    } else {
        // Mark the quest season as completed
        $db->query('UPDATE quest_season_user SET is_complete = 1 WHERE user_id = ? AND quest_season_id = ?');
        $db->execute([$user_class->id, $currentQuestSeason['id']]);
        invalidateQuestSeasonCache($user_class->id, $currentQuestSeason['id']);
    }

    echo "
        <div class='alert alert-success'>
            <strong>Success!</strong> You have completed the mission <strong>{$questSeasonMission['name']}</strong> for the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
            {$payoutsToDisplay}
            <br /><br />
            <a href='quest.php?action=next_mission' class='btn btn-primary'>Start your next mission</a>.
        </div>
    ";
    exit;
}

if (isset($_GET['mode']) && $_GET['mode'] === 'therustnail' && isset($questSeasonMission['requirements']['vinny_the_fish_delivery'])):
    include 'quest/vinny_the_fish_delivery.php';
endif;

if (isset($_GET['mode']) && $_GET['mode'] === 'marocs_pharmacy' && isset($questSeasonMission['requirements']['pharmacy_protection'])):
    include 'quest/pharmacy_protection.php';
endif;

if (isset($_GET['mode']) && $_GET['mode'] === 'follow_salvatore' && isset($questSeasonMission['requirements']['follow_salvatore'])):
    include 'quest/follow_salvatore.php';
endif;

if (isset($_GET['mode']) && $_GET['mode'] === 'steal_books' && isset($questSeasonMission['requirements']['steal_books'])):
    include 'quest/steal_books.php';
endif;

if (isset($_GET['mode']) && $_GET['mode'] === 'interrogate_phil' && isset($questSeasonMission['requirements']['interrogate_phil'])):
    include 'quest/interrogate_phil.php';
endif;
?>

<?php
if ($questSeasonUser) {
    ?>

    <h1>Quest: <?php echo $currentQuestSeason['name'] ?></h1>
    <hr />
    <p><?php echo $currentQuestSeason['description'] ?></p>
    <hr />

    <h2>Mission: <?php echo $questSeasonMission['name'] ?></h2>
    <p><?php echo $questSeasonMission['description'] ?></p>

    <h2><strong>Progress:</strong></h2>
    <ul>
        <?php foreach ($questSeasonMission['requirements'] as $req => $num): ?>
            <li><?php echo getDisplayForQuestReq($req, $num, $questSeasonMissionUser['progress']) ?></li>
        <?php endforeach; ?>
    </ul>

    <?php

} else {
    echo "
        <div class='alert alert-danger'>
           You have completed all available quests. Check back another time for more quests.
        </div>
    ";
}
