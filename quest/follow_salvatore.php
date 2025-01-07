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
