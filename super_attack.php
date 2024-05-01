<?php
include "header.php";

if ($user_class->admin < 1) {
    exit;
}

$userPrestigeSkills = getUserPrestigeSkills($user_class);
if ($userPrestigeSkills['speed_attack_unlock'] < 1) {
    diefun("You need to unlock this feature with prestige unlocks.");
}

if (checkCaptchaRequired($user_class)) {
    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=super_attack');
}

?>

<div class='box_top'>Super Attacks</div>
<div class='box_middle'>
    <div class='pad'>
        <p>Welcome to Super Attacks, click the button below to start attacking! With every click, you'll attack a random attackable offline player.</p>

        <br />
        <a href="#"><button>Commit Attack</button></a>


    </div>
</div>

<?php

include 'footer.php';