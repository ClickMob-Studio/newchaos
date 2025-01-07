<?php
if ($user_class->jail > 0 || $user_class->hospital > 0) {
    echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You are currently in jail or hospital and cannot complete this quest.
            </div>
        ";
    exit;
}
?>

<style>
    .direction-button {
        background-color: #333333; /* Primary color */
        border: none;
        color: white;
        padding: 12px 24px; /* Larger button size */
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px; /* Larger font size */
        margin: 10px 5px; /* Spacing between buttons */
        cursor: pointer;
        border-radius: 5px; /* Rounded corners */
        box-shadow: 0 0 10px #ffd700; /* Initial glow */
        transition: background-color 0.3s, box-shadow 0.3s; /* Smooth transition for background and glow effect */
    }
</style>
<h1>Follow Salvatore</h1><hr />

<?php
if (isset($_GET['follow_salvatore']) && in_array($_GET['follow_salvatore'], array('north', 'west', 'east', 'south'))) {
    $direction = $_GET['follow_salvatore'];

    $chance = mt_rand(1, 100);

    if ($chance <= 10) {
        // Jail
        $jailTime = mt_rand(60, 300);
        $db->query("UPDATE grpgusers SET jail = ? WHERE id = ?");
        $db->execute(array($jailTime, $user_class->id));

        echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You head " . ucfirst($direction) . " and bump into a police officer that's been looking for you. You have been arrested.
            </div>";
    } else if ($chance <= 20) {
        // Hospital
        $hospitalTime = mt_rand(60, 300);
        $db->query("UPDATE grpgusers SET hospital = ? WHERE id = ?");
        $db->execute(array($hospitalTime, $user_class->id));

        echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You head " . ucfirst($direction) . " and bump into one of Salvatore's men. He punches you in the face and you fall to the ground. You'll need to spend some time in the hospital.
            </div>";
    } else if ($chance <= 30) {
        // Success
        echo "
               <div class='alert alert-success'>
                    <strong>Success!</strong> Congratulations! You have completed the search for Salvatore!
               </div>
            ";

        updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'follow_salvatore', 1);

        header('Location: quest.php');
    } else {
        echo "
            <div class='alert alert-info'>
                You head " . ucfirst($direction) . ", following Salvatore's trail. Where do you think he'll head next?
            </div>
            ";
    }
}
?>

<div class="contenthead floaty" style="text-align: center; padding: 20px; margin-bottom: 20px; border-radius: 8px; width: 88%;">
    <br>
    <a class="direction-button" href="?mode=follow_salvatore&follow_salvatore=north">North</a><br><br>
    <a class="direction-button" href="?mode=follow_salvatore&follow_salvatore=west">West</a>
    <a class="direction-button" href="#" onclick="return false;">Search</a>
    <a class="direction-button" href="?mode=follow_salvatore&follow_salvatore=east">East</a><br><br>
    <a class="direction-button" href="?mode=follow_salvatore&follow_salvatore=south">South</a>
</div>

<?php
exit;
