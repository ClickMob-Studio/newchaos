<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
}

if ($questSeasonUser['is_complete'] > 0) {
    // TODO: Check whether there is another quest season to start

    echo "
        <div class='alert alert-success'>
            <strong>Success!</strong> You have completed the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
            <a href='quest.php' class='btn btn-primary'>Start your next quest</a>.
        </div>
    ";
    exit;
}

if (isset($questSeasonMissionUser) && $questSeasonMissionUser && $questSeasonMissionUser['is_complete'] > 0) {
    $payouts = json_decode($questSeasonMission['payouts'], true);
    $payoutsToDisplay = 'You have received the following payouts:<br />';
    $payoutsToDisplay .= '<ul>';
    foreach ($payouts as $field => $value) {
        if ($field === 'items') {
            foreach ($value as $itemId => $quantity) {
                Give_Item($itemId, $user_class->id, $quantity);

                $payoutsToDisplay .= '<li>' . number_format($quantity, 0) . ' x ' . Item_Name($itemId) . '</li>';
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

    $currentMissionId = $questSeasonMission['id'];
    $nextMission = $db->query('SELECT * FROM quest_season_missions WHERE quest_season_id = ? AND id > ? ORDER BY id ASC LIMIT 1');
    $nextMission->execute(array($currentQuestSeason['id'], $currentMissionId));
    $nextMission = $nextMission->fetch_row(true);
    echo 'here'; exit;

    if ($nextMission) {
        $progress = array();
        $nextMission['requirements'] = json_decode($nextMission['requirements']);
        foreach ($nextMission['requirements'] as $key => $req) {
            $progress[$key] = 0;
        }

        $db->query('INSERT INTO quest_season_mission_user (user_id, quest_season_id, quest_season_mission_id, progress, is_complete) VALUES (?, ?, ?, ?, 0)', array($user_class->id, $currentQuestSeason['id'], $nextMission['id'], json_encode($progress)));
    } else {
        // Mark the quest season as completed
        $db->query('UPDATE quest_season_users SET is_complete = 1 WHERE user_id = ? AND quest_season_id = ?', array($user_class->id, $currentQuestSeason['id']));
    }

    echo "
        <div class='alert alert-success'>
            <strong>Success!</strong> You have completed the mission <strong>{$questSeasonMission['name']}</strong> for the quest <strong>{$currentQuestSeason['name']}</strong>.<br /><br />
            {$payoutsToDisplay}
            <br /><br />
            <a href='quest.php' class='btn btn-primary'>Start your next mission</a>.
        </div>
    ";
    exit;
}

if (isset($_GET['mode']) && $_GET['mode'] === 'therustnail' && isset($questSeasonMission['requirements']->vinny_the_fish_delivery)) :
    $doors = ['fail', 'success', 'jail', 'hospital'];
    shuffle($doors);

    if ($user_class->jail > 0 || $user_class->hospital > 0) {
        echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You are currently in jail or hospital and cannot complete this quest.
            </div>
        ";
        exit;
    }

    if (isset($_GET['door'])) {
        $selectedDoor = (int)$_GET['door'];

        if ($doors[$selectedDoor] === 'success') {
            echo "
               <div class='alert alert-success'>
                    <strong>Success!</strong> Congratulations! You have found the success door!
               </div>
            ";

            updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'vinny_the_fish_delivery', 1);

            header('Location: quest.php');
            exit;
        } elseif ($doors[$selectedDoor] === 'jail') {
            echo "
                    <div class='alert alert-danger'>
                        <strong>Fail!</strong> You open the door to find a police officer waiting for you. You have been arrested!
                    </div>
                ";
            $db->query("UPDATE grpgusers SET jail = 300 WHERE id = ?");
            $db->execute(array($user_class->id));
        } elseif ($doors[$selectedDoor] === 'hospital') {
            echo "
                    <div class='alert alert-danger'>
                        <strong>Fail!</strong> You open the door on a disgruntled man taking a piss, he punches you in the face and you fall to the ground!
                    </div>
                ";
            $db->query("UPDATE grpgusers SET hospital = 120 WHERE id = ?");
            $db->execute(array($user_class->id));
        } else {
            echo "
                    <div class='alert alert-danger'>
                        <strong>Fail!</strong> You opened the wrong door.
                    </div>
                ";
        }
    }
    ?>
    <h1>The Rusty Nail</h1><hr />
    <p>
        You enter the Rusty Nail and the bartender tells you to leave the package in the secret hiding spot in the toilet. Your confused because
        the Don never mentioned this, but either way you head to the toilet. You see 4 doors, which one do you choose?
    </p>

    <div class="row">
        <?php foreach ($doors as $index => $outcome): ?>
            <div class="col-md-3">
                <a href="?mode=therustnail&door=<?php echo $index; ?>"><img src="css/images/NewGameImages/cubical-door.png" width="100%" class="img-responsive" /></a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    exit;
endif;
?>

<?php
if ($questSeasonUser) {

?>

<h1>Quest: <?php echo $currentQuestSeason['name'] ?></h1><hr />
<p><?php echo $currentQuestSeason['description'] ?></p>
<hr />

<h2>Mission: <?php echo $questSeasonMission['name'] ?></h2>
<p><?php echo $questSeasonMission['description'] ?></p>

<h2><strong>Progress:</strong></h2>
<ul>
    <?php foreach ($questSeasonMission['requirements'] as $req => $num): ?>
        <li><?php echo getDisplayForQuestReq($req, $num) ?></li>
    <?php endforeach; ?>
</ul>

    <?php

} else {

}
