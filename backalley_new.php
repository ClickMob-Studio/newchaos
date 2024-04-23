<?php

session_start();
include 'header.php';

if ($user_class->admin < 1) {
    echo 'exit'; exit;
}

$userBaStats = getUserBaStats($user_class);

$medPackOneCount = check_items(13, $user_class->id);
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
                    Welcome to the Back Alley! Here you will battle against different opponents,
                    which But will you take the risk when its 20% energy per attack.
                    If you fail you will find yourself in the hospital
                </p>

                <br />
                <table class="new_table" style="width:100%;">
                    <thead>
                        <tr>
                            <th colspan="3">Your Back Alley Stats</th>
                        </tr>
                        <tr>
                            <th>Searches</th>
                            <th>Wins</th>
                            <th>Losses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="ba-stats-searches"><?php echo number_format($userBaStats['turns'], 0) ?></span></td>
                            <td><span class="ba-stats-wins"><?php echo number_format($userBaStats['wins'], 0) ?></span></td>
                            <td><span class="ba-stats-losses"><?php echo number_format($userBaStats['losses'], 0) ?></span></td>
                        </tr>
                    </tbody>
                </table>
                <br /><hr />

                <p style="font-weight: bold;">You Back Alley Skill Set is currently level 1</p>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="progress" role="progressbar" aria-valuenow="<?php echo ($userBaStats['exp'] / $userBaStats['maxexp'] * 100 ); ?>" aria-valuemin="0" aria-valuemax="100" title="<?php echo $userBaStats['exp'] . '/' . number_format($userBaStats['maxexp'], 0); ?>">
                            <div class="progress-bar bg-success" style="width: <?php echo ($userBaStats['exp'] / $userBaStats['maxexp'] * 100 ); ?>%"></div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <hr />


                <div class="alert alert-info gold-rush-mode" <?php if ($userBaStats['gold_rush_credits'] < 1): ?> style="display:none;"<?php endif; ?>>
                    <p style="color: #000;">YOU CURRENTLY HAVE <span class="gold-rush-credits-text"><?php echo $userBaStats['gold_rush_credits'] ?></span> GOLD RUSH CREDITS REMAINING!</p>
                </div>

                <div id="ba-response-message" style="min-height: 145px;"></div>

                <br />
                <hr />

                <button class="ba-btn ba-search-link">Search</button>
                <button class="ba-btn ba-med-pack-link">Use Med Pack (x<span class="med-pack-count"><?php echo $medPackTotalCount ?></span>)</button>
                <button class="ba-btn ba-refill-energy-link">Refill Energy</button>

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
    $(document).ready(function() {
        let requestInProcess = false;

        <?php if ($userBaStats['gold_rush_credits'] > 0): ?>
            $('.ba-btn').addClass('gold-rush-mode');
        <?php endif; ?>

        $('.ba-search-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            if (requestInProcess) {
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                    $("#newtables tbody").prepend('<tr><td>' + res.error + '<hr /></td></tr>');
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

                $('.med-pack-count').html(res.med_pack_count);
                $('.ba-stats-searches').html(res.user_ba_stats.turns);
                $('.ba-stats-wins').html(res.user_ba_stats.wins);
                $('.ba-stats-losses').html(res.user_ba_stats.losses);
                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();
                $(".temp-spinner").remove();
                clicked.show();

                requestInProcess = false;
            });
        });

        $('.ba-refill-energy-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            if (requestInProcess) {
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?ba_action=refill_energy&alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                    $("#newtables tbody").prepend('<tr><td>' + res.error + '<hr /></td></tr>');
                } else {
                    var resMes = "<div class='alert alert-info ajax-alert-div'><center><p>" + res.message + "</p></center></div>";
                    $("#newtables tbody").prepend('<tr><td>' + res.message + '<hr /></td></tr>');
                }

                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();
                $(".temp-spinner").remove();
                clicked.show();

                requestInProcess = false;
            });
        });

        $('.ba-med-pack-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            if (requestInProcess) {
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?ba_action=use_med_pack&alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                    $("#newtables tbody").prepend('<tr><td>' + res.error + '<hr /></td></tr>');
                } else {
                    var resMes = "<div class='alert alert-info ajax-alert-div'><center><p>" + res.message + "</p></center></div>";
                    $("#newtables tbody").prepend('<tr><td>' + res.message + '<hr /></td></tr>');
                }

                $('.med-pack-count').html(res.med_pack_count);
                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();
                $(".temp-spinner").remove();
                clicked.show();

                requestInProcess = false;
            });
        });
    });
</script>
