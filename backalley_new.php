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

                <?php if ($userBaStats['gold_rush_credits'] > 0): ?>
                    <div class="alert alert-info gold-rush-mode">
                        <p>YOU CURRENTLY HAVE <span class="gold-rush-credits-text"><?php echo $userBaStats['gold_rush_credits'] ?></span> GOLD RUSH CREDITS REMAINING!</p>
                    </div>
                <?php endif; ?>

                <div id="ba-response-message" style="min-height: 60px; display: none;"></div>

                <br />
                <hr />

                <button class="ba-search-link">Search</button>
                <button class="ba-med-pack-link">Use Med Pack (x<span class="med-pack-count"><?php echo $medPackTotalCount ?></span>)</button>
                <button class="ba-refill-energy-link">Refill Energy</button>

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

        $('.ba-search-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            if (requestInProcess) {
                console.log('*** IN PROCESS');
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                console.log('***********');
                console.log(res);
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
