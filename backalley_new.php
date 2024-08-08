<?php


session_start();
include 'header.php';

if (isset($_GET['forced_captcha']) && $_GET['forced_captcha'] == 'yes') {
    mysql_query('UPDATE `grpgusers` SET `captcha_timestamp` = 0 WHERE `id` = ' . $user_class->id);

    header('Location: backalley_new.php');
}

//if ($user_class->admin < 1 || $user_class->id < 398) {
//    echo 'exit'; exit;
//}

if (checkCaptchaRequired($user_class)) {
    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=backalley');
}

$userBaStats = getUserBaStats($user_class);

$medPackOneCount = 0;
$medPackTwoCount = check_items(14, $user_class->id);
$medPackTotalCount = $medPackOneCount + $medPackTwoCount;
?>

<style>
    .gold-rush-mode {
        background-color: #FFC300;
        border-color: #F1B801;
        color: #000000;
    }
</style>

<div class="box_top"><h1>Back Alley</h1></div>
<div class="box_middle">
    <div class="row">
        <div class="col-md-12">
            <center>
                <p>
                    Welcome to the Back Alley! Here you will battle against different opponents in the hope of finding something great, but
                    be careful, you may just end up with a trip to the hospital!
                </p>

                <br />
                <table class="new_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th colspan="4">Your Back Alley Stats</th>
                        </tr>
                        <tr>
                            <th>Searches</th>
                            <th>Wins</th>
                            <th>Losses</th>
                            <th>Win %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="ba-stats-searches"><?php echo number_format($userBaStats['turns'], 0) ?></span></td>
                            <td><span class="ba-stats-wins"><?php echo number_format($userBaStats['wins'], 0) ?></span></td>
                            <td><span class="ba-stats-losses"><?php echo number_format($userBaStats['losses'], 0) ?></span></td>
                            <td><span class="ba-stats-win-p"><?php echo number_format(($userBaStats['wins'] / $userBaStats['turns'] * 100), 0) ?>%</span></td>
                        </tr>
                        <tr>
                            <th>Points</th>
                            <th>Cash</th>
                            <th>Items</th>
                            <th>EXP</th>
                        </tr>
                        <tr>
                            <td><span class="ba-stats-points"><?php echo number_format($userBaStats['points_gained'], 0) ?></span></td>
                            <td><span class="ba-stats-cash">$<?php echo number_format($userBaStats['cash_gained'], 0) ?></span></td>
                            <td><span class="ba-stats-items"><?php echo number_format($userBaStats['items_gained'], 0) ?></span></td>
                            <td><span class="ba-stats-exp"><?php echo number_format($userBaStats['exp_gained'], 0) ?></span></td>
                        </tr>
                    </tbody>
                </table>
                <br /><hr />

                <p style="font-weight: bold;">You Back Alley Skill Set is currently level <?php echo $userBaStats['level'] ?></p>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="progress" role="progressbar" aria-valuenow="<?php echo ($userBaStats['exp'] / $userBaStats['maxexp'] * 100 ); ?>" aria-valuemin="0" aria-valuemax="100" title="<?php echo $userBaStats['exp'] . '/' . number_format($userBaStats['maxexp'], 0); ?>">
                            <div class="progress-bar bg-success ba-level-progress-bar" style="width: <?php echo ($userBaStats['exp'] / $userBaStats['maxexp'] * 100 ); ?>%"></div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <hr />


                <div class="alert alert-info gold-rush-mode" <?php if ($userBaStats['gold_rush_credits'] < 1): ?> style="display:none;"<?php endif; ?>>
                    <p style="color: #000;">YOU CURRENTLY HAVE <span class="gold-rush-credits-text"><?php echo $userBaStats['gold_rush_credits'] ?></span> GOLD RUSH CREDITS REMAINING!</p>
                </div>

                <div id="ba-response-message" style="min-height: 195px; max-height: 195px;"></div>

                <br />
                <hr />

                <div id="btn-holder" style="min-height: 30px;">
                    <button class="ba-btn ba-search-link">Search</button>
                    <button class="ba-btn ba-med-pack-link">Use Med Pack (x<span class="med-pack-count"><?php echo $medPackTotalCount ?></span>)</button>
                    <button class="ba-btn ba-refill-energy-link">Refill Energy</button>
                </div>

                <hr />

                <table class="new_table" id="newtables" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Outcomes</th>
                        </tr>
                    </thead>
                    <tbody id="ba-tbody">
                    
                    </tbody>
                </table>
            </center>

        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
<script type="text/javascript">
        // Function to check if JavaScript is enabled
        function isJavaScriptEnabled() {
            // Add a class to the body indicating JavaScript is enabled
            document.body.classList.add('js-enabled');
        }

        // Call the function to check if JavaScript is enabled
        isJavaScriptEnabled();
    </script>

    <style>
        /* Styles to hide content if JavaScript is disabled */
        body:not(.js-enabled) main {
            display: none;
        }
    </style>
<script type="text/javascript">

// Function to check if developer tools are open
function areDevToolsOpen() {
    // Start with an arbitrary large value
    let widthThreshold = window.outerWidth - window.innerWidth > 160;
    let heightThreshold = window.outerHeight - window.innerHeight > 160;

    // Check if the dimensions change when developer tools are opened
    window.addEventListener('resize', function(event) {
        // If the difference in dimensions is significant, consider developer tools open
        let widthChanged = window.outerWidth - window.innerWidth > 160;
        let heightChanged = window.outerHeight - window.innerHeight > 160;
        
        if (widthChanged !== widthThreshold || heightChanged !== heightThreshold) {
            var request = $.ajax({
                    url: 'ajax_autoclick_detection.php?page=backalley&reason=dev_tools_is_open',
                    method: "GET",
                    dataType: "json"
                });
                request.done(function (res) {
                    console.log(res);
                });
            window.location.reload();
        }
    });

    // Check if dimensions are already at the threshold when the page loads
    if (widthThreshold || heightThreshold) {
        return true; // Consider developer tools open initially
    }

    return false;
}

// Check if developer tools are open when the page loads
// if (areDevToolsOpen()) {
//     var request = $.ajax({
//                     url: 'ajax_autoclick_detection.php?page=backalley&reason=dev_tools_is_open',
//                     method: "GET",
//                     dataType: "json"
//                 });
//                 request.done(function (res) {
//                     console.log(res);
//                 });
//     window.location.reload();
// }

    window.setTimeout(function(){
        window.location.reload();
    }, 5 * 60 * 1000); // Reload after 5 mins of being on the page

    let clickCount = 0;

    document.addEventListener("DOMContentLoaded",function(){
        document.body.addEventListener('click', function(evt) {
            clickCount = clickCount + 1;
            if (clickCount > 600) {
                window.location.href = "/backalley_new.php?forced_captcha=yes";
            }

            // Check for an actual mouse click (1, 2 & 3)
            if (evt.which > 3) {
                var request = $.ajax({
                    url: 'ajax_autoclick_detection.php?page=backalley&reason=invalid_click',
                    method: "GET",
                    dataType: "json"
                });
                request.done(function (res) {
                    console.log(res);
                });
            }

            if (evt.isTrusted) {

            } else {
                var request = $.ajax({
                    url: 'ajax_autoclick_detection.php?page=backalley&reason=click_not_trusted',
                    method: "GET",
                    dataType: "json"
                });
                request.done(function (res) {
                    console.log(res);
                });
            }
        }, true);
    });

    $(document).ready(function() {
        let requestInProcess = false;
        let preventClickTime = false;

        let lastClick;
        // $("body").click(function (e) {
        //     if (lastClick > 0) {
        //         var clickDuration = ((new Date()).getTime() - lastClick)
        //         if (clickDuration > 800) {
        //             preventClickTime = false;
        //         } else {
        //             preventClickTime = true
        //         }
        //     }
        //
        //     lastClick = (new Date()).getTime();
        // });

        <?php if ($userBaStats['gold_rush_credits'] > 0): ?>
            $('.ba-btn').addClass('gold-rush-mode');
        <?php endif; ?>

        $('.ba-search-link').click(function(e) {
            e.preventDefault();

            if (lastClick > 0) {
                var clickDuration = ((new Date()).getTime() - lastClick)
                if (clickDuration > 300) {
                    preventClickTime = false;
                } else {
                    preventClickTime = true
                }
            }

            if (preventClickTime) {
                var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>You can only search the Backalley four times per second!</p></center></div>";

                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();

                requestInProcess = false;

            } else {
                lastClick = (new Date()).getTime();

                let clicked = $(this);

                $(".ajax-alert-div").remove();
                $(this).hide();
                $(this).after(
                    '<button class="ba-btn temp-spinner" style="min-width: 100px;"><img id="spinner" src="images/ajax-loader.gif"/></button>'
                );

                if (requestInProcess) {
                    return false;
                }


                var request = $.ajax({
                    url: 'ajax_ba_new.php?alv=yes',
                    headers: { 'AJAVERI': 'IAMAJA' },
                    method: "GET",
                    dataType: "json"
                });
                request.done(function (res) {
                    if (res.success == false || res.success == 'false') {
                        var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                        //$("#newtables tbody").prepend('<tr><td>' + res.error + '<hr /></td></tr>');
                    } else {
                        var resMes = "<div class='alert alert-info ajax-alert-div'><center><p>" + res.message + "</p></center></div>";
                        $("#newtables tbody").prepend('<tr><td>' + res.message + '<hr /></td></tr>');
                    }

                    if (res.gold_rush_credits > 0) {
                        $('.ba-btn').addClass('gold-rush-mode');
                        $('.gold-rush-mode').show();
                        $('.gold-rush-credits-text').html(res.gold_rush_credits);
                    } else {
                        $('.ba-btn').removeClass('gold-rush-mode');
                        $('.gold-rush-mode').hide();
                    }

                    if (res.med_pack_count)  {
                        $('.med-pack-count').html(res.med_pack_count);
                    }

                    if (res.user_ba_stats) {
                        $('.ba-stats-searches').html(addCommas(res.user_ba_stats.turns));
                        $('.ba-stats-wins').html(addCommas(res.user_ba_stats.wins));
                        $('.ba-stats-losses').html(addCommas(res.user_ba_stats.losses));
                        $('.ba-stats-points').html(addCommas(res.user_ba_stats.points_gained));
                        $('.ba-stats-cash').html('$' + addCommas(res.user_ba_stats.cash_gained));
                        $('.ba-stats-items').html(addCommas(res.user_ba_stats.items_gained));
                        $('.ba-stats-exp').html(addCommas(res.user_ba_stats.exp_gained));

                        var pbWidth = res.user_ba_stats.exp / res.user_ba_stats.maxexp * 100;
                        $('.ba-level-progress-bar').width(pbWidth + '%');
                    }
                    $("#ba-response-message").html(resMes);
                    $("#ba-response-message").show();
                    $(".temp-spinner").remove();
                    clicked.show();

                    $('.ba-btn').show();

                    requestInProcess = false;
                });

            }
        });

        $('.ba-refill-energy-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<button class="ba-btn temp-spinner" style="min-width: 100px;"><img id="spinner" src="images/ajax-loader.gif"/></button>');

            if (requestInProcess) {
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?ba_action=refill_energy&alv=yes',
                headers: { 'AJAVERI': 'IAMAJA' },
                method: "GET",
                dataType: "json",
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                } else {
                    var resMes = "<div class='alert alert-info ajax-alert-div'><center><p>" + res.message + "</p></center></div>";
                }

                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();
                $(".temp-spinner").remove();
                clicked.show();

                $('.ba-btn').show();

                requestInProcess = false;
            });
        });

        $('.ba-med-pack-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<button class="ba-btn temp-spinner" style="min-width: 100px;"><img id="spinner" src="images/ajax-loader.gif"/></button>');

            if (requestInProcess) {
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?ba_action=use_med_pack&alv=yes',
                headers: { 'AJAVERI': 'IAMAJA' },
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                } else {
                    var resMes = "<div class='alert alert-info ajax-alert-div'><center><p>" + res.message + "</p></center></div>";
                }

                $('.med-pack-count').html(res.med_pack_count);
                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();
                $(".temp-spinner").remove();
                clicked.show();
                $('.ba-btn').show();

                requestInProcess = false;
            });
        });
    });

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

</script>
