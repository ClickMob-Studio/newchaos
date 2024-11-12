<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
}

if (isset($questSeasonMissionUser) && $questSeasonMissionUser && $questSeasonMissionUser['is_complete'] > 0) {
    $payouts = json_decode($questSeasonMission['payouts'], true);
    $payoutToDisplay = 'You have received the following payouts:<br />';
    foreach ($payouts as $field => $value) {
        if ($field === 'items') {
            foreach ($value as $itemId => $quantity) {
                Give_Item($itemId, $user_class->id, $quantity);

                $payoutsToDisplay .= number_format($quantity, 0) . ' x ' . Item_Name($itemId) . '<br />';
            }
        } else {
            if ($field === 'exp') {
                $value = $user_class->maxexp / 100 * $value;
            }
            $payoutsToDisplay .= number_format($value, 0) . ' ' . ucwords($field) . '<br />';
            $db->query('UPDATE grpgusers SET ' . $field . ' = ' . $field . ' + ? WHERE id = ?');
            $db->execute(array($value, $user_class->id));
        }
    }

    echo "
        <div class='alert alert-success'>
            <strong>Success!</strong> You have completed the mission <strong>{$questSeasonMission['name']}</strong> for the quest <strong>{$currentQuestSeason['name']}</strong>.<br />
            {$payoutsToDisplay}
            <a href='quest.php'>Start your next mission</a>.
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
