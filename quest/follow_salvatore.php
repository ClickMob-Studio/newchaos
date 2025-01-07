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
