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
    <button class="direction-button" href="#">North</button><br><br>
    <button class="direction-button" href="#">West</button>
    <button class="direction-button" href="#">Search</button>
    <button class="direction-button" href="#">East</button><br><br>
    <button class="direction-button" href="#">South</button>
</div>
