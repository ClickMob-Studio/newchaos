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

$csrf = md5(uniqid(rand(), TRUE));
$_SESSION['csrf'] = $csrf;

?>

<div class='box_top'>Super Attacks</div>
<div class='box_middle'>
    <div class='pad'>
        <center>
            <p>Welcome to Super Attacks, click the button below to start attacking! With every click, you'll attack a random attackable offline player.</p>

            <div class='ajax-message-holder' style='min-height: 60px; display: none;'></div>

            <br />
            <a href="ajax_super_attack_id.php" id="commit-super-attack-link"><button>Commit Attack</button></a>
        </center>


    </div>
</div>

<script type="text/javascript">
    $('#commit-super-attack-link').click(function(e) {
        e.preventDefault();

        let clicked = $(this);

        $(".ajax-alert-div").remove();
        $(this).hide();
        $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

        var request = $.ajax({
            url: $(this).attr('href') + '?alv=yes',
            method: "GET",
            dataType: "json"
        });
        request.done(function (res) {
             console.log(res);
            if (res.success == false || res.success == 'false') {
                var resMes = "<div class='alert alert-danger ajax-alert-div'><p>You don't have anyone you can attack at the moment. Consider trying a different city.</p></div>";
                $(".ajax-message-holder").html(resMes);
                $(".ajax-message-holder").show();
            } else {
                var request = $.ajax({
                    url: 'ajax_attack.php?attack=' + res.attack_id + '&csrf=<?php echo $csrf  ?>&alv=yes',
                    method: "GET",
                    dataType: "json"
                });
                request.done(function (resTwo) {
                    console.log(resTwo);
                    if (resTwo.success == false || resTwo.success == 'false') {
                        var resMes = "<div class='alert alert-danger ajax-alert-div'><p>" + resTwo.error + "</p></div>";
                    } else {
                        var resMes = "<div class='alert alert-info ajax-alert-div'><p>" + resTwo.message + "</p></div>";
                    }

                    $(".ajax-message-holder").html(resMes);
                    $(".ajax-message-holder").show();
                    $(".temp-spinner").remove();
                    clicked.show();
                    $('#commit-super-attack-link').show();

                    requestInProcess = false;
                });

            }
        });

    });
</script>

<?php

include 'footer.php';