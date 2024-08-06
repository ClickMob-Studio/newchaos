<?php
include "header.php";


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


                <br />
                <a href="ajax_super_attack_id.php?v2=yes&level_limit=50" class="commit-super-attack-link"><button>Attack Up To Level 50</button></a>
                <a href="ajax_super_attack_id.php?v2=yes&level_limit=100" class="commit-super-attack-link"><button>Attack Up To Level 100</button></a>
                <a href="ajax_super_attack_id.php?v2=yes&level_limit=250" class="commit-super-attack-link"><button>Attack Up To Level 250</button></a>
                <a href="ajax_super_attack_id.php?v2=yes&level_limit=5000" class="commit-super-attack-link"><button>Attack All</button></a>
            </center>


        </div>
    </div>

    <script type="text/javascript">
        let inProcess = 0;
        $('.commit-super-attack-link').click(function(e) {
            e.preventDefault();

            $('.commit-super-attack-link').hide();

            if (inProcess > 0) {
                return false;
            }
            inProcess = 1;

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            var request = $.ajax({
                url: $(this).attr('href') + '&alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><p>You don't have anyone you can attack at the moment. Consider trying a different city.</p></div>";
                    $(".ajax-message-holder").html(resMes);
                    $(".ajax-message-holder").show();
                } else {
                    var i = 1;
                    var arLength = res.attack_id.length;
                    for (let attackingId of res.attack_id) {
                        var request = $.ajax({
                            url: 'ajax_attack.php?attack=' + attackingId.id + '&csrf=<?php echo $csrf  ?>&alv=yes',
                            method: "GET",
                            dataType: "json"
                        });
                        request.done(function (resTwo) {
                            if (resTwo.success == false || resTwo.success == 'false') {
                                var resMes = "<div class='alert alert-danger ajax-alert-div'><p>" + resTwo.error + "</p></div>";
                            } else {
                                var resMes = "<div class='alert alert-info ajax-alert-div'><p>" + resTwo.message + "</p></div>";
                            }

                            $(".ajax-message-holder").html(resMes);
                            $(".ajax-message-holder").show();
                            $(".temp-spinner").remove();
                        });

                        console.log((arLength - 1));
                        if (i > (arLength - 1)) {
                            inProcess = 0;
                        }
                        i++;
                    }
                }
            });

            //for (var i = 1; i < 20; i++) {
            //    var request = $.ajax({
            //        url: $(this).attr('href') + '&alv=yes',
            //        method: "GET",
            //        dataType: "json"
            //    });
            //    request.done(function (res) {
            //        if (res.success == false || res.success == 'false') {
            //            var resMes = "<div class='alert alert-danger ajax-alert-div'><p>You don't have anyone you can attack at the moment. Consider trying a different city.</p></div>";
            //            $(".ajax-message-holder").html(resMes);
            //            $(".ajax-message-holder").show();
            //        } else {
            //            var request = $.ajax({
            //                url: 'ajax_attack.php?attack=' + res.attack_id + '&csrf=<?php //echo $csrf  ?>//&alv=yes',
            //                method: "GET",
            //                dataType: "json"
            //            });
            //            request.done(function (resTwo) {
            //                if (resTwo.success == false || resTwo.success == 'false') {
            //                    var resMes = "<div class='alert alert-danger ajax-alert-div'><p>" + resTwo.error + "</p></div>";
            //                } else {
            //                    var resMes = "<div class='alert alert-info ajax-alert-div'><p>" + resTwo.message + "</p></div>";
            //                }
            //
            //                $(".ajax-message-holder").html(resMes);
            //                $(".ajax-message-holder").show();
            //                $(".temp-spinner").remove();
            //            });
            //
            //        }
            //    });
            //
            //    if (i > 19) {
            //        location.reload();
            //    }
            //}


            $('.commit-super-attack-link').show();

            //location.reload();
        });
    </script>

<?php

include 'footer.php';