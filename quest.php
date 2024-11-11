<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
}

if (isset($_GET['mode']) && $_GET['mode'] === 'therustnail'):
    $doors = ['fail', 'fail', 'success'];
    shuffle($doors);

    if (isset($_GET['door'])) {
        $selectedDoor = (int)$_GET['door'];
        if ($doors[$selectedDoor] === 'success') {
            echo "<p>Congratulations! You have found the success door!</p>";
        } else {
            echo "<p>Sorry, this door is a fail. Try again!</p>";
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
            <div class="col-md-4">
                <a href="?mode=therustnail&door=<?php echo $index; ?>"><img src="css/images/NewGameImages/cubical-door.png" width="50%" /></a>
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
